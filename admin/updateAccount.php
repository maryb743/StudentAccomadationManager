<!-- Update Account Page -->
<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/db.php';

//get user ID from session
$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

//handle form submission for account updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    //validate input

    if ($username === '') {

        $error = 'Please enter a username.';

    } elseif ($current_password === '') {

        $error = 'Please enter your current password to save changes.';

    } else {

        $stmt = $conn->prepare('SELECT password FROM users WHERE user_id = ?');

        if (!$stmt) {

            die('Prepare failed: ' . $conn->error);

        }
        //bind user ID and execute query to get current password hash
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        //check if user exists and verify current password
        if (!$user || !password_verify($current_password, $user['password'])) {

            $error = 'Current password is incorrect.';

        } else {

        //if new password is provided, validate it
            if ($new_password !== '') {

                if ($new_password !== $confirm_password) {

                    $error = 'New password and confirmation do not match.';

                } elseif (strlen($new_password) < 6) {

                    $error = 'New password must be at least 6 characters.';

                }
            }

                //if no errors, update username and password if provided
            if ($error === '') {
                $updateSql = 'UPDATE users SET username = ?' . ($new_password !== '' ? ', password = ?' : '') . ' WHERE user_id = ?';
                $updateStmt = $conn->prepare($updateSql);

                if (!$updateStmt) {

                    die('Update Failed: ' . $conn->error);

                }

                //bind parameters based on whether new password is provided
                if ($new_password !== '') {

                    $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
                    $updateStmt->bind_param('ssi', $username, $hashedPassword, $user_id);

                } else {

                    $updateStmt->bind_param('si', $username, $user_id);

                }

                //update account and check if update was successful
                if ($updateStmt->execute()) {

                    $success = 'Account information updated successfully.';
                    $_SESSION['username'] = $username;

                } else {

                    $error = 'Unable to update account. Please try again later.';

                }
        }
    }
}
}

$stmt = $conn->prepare('SELECT username FROM users WHERE user_id = ?');

    if (!$stmt) {
        
        die('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param('i', $user_id);
    $stmt->execute();
//get result of query and fetch user data to pre-fill username field

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $currentUsername = $user['username'] ?? '';

?>

<!-- start update page html -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Account</title>
    <link rel="stylesheet" href="../css/form_styles.css">
</head>

<body>

    <div class="login-signup-container">

        <div class="login-box">

            <h2>Update Account</h2>

            <!-- error message based on update outcome -->
                <?php if ($error): ?>

                    <p class="error"><?php echo htmlspecialchars($error); ?></p>
                    
            <?php endif; ?>

            <!-- success message -->
            <?php if ($success): ?>

                <p class="message success"><?php echo htmlspecialchars($success); ?></p>

            <?php endif; ?>

            <!-- update form -->
            <form method="POST" action="updateAccount.php">

                <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($currentUsername); ?>" required>
                <input type="password" name="current_password" placeholder="Current password" required>
                <input type="password" name="new_password" placeholder="New password (leave blank to keep current)">
                <input type="password" name="confirm_password" placeholder="Confirm new password">
                <button type="submit">Save Changes</button>

            </form>

            <a class="return-link" href="account.php">🠔Back to Account</a>

        </div>

    </div>

    </body>
</html>
<!-- end update page html -->
