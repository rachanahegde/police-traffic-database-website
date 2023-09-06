<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> Login </title>
        <link rel="stylesheet" href="./resources/style.css">
        <script src="script.js"></script>
    </head>
    <body>
        <main> 
            <h1> Police Traffic Database </h1>

            <form method="POST">
                Username: <input type="text" name="username" required><br/>
                Password: <input type="text" name="password" required><br/>
                <input type="submit" value="Log in">
            </form>

            <?php
            
            error_reporting(E_ALL);
            ini_set('display_errors',1);

            require("./config/database-details.php");  

            //Start the session
            session_start();
            $_SESSION['success'] = "";

            // Open the database connection
            $conn = mysqli_connect($servername, $username, $password, $dbname);

            if(!$conn) {
                die ("Connection failed");
            }

            $redirect_url = 'index.php';

            // Check that user entered correct log in details 
            if (isset($_POST['username'], $_POST['password'])) {
                $username = $_POST['username'];
                $password = $_POST['password'];
                $sql = "SELECT * FROM Users WHERE Username = '$username' AND Password = '$password'" ;
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) == 1) {
                    // PHP code for logging user in is adapted from: https://www.geeksforgeeks.org/how-to-display-logged-in-user-information-in-php/ -
                    
                    while($row = mysqli_fetch_assoc($result)) {
                        // Set session variables
                        $_SESSION['user_id'] = $row["User_ID"];
                        $_SESSION['username'] = $row["Username"];
                        $_SESSION['success'] = "You have logged in!";
                    }

                    // Log user in 
                    header('location: '.$redirect_url);
                } else {
                    echo "Your username or password is incorrect. Please re-enter your login details.";
                } 
            }
                    
            mysqli_close($conn);
            ?> 
        </main>
    </body>
</html>