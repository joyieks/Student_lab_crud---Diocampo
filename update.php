<?php
declare(strict_types=1);
require __DIR__ . '/db_connect.php';

// If GET: show edit form; If POST: process update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $firstName = trim($_POST['first_name'] ?? '');
    $middleName = trim($_POST['middle_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $course = trim($_POST['course'] ?? '');

    $errors = [];
    if ($id <= 0) { $errors[] = 'Invalid ID.'; }
    if ($firstName === '') { $errors[] = 'First name is required.'; }
    if ($lastName === '') { $errors[] = 'Last name is required.'; }
    if ($email === '') { $errors[] = 'Email is required.'; }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Please provide a valid email.'; }
    if ($course === '') { $errors[] = 'Course is required.'; }

    if ($errors) {
        $msg = urlencode(implode(' ', $errors));
        header("Location: update.php?id={$id}&msg={$msg}");
        exit;
    }

    try {
        $stmt = $pdo->prepare('UPDATE students SET first_name = :first_name, middle_name = :middle_name, last_name = :last_name, email = :email, course = :course WHERE id = :id');
        $stmt->execute([
            ':first_name' => $firstName,
            ':middle_name' => ($middleName !== '' ? $middleName : null),
            ':last_name' => $lastName,
            ':email' => $email,
            ':course' => $course,
            ':id' => $id,
        ]);
        header('Location: select.php?msg=' . urlencode('Student updated successfully.'));
    } catch (Throwable $e) {
        header('Location: update.php?id=' . $id . '&msg=' . urlencode('Update failed: ' . $e->getMessage()));
    }
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: select.php?msg=' . urlencode('Invalid student ID.'));
    exit;
}

$stmt = $pdo->prepare('SELECT id, first_name, middle_name, last_name, email, course FROM students WHERE id = :id');
$stmt->execute([':id' => $id]);
$student = $stmt->fetch();
if (!$student) {
    header('Location: select.php?msg=' . urlencode('Student not found.'));
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Student</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="select.php">Student Lab CRUD</a>
  </div>
</nav>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">Edit Student</div>
        <div class="card-body">
          <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
              <?php echo e($_GET['msg']); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>
          <form method="post" action="update.php" novalidate>
            <input type="hidden" name="id" value="<?php echo (int)$student['id']; ?>">
            <div class="mb-3">
              <label class="form-label">First Name</label>
              <input type="text" class="form-control" name="first_name" value="<?php echo e($student['first_name']); ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Middle Name (optional)</label>
              <input type="text" class="form-control" name="middle_name" value="<?php echo e($student['middle_name'] ?? ''); ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Last Name</label>
              <input type="text" class="form-control" name="last_name" value="<?php echo e($student['last_name']); ?>" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" value="<?php echo e($student['email']); ?>" required>
            </div>
            <div class="mb-3">
              <label for="course" class="form-label">Course</label>
              <input type="text" class="form-control" id="course" name="course" value="<?php echo e($student['course']); ?>" required>
            </div>
            <div class="d-flex gap-2">
              <a href="select.php" class="btn btn-outline-secondary">Cancel</a>
              <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>


