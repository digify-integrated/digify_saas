<?php
declare(strict_types=1);

namespace App\Core;

use RuntimeException;

class Security
{
    private const CIPHER_METHOD = 'aes-256-cbc';  // Encryption method to use (AES-256-CBC)

    /**
     * Encrypts data using AES-256-CBC encryption method.
     * The method returns the base64-encoded string of the encrypted data and IV.
     *
     * @param string $plainText The plain text string to encrypt
     * @return string The base64-encoded encrypted string including the IV
     * @throws RuntimeException If encryption fails or input is empty
     */
    public static function encryptData(string $plainText): string {
        $plainText = trim($plainText);
        
        // Check for empty input
        if ($plainText === '') {
            throw new RuntimeException('Encryption failed: Empty input.');
        }

        // Generate a random IV (initialization vector)
        $iv = random_bytes(openssl_cipher_iv_length(self::CIPHER_METHOD));

        // Encrypt the data
        $ciphertext = openssl_encrypt($plainText, self::CIPHER_METHOD, ENCRYPTION_KEY, OPENSSL_RAW_DATA, $iv);

        // If encryption fails, throw an exception
        if (!$ciphertext) {
            throw new RuntimeException('Encryption failed.');
        }

        // Return base64-encoded result with IV prepended
        return rawurlencode(base64_encode($iv . $ciphertext));
    }

    /**
     * Decrypts AES-256-CBC encrypted data.
     * The method assumes the encrypted data includes the IV (pre-pended).
     *
     * @param string $ciphertext The base64-encoded encrypted string including the IV
     * @return string The decrypted plain text string
     * @throws RuntimeException If decryption fails or input is invalid
     */
    public static function decryptData(string $ciphertext): string {
        // Decode the base64-encoded ciphertext
        $decodedData = base64_decode(rawurldecode($ciphertext), true);
        if ($decodedData === false) {
            throw new RuntimeException('Decryption failed: Invalid encoding.');
        }

        // Extract IV from the decoded data
        $ivLength = openssl_cipher_iv_length(self::CIPHER_METHOD);
        if (strlen($decodedData) < $ivLength) {
            throw new RuntimeException('Decryption failed: Invalid data.');
        }

        // Split IV and ciphertext
        $iv = substr($decodedData, 0, $ivLength);
        $ciphertext = substr($decodedData, $ivLength);

        // Decrypt the data
        $decrypted = openssl_decrypt($ciphertext, self::CIPHER_METHOD, ENCRYPTION_KEY, OPENSSL_RAW_DATA, $iv);

        // If decryption fails, throw an exception
        if ($decrypted === false) {
            throw new RuntimeException('Decryption failed.');
        }

        return $decrypted;
    }

    /**
     * Obscures an email address for privacy by masking part of the username.
     * Example: john.doe@example.com -> j***e@example.com
     *
     * @param string $email The email address to obscure
     * @return string The obscured email address
     * @throws RuntimeException If the email format is invalid
     */
    public static function obscureEmail(string $email): string {
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Invalid email format.');
        }

        // Split email into username and domain
        [$username, $domain] = explode('@', $email, 2);

        // Mask the username
        $maskedUsername = substr($username, 0, 1) . str_repeat('*', max(0, strlen($username) - 2)) . substr($username, -1);

        return $maskedUsername . '@' . $domain;
    }

    /**
     * Masks all but the last 4 digits of a credit card number.
     * Example: 1234 5678 9876 4321 -> **** **** **** 4321
     *
     * @param string $cardNumber The credit card number to obscure
     * @return string The obscured card number
     * @throws RuntimeException If the card number is invalid
     */
    public static function obscureCardNumber(string $cardNumber): string {
        // Validate the card number format (only digits and at least 4 digits long)
        if (!ctype_digit($cardNumber) || strlen($cardNumber) < 4) {
            throw new RuntimeException('Invalid card number.');
        }

        // Mask the card number except for the last 4 digits
        return str_pad('', strlen($cardNumber) - 4, '*') . substr($cardNumber, -4);
    }

    /**
     * Generates a random alphanumeric filename of a given length.
     * The length is randomly chosen between minLength and maxLength.
     *
     * @param int $minLength The minimum length of the filename
     * @param int $maxLength The maximum length of the filename
     * @return string The generated random filename
     */
    public static function generateFileName(int $minLength = 4, int $maxLength = 8): string {
        $length = random_int($minLength, $maxLength);
        return self::randomString($length);
    }

    /**
     * Checks if a directory exists and is writable.
     * If it doesn't exist, it will try to create it.
     *
     * @param string $directory The directory path to check
     * @return bool True if the directory exists and is writable, false otherwise
     * @throws RuntimeException If the directory cannot be created or is not writable
     */
    public static function directoryChecker(string $directory): bool {
        // Check if directory exists or try to create it
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new RuntimeException('Error creating directory: ' . (error_get_last()['message'] ?? 'Unknown error'));
        }

        // Check if the directory is writable
        if (!is_writable($directory)) {
            throw new RuntimeException('Directory exists but is not writable.');
        }

        return true;
    }

    /**
     * Generates a secure random token of a given length.
     *
     * @param int $minLength The minimum length of the token
     * @param int $maxLength The maximum length of the token
     * @return string The generated random token
     */
    public static function generateToken(int $minLength = 10, int $maxLength = 12): string {
        $length = random_int($minLength, $maxLength);
        return self::randomString($length);
    }

    /**
     * Generates a secure OTP (One-Time Password) token of the given length.
     * The length must be between 4 and 10 digits.
     *
     * @param int $length The length of the OTP (between 4 and 10)
     * @return string The generated OTP token
     * @throws RuntimeException If the OTP length is invalid
     */
    public static function generateOTPToken(int $length = 6): string {
        if ($length < 4 || $length > 10) {
            throw new RuntimeException('OTP length must be between 4 and 10.');
        }

        // Generate OTP as a random number within the given length
        return (string) random_int(10 ** ($length - 1), (10 ** $length) - 1);
    }

    /**
     * Generates a secure random alphanumeric string of the specified length.
     *
     * @param int $length The length of the string to generate
     * @return string The generated random string
     */
    private static function randomString(int $length): string {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        return implode('', array_map(fn () => $characters[random_int(0, strlen($characters) - 1)], range(1, $length)));
    }
}