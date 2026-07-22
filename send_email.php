<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

// Include PHPMailer library files
require 'Mail/src/Exception.php';
require 'Mail/src/PHPMailer.php';
require 'Mail/src/SMTP.php';

/**
 * Send a welcome email to a newly registered student
 * 
 * @param string $studentEmail The student's email address
 * @param string $studentName  The student's full name
 * 
 * @return bool True if email sent successfully, false otherwise
 */
function sendStudentEmail(string $studentEmail, string $studentName): bool
{
    $mail = new PHPMailer(true);
    
    try {
        // Configure SMTP settings
        $mail->isSMTP();
       require_once 'mail_config.php';

       $mail->Host       = SMTP_HOST;
       $mail->SMTPAuth   = true;
       $mail->Username   = SMTP_USERNAME;
       $mail->Password   = SMTP_PASSWORD;
       $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
       $mail->Port       = SMTP_PORT;

        // Set sender information
        $mail->setFrom(
            'sekinatmutolib48@gmail.com',
            'Student Record System'
        );

        // Set recipient
        $mail->addAddress(
            $studentEmail,
            $studentName
        );

        // Set email content
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to Student Record Management System';

        $mail->Body = "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #006400; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                    .content { background-color: #f9f9f9; padding: 30px; border-radius: 0 0 5px 5px; }
                    .footer { margin-top: 20px; text-align: center; font-size: 12px; color: #888; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Welcome, {$studentName}!</h2>
                    </div>
                    <div class='content'>
                        <p>Your student record has been successfully created.</p>
                        <p>Thank you for registering. We're excited to have you on board!</p>
                        <p><strong>Best regards,</strong><br>
                        Student Record Management System</p>
                    </div>
                    <div class='footer'>
                        2026 Student Record Management System.
                    </div>
                </div>
            </body>
            </html>
        ";

        // Send the email
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        // Log error for debugging
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}
?>