<!-- Sign Up Page -->
<?php
session_start();
require __DIR__ . '/../includes/db.php';

$error = "";
$success = "";
//handle form submission 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    //validate input -
    if ($username === '' || $password === '') {
        $error = "Please enter both username and password.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        if (!$checkStmt) {
            die("Prepare failed: " . $conn->error);
        }
        //check if username already exists -
        $checkStmt->bind_param("s", $username);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();


        // if username exists, show error, otherwise create account -
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

<!-- Start HTML form for signup -->
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <link rel="stylesheet" href="../css/form_styles.css">
</head>
<body>
    <div class="login-signup-container">
        <div class="login-box">
            <h2>Sign Up</h2>

            <?php if ($error): ?>
                <p class="message error"><?php echo $error; ?></p>
            <?php endif; ?>

            <?php if ($success): ?>
                <p class="message success"><?php echo $success; ?></p>
            <?php endif; ?>

            <form method="POST" action="signup.php">

                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Sign Up</button>
                <p class="form-link">Already have an account? <a href="login.php">Login</a></p>

            </form>

            <a class="return-link" href="../index.php" style="color:#4DA0E2; text-decoration:none;" onmouseover="this.style.color='#2A6CB8';" onmouseout="this.style.color='#4DA0E2';">🠔Return to home</a>
        </div>
    </div>
</body>
</html>
<!-- End HTML form for signup -->