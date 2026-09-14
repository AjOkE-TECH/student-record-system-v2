<?php

/*
 * =========================================================
 *  MAIL CONFIGURATION
 * =========================================================
 *
 *  Security rules:
 *   - The real SMTP password is NEVER hard-coded here and is
 *     never printed anywhere in the application.
 *   - The SMTP password is NEVER shown to the user on screen.
 *
 *  Settings are resolved in this priority order:
 *     1. config/mail_settings.php         (local, git-ignored)
 *        Copy config/mail_settings.example.php to
 *        config/mail_settings.php and fill in your real values.
 *        This is the recommended approach for XAMPP because
 *        environment variables set in a terminal are NOT
 *        available to the Apache/PHP web process by default.
 *     2. Environment variables
 *        SMTP_HOST, SMTP_PORT, SMTP_USERNAME, SMTP_PASSWORD,
 *        SMTP_ENCRYPTION, MAIL_FROM, MAIL_FROM_NAME
 *     3. Defaults below.
 *
 *  Gmail SMTP:
 *   - Enable 2-Step Verification on the sending Google account.
 *   - Create an App Password at
 *     https://myaccount.google.com/apppasswords
 *   - Use that 16-character App Password as SMTP_PASSWORD.
 *     A normal Google account password will NOT work.
 */

function srsMailSettings(): array
{
    $settings = array(
        'SMTP_HOST'       => 'smtp.gmail.com',
        'SMTP_PORT'       => 587,
        'SMTP_ENCRYPTION' => 'tls',
        'SMTP_USERNAME'   => '',
        'SMTP_PASSWORD'   => '',
        'MAIL_FROM'       => '',
        'MAIL_FROM_NAME'  => 'Student Record System',
    );

    $settings['CONFIG_SOURCE'] = 'defaults';

    /* 1. Local, git-ignored settings file (highest priority). */
    $localFile = __DIR__ . '/config/mail_settings.php';

    if (is_file($localFile)) {
        $local = @include $localFile;

        if (is_array($local)) {
            foreach (array_keys($settings) as $key) {
                if (!array_key_exists($key, $local)) {
                    continue;
                }

                $value = $local[$key];

                if ($key === 'SMTP_PORT') {
                    $port = (int) $value;
                    if ($port > 0) {
                        $settings[$key] = $port;
                    }
                } elseif (is_string($value) && trim($value) !== '') {
                    $settings[$key] = trim($value);
                }
            }

            $settings['CONFIG_SOURCE'] = 'local-file';
        }
    }

    /* 2. Environment variables (only override when set in PHP's env). */
    $envMap = array(
        'SMTP_HOST'       => 'SMTP_HOST',
        'SMTP_PORT'       => 'SMTP_PORT',
        'SMTP_ENCRYPTION' => 'SMTP_ENCRYPTION',
        'SMTP_USERNAME'   => 'SMTP_USERNAME',
        'SMTP_PASSWORD'   => 'SMTP_PASSWORD',
        'MAIL_FROM'       => 'MAIL_FROM',
        'MAIL_FROM_NAME'  => 'MAIL_FROM_NAME',
    );

    $envUsed = false;

    foreach ($envMap as $key => $envName) {
        $envValue = getenv($envName);

        if ($envValue === false || $envValue === '') {
            continue;
        }

        if ($key === 'SMTP_PORT') {
            $port = (int) $envValue;
            if ($port > 0) {
                $settings[$key] = $port;
                $envUsed = true;
            }
        } else {
            $settings[$key] = trim((string) $envValue);
            $envUsed = true;
        }
    }

    if ($envUsed && $settings['CONFIG_SOURCE'] === 'defaults') {
        $settings['CONFIG_SOURCE'] = 'environment';
    }

    return $settings;
}

$srsMailSettings = srsMailSettings();

if (!defined('SMTP_HOST')) {
    define('SMTP_HOST', $srsMailSettings['SMTP_HOST']);
}
if (!defined('SMTP_PORT')) {
    define('SMTP_PORT', (int) $srsMailSettings['SMTP_PORT']);
}
if (!defined('SMTP_USERNAME')) {
    define('SMTP_USERNAME', $srsMailSettings['SMTP_USERNAME']);
}
if (!defined('SMTP_PASSWORD')) {
    define('SMTP_PASSWORD', $srsMailSettings['SMTP_PASSWORD']);
}
if (!defined('SMTP_ENCRYPTION')) {
    define('SMTP_ENCRYPTION', $srsMailSettings['SMTP_ENCRYPTION']);
}
if (!defined('MAIL_FROM')) {
    define('MAIL_FROM', $srsMailSettings['MAIL_FROM']);
}
if (!defined('MAIL_FROM_NAME')) {
    define('MAIL_FROM_NAME', $srsMailSettings['MAIL_FROM_NAME']);
}