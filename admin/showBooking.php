<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT b.booking_id, b.start_date, b.end_date, b.total_price, b.`status`, b.created_at, h.name, h.location
     FROM bookings b
     JOIN housing_options h ON b.housing_id = h.housing_id
     WHERE b.user_id = ?
     ORDER BY b.created_at DESC"
);

if (!$stmt) {
    die('Database query failed: ' . $conn->error);
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>

</head>
<body>
<div class="login-signup-container">
    <div class="login-box">
        <h2>My Bookings</h2>

        <?php if ($result->num_rows === 0): ?>
            <p>You have no bookings yet. Search for accommodation to make a booking.</p>
        <?php else: ?>
            <div class="booking-list">
                <?php while ($booking = $result->fetch_assoc()): ?>
                    <div class="booking-card">
                        <h3><?php echo htmlspecialchars($booking['name']); ?></h3>
                        <div class="meta">
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($booking['location']); ?></p>
                            <p><strong>Stay:</strong> <?php echo htmlspecialchars($booking['start_date']); ?> to <?php echo htmlspecialchars($booking['end_date']); ?></p>
                            <p><strong>Total price:</strong> €<?php echo htmlspecialchars($booking['total_price']); ?></p>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($booking['status']); ?></p>
                            <p><strong>Booked on:</strong> <?php echo htmlspecialchars($booking['created_at']); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <a class="return-link" href="account.php">← Back to Account</a>
    </div>
</div>
</body>
</html>
