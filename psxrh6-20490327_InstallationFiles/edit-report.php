<!-- This article was referenced to help me structure the PHP code for autofilling each field of the form and updating the form data: https://www.allphptricks.com/insert-view-edit-and-delete-record-from-database-using-php-and-mysqli/  -->


<?php
    // Track user login and logout 
    require('./config/user-session.php');

    // To report all errors
    error_reporting(E_ALL);
    ini_set('display_errors',1);

    require("./config/database-details.php");  

    // Open the database connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if(!$conn) {
        die ("Connection failed");
    }

    $report_id = $_GET['report_id'];
    
    // Get the data for the report from the database to display in the form below
    $sql_search = "SELECT Incident.Incident_ID, Vehicle.Vehicle_licence, Vehicle.Vehicle_ID, People.People_ID, People.People_name, People.People_licence, Incident.Incident_Date, Incident.Incident_Time, Incident.Incident_Report, Offence.Offence_description, Offence.Offence_ID FROM Incident, Vehicle, People, Offence WHERE Incident.Vehicle_ID = Vehicle.Vehicle_ID AND Incident.People_ID = People.People_ID AND Incident.Offence_ID = Offence.Offence_ID AND Incident.Incident_ID = '$report_id'";

    $result = mysqli_query($conn, $sql_search);
    $row = mysqli_fetch_assoc($result);

    // For auditing
    $original_people_ID = $row['People_ID'];
    $original_vehicle_ID = $row['Vehicle_ID'];
    $original_offence_ID = $row['Offence_ID'];

    // Get offences from the database for dropdown
    $offences_sql_search = "SELECT Offence_description FROM Offence";
    $offences_result = mysqli_query($conn, $offences_sql_search);

    // Get the list of people names and driver licences for dropdown 
    $name_licence_sql = "SELECT People_name, People_licence FROM People";
    $name_licence_result = mysqli_query($conn, $name_licence_sql);

    // Get the list of vehicle licence plates for dropdown
    $vehicles_sql = "SELECT Vehicle_licence FROM Vehicle";
    $vehicles_result = mysqli_query($conn, $vehicles_sql);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> Edit Report </title>

        <!-- JQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />

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
            <?php 
                echo '<h2> Edit Incident Report #'.$report_id.'</h2>'; 
                // Check if the form data was updated
                $status = "";
                if(isset($_POST['update']) AND $_POST['update']==1) {
                    $date = $_POST['date'];
                    $time = $_POST['time'];
                    $description = $_POST['description'];
                    $name_and_licence = $_POST['name_and_licence'];
                    $driver_name = explode(" ",$name_and_licence)[0].' '.explode(" ",$name_and_licence)[1];
                    $driver_licence = explode(" ",$name_and_licence)[2];
                    $plate_no = $_POST['plate_no'];
                    $offence = $_POST['offence'];

                    $sql_update = "UPDATE Incident 
                    SET Vehicle_ID = (SELECT Vehicle_ID FROM Vehicle WHERE Vehicle_licence = '$plate_no'),
                    People_ID = (SELECT People_ID From People WHERE People_name = '$driver_name' AND People_licence = '$driver_licence'),
                    Incident_Date = '$date',
                    Incident_Time = '$time',
                    Incident_Report = '$description',
                    Offence_ID = (SELECT Offence_ID FROM Offence WHERE Offence_description = '$offence')
                    WHERE Incident_ID = '$report_id'";
                                 
                    if ($update_result = mysqli_query($conn, $sql_update)) {
                        $status = "Report updated successfully.";

                        // Audit 
                        $user_id = $_SESSION['user_id'];
                        $current_date=getdate(date("U"));
                        $date_time = "$current_date[year]-$current_date[mon]-$current_date[mday] $current_date[hours]:$current_date[minutes]:$current_date[seconds]";

                        $original_date = $row['Incident_Date'];
                        $original_time = $row['Incident_Time'];
                        $original_description = $row['Incident_Report'];
                        $original_driver_name = $row['People_name'];
                        $original_driver_licence = $row['People_licence'];
                        $original_plate_no = $row["Vehicle_licence"];
                        $original_offence = $row["Offence_description"];
    
                        // Check if value is changed and then create a new record in the audit table for each changed value

                        if ($original_date != $date) {
                            $audit_sql_1 = "INSERT INTO Audit (Table_ID, Action, Table_Column, Row_ID, Old_Value, New_Value, User_ID, Timestamp) VALUES ((SELECT Table_ID FROM Tables WHERE Table_Name = 'Incident'), 'UPDATE', 'Incident_Date', '$report_id', '$original_date',  '$date', '$user_id', '$date_time')";
                            $audit_result_1 = mysqli_query($conn, $audit_sql_1);
                        } 
                        
                        $time = $time.':00';
                        if ($original_time != $time) {
                            $audit_sql_2 = "INSERT INTO Audit (Table_ID, Action, Table_Column, Row_ID, Old_Value, New_Value, User_ID, Timestamp) VALUES ((SELECT Table_ID FROM Tables WHERE Table_Name = 'Incident'), 'UPDATE', 'Incident_Time', '$report_id', '$original_time',  '$time', '$user_id', '$date_time')";
                            $audit_result_2 = mysqli_query($conn, $audit_sql_2);
                        } 
                        
                        if ($original_description != $description) {
                            $audit_sql_3 = "INSERT INTO Audit (Table_ID, Action, Table_Column, Row_ID, Old_Value, New_Value, User_ID, Timestamp) VALUES ((SELECT Table_ID FROM Tables WHERE Table_Name = 'Incident'), 'UPDATE', 'Incident_Report', '$report_id', '$original_description',  '$description', '$user_id', '$date_time')";
                            $audit_result_3 = mysqli_query($conn, $audit_sql_3);
                        } 
                        
                        if ($original_driver_name != $driver_name AND $original_driver_licence != $driver_licence) {
                            $audit_sql_4 = "INSERT INTO Audit (Table_ID, Action, Table_Column, Row_ID, Old_Value, New_Value, User_ID, Timestamp) VALUES ((SELECT Table_ID FROM Tables WHERE Table_Name = 'Incident'), 'UPDATE', 'People_ID', '$report_id', '$original_people_ID',  (SELECT People_ID From People WHERE People_name = '$driver_name' AND People_licence = '$driver_licence'), '$user_id', '$date_time')";
                            $audit_result_4 = mysqli_query($conn, $audit_sql_4);
                        } 
                        
                        if ($original_plate_no != $plate_no) {
                            $audit_sql_5 = "INSERT INTO Audit (Table_ID, Action, Table_Column, Row_ID, Old_Value, New_Value, User_ID, Timestamp) VALUES ((SELECT Table_ID FROM Tables WHERE Table_Name = 'Incident'), 'UPDATE', 'Vehicle_ID', '$report_id', '$original_vehicle_ID', (SELECT Vehicle_ID FROM Vehicle WHERE Vehicle_licence = '$plate_no'), '$user_id', '$date_time')";
                            $audit_result_5 = mysqli_query($conn, $audit_sql_5);
                        } 
                        
                        if ($original_offence != $offence) {
                            $audit_sql_6 = "INSERT INTO Audit (Table_ID, Action, Table_Column, Row_ID, Old_Value, New_Value, User_ID, Timestamp) VALUES ((SELECT Table_ID FROM Tables WHERE Table_Name = 'Incident'), 'UPDATE', 'Offence_ID', '$report_id', '$original_offence_ID', (SELECT Offence_ID FROM Offence WHERE Offence_description = '$offence'), '$user_id', '$date_time')";
                            $audit_result_6 = mysqli_query($conn, $audit_sql_6);
                        }

                    } else {
                        $status = 'There was an error. Please try again';
                        $status = $status." and click <a href= 'edit-report.php?report_id=".$report_id."'>here</a> to edit the report and resubmit.";
                    }
                    echo '<p>'.$status.'</p>';
                } else {
                ?>
                <!-- Autofill each field of the form -->
                <p> Can't find the person or vehicle plate number you're searching for? Add them to the database: 
                <p> <a href="submit-person.php"> Submit record for a new person</a></p>
                <p> <a href="submit-vehicle.php"> Submit record for a new vehicle</a></p>
                <br>
                <form method="POST">
                    <input type="hidden" name="update" value="1" />
                    Date: <input type="date" name="date" value="<?php echo $row['Incident_Date']?>" required>
                    Time: <input type="time" name="time" value="<?php echo $row['Incident_Time']?>" required>
                    Description: <input type="text" name="description" value="<?php echo $row['Incident_Report']?>" required>

                    Offender:
                    <div class="select_box">
                        <?php
                        // Offender name and drivers licence dropdown list
                        echo '<select name="name_and_licence" required>';
                            echo'<option>'.$row['People_name'].' '.$row['People_licence'].'</option>';
                            while($name_licence_row = mysqli_fetch_assoc($name_licence_result)) {
                                if ($name_licence_row["People_name"] != $row["People_name"] AND $name_licence_row["People_licence"] != $row["People_licence"]) {
                                    echo'<option>'.$name_licence_row["People_name"].' '.$name_licence_row["People_licence"].'</option>';
                                }
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
                            echo'<option>'.$row["Offence_description"].'</option>';
                            while($offence_row = mysqli_fetch_assoc($offences_result)) {
                                if ($offence_row["Offence_description"] != $row["Offence_description"]) {
                                    echo'<option>'.$offence_row["Offence_description"].'</option>';
                                }
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
                            echo'<option>'.$row["Vehicle_licence"].'</option>';
                            while($vehicle_row = mysqli_fetch_assoc($vehicles_result)) {
                                if ($vehicle_row["Vehicle_licence"] != $row["Vehicle_licence"]) {
                                    echo'<option>'.$vehicle_row["Vehicle_licence"].'</option>';
                                }
                            }
                        echo '</select>';
                        ?>
                    </div>
                    <br>

                    <input type="submit" value = "Update Report">
                </form>
            <?php } 
                mysqli_close($conn);
            ?>
        </main>
    </body>
</html>



