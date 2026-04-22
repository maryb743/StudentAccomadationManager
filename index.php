<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/header_styles.css">
    <link rel="stylesheet" href="css/grid.css">
    <link rel="stylesheet" href="css/fom_styles.css">
    <link rel="stylesheet" href="css/loc_styles.css">

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
    
<!--start of fom -->
<section class="findOutMore">
    <div class="container info-container">

        <div class="info-item">
            <div class="info desc1">
                <h3>Our picks</h3>
            </div>
            <p>Discover your new home with recommendations from students.</p>
        </div>

        <div class="info-item">
            <div class="info desc2">
                <h3>Highest Ranking</h3>
            </div>
            <p>Discover the top-rated properties based on student reviews.</p>
        </div>
      
        <div class="info-item">
            <div class="info desc3">
                <h3>The best deals!</h3>
            </div>
            <a href="<?php echo isset($_SESSION['user_id']) ? 'admin/account.php' : 'admin/login.php'; ?>">
                Find out more!<i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

    </div>
</section>
<!-- end of fom -->

<!-- Start of List of Universitys -->
<section class="listOfColleges">
    <div class="container college-container">
        <div class="width-12">
            <h2>Our Top University Partners</h2>
        </div>

<div class="width-12">
<ul>
    
    <li><a href="https://www.tudublin.ie/">Technological University Dublin</a></li>
    <li><a href="https://www.ucc.ie/">University College Cork</a></li>
    <li><a href="https://www.nuigalway.ie/">University of Galway</a></li>
    <li><a href="https://www.ul.ie/">University of Limerick</a></li>
    <li><a href="https://www.dcu.ie/">Dublin City University</a></li>
    <li><a href="https://www.maynoothuniversity.ie/">Maynooth University</a></li>
    <li><a href="https://www.rcs.ie/">Royal College of Surgeons</a></li>
    
</ul>
</div>

</div>

</section>
<!-- End of List of Universitys -->
    
</body>

</html>

//http://localhost/studentAccomadationManager/index.php