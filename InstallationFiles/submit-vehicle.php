<?php
    // Track user login and logout 
    require('./config/user-session.php');

    // To report all errors except notices
    // error_reporting(E_ALL & ~E_NOTICE);

    // To report all errors
    error_reporting(E_ALL);
    ini_set('display_errors',1);

    require("./config/database-details.php");  

    // Open the database connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if(!$conn) {
        die ("Connection failed");
    }
            
    // Get the list of people names and driver licences for dropdown 
    $name_licence_sql = "SELECT People_name, People_licence FROM People";
    $name_licence_result = mysqli_query($conn, $name_licence_sql);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> Submit New Vehicle </title>

        <!-- JQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />

        <script src="script.js"></script>
        <link rel="stylesheet" href="./resources/style.css">
    </head>
    <?php
        // Add navigation bar 
        require('nav-bar.php');
    ?>
    <body>
        <main>
            <h1> Police Traffic Database </h1>
                <p> Can't find the vehicle owner you're searching for? Add them to the database: 
                <p> <a href="submit-person.php"> Submit record for a new person</a></p>
                <h2> Submit vehicle record </h2>
                <?php
                    $status = "";
                    if (isset($_POST['vehicle_type'], $_POST['vehicle_colour'], $_POST['plate_no'], $_POST['name_and_licence'])) {
                        $vehicle_type = $_POST['vehicle_type'];
                        $vehicle_colour = $_POST['vehicle_colour'];
                        $plate_no = $_POST['plate_no'];
                        $name_and_licence = $_POST['name_and_licence'];
                        $driver_name = explode(" ",$name_and_licence)[0].' '.explode(" ",$name_and_licence)[1];
                        $driver_licence = explode(" ",$name_and_licence)[2];

                        $submit_vehicle_sql = "INSERT INTO Vehicle (Vehicle_type, Vehicle_colour, Vehicle_licence) VALUES ('$vehicle_type', '$vehicle_colour', '$plate_no')";
                        
                        $submit_owner_sql = "INSERT INTO Ownership (People_ID, Vehicle_ID) VALUES 
                        ((SELECT People_ID FROM People WHERE People_name = '$driver_name' AND People_licence = '$driver_licence'), (SELECT Vehicle_ID FROM Vehicle WHERE Vehicle_licence = '$plate_no'))";

                        if ($vehicle_sql_result = mysqli_query($conn, $submit_vehicle_sql) AND $owner_sql_result = mysqli_query($conn, $submit_owner_sql)) {
                            $status = 'Vehicle record submitted successfully.';

                            // Audit
                            $user_id = $_SESSION['user_id'];
                            $date=getdate(date("U"));
                            $date_time = "$date[year]-$date[mon]-$date[mday] $date[hours]:$date[minutes]:$date[seconds]";

                            $vehicle_id_sql = "SELECT Vehicle_ID FROM Vehicle WHERE Vehicle_licence = '$plate_no'";
                            $vehicle_id_result = mysqli_query($conn, $vehicle_id_sql);
                            while($vehicle_id_row = mysqli_fetch_assoc($vehicle_id_result)) {
                                $Vehicle_ID = $vehicle_id_row['Vehicle_ID'];
                            }

                            $audit_sql = "INSERT INTO Audit (Table_ID, Action, Row_ID, User_ID, Timestamp) VALUES ((SELECT Table_ID FROM Tables WHERE Table_Name = 'Vehicle'), 'INSERT', (SELECT Vehicle_ID FROM Vehicle WHERE Vehicle_licence = '$plate_no'), '$user_id', '$date_time')";
                            $audit_result = mysqli_query($conn, $audit_sql);

                            $audit_sql_1 = "INSERT INTO Audit (Table_ID, Action, Row_ID, User_ID, Timestamp) VALUES ((SELECT Table_ID FROM Tables WHERE Table_Name = 'Ownership'), 'INSERT', (SELECT Ownership_ID FROM Ownership WHERE Vehicle_ID = '$Vehicle_ID'), '$user_id', '$date_time')";
                            $audit_result_1 = mysqli_query($conn, $audit_sql_1);
                        } else {
                            $status = 'There was an error. Please try again.';
                        }
                        echo '<p>'.$status.'</p>';
                    } else { ?>
                    <form method="POST">
                        Vehicle Type: <input type="text" name="vehicle_type" required>
                        Vehicle Colour:  <input type="text" name="vehicle_colour" required>
                        Plate Number: <input type="text" name="plate_no" pattern='[A-Z0-9]+' title='Numbers and letters only' minlength='7' maxlength='7' required>
                        Vehicle Owner:
                        <div class="select_box">
                            <?php
                            // Vehicle owner names and drivers licence dropdown list
                            echo '<select name="name_and_licence" required>';
                                echo'<option value=""> Choose a name and licence </option>';
                                while($name_licence_row = mysqli_fetch_assoc($name_licence_result)) {
                                    echo'<option>'.$name_licence_row["People_name"].' '.$name_licence_row["People_licence"].'</option>';
                                }
                            echo '</select>';
                            ?>
                        </div>
                        <br>
                        <input type="submit" value="Submit Vehicle"> 
                    </form>
                    <?php }
                mysqli_close($conn);
            ?>
        </main>
    </body>
</html>