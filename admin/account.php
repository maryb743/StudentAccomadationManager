<!-- Account Page -->
<?php
session_start();
//Checks if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require __DIR__ . '/../includes/db.php';

//get user ID from session
$user_id = $_SESSION['user_id'];
$error = "";

//delete account option
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {

//delete user from database
    $deleteStmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    //error handling
    if (!$deleteStmt) {
        die("Prepare failed: " . $conn->error);
    }

    $deleteStmt->bind_param("i", $user_id);
    //delet and log user out
    if ($deleteStmt->execute()) {
        session_unset();
        session_destroy();
        header("Location: ../index.php");
        exit();
    }

    $error = "Unable to delete account. Please try again later.";
}

//get user info from database
$stmt = $conn->prepare("SELECT username, role, created_at FROM users WHERE user_id = ?");

//error handling
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

//bind user ID and execute query
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

//welcome message to new users
$created = strtotime($user['created_at']);
$isNew = (time() - $created) < 86400;

$message = "Welcome, " . htmlspecialchars($user['username']) . "!";

if ($user['role'] === 'student' && $isNew) {
    $message = "Welcome to Student Accommodation Manager, " . htmlspecialchars($user['username']) . "!";
} elseif ($user['role'] === 'student') {
    $message = "Welcome back, " . htmlspecialchars($user['username']) . "!";
}
?>

<!-- Start HTML form for account -->
<!DOCTYPE html>
<html>
<head>
    <title>My Account</title>
    <link rel="stylesheet" href="../css/account_styles.css">
</head>
    <body>
    <div class="account-page">
        <div class="account-card">

            <h2>My Account</h2>

            <h3><?php echo $message; ?></h3>

           <!-- error handling -->
            <?php if ($error): ?>

                <p class="message error"><?php echo htmlspecialchars($error); ?></p>

            <?php endif; ?>

            <!-- account details section -->
            <div class="account-details">

                <p><span>Username:</span> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><span>Role:</span> <?php echo htmlspecialchars($user['role']); ?></p>
                <p><span>Member Since:</span> <?php echo $user['created_at']; ?></p>

            </div>

                <!-- account actions -->
            <div class="account-actions">
                <a class="button secondary" href="<?php echo dirname($_SERVER['PHP_SELF']); ?>/showBooking.php" style="color:#4DA0E2; text-decoration:none;" onmouseover="this.style.color='#2A6CB8';" onmouseout="this.style.color='#4DA0E2';">Manage Bookings</a>
                <a class="button secondary" href="<?php echo dirname($_SERVER['PHP_SELF']); ?>/updateAccount.php" style="color:#4DA0E2; text-decoration:none;" onmouseover="this.style.color='#2A6CB8';" onmouseout="this.style.color='#4DA0E2';">Update Account Info</a>
                
                <!-- search fprms -->
                <form class="search-form" action="<?php echo dirname($_SERVER['PHP_SELF']); ?>/search.php" method="GET">
                    <input type="text" name="query" placeholder="Search accommodation...">
                    <button type="submit" class="button">Search</button>
                </form>

                <!-- update database -->
                <div class="account-row">
                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete your account?');">
                        <button type="submit" name="delete_account" value="1" class="button danger">Delete Account</button>
                    </form>

                    <a class="button logout" href="logout.php" style="color:#4DA0E2; text-decoration:none;" onmouseover="this.style.color='#2A6CB8';" onmouseout="this.style.color='#4DA0E2';">Logout</a>

                </div>

                <a class="return-link" href="../index.php" style="color:#4DA0E2; text-decoration:none;" onmouseover="this.style.color='#2A6CB8';" onmouseout="this.style.color='#4DA0E2';">🠔Return to home</a>
            </div>
        </div>
    </div>
    </body>

</html>

<!-- End HTML form for account -->