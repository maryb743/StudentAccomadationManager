<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require __DIR__ . '/../includes/db.php';

$user_id = $_SESSION['user_id'];
$error = "";

/* DELETE ACCOUNT */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {

    // ✅ FIXED: user_id instead of id
    $deleteStmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    
    if (!$deleteStmt) {
        die("Prepare failed: " . $conn->error);
    }

    $deleteStmt->bind_param("i", $user_id);

    if ($deleteStmt->execute()) {
        session_unset();
        session_destroy();
        header("Location: ../index.php");
        exit();
    }

    $error = "Unable to delete account. Please try again later.";
}

/* FETCH USER */
$stmt = $conn->prepare("SELECT username, role, created_at FROM users WHERE user_id = ?");

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

/* WELCOME MESSAGE */
$created = strtotime($user['created_at']);
$isNew = (time() - $created) < 86400;

$message = "Welcome, " . htmlspecialchars($user['username']) . "!";

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