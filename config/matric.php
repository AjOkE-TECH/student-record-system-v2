<?php

/*
 * =========================================================
 *  MATRIC NUMBER HELPER LIBRARY
 * =========================================================
 *
 *  Server-side generation and validation of automatic
 *  matriculation numbers for the Student Record Management
 *  System.
 *
 *  Format (exact):
 *      ND + <department_code> + "/" + <2-digit year> + "/" + <4-digit seq>
 *
 *  Example:
 *      NDCS/26/0001
 *
 *  The sequence is stored in `matric_sequences` with a UNIQUE
 *  combination of (department_id, admission_year). Generation
 *  uses an atomic UPDATE ... LAST_INSERT_ID(current_value + 1)
 *  so concurrent requests can never receive the same number,
 *  and the sequence only ever moves FORWARD - numbers are
 *  never re-used even if a student is archived.
 */

if (!function_exists('srsValidateAdmissionYear')) {

    /**
     * Validate an admission year. Keeps the FULL year (e.g. 2026).
     *
     * @param mixed $year
     * @return int|false Valid full year, or false if invalid.
     */
    function srsValidateAdmissionYear($year)
    {
        $year = trim((string) $year);

        if ($year === '') {
            return (int) date('Y');
        }

        if (!preg_match('/^\d{4}$/', $year)) {
            return false;
        }

        $year_int = (int) $year;

        if ($year_int < 1950 || $year_int > 2100) {
            return false;
        }

        return $year_int;
    }
}

if (!function_exists('srsGetDepartmentById')) {

    /**
     * Fetch a department row by id.
     *
     * @return array|null
     */
    function srsGetDepartmentById($conn, $departmentId)
    {
        $departmentId = (int) $departmentId;

        if ($departmentId <= 0) {
            return null;
        }

        $stmt = mysqli_prepare($conn,
            "SELECT id, department_name, department_code
             FROM departments
             WHERE id = ?
             LIMIT 1"
        );

        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $departmentId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return $row ? $row : null;
    }
}

if (!function_exists('srsGetDepartmentByName')) {

    /**
     * Fetch a department row by exact (case-insensitive) name.
     *
     * @return array|null
     */
    function srsGetDepartmentByName($conn, $departmentName)
    {
        $departmentName = trim((string) $departmentName);

        if ($departmentName === '') {
            return null;
        }

        $stmt = mysqli_prepare($conn,
            "SELECT id, department_name, department_code
             FROM departments
             WHERE UPPER(department_name) = UPPER(?)
             LIMIT 1"
        );

        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, 's', $departmentName);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return $row ? $row : null;
    }
}

if (!function_exists('srsBuildMatric')) {

    /**
     * Build the exact matric string from a department code,
     * a full admission year, and a raw sequence number.
     *
     * @return string
     */
    function srsBuildMatric($departmentCode, $admissionYear, $sequenceNumber)
    {
        $code    = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $departmentCode));
        $yearStr = substr((string) $admissionYear, -2);
        $seqStr  = sprintf('%04d', (int) $sequenceNumber);

        return 'ND' . $code . '/' . $yearStr . '/' . $seqStr;
    }
}

if (!function_exists('srsMatricExists')) {

    /**
     * Check whether a matric number already exists - in BOTH
     * active AND archived students. An archived matric number
     * is permanently reserved.
     *
     * @return bool
     */
    function srsMatricExists($conn, $matricNo)
    {
        $stmt = mysqli_prepare($conn,
            "SELECT id FROM students WHERE matric_no = ? LIMIT 1"
        );

        if (!$stmt) {
            return true; /* fail safe */
        }

        mysqli_stmt_bind_param($stmt, 's', $matricNo);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $exists = mysqli_num_rows($result) > 0;
        mysqli_stmt_close($stmt);

        return $exists;
    }
}

if (!function_exists('srsAutoMatric')) {

    /**
     * Generate the next automatic matric number for a department
     * and admission year.
     *
     * The value is obtained inside the caller's transaction via an
     * atomic, serialized UPDATE ... LAST_INSERT_ID(current_value + 1),
     * so two simultaneous requests can never receive the same number.
     *
     * @param mysqli $conn
     * @param int    $departmentId
     * @param int    $admissionYear  Full year, e.g. 2026
     * @param string &$error         Set on failure
     * @return string                Generated matric number ("" on failure)
     */
    function srsAutoMatric($conn, $departmentId, $admissionYear, &$error)
    {
        $error = '';

        $departmentId  = (int) $departmentId;
        $admissionYear = srsValidateAdmissionYear($admissionYear);

        if ($departmentId <= 0) {
            $error = 'Please select a valid department.';
            return '';
        }

        if ($admissionYear === false) {
            $error = 'Please enter a valid admission year (full year, e.g. 2026).';
            return '';
        }

        $department = srsGetDepartmentById($conn, $departmentId);

        if (!$department) {
            $error = 'The selected department no longer exists.';
            return '';
        }

        $code = strtoupper(trim($department['department_code']));

        if ($code === '' || !preg_match('/^[A-Z0-9]+$/', $code)) {
            $error = 'The selected department has no valid department code.';
            return '';
        }

        /* Make sure a sequence row exists. */
        $stmt = mysqli_prepare($conn,
            "INSERT IGNORE INTO matric_sequences (department_id, admission_year, current_value)
             VALUES (?, ?, 0)"
        );
        mysqli_stmt_bind_param($stmt, 'ii', $departmentId, $admissionYear);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $matricNo = '';
        $attempts = 0;

        do {

            /* serialized increment. Each connection sees its own value. */
            $stmt = mysqli_prepare($conn,
                "UPDATE matric_sequences
                 SET current_value = LAST_INSERT_ID(current_value + 1)
                 WHERE department_id = ? AND admission_year = ?"
            );
            mysqli_stmt_bind_param($stmt, 'ii', $departmentId, $admissionYear);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $seqRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT LAST_INSERT_ID() AS seq"));
            $seq    = (int) $seqRow['seq'];

            if ($seq < 1) {
                $error = 'Unable to generate a matric sequence value.';
                return '';
            }

            $matricNo = srsBuildMatric($code, $admissionYear, $seq);
            $attempts++;

            /* Defensive: skip if it already exists (legacy data). */
        } while (srsMatricExists($conn, $matricNo) && $attempts < 10000);

        if ($attempts >= 10000) {
            $error = 'Could not find a free matric number for this department and year.';
            return '';
        }

        return $matricNo;
    }
}

if (!function_exists('srsNextMatricPreview')) {

    /**
     * Read-only PREVIEW of the next matric number for a
     * department and year. Does NOT consume the sequence.
     *
     * @return string|null Matric preview, or null on error
     */
    function srsNextMatricPreview($conn, $departmentId, $admissionYear)
    {
        $departmentId  = (int) $departmentId;
        $admissionYear = srsValidateAdmissionYear($admissionYear);

        if ($departmentId <= 0 || $admissionYear === false) {
            return null;
        }

        $department = srsGetDepartmentById($conn, $departmentId);

        if (!$department) {
            return null;
        }

        $code = strtoupper(trim($department['department_code']));

        $stmt = mysqli_prepare($conn,
            "SELECT current_value FROM matric_sequences
             WHERE department_id = ? AND admission_year = ?"
        );
        mysqli_stmt_bind_param($stmt, 'ii', $departmentId, $admissionYear);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $current = 0;

        if ($row = mysqli_fetch_assoc($result)) {
            $current = (int) $row['current_value'];
        }

        mysqli_stmt_close($stmt);

        $next = $current + 1;
        $matricNo = srsBuildMatric($code, $admissionYear, $next);

        /* Skip any numbers already in use  */
        while (srsMatricExists($conn, $matricNo) && $next < 100000) {
            $next++;
            $matricNo = srsBuildMatric($code, $admissionYear, $next);
        }

        return $matricNo;
    }
}