<?php
declare(strict_types=1);
require __DIR__ . '/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: select.php');
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    header('Location: select.php?msg=' . urlencode('Invalid student ID.'));
    exit;
}

try {
    $stmt = $pdo->prepare('DELETE FROM students WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $msg = urlencode('Student deleted successfully.');
} catch (Throwable $e) {
    $msg = urlencode('Delete failed: ' . $e->getMessage());
}

header('Location: select.php?msg=' . $msg);
exit;


