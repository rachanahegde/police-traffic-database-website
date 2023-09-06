<?php
    // Track user login and logout 
    require('./config/user-session.php');
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> Home </title>
        <link rel="stylesheet" href="./resources/style.css">
        <script src="script.js"></script>
    </head>
    <?php
        // Add navigation bar 
        require('nav-bar.php');
    ?>
    <body>
        <main>
            <h1> Police Traffic Database </h1>
            <h2> A system for police officers to record and retrieve information about vehicles, people and traffic incidents.</h2>
            <hr> </hr>
            <div class='section'> 
                <h3> Search Database </h3>
                <p> Officers can search the database in order to view and update records on people, vehicles, and traffic incident reports. </p>
            </div>
            <hr> </hr>
            <div class='section'> 
                <h3> Submit Records </h3>
                <p> Officers can submit new records for vehicles and people. They may also assign vehicles to owners and file incident reports. </p>
            </div>
        </main>
    </body>
</html>