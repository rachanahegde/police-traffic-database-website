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
        <title> Search People </title>
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
            <h2> Search for people </h2>
            <form method="POST">
                Name: <input type="text" name="name">
                License: <input type="text" name="licence" pattern='[a-zA-Z0-9]+' maxlength='16' title='16 letters and numbers only'>
                <input type="submit" value = "Search">
            </form>
            <br>
            <?php
                error_reporting(E_ALL);
                ini_set('display_errors',1);

                require("./config/database-details.php");  

                // Open the database connection
                $conn = mysqli_connect($servername, $username, $password, $dbname);

                if(!$conn) {
                    die ("Connection failed");
                }
                
                if (isset($_POST['name'], $_POST['licence']) && ($_POST['name'] != '' OR $_POST['licence'] !='')) {
                    $name = $_POST['name'];
                    $licence = $_POST['licence'];
                    $sql = "SELECT People.People_ID, People.People_name, People.DOB, People.People_address, People.People_licence, Fines.Fine_ID, SUM(Fines.Fine_points) AS 'Fine_Points' FROM People LEFT JOIN Incident ON Incident.People_ID = People.People_ID LEFT JOIN Fines ON Fines.Incident_ID = Incident.Incident_ID";

                    if ($name !='' AND $licence != '') {
                        $sql = $sql." WHERE People.People_name LIKE '%$name%' AND People.People_licence LIKE '%$licence%'";
                    } elseif ($name != '') {
                        $sql = $sql." WHERE People.People_name LIKE '%$name%'";
                    } elseif ($licence != '') {
                        $sql = $sql." WHERE People.People_licence LIKE '%$licence%'";
                    } 

                    $sql = $sql." GROUP BY People.People_ID";

                    $result = mysqli_query($conn, $sql);
                    if (mysqli_num_rows($result) > 0) {
                        echo "<table>";
                        echo "<tr><th>Name</th><th>Date of Birth</th><th>Address</th><th>Licence</th><th>Penalty Points</th></tr>";
                        while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <td>".$row["People_name"]."</td>
                            <td>".$row["DOB"]."</td>
                            <td>".$row["People_address"]."</td>
                            <td>". $row["People_licence"]."</td>
                            <td>". $row["Fine_Points"]."</td>
                            </tr>";

                            // Audit 
                            $People_ID = $row['People_ID']; 
                            $Fine_ID = $row['Fine_ID'];
                            $user_id = $_SESSION['user_id'];
                            $current_date=getdate(date("U"));
                            $date_time = "$current_date[year]-$current_date[mon]-$current_date[mday] $current_date[hours]:$current_date[minutes]:$current_date[seconds]";

                            $audit_sql = "INSERT INTO Audit (Table_ID, Action, Row_ID, User_ID, Timestamp) VALUES ((SELECT Table_ID FROM Tables WHERE Table_Name = 'People'), 'READ',  '$People_ID', '$user_id', '$date_time')";
                            $audit_sql_result = mysqli_query($conn, $audit_sql);
                            
                            // Check that there was a fine associated with this person and the Fines table was accessed to show it
                            if ($row["Fine_Points"] != NULL) {
                                $audit_sql_1 = "INSERT INTO Audit (Table_ID, Action, Row_ID, User_ID, Timestamp) VALUES ((SELECT Table_ID FROM Tables WHERE Table_Name = 'Fines'), 'READ',  '$Fine_ID', '$user_id', '$date_time')";
                                $audit_sql_result_1 = mysqli_query($conn, $audit_sql_1);
                                }
                            } 
                        echo "</table>";

                    } else {
                        echo "No results found. This person is not in the database.";
                    }
                } 
                mysqli_close($conn);
            ?>
        </main>
    </body>
</html>