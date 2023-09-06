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
        <title> Add Fine </title>

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
                <h2> Add Fine </h2>
                <form method="POST">
                    Report ID: <input type="number" min="0" name="incident_id" required>
                    Penalty Points: <input type="number" name="fine_points" min="0" max='12' class='long_field' required>
                    Fine Amount: <input type="text" name="fine_amount" pattern='[0-9]+' title='Only numbers' required>
                    <input type="submit" value="Submit">
                </form>
                <?php
                    error_reporting(E_ALL);
                    ini_set('display_errors',1);

                    require("./config/database-details.php"); 

                    // Open the database connection
                    $conn = mysqli_connect($servername, $username, $password, $dbname);

                    if(!$conn) {
                        die ("Connection failed");
                    }

                    if (isset($_POST['incident_id'], $_POST['fine_points'], $_POST['fine_amount'])) {
                        $incident_id = $_POST['incident_id'];
                        $fine_points = $_POST['fine_points'];
                        $fine_amount = $_POST['fine_amount'];
                        $fine_query = "INSERT INTO Fines (Incident_ID, Fine_Points, Fine_Amount) VALUES ('$incident_id', '$fine_points', '$fine_amount')";
                        if (mysqli_query($conn, $fine_query)) {
                            echo '<p> Fine added successfully. </p>';

                            // Audit
                            $user_id = $_SESSION['user_id'];

                            $date=getdate(date("U"));
                            $date_time = "$date[year]-$date[mon]-$date[mday] $date[hours]:$date[minutes]:$date[seconds]";

                            $audit_sql = "INSERT INTO Audit (Table_ID, Action, Row_ID, User_ID, Timestamp) VALUES ((SELECT Table_ID FROM Tables WHERE Table_Name = 'Fines'), 'INSERT', (SELECT Fine_ID FROM Fines WHERE Incident_ID = '$incident_id' AND Fine_Points = '$fine_points' AND Fine_Amount = '$fine_amount'), '$user_id', '$date_time')";
                            $audit_result = mysqli_query($conn, $audit_sql);

                        } else {
                            echo '<p> There was a error, please enter the fine again. </p>';
                        }
                    }
                    mysqli_close($conn);
                ?>
        </main>
    </body>
</html>