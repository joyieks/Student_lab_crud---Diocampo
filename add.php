<?php
declare(strict_types=1);
require __DIR__ . '/db_connect.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add Student</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 sticky-top">
  <div class="container">
    <a class="navbar-brand" href="select.php">STUDENT LAB CRUD</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active fw-semibold" aria-current="page" href="add.php">Add Student</a></li>
        <li class="nav-item"><a class="nav-link" href="select.php">List of Students</a></li>
      </ul>
    </div>
  </div>
  </nav>

<div class="container">
  <div class="row">
    <div class="col-12 col-lg-10 col-xl-8 mx-auto">
      <div class="card mb-4">
        <div class="card-header">Add Student</div>
        <div class="card-body">
          <form method="post" action="insert.php" novalidate>
            <div class="mb-3">
              <label class="form-label">First Name</label>
              <input type="text" class="form-control" name="first_name" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Middle Name (optional)</label>
              <input type="text" class="form-control" name="middle_name">
            </div>
            <div class="mb-3">
              <label class="form-label">Last Name</label>
              <input type="text" class="form-control" name="last_name" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
              <label for="course" class="form-label">Course</label>
              <input type="text" class="form-control" id="course" name="course" required>
            </div>
            <div class="d-flex gap-2">
              <a href="select.php" class="btn btn-outline-secondary">Cancel</a>
              <button type="submit" class="btn btn-primary">Add</button>
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


