<?php
session_start();
require __DIR__ . '/../includes/db.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($username === '' || $password === '') {
        $error = "Please enter both username and password.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // ✅ FIXED: use user_id instead of id
        $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        if (!$checkStmt) {
            die("Prepare failed: " . $conn->error);
        }

        $checkStmt->bind_param("s", $username);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $error = "Username already exists";
        } else {
            $insertStmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'student')");
            
            if (!$insertStmt) {
                die("Insert prepare failed: " . $conn->error);
            }

            $insertStmt->bind_param("ss", $username, $hashedPassword);

            if ($insertStmt->execute()) {
                $success = "Account created! Please login.";
            } else {
                $error = "Error creating account: " . $insertStmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
</head>
<body>

<h2>Sign Up</h2>

<?php if ($error): ?>
    <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p style="color:green;"><?php echo $success; ?></p>
<?php endif; ?>

<form method="POST" action="signup.php">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Sign Up</button>
    <p>Already have an account? <a href="login.php">Login</a></p>
</form>

<a href="../index.php">←Return to home</a>

</body>
</html>