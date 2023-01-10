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
        <title> My Account</title>
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
            <h2> My Account </h2>
            <h3> Change Password </h3>
            <form method="POST">
                Current Password: <input type="text" name="current_password" required><br/>
                New Password: <input type="text" name="new_password" required><br/>
                Confirm New Password: <input type="text" name="confirm_password" required>
                <input type="submit" value = "Update Password">
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

            if (isset($_POST['current_password'], $_POST['new_password'], $_POST['confirm_password'])) {
                $user = $_SESSION['username'];
                $sql = "SELECT * FROM Users WHERE Username = '$user'";
                $result = mysqli_query($conn, $sql);

                while($row = mysqli_fetch_assoc($result)) {
                    if ($_POST['current_password'] == $row['Password']) {
                        if ($_POST['new_password'] == $_POST['confirm_password']) {
                            $new_password = $_POST['new_password'];
                            $update_sql = "UPDATE Users SET Password='$new_password' WHERE Username='$user'";
                            if (mysqli_query($conn, $update_sql)) {
                                echo 'Password successfully updated!';

                                // Audit
                                $user_id = $_SESSION['user_id'];
                                $old_value = $_POST['current_password'];
                                $new_value = $_POST['new_password'];
                    
                                $date=getdate(date("U"));
                                $date_time = "$date[year]-$date[mon]-$date[mday] $date[hours]:$date[minutes]:$date[seconds]";

                                $audit_sql = "INSERT INTO Audit (Table_ID, Action, Table_Column, Row_ID, Old_Value, New_Value, User_ID, Timestamp) VALUES ((SELECT Table_ID FROM Tables WHERE Table_Name = 'Users'), 'UPDATE', 'Password', '$user_id', '$old_value', '$new_value', '$user_id', '$date_time')";
                                $audit_result = mysqli_query($conn, $audit_sql);

                            } else {
                                echo "There was an error, please try again.";
                            }
                        } else {
                            echo 'Passwords do not match, please re-enter your new password.';
                        }
                    } else {
                        echo 'Your current password is incorrect, please re-enter it.';
                    }
                }
            }
            mysqli_close($conn);
            ?>
        </main>
    </body>
</html>