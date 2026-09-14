<?php

/*
 * =========================================================
 *  SMTP SETTINGS - TEMPLATE
 * =========================================================
 *
 *  Copy this file to  config/mail_settings.php
 *  and fill in your real values there.
 *
 *  config/mail_settings.php is git-ignored, so your credentials
 *  are never committed to the repository.
 *
 *  Why a local file? On XAMPP, environment variables set in a
 *  terminal are NOT automatically visible to the Apache/PHP web
 *  process. This file is loaded directly by PHP, so it always
 *  works. Environment variables (SMTP_HOST, SMTP_PORT,
 *  SMTP_USERNAME, SMTP_PASSWORD, SMTP_ENCRYPTION, MAIL_FROM,
 *  MAIL_FROM_NAME) are used instead only if this file does not
 *  exist.
 *
 *  Gmail example (recommended):
 *    1. Enable 2-Step Verification on the sending Google Account.
 *    2. Create an App Password:  https://myaccount.google.com/apppasswords
 *    3. Put the 16-character App Password in SMTP_PASSWORD below.
 *       A normal Google account password will NOT be accepted.
 */

return array(

    'SMTP_HOST'       => 'smtp.gmail.com',

    'SMTP_PORT'       => 587,

    /* 'tls' (STARTTLS, usually port 587),
       'ssl' (SMTPS, usually port 465),
       or '' / 'none' to disable encryption (not recommended). */
    'SMTP_ENCRYPTION' => 'tls',

    'SMTP_USERNAME'   => 'youraccount@gmail.com',

    /* Your 16-character Gmail App Password. Leave empty to send
       clear "credentials missing" diagnostics instead of fatal
       auth errors. */
    'SMTP_PASSWORD'   => '',

    /* Sender address. Falls back to SMTP_USERNAME when empty. */
    'MAIL_FROM'       => 'youraccount@gmail.com',

    'MAIL_FROM_NAME'  => 'Student Record System',
);