<?php
require '../includes/auth.php'; //requires account to access
require '../includes/db.php'; //connect to database

//get search query from URL parameters
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
?>

<!DOCTYPE html>
<html>
<head>

    <title>Search Results</title>
</head>

<body>

<h1>Search Results</h1>

<?php if ($query): ?>

    <?php
    //adjust table/columns structure
    $stmt = $conn->prepare("SELECT * FROM housing_options WHERE name LIKE ? OR location LIKE ?");
    $searchTerm = "%$query%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    ?>

    <?php if ($result->num_rows > 0): ?>
        <!-- display results -->
        <ul>
            <?php while ($row = $result->fetch_assoc()): ?>
                
                <li>
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p><?php echo htmlspecialchars($row['location']); ?></p>
                    <p>€<?php echo htmlspecialchars($row['price']); ?></p>

                    <!-- future link to booking page -->
                    <a href="booking.php?id=<?php echo $row['id']; ?>">View / Book</a>
                </li>

            <?php endwhile; ?>

        </ul>

    <?php else: ?>
        <p>No results found.</p>
    <?php endif; ?>

<?php else: ?>
    <p>Please enter a search term.</p>
<?php endif; ?>

</body>
</html>