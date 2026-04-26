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

</head>
    <body>

    <h2>My Account</h2>

    <h3><?php echo $message; ?></h3>

    <?php if ($error): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <p><strong>Username: </strong> <?php echo htmlspecialchars($user['username']); ?></p>
    <p><strong>Role: </strong> <?php echo htmlspecialchars($user['role']); ?></p>
    <p><strong>Member Since: </strong> <?php echo $user['created_at']; ?></p>

    <a href="bookings.php">Manage Bookings</a><br>
    <form action="search.php" method="GET">

        <input type="text" name="query" placeholder="Search accommodation...">
        <button type="submit">Search</button>

    </form>

    <form method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
        <button type="submit" name="delete_account" value="1" style="color:red;">Delete Account</button>
    </form>

    <a href="logout.php">Logout</a><br>
    <a href="../index.php">←Return to home</a>

    </body>

</html>

<!-- End HTML form for account -->