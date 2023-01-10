<!-- PHP code for checking user has logged in and logging the user is out is taken from: https://www.geeksforgeeks.org/how-to-display-logged-in-user-information-in-php/ -->

<?php
 // Start the session
 session_start();
   
 // If the session variable is empty, user has not logged in and will be sent to 'login.php' page
 if (!isset($_SESSION['user_id'])) {
     header('location: login.php');
 }

//  Set the timezone for auditing
date_default_timezone_set('Europe/London');
   
 // Logout button will end  session, and unset session variables
 // User will directed to 'login.php' after logging out
 if (isset($_GET['logout'])) {
     session_destroy();
     unset($_SESSION['user_id']);
     unset($_SESSION['username']);
     unset($_SESSION['success']);
     header("location: login.php");
 }
 ?>