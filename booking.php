<?php
session_start();
require 'db.php';

$success = '';
$error   = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $service  = $_POST['service_type'];
    $date     = $_POST['date'];
    $time     = $_POST['time'];
    $datetime = $date . ' ' . $time . ':00';

   
    $check = $pdo->prepare("SELECT id FROM bookings WHERE date = ?");
    $check->execute([$datetime]);

    if ($check->fetch()) {
        $error = 'That time slot is already booked. Please choose another.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, service_type, date, approved_by_client) VALUES (?, ?, ?, 1)");
        $stmt->execute([$_SESSION['user_id'], $service, $datetime]);
        $success = 'Your booking has been submitted! We will confirm shortly.';
    }
}


$booked = $pdo->query("SELECT date FROM bookings")->fetchAll(PDO::FETCH_COLUMN);
$bookedJson = json_encode($booked);


$myBookings = [];
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY date DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $myBookings = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book a Service | Fine Lines Lawn Care</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.html">Fine Lines Lawn Care</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="lawn-mowing.html">Lawn Mowing</a></li>
                <li class="nav-item"><a class="nav-link" href="hedge-trimming.html">Hedge Trimming</a></li>
                <li class="nav-item"><a class="nav-link" href="snow-plowing.html">Snow Plowing</a></li>
                <li class="nav-item"><a class="nav-link active" href="booking.php">Book Now</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item ms-lg-2"><a class="btn btn-outline-light btn-sm" href="logout.php">Logout (<?= htmlspecialchars($_SESSION['user_name']) ?>)</a></li>
                <?php else: ?>
                    <li class="nav-item ms-lg-2"><a class="btn btn-success btn-sm" href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<section class="hero text-center text-white">
    <div class="container">
        <h1>Book a Service</h1>
        <p>Select a date and time that works for you.</p>
    </div>
</section>

<div class="container my-5">

    <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="alert alert-info text-center">
            You need to <a href="login.php">login</a> or <a href="register.php">create an account</a> to book a service.
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Booking Form -->
        <div class="col-md-6">
            <h3>Request a Booking</h3>
            <form method="POST" id="bookingForm">
                <div class="mb-3">
                    <label class="form-label">Service</label>
                    <select name="service_type" class="form-select" required>
                        <option value="Lawn Mowing">Lawn Mowing</option>
                        <option value="Hedge Trimming">Hedge Trimming</option>
                        <option value="Snow Plowing">Snow Plowing</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" id="dateInput" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Time Slot</label>
                    <select name="time" id="timeSelect" class="form-select" required>
                        <option value="08:00">8:00 AM</option>
                        <option value="10:00">10:00 AM</option>
                        <option value="12:00">12:00 PM</option>
                        <option value="14:00">2:00 PM</option>
                        <option value="16:00">4:00 PM</option>
                    </select>
                </div>
                <div id="slotStatus" class="mb-3"></div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <button type="submit" class="btn btn-primary">Submit Booking</button>
                <?php else: ?>
                    <button type="button" class="btn btn-secondary" disabled>Login to Book</button>
                <?php endif; ?>
            </form>
        </div>

        <!-- Booked Slots Info -->
        <div class="col-md-6">
            <h3>Availability</h3>
            <p class="text-muted">Select a date to see which slots are taken.</p>
            <div id="availabilityDisplay"></div>
        </div>
    </div>

    <!-- Customer's Own Bookings -->
    <?php if (!empty($myBookings)): ?>
    <div class="mt-5">
        <h3>My Bookings</h3>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Service</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($myBookings as $b): ?>
                <tr>
                    <td><?= htmlspecialchars($b['service_type']) ?></td>
                    <td><?= htmlspecialchars($b['date']) ?></td>
                    <td><?= $b['approved_by_client'] ? '<span class="badge bg-success">Confirmed</span>' : '<span class="badge bg-warning">Pending</span>' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

</div>

<footer class="bg-dark text-white text-center p-4">
    <p>Fine Lines Lawn Care | Barre, Vermont</p>
    <p>Phone: 802-622-4117 | Email: baileyerwin01@gmail.com</p>
    <p class="mb-0">&copy; 2026 Fine Lines Lawn Care</p>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script>
// All booked datetimes from the server
const bookedSlots = <?= $bookedJson ?>;

const slots = ['08:00', '10:00', '12:00', '14:00', '16:00'];
const slotLabels = {
    '08:00': '8:00 AM', '10:00': '10:00 AM', '12:00': '12:00 PM',
    '14:00': '2:00 PM', '16:00': '4:00 PM'
};

const dateInput    = document.getElementById('dateInput');
const timeSelect   = document.getElementById('timeSelect');
const slotStatus   = document.getElementById('slotStatus');
const availDisplay = document.getElementById('availabilityDisplay');

// Set min date to today
dateInput.min = new Date().toISOString().split('T')[0];

function checkSlots() {
    const selectedDate = dateInput.value;
    if (!selectedDate) return;

    // Check selected slot
    const selectedTime = timeSelect.value;
    const selectedDT   = selectedDate + ' ' + selectedTime + ':00';
    const isTaken      = bookedSlots.includes(selectedDT);

    slotStatus.innerHTML = isTaken
        ? '<div class="alert alert-warning mb-0">That slot is already booked. Please pick another time.</div>'
        : '<div class="alert alert-success mb-0">This slot is available!</div>';

    // Show all slots for that day
    let html = '<ul class="list-group">';
    slots.forEach(slot => {
        const dt    = selectedDate + ' ' + slot + ':00';
        const taken = bookedSlots.includes(dt);
        html += `<li class="list-group-item d-flex justify-content-between">
            ${slotLabels[slot]}
            <span class="badge ${taken ? 'bg-danger' : 'bg-success'}">${taken ? 'Booked' : 'Open'}</span>
        </li>`;
    });
    html += '</ul>';
    availDisplay.innerHTML = html;
}

dateInput.addEventListener('change', checkSlots);
timeSelect.addEventListener('change', checkSlots);
</script>
</body>
</html>
