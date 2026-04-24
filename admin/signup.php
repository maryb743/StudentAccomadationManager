<?php
session_start();
//allow user registration and create account records
require __DIR__ . '/../includes/db.php';

$error = "";
$success = "";

//validate input and create user account
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    //check for empty fields
    if ($username === '' || $password === '') {
        $error = "Please enter both username and password.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        //check if username already exists
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $checkStmt->bind_param("s", $username);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        //if username exists, show error. Otherwise, insert new user with student role
        if ($checkResult->num_rows > 0) {
            $error = "Username already exists";
        } else {
            $insertStmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'student')");
            $insertStmt->bind_param("ss", $username, $hashedPassword);

            //execute insert and show success or error message
            if ($insertStmt->execute()) {
                $success = "Account created! Please login.";
            } else {
                $error = "Error creating account";
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

<!-- error messages based on registration outcome -->
<?php if ($error): ?>
    <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p style="color:green;"><?php echo $success; ?></p>
<?php endif; ?>

<!-- signup form -->
<form method="POST" action="signup.php">

    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Sign Up</button>
    <p>Already have an account? <a href="login.php">Login</a></p>

</form>

<a href="../index.php">←Return to home</a>