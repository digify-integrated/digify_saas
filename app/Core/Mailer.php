<?php
declare(strict_types=1);

namespace App\Core;

use App\Helpers\PHPMailer\PHPMailer;
use App\Models\EmailSetting;
use App\Core\Security;

/**
 * Class Mailer
 * 
 * This class handles email sending functionality, including configuring SMTP settings
 * and sending emails using PHPMailer. It supports customizable email templates
 * and message content with placeholder replacements.
 * 
 * @package App\Core
 */
class Mailer {
    /**
     * @var EmailSetting
     */
    private EmailSetting $emailSetting;

    /**
     * @var Security
     */
    private Security $security;

    /**
     * Mailer constructor.
     *
     * @param EmailSetting $emailSetting The EmailSetting instance used for email configuration.
     * @param Security $security The Security instance used for encryption and decryption operations.
     */
    public function __construct(
        EmailSetting $emailSetting,
        Security $security
    ) {
        $this->emailSetting = $emailSetting;
        $this->security = $security;
    }

    /**
     * Sends an email to the specified recipient using PHPMailer.
     * 
     * @param string $recipient The recipient's email address.
     * @param string $emailSubject The subject of the email.
     * @param string $message The body of the email.
     * @param string $email The sender's email address (default: system's configured email).
     * @param string $password The sender's email password (default: system's configured password).
     * @param string $mailFromEmail The sender's email address (default: system's configured sender email).
     * @param string $mailFromName The sender's name (default: system's configured sender name).
     * 
     * @return bool|string Returns true if email was sent successfully, or error message if failed.
     */
    public function send(
        string $recipient, 
        string $emailSubject, 
        string $message, 
        string $email = MAIL_USERNAME, 
        string $password = MAIL_PASSWORD, 
        string $mailFromEmail = MAIL_FROM_EMAIL, 
        string $mailFromName = MAIL_FROM_NAME
    ) {
        $mailer = new PHPMailer();

        $this->configureSMTP($mailer, $email, $password);

        $mailer->setFrom($mailFromEmail, $mailFromName);
        $mailer->addAddress($recipient);
        $mailer->Subject = $emailSubject;
        $mailer->Body = $message;

        if ($mailer->send()) {
            return true;
        } else {
            return 'Failed to send email. Error: ' . $mailer->ErrorInfo;
        }
    }

    /**
     * Configures the SMTP settings for the PHPMailer instance.
     * 
     * @param PHPMailer $mailer The PHPMailer instance to configure.
     * @param string $email The email address used to send the email.
     * @param string $password The password used for SMTP authentication.
     * @param bool $isHTML Whether the email should be sent as HTML (default: true).
     * 
     * @return void
     */
    private function configureSMTP(PHPMailer $mailer, string $email, string $password, bool $isHTML = true): void {
        $mailer->isSMTP();
        $mailer->isHTML($isHTML);
        $mailer->Host = MAIL_HOST;
        $mailer->SMTPAuth = MAIL_SMTP_AUTH;
        $mailer->Username = $email;
        $mailer->Password = $password;
        $mailer->SMTPSecure = MAIL_SMTP_SECURE;
        $mailer->Port = MAIL_PORT;
    }

    /**
     * Generates a basic HTML email template with subject and body placeholders.
     * 
     * @param string $subject The subject of the email.
     * @param string $body The body content of the email.
     * 
     * @return string The final HTML email template with placeholders replaced by subject and body.
     */
    public function getEmailTemplate(string $subject, string $body): string {
        $template = '<!DOCTYPE html>
                        <html>
                        <head>
                        <meta charset="UTF-8">
                        <title>{SUBJECT}</title>
                        </head>
                        <body>
                        {BODY}
                        </body>
                    </html>';

        // Replacing placeholders with actual subject and body
        $template = str_replace('{SUBJECT}', $subject, $template);
        $template = str_replace('{BODY}', $body, $template);

        return $template;
    }

    /**
     * Replaces placeholders in the email template with the provided values.
     * 
     * @param string $template The email template containing placeholders.
     * @param array $replacements An associative array of placeholders and their replacement values.
     * 
     * @return string The final email template with all placeholders replaced.
     */
    public function replacePlaceholders(string $template, array $replacements): string {
        // Replace each placeholder in the template with the corresponding value
        foreach ($replacements as $placeholder => $value) {
            $template = str_replace($placeholder, $value, $template);
        }
        return $template;
    }
}
