<?php
session_start();

//require authentication to access account page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require __DIR__ . '/../includes/db.php';

$user_id = $_SESSION['user_id'];
$error = "";
//handle account deletion request

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    
    $deleteStmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $deleteStmt->bind_param("i", $user_id);

//execute deletion and log out user if successful
    if ($deleteStmt->execute()) {

            session_unset();
        session_destroy();
        header("Location: ../index.php");
        exit();

    }

    //error handling
    $error = "Unable to delete account. Please try again later.";
}

//fetch user details for account page display
$stmt = $conn->prepare("SELECT username, role, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

//welcome logic based on account age and user role
$created = strtotime($user['created_at']);
$isNew = (time() - $created) < 86400;

$message = "Welcome, " . htmlspecialchars($user['username']) . "!";

//personalized welcome message for new student users
if ($user['role'] === 'student' && $isNew) {
    $message = "Welcome to Student Accommodation Manager, " . htmlspecialchars($user['username']) . "!";
} elseif ($user['role'] === 'student') {
    $message = "Welcome back, " . htmlspecialchars($user['username']) . "!";
}
?>

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