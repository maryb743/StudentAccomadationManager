<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/db.php';

//get user ID from session
$user_id = $_SESSION['user_id'];
$error = '';
$message = '';

//booking deletion option 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_booking'])) {
    $deleteBookingId = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
    if ($deleteBookingId > 0) {
        $deleteStmt = $conn->prepare("DELETE FROM bookings WHERE booking_id = ? AND user_id = ?");
        if (!$deleteStmt) {
            $error = 'Unable to prepare delete query.';
        } else {
            //bind parameters and execute delete query
            $deleteStmt->bind_param('ii', $deleteBookingId, $user_id);
            if ($deleteStmt->execute()) {
                if ($deleteStmt->affected_rows > 0) {
                    $message = 'Booking deleted.';

                } else {

                    $error = 'Booking not found';
                }
            } else {
                $error = 'Unable to delete booking.';
            }
        }

        //error handling
    } else {
        $error = 'Invalid booking selected for deletion.';
    }
}

//get user bookings from database
$stmt = $conn->prepare(
    "SELECT b.booking_id, b.start_date, b.end_date, b.total_price, b.`status`, b.created_at, h.name, h.location
     FROM bookings b
     JOIN housing_options h ON b.housing_id = h.housing_id
     WHERE b.user_id = ?
     ORDER BY b.created_at DESC"
);

//error handling
if (!$stmt) {
    die('Database query failed: ' . $conn->error);
}

//bind user ID and execute query
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
    <link rel="stylesheet" href="../css/form_styles.css">
</head>
<body>
<div class="login-signup-container">
    <div class="login-box">
        <h2>My Bookings</h2>

        <?php if ($message): ?>
            <p class="message success"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p class="message error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

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
                        <form method="POST" action="showBooking.php" onsubmit="return confirm('Are you sure you want to delete this booking?');">
                            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['booking_id']); ?>">
                            <button type="submit" name="delete_booking">Delete Booking</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <a class="return-link" href="account.php" style="color:#4DA0E2; text-decoration:none;" onmouseover="this.style.color='#2A6CB8';" onmouseout="this.style.color='#4DA0E2';">← Back to Account</a>
    </div>
</div>
</body>
</html>
