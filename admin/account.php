<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require __DIR__ . '/../includes/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT username, role, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

//welcome logic based on account age and user role
$created = strtotime($user['created_at']);
$isNew = (time() - $created) < 86400;

$message = "Welcome, " . htmlspecialchars($user['username']) . "!";

if ($user['role'] === 'student' && $isNew) {
    $message = "Welcome to Student Accommodation Manager, " . htmlspecialchars($user['username']) . "! 🎉";
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

    <p><strong>Username: </strong> <?php echo htmlspecialchars($user['username']); ?></p>
    <p><strong>Role: </strong> <?php echo htmlspecialchars($user['role']); ?></p>
    <p><strong>Member Since: </strong> <?php echo $user['created_at']; ?></p>

    <a href="bookings.php">Manage Bookings</a><br>
    <a href="search.php">Search Properties</a><br>
    <a href="logout.php">Logout</a><br>
    <a href="../index.php">←Return to home</a>

    </body>

</html>