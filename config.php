<?php
declare(strict_types=1);

const DB_HOST = 'localhost';
const DB_NAME = 'secure_portal';
const DB_USER = 'portal_app';
const DB_PASS = 'Db4314931!';

const COOKIE_SECURE = false; // ใช้ localhost แบบ HTTP เท่านั้น

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $pdo = new PDO(
        'mysql:host=' . DB_HOST .
        ';dbname=' . DB_NAME .
        ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    return $pdo;
}
