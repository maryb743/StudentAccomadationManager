<?php
require '../includes/auth.php'; //makes sure user is logged in
require '../includes/db.php';   //connect to database

//get search query from URL
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
?>

<!-- start HTML form for search -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="../css/search_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

<h1>Search Results</h1>

<!-- Search bar -->
<form action="search.php" method="GET">
    <input 
        type="text" 
        name="query" 
        placeholder="Search accommodation..." 
        value="<?php echo htmlspecialchars($query); ?>"
    >
    <button type="submit">Search</button>
</form>

<!-- Show all listings button -->
<form action="search.php" method="GET" style="margin-top:10px;">
    <button type="submit">Show All listings</button>
</form>

<!-- Back to account link -->
<a href="account.php" style="color:#4DA0E2; text-decoration:none;" onmouseover="this.style.color='#2A6CB8';" onmouseout="this.style.color='#4DA0E2';">← Back to Account</a>

<?php
//show all listings unless search query has been done then show results
    if ($query !== '') {
        echo '<h2>Results for "' . htmlspecialchars($query) . '"</h2>';

        $stmt = $conn->prepare(
            "SELECT * FROM housing_options 
            WHERE name LIKE ? OR location LIKE ? OR description LIKE ?"
        );

        $searchTerm = "%$query%";
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    } else {
        echo '<h2>All Properties</h2>';

        $stmt = $conn->prepare("SELECT * FROM housing_options");
    }

    $stmt->execute();
    $result = $stmt->get_result();
?>

<?php if ($result->num_rows > 0): ?>
   <!-- loop through results and display them -->

    <section class="search-section">
        <div class="container search-container">
            <?php while ($row = $result->fetch_assoc()): ?>
                
                <div class="search-card">

                    <div class="info desc<?php echo ($row['housing_id'] % 3) + 1; ?>">
                        <?php
                            $imageSrc = trim($row['image']);
                            if ($imageSrc !== '' && !preg_match('#^(?:[a-z]+:)?//#i', $imageSrc) && strpos($imageSrc, '/') !== 0) {
                                $imageSrc = '../' . $imageSrc;
                            }
                        ?>

                    <!--display image if it exists, otherwise show placeholder -->
                        <img 
                            src="<?php echo htmlspecialchars($imageSrc); ?>" 
                            alt="<?php echo htmlspecialchars($row['name']); ?>"
                            style="width:100%; height:180px; object-fit:cover; border-radius:10px;"
                        >

                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    </div>

                    <p><?php echo htmlspecialchars($row['description']); ?></p>

                    <p><strong>Location:</strong> 
                        <?php echo htmlspecialchars($row['location']); ?>
                    </p>

                    <p><strong>€<?php echo htmlspecialchars($row['price']); ?>/month</strong></p>

                    <!-- link to booking page -->
                    <a href="addBooking.php?id=<?php echo $row['housing_id']; ?>">
                        Book <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            <?php endwhile; ?>

        </div>
    </section>

<?php else: ?>
    <p>No results found.</p>
<?php endif; ?>
</body>
</html>
<!-- End HTML form for signup -->