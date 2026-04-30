<?php
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require 'db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $stmt = $pdo->prepare("UPDATE bookings SET quoted_price = ?, assigned_employee = ? WHERE id = ?");
    $stmt->execute([$_POST['quoted_price'], $_POST['assigned_employee'], $_POST['booking_id']]);
}


$bookings = $pdo->query("
    SELECT b.*, u.first_name, u.last_name, u.email,
           e.first_name AS emp_first, e.last_name AS emp_last
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    LEFT JOIN employees e ON b.assigned_employee = e.id
    ORDER BY b.date ASC
")->fetchAll();


$employees = $pdo->query("SELECT * FROM employees")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Fine Lines Lawn Care</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.html">Fine Lines Lawn Care</a>
        <div class="navbar-nav ms-auto">
            <span class="nav-link text-warning">Admin: <?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <a class="nav-link" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <h2>Admin Dashboard</h2>
    <p class="text-muted">All customer bookings are listed below. You can assign an employee and set a quoted price.</p>

    <?php if (empty($bookings)): ?>
        <div class="alert alert-info">No bookings yet.</div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Service</th>
                    <th>Date & Time</th>
                    <th>Assigned Employee</th>
                    <th>Quoted Price</th>
                    <th>Update</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $b): ?>
                <tr>
                    <td><?= $b['id'] ?></td>
                    <td><?= htmlspecialchars($b['first_name'] . ' ' . $b['last_name']) ?></td>
                    <td><?= htmlspecialchars($b['email']) ?></td>
                    <td><?= htmlspecialchars($b['service_type']) ?></td>
                    <td><?= htmlspecialchars($b['date']) ?></td>
                    <td>
                        <form method="POST" class="d-flex gap-2">
                            <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                            <select name="assigned_employee" class="form-select form-select-sm">
                                <option value="">-- None --</option>
                                <?php foreach ($employees as $emp): ?>
                                    <option value="<?= $emp['id'] ?>" <?= $b['assigned_employee'] == $emp['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                    </td>
                    <td>
                            <input type="number" name="quoted_price" class="form-control form-control-sm" value="<?= htmlspecialchars($b['quoted_price'] ?? '') ?>" placeholder="$">
                    </td>
                    <td>
                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<footer class="bg-dark text-white text-center p-4">
    <p>Fine Lines Lawn Care — Admin Panel</p>
    <p class="mb-0">&copy; 2026 Fine Lines Lawn Care</p>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
