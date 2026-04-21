<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/header_styles.css">
    <link rel="stylesheet" href="css/grid.css">
    <link rel="stylesheet" href="css/houseops_styles.css">

    <title>Student Accommodation Manager</title>

</head>

<body>

<!-- start of header -->
 <header>

     <div class="container">
        <!-- logo & nav bar -->
        <div class="width-4"><i class="fa-sharp-duotone fa-light fa-people-roof"></i></div>
        <div class="width-8">
            <nav>
        <?php session_start(); ?>

            <ul>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="admin/account.php">Account</a></li>
                    <li><a href="admin/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="admin/login.php">Login or Signup</a></li>

                <?php endif; ?>
        </ul>
            </nav>
        </div>

        <!-- search box & image -->
        <div class="width-6">
            <h1>Search, find and book your new home!</h1>

            <div class="searchBox">
                <form action="">
                    <input type="text" value="Search"><button class="searchButton">Search</button>
                </form>
            </div>
        </div>

     </div> 
    </header>
    <!-- end of header -->
    
<!--start of houseops -->
<section class="housingOptions">
    <div class="container house-container">

        <div class="house-item">
            <div class="house house1">
                <h3>Our picks</h3>
            </div>
            <p>Discover your new home with recommendations from students.</p>
        </div>

        <div class="house-item">
            <div class="house house2">
                <h3>Highest Ranking</h3>
            </div>
            <p>Discover the top-rated properties based on student reviews.</p>
        </div>
      
        <div class="house-item">
            <div class="house house3">
                <h3>Only on blank</h3>
            </div>
            <p>You won't find better deals anywhere else.</p><?php session_start(); ?>

            <a href="<?php echo isset($_SESSION['user_id']) ? 'account.php' : 'login.php'; ?>">
                Find out more!<i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

    </div>
</section>
<!-- end of houseops -->
    
</body>

</html>

//http://localhost/studentAccomadationManager/index.php