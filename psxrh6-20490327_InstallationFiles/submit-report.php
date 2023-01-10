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

    // Get the list of vehicle licence plates for dropdown
    $vehicles_sql = "SELECT Vehicle_licence FROM Vehicle";
    $vehicles_result = mysqli_query($conn, $vehicles_sql);

    // Get the list of offences for dropdown
    $offences_sql_search = "SELECT Offence_description FROM Offence";
    $offences_result = mysqli_query($conn, $offences_sql_search);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> Submit Report </title>

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
            <p> Can't find the person or vehicle plate number you're searching for? Add them to the database: 
            <p> <a href="submit-person.php"> Submit record for a new person</a></p>
            <p> <a href="submit-vehicle.php"> Submit record for a new vehicle</a></p>
            <br>
            <h2> Submit incident report </h2>

            <?php
                $status = "";

                if (isset($_POST['date'], $_POST['description'], $_POST['name_and_licence'], $_POST['offence'], $_POST['plate_no'])) {
                    $date = $_POST['date'];
                    $time = $_POST['time'];
                    $description = $_POST['description'];
                    $name_and_licence = $_POST['name_and_licence'];
                    $driver_name = explode(" ",$name_and_licence)[0].' '.explode(" ",$name_and_licence)[1];
                    $driver_licence = explode(" ",$name_and_licence)[2];
                    $plate_no = $_POST['plate_no'];
                    $offence = $_POST['offence'];
 
                    $officer_id = $_SESSION["user_id"];
                    $submittedby = $_SESSION["user_id"];
                    
                    $report_sql = "INSERT INTO Incident (Vehicle_ID, People_ID, Incident_Date, Incident_Time, Incident_Report, Offence_ID, Officer_ID) VALUES ((SELECT Vehicle_ID FROM Vehicle WHERE Vehicle_licence = '$plate_no'), (SELECT People_ID FROM People WHERE People_name = '$driver_name' AND People_licence = '$driver_licence'), '$date', '$time', '$description', (SELECT Offence_ID FROM Offence WHERE Offence_description = '$offence'), $officer_id)";

                    if ($result = mysqli_query($conn, $report_sql)) {
                        $status = 'Report submitted successfully.';

                        // Audit
                        $user_id = $_SESSION['user_id'];
                        $current_date=getdate(date("U"));
                        $date_time = "$current_date[year]-$current_date[mon]-$current_date[mday] $current_date[hours]:$current_date[minutes]:$current_date[seconds]";
                        $vehicle_id_sql = "SELECT Vehicle_ID FROM Vehicle WHERE Vehicle_licence = '$plate_no'";
                        $people_id_sql = "SELECT People_ID FROM People WHERE People_name = '$driver_name' AND People_licence = '$driver_licence'";
                        $offence_id_sql = "SELECT Offence_ID FROM Offence WHERE Offence_description = '$offence'";

                        $vehicle_id_result = mysqli_query($conn, $vehicle_id_sql);
                        $people_id_result = mysqli_query($conn, $people_id_sql);
                        $offence_id_result = mysqli_query($conn, $offence_id_sql);

                        while($vehicle_id_row = mysqli_fetch_assoc($vehicle_id_result)) {
                            $Vehicle_ID = $vehicle_id_row['Vehicle_ID'];
                        }
            
                        while($people_id_row = mysqli_fetch_assoc($people_id_result)) {
                            $People_ID = $people_id_row['People_ID'];
                        }

                        while($offence_id_row = mysqli_fetch_assoc($offence_id_result)) {
                            $Offence_ID = $offence_id_row['Offence_ID'];
                        }
                        
                        $audit_sql = "INSERT INTO Audit (Table_ID, Action, Row_ID, User_ID, Timestamp) VALUES ((SELECT Table_ID FROM Tables WHERE Table_Name = 'Incident'), 'INSERT', (SELECT Incident_ID FROM Incident WHERE Vehicle_ID = '$Vehicle_ID' AND People_ID = '$People_ID' AND Incident_Date = '$date' AND Incident_Time = '$time' AND Incident_Report = '$description' AND Offence_ID = '$Offence_ID'), '$user_id', '$date_time')";
                        $audit_result = mysqli_query($conn, $audit_sql);
                    } else {
                        $status = 'There was an error. Please try again.';
                    }
                    echo '<p>'.$status.'</p>';
                } else { ?>
                <form method="POST">
                    Date: <input type="date" name="date" required>
                    Time: <input type="time" name="time" required>
                    Description: <input type="text" name="description" required>
                    Offender:
                    <div class="select_box">
                        <?php
                        // Offender name and drivers licence dropdown list
                        echo '<select name="name_and_licence" required>';
                            echo'<option value=""> Choose a name and licence </option>';
                            while($name_licence_row = mysqli_fetch_assoc($name_licence_result)) {
                                echo'<option>'.$name_licence_row["People_name"].' '.$name_licence_row["People_licence"].'</option>';
                            }
                        echo '</select>';
                        ?>
                    </div>
                    <br>

                    Offence:
                    <div class="select_box">
                        <?php
                        // Offences dropdown list
                        echo '<select name="offence" required>';
                            echo'<option value=""> Choose a offence </option>';
                            while($offence_row = mysqli_fetch_assoc($offences_result)) {
                                echo'<option>'.$offence_row["Offence_description"].'</option>';
                            }
                        echo '</select>';
                        ?>
                    </div>
                    <br>

                    Vehicle Plate No.:
                    <div class="select_box">
                        <?php
                        // Vehicle plates dropdown list
                        echo '<select name="plate_no" required>';
                            echo'<option value=""> Choose a licence plate </option>';
                            while($vehicle_row = mysqli_fetch_assoc($vehicles_result)) {
                                echo'<option>'.$vehicle_row["Vehicle_licence"].'</option>';
                            }
                        echo '</select>';
                        ?>
                    </div>
                    <br>
                    <input type="submit" value="Submit Report"> 
                </form> 
                <?php } 
                mysqli_close($conn);
            ?>
        </main>
    </body>
</html>





            