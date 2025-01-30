<?php

namespace App\Core;

class Security
{
    private const CIPHER_METHOD = 'aes-256-cbc';
    
    /**
     * Encrypts a given plaintext string.
     */
    public static function encrypt(string $plainText): string|false
    {
        $plainText = trim($plainText);
        if ($plainText === '') {
            return false;
        }

        $ivLength = openssl_cipher_iv_length(self::CIPHER_METHOD);
        $iv = random_bytes($ivLength);
        $ciphertext = openssl_encrypt($plainText, self::CIPHER_METHOD, ENCRYPTION_KEY, OPENSSL_RAW_DATA, $iv);

        return $ciphertext ? rawurlencode(base64_encode($iv . $ciphertext)) : false;
    }

    /**
     * Decrypts an encrypted string.
     */
    public static function decrypt(string $ciphertext): string|false
    {
        $decodedData = base64_decode(rawurldecode($ciphertext));
        if ($decodedData === false) {
            return false;
        }

        $ivLength = openssl_cipher_iv_length(self::CIPHER_METHOD);
        if (strlen($decodedData) < $ivLength) {
            return false;
        }

        $iv = substr($decodedData, 0, $ivLength);
        $ciphertext = substr($decodedData, $ivLength);

        return openssl_decrypt($ciphertext, self::CIPHER_METHOD, ENCRYPTION_KEY, OPENSSL_RAW_DATA, $iv) ?: false;
    }

    /**
     * Obscures an email address for privacy.
     */
    public static function obscureEmail(string $email): string
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Invalid email format';
        }

        [$username, $domain] = explode('@', $email);
        $maskedUsername = substr($username, 0, 1) . str_repeat('*', max(0, strlen($username) - 2)) . substr($username, -1);

        return "{$maskedUsername}@{$domain}";
    }

    /**
     * Obscures a credit card number, showing only the last 4 digits.
     */
    public static function obscureCardNumber(string $cardNumber): string
    {
        $length = strlen($cardNumber);
        if ($length < 4) {
            return 'Invalid card number';
        }

        $masked = str_repeat('*', $length - 4);
        return wordwrap($masked, 4, ' ', true) . ' ' . substr($cardNumber, -4);
    }

    /**
     * Generates a secure random filename.
     */
    public static function generateFileName(int $minLength = 4, int $maxLength = 8): string
    {
        return self::generateRandomString($minLength, $maxLength);
    }

    /**
     * Checks if a directory exists, creates it if necessary.
     */
    public static function ensureDirectoryExists(string $directory): bool|string
    {
        if (is_dir($directory) && is_writable($directory)) {
            return true;
        }

        return mkdir($directory, 0755, true) ? true : 'Error creating directory: ' . (error_get_last()['message'] ?? 'Unknown error');
    }

    /**
     * Generates a secure token.
     */
    public static function generateToken(int $minLength = 10, int $maxLength = 12): string
    {
        return self::generateRandomString($minLength, $maxLength);
    }

    /**
     * Generates a numeric OTP of specified length.
     */
    public static function generateOTP(int $length = 6): string
    {
        return (string) random_int(10 ** ($length - 1), (10 ** $length) - 1);
    }

    /**
     * Generates a secure random string.
     */
    private static function generateRandomString(int $minLength, int $maxLength): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $length = random_int($minLength, $maxLength);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $randomString;
    }
}