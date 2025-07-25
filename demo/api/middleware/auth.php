<?php
class Auth {
    private static $secret = 'Su1t3Scr1pt@2025%'; // Cambia por una clave más robusta.

    // Generar un token básico (puedes usar librerías más avanzadas si lo deseas).
    public static function generateToken($userId) {
        $payload = [
            'iat' => time(),
            'exp' => time() + 3600, // Expirará en 1 hora.
            'uid' => $userId
        ];
        return base64_encode(json_encode($payload));
    }

    // Verificar y decodificar un token.
    public static function verifyToken($token) {
        $decoded = json_decode(base64_decode($token), true);

        if (!$decoded || $decoded['exp'] < time()) {
            return null; // Token inválido o expirado.
        }

        return $decoded;
    }
}
?>