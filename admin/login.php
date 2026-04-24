<?php
session_start();
//llow user to log in and create session data
require __DIR__ . '/../includes/db.php';

$error = "";

//validate login credentials and create session if valid
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    //verify password
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        //verify if password is hashed or plain text
        $passwordMatch = password_verify($password, $user['password'])
            || $password === $user['password'];

        if ($passwordMatch) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: account.php");
            exit();
        }
    }

    $error = "Invalid login details. Please try again.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>

<h2>Login</h2>

<!-- error message based on registration outcome -->
<?php if ($error): ?>
    <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<!-- login form -->
<form method="POST" action="login.php">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
    <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
</form>

<a href="../index.php">←Return to home</a>