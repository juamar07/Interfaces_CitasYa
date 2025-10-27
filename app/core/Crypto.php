<?php
declare(strict_types=1);

namespace App\Core;

final class Crypto
{
    public static function encrypt(string $plain, string $key): string
    {
        $iv = random_bytes(12); // GCM nonce
        $cipher = 'aes-256-gcm';
        $tag = '';
        $ciphertext = openssl_encrypt($plain, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag);
        return base64_encode($iv . $tag . $ciphertext); // [iv|tag|ct]
    }

    public static function decrypt(string $pack, string $key): string
    {
        $bin = base64_decode($pack, true);
        $iv  = substr($bin, 0, 12);
        $tag = substr($bin, 12, 16);
        $ct  = substr($bin, 28);
        return openssl_decrypt($ct, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    }
}
