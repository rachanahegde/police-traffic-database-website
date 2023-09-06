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
        <title> Search Reports </title>
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
            <h2> Search for incident reports </h2>
            <form method="POST">
                Report ID: <input type='number' name="report_id" min='0'>
                Vehicle Plate Number: <input type="text" name="plate_no" pattern='[A-Z0-9]+' title='Numbers and letters only' maxlength='7'>
                Offender: <input type="text" name="full_name" pattern='[a-zA-Z]+' title='Letters only'>
                Driver's Licence: <input type="text" name="driver_licence" pattern='[a-zA-Z0-9]+' maxlength='16' title='16 letters and numbers only'>
                Date: <input type="date" name="date">
                Time: <input type="time" name="time">
                Description: <input type="text" name="description">
                Offence: <input type="text" name="offence">
                Officer ID: <input type='number' name="officer_id" min='0'>
                <input type="submit" value = "Search" class='btn'> 
                <input type="submit" name="view_reports" value="View All Reports" class='btn'>
            </form>
            <br>

            <?php
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
                
                // Audit 
                $user_id = $_SESSION['user_id'];

                // Get all reports from database and display

                if (isset($_POST['view_reports'])) {
                    $sql_search_all = "SELECT Incident.Incident_ID, Vehicle.Vehicle_licence, People.People_name, People.People_licence, Incident.Incident_Date, Incident.Incident_Time, Incident.Incident_Report, Incident.Officer_ID, Offence.Offence_description FROM Incident, Vehicle, People, Offence WHERE Incident.Vehicle_ID = Vehicle.Vehicle_ID AND Incident.People_ID = People.People_ID AND Incident.Offence_ID = Offence.Offence_ID";

                    $result_all = mysqli_query($conn, $sql_search_all);

                    if (mysqli_num_rows($result_all) > 0) {
                        echo '<table>';  // Add class="big_table" if number of rows increases
                        echo "<tr>
                            <th>Report ID</th>
                            <th>Plate No.</th>
                            <th>Offender</th>
                            <th> Driver's Licence</th>
                            <th> Date </th>
                            <th> Time </th>
                            <th> Description </th>
                            <th> Offence </th>
                            <th> Officer ID </th>
                        </tr>";
                        while($row = mysqli_fetch_assoc($result_all)) {
                            echo "<tr>
                                <td>". $row["Incident_ID"]."</td>
                                <td>". $row["Vehicle_licence"]."</td>
                                <td>". $row["People_name"]."</td>
                                <td>". $row["People_licence"]."</td>
                                <td>". $row["Incident_Date"]."</td>
                                <td>". $row["Incident_Time"]."</td>
                                <td>". $row["Incident_Report"]."</td>
                                <td>". $row["Offence_description"]."</td>
                                <td>". $row["Officer_ID"]."</td>
                                <td> <a href=edit-report.php?report_id=".$row["Incident_ID"].">Edit</a></td>
                            </tr>";
                        } 
                        echo "</table>";

                        // Audit 
                        $current_date=getdate(date("U"));
                        $date_time = "$current_date[year]-$current_date[mon]-$current_date[mday] $current_date[hours]:$current_date[minutes]:$current_date[seconds]";

                        $audit_sql = "INSERT INTO Audit (Table_ID, Action, User_ID, Timestamp) VALUES ((SELECT Table_ID FROM Tables WHERE Table_Name = 'Incident'), 'READ ALL', '$user_id', '$date_time')";
                        $audit_sql_result = mysqli_query($conn, $audit_sql);
                    }
                }

                if (isset($_POST['report_id'], $_POST['plate_no'], $_POST['full_name'], $_POST['date'], $_POST['time'], $_POST['description'], $_POST['offence'], $_POST['officer_id'], $_POST['driver_licence']) AND
                    ($_POST['report_id'] != '' OR $_POST['plate_no'] != '' OR $_POST['full_name'] != '' 
                    OR $_POST['date'] != '' OR $_POST['time'] != '' OR $_POST['description'] != '' OR $_POST['offence'] != '' OR $_POST['officer_id'] != '' OR $_POST['driver_licence'] != '')) {
                    $report_id = $_POST['report_id'];
                    $plate_no = $_POST['plate_no'];
                    $full_name = $_POST['full_name'];
                    $driver_licence = $_POST['driver_licence'];
                    $date = $_POST['date'];
                    $time = $_POST['time'];
                    $description = $_POST['description'];
                    $offence = $_POST['offence'];
                    $officer_id = $_POST['officer_id'];

                    $sql_search = "SELECT Incident.Incident_ID, Vehicle.Vehicle_licence, People.People_name, People.People_licence, Incident.Incident_Date, Incident.Incident_Report, Incident.Incident_Time, Incident.Officer_ID, Offence.Offence_description FROM Incident, Vehicle, People, Offence WHERE Incident.Vehicle_ID = Vehicle.Vehicle_ID AND Incident.People_ID = People.People_ID AND Incident.Offence_ID = Offence.Offence_ID";

                    // Append each piece of data submitted through the form to the original query with separate if statements

                    if ($report_id != '') {
                        $sql_search = $sql_search." AND Incident.Incident_ID = '$report_id'";
                    }
                                        
                    if ($plate_no != '') {
                        $sql_search = $sql_search." AND Vehicle.Vehicle_licence LIKE '%$plate_no%'";
                    }
                    
                    if ($full_name != '') {
                        $sql_search = $sql_search." AND People.People_name LIKE '%$full_name%'";
                    }

                    if ($driver_licence != '') {
                        $sql_search = $sql_search." AND People.People_licence LIKE '%$driver_licence%'";
                    }
                    
                    if ($date != '') {
                        $sql_search = $sql_search." AND Incident.Incident_Date LIKE '%$date%'";
                    }

                    if ($time != '') {
                        $sql_search = $sql_search." AND Incident.Incident_Time LIKE '%$time%'";
                    }

                    if ($description != '') {
                        $sql_search = $sql_search." AND Incident.Incident_Report LIKE '%$description%'";
                    } 
                    
                    if ($offence != '') {
                        $sql_search = $sql_search." AND Offence.Offence_description LIKE '%$offence%'";
                    } 

                    if ($officer_id != '') {
                        $sql_search = $sql_search." AND Incident.Officer_ID = '$officer_id'";
                    }
                    
                    $result = mysqli_query($conn, $sql_search);

                    if (mysqli_num_rows($result) > 0) {
                        echo "<table>";
                        echo "<tr>
                            <th>Report ID</th>
                            <th>Plate No.</th>
                            <th>Offender</th>
                            <th> Driver's Licence</th>
                            <th> Date </th>
                            <th> Time </th>
                            <th> Description </th>
                            <th> Offence </th>
                            <th> Officer ID </th>
                        </tr>";
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>
                                <td>". $row["Incident_ID"]."</td>
                                <td>". $row["Vehicle_licence"]."</td>
                                <td>". $row["People_name"]."</td>
                                <td>". $row["People_licence"]."</td>
                                <td>". $row["Incident_Date"]."</td>
                                <td>". $row["Incident_Time"]."</td>
                                <td>". $row["Incident_Report"]."</td>
                                <td>". $row["Offence_description"]."</td>
                                <td>". $row["Officer_ID"]."</td>
                                <td> <a href=edit-report.php?report_id=".$report_id.">Edit</a></td>
                            </tr>";

                            // Audit 
                            $current_date_1=getdate(date("U"));
                            $date_time_1 = "$current_date_1[year]-$current_date_1[mon]-$current_date_1[mday] $current_date_1[hours]:$current_date_1[minutes]:$current_date_1[seconds]";

                            $Incident_ID = $row["Incident_ID"];

                            $audit_sql_1 = "INSERT INTO Audit (Table_ID, Action, Row_ID, User_ID, Timestamp) VALUES ((SELECT Table_ID FROM Tables WHERE Table_Name = 'Incident'), 'READ', '$Incident_ID', '$user_id', '$date_time_1')";
                            $audit_sql_result_1 = mysqli_query($conn, $audit_sql_1);
                        } 
                        echo "</table>";
                    } else {
                        echo "No results found. This incident report is not in the database.";
                    }
                }
            mysqli_close($conn);
            ?>
        </main>
    </body>
</html>

