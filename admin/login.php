<?php
session_start();
require __DIR__ . '/../includes/db.php';

$error = "";
//handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // get user by username
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    //bind sql querys before submission
    $stmt->bind_param("s", $username);
    $stmt->execute();

    //get result of query and fetch user data
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        //check hashed password
        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: account.php");
            exit();
        }
    }

    //error handling
    $error = "Invalid login details. Please try again.";
}
?>

<!-- Start HTML form for login -->
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
</body>
</html>
<!-- End HTML form for login -->