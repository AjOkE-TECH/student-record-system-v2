<?php

/*
 * =========================================================
 *  SEND MAIL (PHPMailer)
 * =========================================================
 *
 *  sendStudentEmail() NEVER throws to the caller and NEVER
 *  rolls back a student record. It returns an array:
 *
 *      array(
 *          'success' => bool,
 *          'reason'  => string (machine-readable code),
 *          'message' => string (safe, human-readable, no secrets),
 *          'detail'  => string (server-side diagnostic only,
 *                               written to the PHP error log,
 *                               never shown to the user),
 *      )
 *
 *  srsEmailDiagnose() performs a safe pre-flight that tells the
 *  difference between missing credentials, connection failure,
 *  TLS failure, missing PHP extensions/library and Gmail
 *  App-Password issues - without ever exposing the password.
 */

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

// Include PHPMailer library files
require 'Mail/src/Exception.php';
require 'Mail/src/PHPMailer.php';
require 'Mail/src/SMTP.php';
require_once 'mail_config.php';

/**
 * Safe SMTP configuration/connectivity pre-flight.
 *
 * @return array{status:string,reason:string,message:string}
 */
function srsEmailDiagnose(): array
{
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        return array(
            'status'  => 'error',
            'reason'  => 'library_missing',
            'message' => 'The PHPMailer library is not installed or cannot be loaded on the server.',
        );
    }

    if (!extension_loaded('openssl')) {
        return array(
            'status'  => 'error',
            'reason'  => 'openssl_missing',
            'message' => 'The PHP OpenSSL extension is not enabled, so TLS/encrypted email sending is unavailable.',
        );
    }

    $s = srsMailSettings();

    $username = trim($s['SMTP_USERNAME']);

    if ($username === '') {
        return array(
            'status'  => 'error',
            'reason'  => 'credentials_missing',
            'message' => 'SMTP credentials are missing: no SMTP username is configured.',
        );
    }

    if ($s['SMTP_PASSWORD'] === '') {
        return array(
            'status'  => 'error',
            'reason'  => 'credentials_missing',
            'message' => 'SMTP credentials are missing: the SMTP password for the configured username is empty.',
        );
    }

    $host = trim($s['SMTP_HOST']);
    $port = (int) $s['SMTP_PORT'];

    if (stripos($host, 'gmail.com') !== false && strlen($s['SMTP_PASSWORD']) < 16) {
        return array(
            'status'  => 'error',
            'reason'  => 'credentials_invalid',
            'message' => 'The configured SMTP password is too short for Gmail. Gmail requires a 16-character App Password '
                       . '(enable 2-Step Verification first). A normal Google account password will be rejected.',
        );
    }

    /* Raw TCP pre-flight (with timeout) to separate "cannot reach
       the server" from auth/TLS problems. No credentials are sent. */
    $errno = 0;
    $errstr = '';
    $fp = @stream_socket_client(
        'tcp://' . $host . ':' . $port,
        $errno,
        $errstr,
        10
    );

    if (!$fp) {
        return array(
            'status'  => 'error',
            'reason'  => 'connection_failed',
            'message' => 'Could not connect to the SMTP server on ' . $host . ':' . $port
                       . '. Verify the SMTP host/port and that outbound mail traffic is allowed on this network.',
        );
    }

    stream_set_timeout($fp, 10);

    $readReply = function ($sock) {
        $out = '';
        while (($line = fgets($sock)) !== false) {
            $out .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        return $out;
    };

    $greeting = $readReply($fp);
    fwrite($fp, "EHLO srs.local\r\n");
    $ehlo = $readReply($fp);

    $encryption = strtoupper(trim($s['SMTP_ENCRYPTION']));
    $tlsOk = true;

    if ($encryption === 'TLS' || $encryption === 'STARTTLS') {
        fwrite($fp, "STARTTLS\r\n");
        $tlsReply = $readReply($fp);
        if (!preg_match('/^2\d\d/', trim($tlsReply))) {
            $tlsOk = false;
        }
    }

    fclose($fp);

    if (!$tlsOk) {
        return array(
            'status'  => 'error',
            'reason'  => 'tls_failed',
            'message' => 'The SMTP server was reachable, but STARTTLS/TLS negotiation failed. '
                       . 'Check the encryption setting (tls/ssl) and confirm the server supports it.',
        );
    }

    return array(
        'status'  => 'ok',
        'reason'  => 'ok',
        'message' => 'SMTP server reachable and TLS capable. Credentials are configured.',
        'host'    => $host,
        'port'    => $port,
        'source'  => $s['CONFIG_SOURCE'],
        'greeting'=> trim($greeting),
    );
}

/**
 * Send a welcome email to a newly registered student.
 *
 * @param string $studentEmail  The student's email address
 * @param string $studentName   The student's full name
 * @param string $matricNo      The student's matric number
 * @param string $department    The student's department
 * @param string $level         The student's level
 * @param string $admissionYear The student's admission year
 *
 * @return array{success:bool,reason:string,message:string,detail:string}
 */
function sendStudentEmail(
    string $studentEmail,
    string $studentName,
    string $matricNo = '',
    string $department = '',
    string $level = '',
    string $admissionYear = ''
): array
{
    $makeResult = function (bool $success, string $reason, string $message, string $detail = '') {
        return array(
            'success' => $success,
            'reason'  => $reason,
            'message' => $message,
            'detail'  => $detail,
        );
    };

    /* 1. Invalid recipient - detected before any network work. */
    if (!filter_var($studentEmail, FILTER_VALIDATE_EMAIL)) {
        return $makeResult(
            false,
            'recipient_invalid',
            'The student email address appears to be invalid, so the welcome email was not attempted.'
        );
    }

    /* 2. Pre-flight diagnostics (missing credentials, connection,
          TLS and Gmail App-Password checks). All messages are safe. */
    $pre = srsEmailDiagnose();

    if (($pre['status'] ?? '') !== 'ok') {
        error_log('[SRMS email] Pre-flight blocked: ' . $pre['reason'] . ' - ' . $pre['message']);
        return $makeResult(false, $pre['reason'], $pre['message']);
    }

    $s = srsMailSettings();

    $mail = new PHPMailer(true);

    try {

        /* ===========================
           SMTP configuration
        =========================== */
        $mail->isSMTP();
        $mail->Host     = $s['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $s['SMTP_USERNAME'];
        $mail->Password = $s['SMTP_PASSWORD'];

        $encryption = strtoupper(trim($s['SMTP_ENCRYPTION']));

        if ($encryption === 'SSL' || $encryption === 'SMTPS') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($encryption === 'TLS' || $encryption === 'STARTTLS') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mail->SMTPSecure = '';
        }

        $mail->Port = (int) $s['SMTP_PORT'];

        /* Sender: prefer MAIL_FROM, otherwise fall back to the
           authenticated SMTP user (never hard-coded). */
        $fromAddress = trim($s['MAIL_FROM']) !== '' ? trim($s['MAIL_FROM']) : trim($s['SMTP_USERNAME']);
        $fromName    = trim($s['MAIL_FROM_NAME']) !== '' ? trim($s['MAIL_FROM_NAME']) : 'Student Record System';

        $mail->setFrom($fromAddress, $fromName);

        $mail->addAddress($studentEmail, $studentName);

        /* ===========================
           Content
        =========================== */
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to Student Record Management System';

        $safeName   = htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8');
        $safeEmail  = htmlspecialchars($studentEmail, ENT_QUOTES, 'UTF-8');
        $safeMatric = htmlspecialchars($matricNo, ENT_QUOTES, 'UTF-8');
        $safeDept   = htmlspecialchars($department, ENT_QUOTES, 'UTF-8');
        $safeLevel  = htmlspecialchars($level, ENT_QUOTES, 'UTF-8');
        $safeYear   = htmlspecialchars($admissionYear, ENT_QUOTES, 'UTF-8');

        $mail->Body = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #006400; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background-color: #f9f9f9; padding: 30px; border-radius: 0 0 5px 5px; }
        .info-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .info-table td { padding: 8px 12px; border-bottom: 1px solid #e0e0e0; }
        .info-table td:first-child { font-weight: bold; width: 160px; color: #555; }
        .footer { margin-top: 20px; text-align: center; font-size: 12px; color: #888; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Welcome, {$safeName}!</h2>
        </div>
        <div class="content">
            <p>Your student record has been successfully created. Here are your details:</p>
            <table class="info-table">
                <tr><td>Matric Number</td><td>{$safeMatric}</td></tr>
                <tr><td>Name</td><td>{$safeName}</td></tr>
                <tr><td>Department</td><td>{$safeDept}</td></tr>
                <tr><td>Level</td><td>{$safeLevel}</td></tr>
                <tr><td>Admission Year</td><td>{$safeYear}</td></tr>
                <tr><td>Email</td><td>{$safeEmail}</td></tr>
            </table>
            <p>Thank you for registering. We're excited to have you on board!</p>
            <p><strong>Best regards,</strong><br>
            Student Record Management System</p>
        </div>
        <div class="footer">
            &copy; 2026 Student Record Management System.
        </div>
    </div>
</body>
</html>
HTML;

        $mail->AltBody = "Welcome, {$studentName}!\n\n"
            . "Your student record has been successfully created.\n\n"
            . "Matric Number: {$matricNo}\n"
            . "Name: {$studentName}\n"
            . "Department: {$department}\n"
            . "Level: {$level}\n"
            . "Admission Year: {$admissionYear}\n"
            . "Email: {$studentEmail}\n\n"
            . "Thank you for registering. We're excited to have you on board!\n\n"
            . "Best regards,\n"
            . "Student Record Management System";

        /* Send */
        $mail->send();

        return $makeResult(true, 'sent', 'Welcome email sent successfully.');

    } catch (Exception $e) {

        /* SMTP ErrorInfo / throw text goes to the server log ONLY.
           The password is never part of it and it is never shown
           in the browser. */
        $errorInfo = ($mail->ErrorInfo !== '')
            ? $mail->ErrorInfo
            : $e->getMessage();

        error_log('[SRMS email] Welcome email send failed: ' . $errorInfo);

        $reason = 'smtp_error';
        $message = 'The SMTP server did not accept the message (general SMTP error). Consult the server error log for details.';

        $lower = strtolower($errorInfo);

        if (strpos($lower, 'authenticate') !== false) {

            $reason = 'auth_failed';
            $message = 'SMTP authentication failed. Double-check the SMTP username and password. '
                     . 'For Gmail, a 16-character App Password is required (not your normal Google account password).';

        } elseif (
            strpos($lower, 'smtp connect() failed') !== false
            || strpos($lower, 'connection refused') !== false
            || strpos($lower, 'timed out') !== false
        ) {

            $reason = 'connection_failed';
            $message = 'Could not connect to the SMTP server. Check the SMTP host, port and outbound network access.';

        } elseif (
            strpos($lower, 'invalid address') !== false
            || strpos($lower, 'valid address') !== false
        ) {

            $reason = 'recipient_invalid';
            $message = 'The student email address was rejected as invalid by the mail library.';

        }

        return $makeResult(false, $reason, $message, $errorInfo);
    }
}
?>