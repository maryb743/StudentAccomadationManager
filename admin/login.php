<?php
session_start();
//allow user to log in and create session data
require __DIR__ . '/../includes/db.php';

$error = "";
$success = "";
$action = $_GET['action'] ?? 'login';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (isset($_POST['signup'])) {
        //signup page to hash passwords and create users
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $checkStmt->bind_param("s", $username);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            $error = "Username already exists.";
        } else {
            $insertStmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'student')");
            $insertStmt->bind_param("ss", $username, $hashedPassword);
            
            if ($insertStmt->execute()) {
                $success = "Account created! Please login.";
                $action = 'login';
            } else {
                $error = "Error creating account.";
            }
        }
    } else {
        //verify password
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            //erfiy if password is hashed or plain text
            $passwordMatch = password_verify($password, $user['password']) 
                || $password === $user['password'];

            if ($passwordMatch) {
                //stores session data
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header("Location: account.php");
                exit();
            }
        }
        
        $error = "Invalid login details. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>

<h2><?php echo $action === 'signup' ? 'Sign Up' : 'Login'; ?></h2>

<?php if ($error): ?>
    <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p style="color:green;"><?php echo $success; ?></p>
<?php endif; ?>

<?php if ($action === 'signup'): ?>
<form method="POST" action="login.php?action=signup">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="signup" value="1">Sign Up</button>
    <p>Already have an account? <a href="login.php">Login</a></p>
</form>
<?php else: ?>
<form method="POST" action="login.php">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
    <p>Don't have an account? <a href="login.php?action=signup">Sign Up</a></p>
</form>
<?php endif; ?>

<a href="../index.php">←Return to home</a>