<!-- Add Booking Page -->
<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/db.php';

//get housing ID from URL
$housing_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = '';
$error = '';
$listing = null;

//getlisting details from database
if ($housing_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM housing_options WHERE housing_id = ?");
    $stmt->bind_param("i", $housing_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $listing = $result->fetch_assoc();
}

//error handling
if (!$listing) {
    $error = 'Selected listing was not found. Please go back and choose a valid option.';
}

//handle booking form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_now']) && $listing) {
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');
    //validate dates
    if ($start_date === '' || $end_date === '') {
        $error = 'Please select both a start date and an end date.';
    } else {
        $start_ts = strtotime($start_date);
        $end_ts = strtotime($end_date);

        //error handling
        if (!$start_ts || !$end_ts || $end_ts <= $start_ts) {
            $error = 'Please choose a valid date range with end date after start date.';
        } else {
            //calculate total price based on duration and listing price
            $days = ceil(($end_ts - $start_ts) / 86400);
            $months = max(1, ceil($days / 30));
            $total_price = $listing['price'] * $months;
            $status = 'pending';
            $created_at = date('Y-m-d H:i:s');
            $user_id = $_SESSION['user_id'];

            //insert booking into database
            $insert = $conn->prepare(
                "INSERT INTO bookings (user_id, housing_id, start_date, end_date, total_price, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            //error handling
            if (!$insert) {
                $error = 'Unable to prepare booking query.';
            } else {
                $insert->bind_param(
                    
                    'iissdss',
                    $user_id,
                    $housing_id,
                    $start_date,
                    $end_date,
                    $total_price,
                    $status,
                    $created_at
                );

                //error handling
                if ($insert->execute()) {
                    $message = 'Booking request submitted successfully! You can manage your bookings from the Bookings page.';
                } else {
                    $error = 'Unable to save your booking. Please try again later.';
                }
            }
        }
    }
}
?>

<!--start addBookings html-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Accommodation</title>
    <link rel="stylesheet" href="../css/form_styles.css">
</head>
<body>
<div class="login-signup-container">
    <div class="login-box">
        <h2>Book Accommodation</h2>

        
        <?php if ($error): ?>
            
            <p class="message error"><?php echo htmlspecialchars($error); ?></p>

        <?php endif; ?>

        <?php if ($message): ?>

            <p class="message success"><?php echo htmlspecialchars($message); ?></p>

        <?php endif; ?>

        <?php if ($listing): ?>

            <!-- Listing details -->
            <p><strong><?php echo htmlspecialchars($listing['name']); ?></strong></p>

            <p><?php echo nl2br(htmlspecialchars($listing['description'])); ?></p>

            <p><strong>Location:</strong> <?php echo htmlspecialchars($listing['location']); ?></p>

            <p><strong>Price:</strong> €<?php echo htmlspecialchars($listing['price']); ?> / month</p>

            <!-- Booking form -->
            <form method="POST" action="addBooking.php?id=<?php echo $housing_id; ?>">
                <label>
                    Start date
                    <input type="date" name="start_date" required>
                </label>

                <label>
                    End date
                    <input type="date" name="end_date" required>
                </label>

                <button type="submit" name="book_now">Book Now</button>
            </form>
        <?php else: ?>
            <p>Please return to search and choose a valid accommodation option.</p>
        <?php endif; ?>

        <a class="return-link" href="search.php">🠔Back to search</a>

    </div>
</div>

</body>
</html>
<!--end showBookings -->