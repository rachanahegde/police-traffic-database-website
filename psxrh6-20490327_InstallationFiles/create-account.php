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
        <title> Create New Account</title>

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
            <h2> Create New Officer Account </h2>
            <form method="POST">
                Full Name: <input type="text" name="name" required><br/>
                Username: <input type="text" name="username" required><br/>
                Password: <input type="text" name="password" required><br/>
                Confirm Password: <input type="text" name="confirm_password" required>
                <input type="submit" value="Create Account">
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


            if (isset($_POST['name'], $_POST['username'], $_POST['password'], $_POST['confirm_password'])) {
                $name = $_POST['name'];
                $user = $_POST['username'];
                $password = $_POST['password'];
                $confirm_password = $_POST['confirm_password'];
                
                $users_sql = "SELECT * FROM Users WHERE Username='$user'";
                $users_results = mysqli_query($conn, $users_sql);

                if (mysqli_num_rows($users_results) > 0) {
                    echo 'This username already exists. Please enter a different username.';
                } else {
                    if ($password == $confirm_password) {
                        // Insert new user record into Users table
                        $new_user_query = "INSERT INTO Users (Username, Password) VALUES ('$user', '$password')";
                        $query_results = mysqli_query($conn, $new_user_query);

                        // Get User ID 
                        $get_id_query = "SELECT User_ID FROM Users WHERE Username = '$user'";
                        $get_id_result = mysqli_query($conn, $get_id_query);
                        while($row = mysqli_fetch_assoc($get_id_result)) {
                            $officer_id = $row['User_ID'];
                        }
                        // Insert record into Officer table 
                        $new_officer_query = "INSERT INTO Officer (Officer_ID, Officer_name) VALUES ('$officer_id', '$name')";
                        $new_officer_result = mysqli_query($conn, $new_officer_query);

                        if ($query_results AND $new_officer_result) {
                            echo 'New account created successfully.';

                            // Audit
                            $user_id = $_SESSION['user_id'];

                            $date=getdate(date("U"));
                            $date_time = "$date[year]-$date[mon]-$date[mday] $date[hours]:$date[minutes]:$date[seconds]";

                            $audit_sql_1 = "INSERT INTO Audit (Table_ID, Action, Row_ID, User_ID, Timestamp) VALUES ((SELECT Table_ID FROM Tables WHERE Table_Name = 'Users'), 'INSERT', '$officer_id', '$user_id', '$date_time')";
                            $audit_result_1 = mysqli_query($conn, $audit_sql_1);

                            $audit_sql_2 = "INSERT INTO Audit (Table_ID, Action, Row_ID, User_ID, Timestamp) VALUES ((SELECT Table_ID FROM Tables WHERE Table_Name = 'Officer'), 'INSERT', '$officer_id', '$user_id', '$date_time')";
                            $audit_result_2 = mysqli_query($conn, $audit_sql_2);

                        } else {
                            echo 'There was an error, please try again.';
                        }
                    } else {
                        echo 'Passwords do not match, please enter your password correctly.';
                    }
                }
            }

            mysqli_close($conn);
            ?>
        </main>
    </body>
</html>

