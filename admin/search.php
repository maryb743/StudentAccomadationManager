<?php
require '../includes/auth.php'; // makes sure user is logged in
require '../includes/db.php';   // connect to database

//get search query from URL
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
?>

<!-- start HTML form for search -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="../css/search_styles.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Search Results</title>
</head>

<body>
    <div class="search-page-container">
        <div class="search-box">
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
            <form action="search.php" method="GET">
                <button type="submit">Show All listings</button>
            </form>

            <!-- Back to account link -->
            <a class="back-link" href="account.php">← Back to Account</a>

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
    
     <!-- display search results-->
            <?php if ($result->num_rows > 0): ?>
                <section class="search-section">

                    <div class="results-grid">

                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="result-card">

                                <?php
                                    $imageSrc = trim($row['image']);

                                    if ($imageSrc !== '') {
                                        if (!preg_match('#^(?:[a-z]+:)?//#i', $imageSrc)
                                            && strpos($imageSrc, '/') !== 0
                                            && strpos($imageSrc, './') !== 0
                                            && strpos($imageSrc, '../') !== 0
                                        ) {
                                            $imageSrc = '../' . $imageSrc;
                                        }
                                    }

                                    if ($imageSrc === '') {
                                        $imageSrc = '../images/searchImage1.jpg';
                                    }
                                ?>

                                <img 
                                    src="<?php echo htmlspecialchars($imageSrc); ?>" 
                                    alt="<?php echo htmlspecialchars($row['name']); ?>"
                                >

                                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                                <p><?php echo htmlspecialchars($row['description']); ?></p>
                                <p class="meta"><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                                <p class="price">€<?php echo htmlspecialchars($row['price']); ?>/month</p>
                                <a class="book-btn" href="addBooking.php?id=<?php echo $row['housing_id']; ?>">Book <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </section>
            <?php else: ?>
                <p class="no-results">No results found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
<!-- End HTML form for signup -->