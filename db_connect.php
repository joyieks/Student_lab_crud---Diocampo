<?php
// Database connection using PDO. Auto-creates database and table if not exists.

declare(strict_types=1);

// Configuration - adjust if your MySQL credentials differ
$dbHost = '127.0.0.1';
$dbUser = 'root';
$dbPass = '';
$dbName = 'Diocampo,JoanJoy_lab_crud';
$dbCharset = 'utf8mb4';

// Create database if it doesn't exist
try {
    $pdoRoot = new PDO("mysql:host={$dbHost};charset={$dbCharset}", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdoRoot->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET {$dbCharset} COLLATE {$dbCharset}_unicode_ci");
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h1>Database Initialization Error</h1>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    exit;
}

// Connect to the target database
try {
    $dsn = "mysql:host={$dbHost};dbname={$dbName};charset={$dbCharset}";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h1>Database Connection Error</h1>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    exit;
}

// Create students table (new schema) if it doesn't exist
try {
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS students (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(100) NOT NULL,
            middle_name VARCHAR(100) NULL,
            last_name VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            course VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=' . $dbCharset
    );
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h1>Table Creation Error</h1>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    exit;
}

// Migration: if legacy column full_name exists, add new columns and backfill
try {
    // Add columns if they do not exist (MySQL 8+)
    $pdo->exec('ALTER TABLE students 
        ADD COLUMN IF NOT EXISTS first_name VARCHAR(100) NULL AFTER id,
        ADD COLUMN IF NOT EXISTS middle_name VARCHAR(100) NULL AFTER first_name,
        ADD COLUMN IF NOT EXISTS last_name VARCHAR(100) NULL AFTER middle_name');

    // Backfill names from full_name when present
    $pdo->exec("UPDATE students 
        SET 
            first_name = COALESCE(first_name, NULLIF(TRIM(SUBSTRING_INDEX(full_name, ' ', 1)), '')),
            last_name = COALESCE(last_name, NULLIF(TRIM(SUBSTRING_INDEX(full_name, ' ', -1)), ''))
        WHERE (first_name IS NULL OR last_name IS NULL) AND full_name IS NOT NULL");

    // Ensure NOT NULL after backfill
    $pdo->exec('ALTER TABLE students 
        MODIFY COLUMN first_name VARCHAR(100) NOT NULL,
        MODIFY COLUMN last_name VARCHAR(100) NOT NULL');

    // Drop legacy column if exists
    $pdo->exec('ALTER TABLE students DROP COLUMN IF EXISTS full_name');
} catch (Throwable $e) {
    // Non-fatal; surface as info in HTML (not breaking the app)
}

// Helper to sanitize output
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Expose $pdo for includes
// Usage: include 'db_connect.php'; then use $pdo
?>


