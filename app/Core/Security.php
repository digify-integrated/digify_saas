<?php

namespace App\Core;

class Security
{
    public static function encryptData(string $plainText)    {
        $plainText = trim($plainText);
        if (empty($plainText)) return false;

        $iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $ciphertext = openssl_encrypt($plainText, 'aes-256-cbc', ENCRYPTION_KEY, OPENSSL_RAW_DATA, $iv);

        return $ciphertext ? rawurlencode(base64_encode($iv . $ciphertext)) : false;
    }
    
    public static function decryptData(string $ciphertext)    {
        $decodedData = base64_decode(rawurldecode($ciphertext));
        if (!$decodedData) return false;

        $iv_length = openssl_cipher_iv_length('aes-256-cbc');
        if (strlen($decodedData) < $iv_length) return false;

        $iv = substr($decodedData, 0, $iv_length);
        $ciphertext = substr($decodedData, $iv_length);

        return openssl_decrypt($ciphertext, 'aes-256-cbc', ENCRYPTION_KEY, OPENSSL_RAW_DATA, $iv) ?: false;
    }
    
    public static function obscureEmail(string $email)    {
        [$username, $domain] = explode('@', $email, 2);
        $maskedUsername = substr($username, 0, 1) . str_repeat('*', max(0, strlen($username) - 2)) . substr($username, -1);

        return $maskedUsername . '@' . $domain;
    }
    
    public static function obscureCardNumber(string $cardNumber)    {
        $last4Digits = substr($cardNumber, -4);
        $masked = str_repeat('*', max(0, strlen($cardNumber) - 4));

        return substr(implode(' ', str_split($masked, 4)), 0, -1) . ' ' . $last4Digits;
    }
    
    public static function generateFileName(int $minLength = 4, int $maxLength = 8){
        $validCharacters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $filename = '';
        $length = random_int($minLength, $maxLength);

        for ($i = 0; $i < $length; $i++) {
            $filename .= $validCharacters[random_int(0, strlen($validCharacters) - 1)];
        }

        return $filename;
    }
    
    public static function directoryChecker(string $directory)    {
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true)) {
                return 'Error creating directory: ' . (error_get_last()['message'] ?? 'Unknown error');
            }
        }
        else if (!is_writable($directory)) {
            return 'Directory exists but is not writable.';
        }

        return true;
    }

    public static function generateToken($minLength = 10, $maxLength = 12) {
        $length = random_int($minLength, $maxLength);
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        $resetToken = '';
        for ($i = 0; $i < $length; $i++) {
            $resetToken .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $resetToken;
    }

    public static function generateOTPToken($length = 6) {
        $minValue = 10 ** ($length - 1);
        $maxValue = (10 ** $length) - 1;

        return (string) random_int($minValue, $maxValue);
    }
}

?>