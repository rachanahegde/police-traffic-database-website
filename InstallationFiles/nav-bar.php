<!-- Nav bar for all website pages -->

<!DOCTYPE html>
    <header>
        <nav>
            <ul>
                <li> <a href="index.php">Home</a> </li>
                    <li> <a href='#'> Search </a>  
                    <ul> 
                        <li> <a href="search-people.php">Search People</a>  </li>
                        <li> <a href="search-vehicles.php">Search Vehicles</a>  </li>
                        <li> <a href="search-reports.php">Search Reports</a>  </li>
                    </ul>
                </li>
                <li> <a href='#'> Submit </a>    
                    <ul> 
                        <li> <a href="submit-vehicle.php">Submit Vehicle Record </a> </li>
                        <li> <a href="submit-person.php">Submit Person Record </a> </li>
                        <li> <a href="submit-report.php"> Submit Incident Report </a> </li>
                    </ul>
                </li>

                <!-- Admin access only pages -->
                <?php 
                    if ($_SESSION['username'] == 'daniels') { 
                        echo '<li> <a href="#"> Admin </a>';
                            echo '<ul>';
                                    echo '<li> <a href="create-account.php"> Create Account </a> </li>';
                                    echo '<li> <a href="add-fine.php"> Add Fine </a> </li>';
                                    echo '<li> <a href="audit.php"> Audit Trail </a> </li>';
                            echo '</ul>';
                        echo '</li>';
                    }
                ?>

                <li> <a href="account.php"> Account </a>
                    <ul>
                        <li> <a href="index.php?logout='1'"> Log out </a> </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </header>
</html>


