<?php

namespace App\Mail;

use App\Core\Mailer;
use App\Models\NotificationSetting;
use App\Models\EmailSetting;
use App\Models\SecuritySetting;
use App\Helpers\SystemHelper;

/**
 * Class OTPMail
 * 
 * This class handles the sending of One-Time Password (OTP) emails. It generates
 * an OTP message using the configured email template, applies necessary dynamic
 * data replacements, and sends the email using the Mailer class.
 * 
 * @package App\Mail
 */
class OTPMail {
    /**
     * @var Mailer
     */
    private Mailer $mailer;

    /**
     * @var NotificationSetting
     */
    private NotificationSetting $notificationSetting;

    /**
     * @var EmailSetting
     */
    private EmailSetting $emailSetting;

    /**
     * @var SecuritySetting
     */
    private SecuritySetting $securitySetting;

    /**
     * @var SystemHelper
     */
    private SystemHelper $systemHelper;

    /**
     * OTPMail constructor.
     *
     * @param Mailer $mailer The Mailer instance used to send emails.
     * @param NotificationSetting $notificationSetting The NotificationSetting model for fetching email templates.
     * @param EmailSetting $emailSetting The EmailSetting model for email configuration (unused in this class but may be used for future features).
     * @param SecuritySetting $securitySetting The SecuritySetting model used for OTP validity duration configuration.
     * @param SystemHelper $systemHelper The SystemHelper instance used for error handling.
     */
    public function __construct(
        Mailer $mailer,
        NotificationSetting $notificationSetting,
        EmailSetting $emailSetting,
        SecuritySetting $securitySetting,
        SystemHelper $systemHelper
    ) {
        $this->mailer = $mailer;
        $this->notificationSetting = $notificationSetting;
        $this->emailSetting = $emailSetting;
        $this->securitySetting = $securitySetting;
        $this->systemHelper = $systemHelper;
    }

    /**
     * Sends an OTP email to the specified recipient.
     *
     * This method retrieves the OTP email template, replaces the placeholders
     * with the actual OTP code and validity duration, and sends the email to the
     * recipient using the Mailer class.
     * 
     * @param string $email The recipient's email address.
     * @param string $otp The OTP code to be included in the email body.
     * 
     * @return bool Returns true if the email was sent successfully, otherwise returns false.
     */
    public function sendOTP(string $email, string $otp): bool {
        // Fetch notification settings for email templates
        $notificationSettingDetails = $this->notificationSetting->getNotificationSettingEmailTemplate(1);
        if (!$notificationSettingDetails) {
            $this->systemHelper->sendErrorResponse('Email template not found.');
        }

        // Fetch OTP validity duration from security settings
        $securitySettingDetails = $this->securitySetting->getSecuritySetting(5);
        $otpDuration = $securitySettingDetails['value'] ?? DEFAULT_OTP_DURATION;

        // Define email subject and body template, applying default values if not set
        $emailSubject = $notificationSettingDetails['email_notification_subject'] ?? 'OTP Code';
        $emailBodyTemplate = $notificationSettingDetails['email_notification_body'] ?? 'Your OTP code is: #{OTP_CODE}. Valid for #{OTP_CODE_VALIDITY}.';

        // Replace placeholders in the email body template with actual values
        $emailBody = $this->mailer->replacePlaceholders($emailBodyTemplate, [
            '#{OTP_CODE}' => $otp,
            '#{OTP_CODE_VALIDITY}' => $otpDuration . ' minutes',
        ]);

        // Generate the full email message using the email template and body content
        $message = $this->mailer->getEmailTemplate($emailSubject, $emailBody);

        // Send the email and return the result (true for success, error message otherwise)
        return $this->mailer->send($email, $emailSubject, $message);
    }
}
