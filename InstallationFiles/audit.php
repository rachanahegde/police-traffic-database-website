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
        <title> Audit Trail </title>
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
            <h2> Audit Trail </h2>
            <form method="POST">
                Username: <input type='text' name="user_name">
                Action: <input type='text' name="action" pattern='[a-zA-Z]+' title='Letters only'>
                Table: <input type='text' name="table_name" pattern='[a-zA-Z]+' title='Letters only'>
                <input type="submit" value = "Search" class='btn'> 
                <input type="submit" name="view_all" value="View All" class='btn'>
            </form>
            <br>
            <?php

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

                // Get all records from database and display
                if (isset($_POST['view_all'])) {
                    $sql_search_all = "SELECT Audit.Audit_ID, Audit.Action, Audit.Table_Column, Audit.Row_ID, Audit.Old_Value, Audit.New_Value, Audit.Timestamp, Tables.Table_Name, Users.Username FROM Audit LEFT JOIN Tables ON Audit.Table_ID = Tables.Table_ID LEFT JOIN Users ON Audit.User_ID = Users.User_ID";
                    $result_all = mysqli_query($conn, $sql_search_all);

                    if (mysqli_num_rows($result_all) > 0) {
                        echo '<table class="big_table">'; 
                        echo "<tr>
                            <th>Audit ID</th>
                            <th>Table</th>
                            <th>Action</th>
                            <th>Table Column</th>
                            <th> Row ID </th>
                            <th> Old Value </th>
                            <th> New Value </th>
                            <th> User </th>
                            <th> Timestamp </th>
                        </tr>";
                        while($row = mysqli_fetch_assoc($result_all)) {
                            echo "<tr>
                                <td>". $row["Audit_ID"]."</td>
                                <td>". $row["Table_Name"]."</td>
                                <td>". $row["Action"]."</td>
                                <td>". $row["Table_Column"]."</td>
                                <td>". $row["Row_ID"]."</td>
                                <td>". $row["Old_Value"]."</td>
                                <td>". $row["New_Value"]."</td>
                                <td>". $row["Username"]."</td>
                                <td>". $row["Timestamp"]."</td>
                            </tr>";
                        } 
                        echo "</table>";

                        // Audit 
                        $current_date=getdate(date("U"));
                        $date_time = "$current_date[year]-$current_date[mon]-$current_date[mday] $current_date[hours]:$current_date[minutes]:$current_date[seconds]";

                        $audit_sql = "INSERT INTO Audit (Table_ID, Action, User_ID, Timestamp) VALUES ((SELECT Table_ID FROM Tables WHERE Table_Name = 'Audit'), 'READ ALL', '$user_id', '$date_time')";
                        $audit_sql_result = mysqli_query($conn, $audit_sql);
                    }
                }

                if (isset($_POST['user_name'], $_POST['action'], $_POST['table_name']) AND ($_POST['user_name'] != '' OR $_POST['action'] != '' OR $_POST['table_name'] != '')) {
                    $username = $_POST['user_name'];
                    $action =  $_POST['action'];
                    $table = $_POST['table_name'];

                    $search_audit_sql = "SELECT Audit.Audit_ID, Audit.Action, Audit.Table_Column, Audit.Row_ID, Audit.Old_Value, Audit.New_Value, Audit.Timestamp, Tables.Table_Name, Users.Username FROM Audit, Tables, Users WHERE Audit.Table_ID = Tables.Table_ID AND Audit.User_ID = Users.User_ID";

                    if ($username != '') {
                        $search_audit_sql = $search_audit_sql." AND Users.Username = '$username'"; 
                    }
                    
                    if ($action != '') {
                        $search_audit_sql = $search_audit_sql." AND Audit.Action = '$action'"; 
                    }
                    
                    if ($table != '') {
                        $search_audit_sql = $search_audit_sql." AND Tables.Table_Name = '$table'"; 
                    }

                    $search_result = mysqli_query($conn, $search_audit_sql);

                    if (mysqli_num_rows($search_result) > 0) {
                        echo '<table>'; 
                        echo "<tr>
                            <th>Audit ID</th>
                            <th>Table</th>
                            <th>Action</th>
                            <th>Table Column</th>
                            <th> Row ID </th>
                            <th> Old Value </th>
                            <th> New Value </th>
                            <th> User </th>
                            <th> Timestamp </th>
                        </tr>";
                        while($search_row = mysqli_fetch_assoc($search_result)) {
                            echo "<tr>
                                <td>". $search_row["Audit_ID"]."</td>
                                <td>". $search_row["Table_Name"]."</td>
                                <td>". $search_row["Action"]."</td>
                                <td>". $search_row["Table_Column"]."</td>
                                <td>". $search_row["Row_ID"]."</td>
                                <td>". $search_row["Old_Value"]."</td>
                                <td>". $search_row["New_Value"]."</td>
                                <td>". $search_row["Username"]."</td>
                                <td>". $search_row["Timestamp"]."</td>
                            </tr>";

                            // Audit each record that the admin views
                            $date=getdate(date("U"));
                            $date_time = "$date[year]-$date[mon]-$date[mday] $date[hours]:$date[minutes]:$date[seconds]";
                            $Audit_ID = $search_row["Audit_ID"];

                            $audit_sql_1 = "INSERT INTO Audit (Table_ID, Action, Row_ID, User_ID, Timestamp) VALUES ((SELECT Table_ID FROM Tables WHERE Table_Name = 'Audit'), 'READ', '$Audit_ID', '$user_id', '$date_time')";
                            $audit_sql_result_1 = mysqli_query($conn, $audit_sql_1);
                        } 
                        echo "</table>";                    
                    } else {
                        echo "No results found. Please modify your search query and try again.";
                    }
                }
            
                mysqli_close($conn);
            ?>
        </main>
    </body>
</html>




<!-- Admin can view all records in audit table -->
<!-- Admin can search for a username and view records associated with that user (stuff they did on this website) -->
<!-- Other auditing views...per record?..or make it possible to filter out different results (i.e. by action, table, or timestamp)  -->

<!-- Values: SEARCH/VIEW/SELECT, INSERT, UPDATE, DELETE -->