<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/db.php';

//get user ID from session
$user_id = $_SESSION['user_id'];
$error = '';
$message = '';
$editBookingId = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$editBooking = null;

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
    } else {
        $error = 'Invalid booking selected for deletion.';
    }
}

//booking update option
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_booking'])) {
    $updateBookingId = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');

    //validate input
    if ($updateBookingId <= 0) {

        $error = 'Invalid booking selected for update.';

    } elseif ($start_date === '' || $end_date === '') {

        $error = 'Please enter both start and end dates.';

    } else {

        $start_ts = strtotime($start_date);
        $end_ts = strtotime($end_date);
    //validate date range
        if (!$start_ts || !$end_ts || $end_ts <= $start_ts) {
            $error = 'Please choose a valid date range with end date after start date.';

        } else {
            //fetch booking details to recalculate total price based on new dates
            $fetchBookingStmt = $conn->prepare(
                "SELECT b.booking_id, b.user_id, b.housing_id, h.price
                 FROM bookings b
                 JOIN housing_options h ON b.housing_id = h.housing_id
                 WHERE b.booking_id = ? AND b.user_id = ?"
            );

            //error handling
            if (!$fetchBookingStmt) {

                $error = 'Unable to prepare booking fetch query.';

            } else {
    //bind parameters and execute query to get booking details
                $fetchBookingStmt->bind_param('ii', $updateBookingId, $user_id);
                $fetchBookingStmt->execute();
                $fetchResult = $fetchBookingStmt->get_result();
                $bookingData = $fetchResult->fetch_assoc();

                //error handling
                if (!$bookingData) {

                    $error = 'Booking not found or access denied.';

                } else {

                    $days = ceil(($end_ts - $start_ts) / 86400);
                    $months = max(1, ceil($days / 30));
                    $total_price = $bookingData['price'] * $months;

                    //update booking in database
                    $updateStmt = $conn->prepare(
                        "UPDATE bookings SET start_date = ?, end_date = ?, total_price = ? WHERE booking_id = ? AND user_id = ?"
                    );

                    //error handling
                    if (!$updateStmt) {

                        $error = 'Unable to prepare booking update query.';

                    } else {

                    //bind parameters and execute update query
                        $updateStmt->bind_param('ssdii', $start_date, $end_date, $total_price, $updateBookingId, $user_id);

                    //update booking and check if update was successful
                        if ($updateStmt->execute()) {

                            if ($updateStmt->affected_rows > 0) {

                                $message = 'Booking updated successfully.';

                            } else {

                                $error = 'No changes were saved.';

                            }
                        } else {
                            $error = 'Unable to update booking. Please try again later.';
                        }

                    }
                }
        }
        }
    }
}

//get user bookings from database
$stmt = $conn->prepare(

    "SELECT b.booking_id, b.start_date, b.end_date, b.total_price, b.`status`, b.created_at, h.name, h.location, h.price
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

<!--end showBookings html-->
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
                        <?php if ($editBookingId === intval($booking['booking_id'])): ?>
                            <form method="POST" action="showBooking.php">
                                <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['booking_id']); ?>">

                                <label>
                                    Start date
                                    <input type="date" name="start_date" value="<?php echo htmlspecialchars($booking['start_date']); ?>" required>
                                </label>

                                <label>
                                    End date
                                    <input type="date" name="end_date" value="<?php echo htmlspecialchars($booking['end_date']); ?>" required>
                                </label>

                                <button type="submit" name="update_booking">Save Booking</button>
                                <a class="return-link" href="showBooking.php" style="display:inline-block; margin-top:10px; color:#4DA0E2; text-decoration:none;" onmouseover="this.style.color='#2A6CB8';" onmouseout="this.style.color='#4DA0E2';">Cancel</a>
                            </form>
                        <?php else: ?>
                            <a class="return-link" href="showBooking.php?edit=<?php echo htmlspecialchars($booking['booking_id']); ?>" style="display:inline-block; margin-bottom:10px; color:#4DA0E2; text-decoration:none;" onmouseover="this.style.color='#2A6CB8';" onmouseout="this.style.color='#4DA0E2';">Update Booking</a>
                            <form method="POST" action="showBooking.php" onsubmit="return confirm('Are you sure you want to delete this booking?');">
                                <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['booking_id']); ?>">
                                <button type="submit" name="delete_booking">Delete Booking</button>
                            </form>
                        <?php endif; ?>

                    </div>

                <?php endwhile; ?>

            </div>

        <?php endif; ?>

        <a class="return-link" href="account.php" style="color:#4DA0E2; text-decoration:none;" onmouseover="this.style.color='#2A6CB8';" onmouseout="this.style.color='#4DA0E2';">← Back to Account</a>
    </div>

    </div>

    </body>

</html>
<!--end showBookings -->
