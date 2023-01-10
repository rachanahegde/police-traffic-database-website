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
        <title> Submit Person Record </title>
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
            <h2> Submit a record for a new person </h2>
            <?php
                // To report all errors
                error_reporting(E_ALL);
                ini_set('display_errors',1);

                require("./config/database-details.php");  

                // Open the database connection
                $conn = mysqli_connect($servername, $username, $password, $dbname);

                if(!$conn) {
                    die ("Connection failed");
                }
            
                $status = "";
           
                if (isset($_POST['name'], $_POST['dob'], $_POST['address'], $_POST['driver_licence'])) {
                    $name = $_POST['name'];
                    $dob = $_POST['dob'];
                    $address = $_POST['address'];
                    $driver_licence = $_POST['driver_licence'];

                    $person_sql = "INSERT INTO People (People_name, DOB, People_address, People_licence) 
                    VALUES ('$name', '$dob', '$address', '$driver_licence')";

                    if ($result = mysqli_query($conn, $person_sql)) {
                        $status = 'Record for a new person submitted successfully.';

                        // Audit
                        $user_id = $_SESSION['user_id'];
                        $date=getdate(date("U"));
                        $date_time = "$date[year]-$date[mon]-$date[mday] $date[hours]:$date[minutes]:$date[seconds]";

                        $audit_sql = "INSERT INTO Audit (Table_ID, Action, Row_ID, User_ID, Timestamp) VALUES ((SELECT Table_ID FROM Tables WHERE Table_Name = 'People'), 'INSERT', (SELECT People_ID FROM People WHERE People_licence = '$driver_licence'), '$user_id', '$date_time')";
                        $audit_result = mysqli_query($conn, $audit_sql);
                    } else {
                        $status = 'There was an error. Please try again.';
                    }
                    echo '<p>'.$status.'</p>';
            } else { ?>
            <form method="POST">
                Full Name: <input type="text" name="name" required>
                Date of Birth: <input type="date" name="dob" class='long_field' required>
                Address: <input type="text" name="address" required> 
                Driver's Licence: <input type="text" name="driver_licence" pattern='[a-zA-Z0-9]+' minlength='16' maxlength='16' title='16 letters and numbers only' required>
                <input type="submit" value="Submit"> 
            </form>
            <?php } 
                mysqli_close($conn);
            ?>
        </main>
    </body>
</html>
