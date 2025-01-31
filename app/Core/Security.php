<?php
declare(strict_types=1);

namespace App\Core;

use RuntimeException;

class Security
{
    private const CIPHER_METHOD = 'aes-256-cbc';

    /**
     * Encrypts data using AES-256-CBC.
     */
    public static function encryptData(string $plainText): string {
        $plainText = trim($plainText);
        if ($plainText === '') {
            throw new RuntimeException('Encryption failed: Empty input.');
        }

        $iv = random_bytes(openssl_cipher_iv_length(self::CIPHER_METHOD));
        $ciphertext = openssl_encrypt($plainText, self::CIPHER_METHOD, ENCRYPTION_KEY, OPENSSL_RAW_DATA, $iv);

        if (!$ciphertext) {
            throw new RuntimeException('Encryption failed.');
        }

        return rawurlencode(base64_encode($iv . $ciphertext));
    }

    /**
     * Decrypts AES-256-CBC encrypted data.
     */
    public static function decryptData(string $ciphertext): string {
        $decodedData = base64_decode(rawurldecode($ciphertext), true);
        if ($decodedData === false) {
            throw new RuntimeException('Decryption failed: Invalid encoding.');
        }

        $ivLength = openssl_cipher_iv_length(self::CIPHER_METHOD);
        if (strlen($decodedData) < $ivLength) {
            throw new RuntimeException('Decryption failed: Invalid data.');
        }

        $iv = substr($decodedData, 0, $ivLength);
        $ciphertext = substr($decodedData, $ivLength);
        $decrypted = openssl_decrypt($ciphertext, self::CIPHER_METHOD, ENCRYPTION_KEY, OPENSSL_RAW_DATA, $iv);

        return $decrypted !== false ? $decrypted : throw new RuntimeException('Decryption failed.');
    }

    /**
     * Obscures an email for privacy.
     */
    public static function obscureEmail(string $email): string {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Invalid email format.');
        }

        [$username, $domain] = explode('@', $email, 2);
        $maskedUsername = substr($username, 0, 1) . str_repeat('*', max(0, strlen($username) - 2)) . substr($username, -1);

        return $maskedUsername . '@' . $domain;
    }

    /**
     * Masks all but the last 4 digits of a card number.
     */
    public static function obscureCardNumber(string $cardNumber): string {
        if (!ctype_digit($cardNumber) || strlen($cardNumber) < 4) {
            throw new RuntimeException('Invalid card number.');
        }

        return str_pad('', strlen($cardNumber) - 4, '*') . substr($cardNumber, -4);
    }

    /**
     * Generates a random alphanumeric filename.
     */
    public static function generateFileName(int $minLength = 4, int $maxLength = 8): string {
        $length = random_int($minLength, $maxLength);
        return self::randomString($length);
    }

    /**
     * Checks if a directory exists and is writable.
     */
    public static function directoryChecker(string $directory): bool {
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new RuntimeException('Error creating directory: ' . (error_get_last()['message'] ?? 'Unknown error'));
        }

        if (!is_writable($directory)) {
            throw new RuntimeException('Directory exists but is not writable.');
        }

        return true;
    }

    /**
     * Generates a secure random token.
     */
    public static function generateToken(int $minLength = 10, int $maxLength = 12): string {
        $length = random_int($minLength, $maxLength);
        return self::randomString($length);
    }

    /**
     * Generates a secure OTP code of given length.
     */
    public static function generateOTPToken(int $length = 6): string {
        if ($length < 4 || $length > 10) {
            throw new RuntimeException('OTP length must be between 4 and 10.');
        }

        return (string) random_int(10 ** ($length - 1), (10 ** $length) - 1);
    }

    /**
     * Generates a secure random alphanumeric string.
     */
    private static function randomString(int $length): string {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        return implode('', array_map(fn () => $characters[random_int(0, strlen($characters) - 1)], range(1, $length)));
    }
}
