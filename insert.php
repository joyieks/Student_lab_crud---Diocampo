<?php
declare(strict_types=1);
require __DIR__ . '/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: select.php');
    exit;
}

$firstName = trim($_POST['first_name'] ?? '');
$middleName = trim($_POST['middle_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$course = trim($_POST['course'] ?? '');

// Basic validation
$errors = [];
if ($firstName === '') { $errors[] = 'First name is required.'; }
if ($lastName === '') { $errors[] = 'Last name is required.'; }
if ($email === '') { $errors[] = 'Email is required.'; }
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Please provide a valid email.'; }
if ($course === '') { $errors[] = 'Course is required.'; }

if ($errors) {
    $msg = urlencode(implode(' ', $errors));
    header("Location: select.php?msg={$msg}");
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO students (first_name, middle_name, last_name, email, course) VALUES (:first_name, :middle_name, :last_name, :email, :course)');
    $stmt->execute([
        ':first_name' => $firstName,
        ':middle_name' => ($middleName !== '' ? $middleName : null),
        ':last_name' => $lastName,
        ':email' => $email,
        ':course' => $course,
    ]);
    $msg = urlencode('Student added successfully.');
} catch (Throwable $e) {
   
    $msg = urlencode('Insert failed: ' . $e->getMessage());
}

header("Location: select.php?msg={$msg}");
exit;


