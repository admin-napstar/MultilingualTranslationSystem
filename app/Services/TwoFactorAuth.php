<?php

namespace Napstar\MultilingualTranslationSystem\Services;

class TwoFactorAuth
{
    public function generateSecret(int $length = 20): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';

        for ($i = 0; $i < $length; $i++) {
            $secret .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return $secret;
    }

    public function getOtpAuthUrl(
        string $issuer,
        string $account,
        string $secret
    ): string {
        $label = rawurlencode($issuer . ':' . $account);

        return 'otpauth://totp/' . $label
            . '?secret=' . rawurlencode($secret)
            . '&issuer=' . rawurlencode($issuer)
            . '&algorithm=SHA1&digits=6&period=30';
    }

    public function verifyCode(
        string $secret,
        string $code
    ): bool {
        if (!preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        $key = $this->base32Decode($secret);

        if ($key === false) {
            return false;
        }

        $timeStep = (int) floor(time() / 30);

        // Allow the previous, current, and next 30-second window.
        for ($offset = -1; $offset <= 1; $offset++) {
            $counter = $timeStep + $offset;

            $high = intdiv($counter, 4294967296);
            $low = $counter % 4294967296;

            $binaryCounter = pack('N2', $high, $low);

            $hash = hash_hmac(
                'sha1',
                $binaryCounter,
                $key,
                true
            );

            $position = ord($hash[19]) & 0x0F;

            $value = (
                ((ord($hash[$position]) & 0x7F) << 24)
                | ((ord($hash[$position + 1]) & 0xFF) << 16)
                | ((ord($hash[$position + 2]) & 0xFF) << 8)
                | (ord($hash[$position + 3]) & 0xFF)
            );

            $expectedCode = str_pad(
                (string) ($value % 1000000),
                6,
                '0',
                STR_PAD_LEFT
            );

            if (hash_equals($expectedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    private function base32Decode(string $secret): string|false
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = strtoupper(rtrim($secret, '='));
        $bits = '';

        foreach (str_split($secret) as $character) {
            $value = strpos($alphabet, $character);

            if ($value === false) {
                return false;
            }

            $bits .= str_pad(
                decbin($value),
                5,
                '0',
                STR_PAD_LEFT
            );
        }

        $binary = '';

        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) === 8) {
                $binary .= chr(bindec($byte));
            }
        }

        return $binary;
    }
}
