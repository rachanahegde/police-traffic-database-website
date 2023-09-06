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
        <title> Search Vehicles </title>
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
            <h2> Search for vehicles </h2>
            <form method="POST">
                Plate Number: <input type="text" name="plate_no" pattern='[A-Z0-9]+' title='Numbers and letters only' maxlength='7' required>
                <input type="submit" value = "Search">
            </form>
            <br>
            <?php
                // To report all errors except notices
                // error_reporting(E_ALL & ~E_NOTICE);

                error_reporting(E_ALL);
                ini_set('display_errors',1);

                require("./config/database-details.php");  

                // Open the database connection
                $conn = mysqli_connect($servername, $username, $password, $dbname);

                if(!$conn) {
                    die ("Connection failed");
                }

                if (isset($_POST['plate_no'])) {
                    $plate_no = $_POST['plate_no'];
                    $sql = "SELECT Vehicle.Vehicle_ID, Vehicle.Vehicle_type, Vehicle.Vehicle_colour, Vehicle.Vehicle_licence, People.People_name, People.People_licence, People.People_ID, Ownership.Ownership_ID FROM Vehicle LEFT JOIN Ownership ON Vehicle.Vehicle_ID = Ownership.Vehicle_ID LEFT JOIN People ON Ownership.People_ID = People.People_ID WHERE Vehicle.Vehicle_licence LIKE '%$plate_no%'";
                    $result = mysqli_query($conn, $sql);
                    if (mysqli_num_rows($result) > 0) {
                        echo "<table>";
                        echo "<tr>
                            <th>Vehicle Type</th>
                            <th>Vehicle Color</th>
                            <th>Plate Number</th>
                            <th> Owner</th>
                            <th> Driver's License </th>
                        </tr>";
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>
                                <td>".$row["Vehicle_type"]."</td>
                                <td>".$row["Vehicle_colour"]."</td>
                                <td>". $row["Vehicle_licence"]."</td>
                                <td>". $row["People_name"]."</td>
                                <td>". $row["People_licence"]."</td>
                            </tr>";

                             // Audit 
                            $Vehicle_ID = $row['Vehicle_ID']; 
                            $Ownership_ID = $row['Ownership_ID']; 
                            $user_id = $_SESSION['user_id'];
                            $current_date=getdate(date("U"));
                            $date_time = "$current_date[year]-$current_date[mon]-$current_date[mday] $current_date[hours]:$current_date[minutes]:$current_date[seconds]";

                            $audit_sql = "INSERT INTO Audit (Table_ID, Action, Row_ID, User_ID, Timestamp) VALUES ((SELECT Table_ID FROM Tables WHERE Table_Name = 'Vehicle'), 'READ',  '$Vehicle_ID', '$user_id', '$date_time')";
                            $audit_sql_result = mysqli_query($conn, $audit_sql);

                            if ($row['People_name'] != NULL) {
                                $audit_sql_1 = "INSERT INTO Audit (Table_ID, Action, Row_ID, User_ID, Timestamp) VALUES ((SELECT Table_ID FROM Tables WHERE Table_Name = 'Ownership'), 'READ',  '$Ownership_ID', '$user_id', '$date_time')";
                                $audit_sql_result_1 = mysqli_query($conn, $audit_sql_1);
                            }
                        } 
                        echo "</table>";
                    } else {
                        echo "No results found. This vehicle is not in the database.";
                    }
                }
                mysqli_close($conn);
                ?>
        </main>
    </body>
</html>