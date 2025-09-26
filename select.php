<?php
declare(strict_types=1);
require __DIR__ . '/db_connect.php';

// Sorting logic (allow by name columns or email)
$allowedSort = ['first_name', 'last_name', 'email', 'created_at'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowedSort, true) ? $_GET['sort'] : 'created_at';
$dir = isset($_GET['dir']) && in_array(strtoupper($_GET['dir']), ['ASC', 'DESC'], true) ? strtoupper($_GET['dir']) : 'DESC';

// Fetch all students
$stmt = $pdo->query("SELECT id, first_name, middle_name, last_name, email, course, created_at FROM students ORDER BY {$sort} {$dir}");
$students = $stmt->fetchAll();

// Toggle helper
function toggleDir(string $current): string { return $current === 'ASC' ? 'DESC' : 'ASC'; }

// Format full name in Title Case, with optional middle name
function formatName(array $row): string {
    $parts = [];
    if (!empty($row['first_name'])) { $parts[] = trim((string)$row['first_name']); }
    if (!empty($row['middle_name'])) { $parts[] = trim((string)$row['middle_name']); }
    if (!empty($row['last_name'])) { $parts[] = trim((string)$row['last_name']); }
    $name = trim(implode(' ', array_filter($parts)));
    if ($name === '') { return ''; }
    return mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student CRUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body { background-color: #f8f9fa; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 sticky-top">
  <div class="container">
    <a class="navbar-brand" href="select.php">STUDENT LAB CRUD</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="add.php">Add Student</a></li>
        <li class="nav-item"><a class="nav-link active fw-semibold" aria-current="page" href="select.php">List of Students</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
  <div class="row">
    

    <div class="col-12 col-lg-10 col-xl-8 mx-auto">
      <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
          <?php echo e($_GET['msg']); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span>Students</span>
          <div class="small text-muted">Sort: 
            <a href="?sort=first_name&dir=<?php echo e(toggleDir($dir)); ?>">First</a> |
            <a href="?sort=last_name&dir=<?php echo e(toggleDir($dir)); ?>">Last</a> |
            <a href="?sort=email&dir=<?php echo e(toggleDir($dir)); ?>">Email</a> |
            <a href="?sort=created_at&dir=<?php echo e(toggleDir($dir)); ?>">Created</a>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-light">
            <tr>
              <th>#</th>
              <th class="text-nowrap">Name</th>
              <th>Email</th>
              <th>Course</th>
              <th>Created</th>
              <th class="text-end">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!$students): ?>
              <tr><td colspan="6" class="text-center text-muted">No records yet.</td></tr>
            <?php else: ?>
              <?php foreach ($students as $row): ?>
                <tr>
                  <td><?php echo (int)$row['id']; ?></td>
                  <td class="text-nowrap"><?php echo e(formatName($row)); ?></td>
                  <td><?php echo e($row['email']); ?></td>
                  <td><?php echo e($row['course']); ?></td>
                  <td><?php echo e($row['created_at']); ?></td>
                  <td class="text-end">
                    <div class="d-inline-flex gap-2">
                      <a class="btn btn-sm btn-outline-secondary" href="update.php?id=<?php echo (int)$row['id']; ?>">Edit</a>
                      <button 
                        class="btn btn-sm btn-outline-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#confirmDeleteModal"
                        data-id="<?php echo (int)$row['id']; ?>"
                        data-name="<?php echo e(formatName($row)); ?>">
                        Delete
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete <strong id="deleteStudentName">this student</strong>?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form method="post" action="delete.php" class="m-0">
          <input type="hidden" name="id" id="deleteStudentId" value="">
          <button type="submit" class="btn btn-danger">Delete</button>
        </form>
      </div>
  </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
  const modal = document.getElementById('confirmDeleteModal');
  modal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id');
    const name = button.getAttribute('data-name');
    document.getElementById('deleteStudentId').value = id;
    document.getElementById('deleteStudentName').textContent = name;
  });
</script>
</body>
</html>


