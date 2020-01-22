<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Database connection and title -->
    <?php include "includes/connect.php" ?>
    <title>Project List - SPAS</title>

</head>


<body>

    <!-- Includes navigation bar -->
    <?php include "includes/nav.php" ?>

    <!-- Header containing the title and subtitle of the page -->
    <header>

        <br>
        <center><h2>List of Projects</h2></center>
        <br>
        
    </header>

    <!-- Main Page Content -->
    <div class="container">

        <div class="row">

            <div class="dropdown">
                <!-- from https://getbootstrap.com/docs/4.0/components/dropdowns/ -->
              <button class="btn dropdown-toggle" type="button" id="courseSelector" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Select Course
              </button>
              <div class="dropdown-menu" aria-labelledby="courseSelector">
                 <?php

                    $query = "SELECT * FROM course";

                    if ($result = mysqli_query($connection, $query)) {
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_array($result)){
                                $courseName = $row['courseName'];
                                $courseID = $row['courseID'];

                                echo '<a class="dropdown-item" href="#">'.$courseName.'</a>';
                            }
                        }
                    } else {
                        echo "Error: " . $query . "<br>" . $connection->error;
                    }
                    ?>
              </div>
            </div>

            <br><br>

            <table class="table table-striped">
                <thread>
                    <tr>
                        <th scope="col">Project Title</th>
                        <th scope="col">Project Brief</th>
                        <th scope="col">Supervisor</th>
                    </tr>
                </thread>
                <tbody>

                <?php

                    $query = "SELECT * FROM project LIMIT 20";

                    if ($result = mysqli_query($connection, $query)) {
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_array($result)){
                                $projectTitle = $row['projectTitle'];
                                $projectBrief = $row['projectBrief'];
                                $projectID = $row['projectID'];
                                $projectSupervisor = getSupervisorName($connection, $row['supervisorID']);

                                echo '<tr>';
                                echo '<th scope="row"><a href="../projects/'.$projectID.'.php">' . $projectTitle . '</a></th>';
                                echo '<td>' . $projectBrief . '</td>';
                                echo '<td>' . $projectSupervisor . '</td>';
                                echo '</tr>';
                            }
                        }
                    } else {
                        echo "Error: " . $query . "<br>" . $connection->error;
                    }

                    // Function uses the ID to get the supervisor name from the supervisor table.
                    function getSupervisorName($connection, $supervisorID) {

                        $supervisorName = "";
                        $query = "SELECT * FROM supervisor WHERE supervisorID='" . $supervisorID . "'";

                        if ($result = mysqli_query($connection, $query)) {
                            if (mysqli_num_rows($result) > 0) {
                                while($row = mysqli_fetch_array($result)) {
                                    $supervisorTitle = $row['supervisorTitle'];
                                    $supervisorFirstName = $row['firstName'];
                                    $supervisorLastName = $row['lastName'];
                                    $supervisorName = $supervisorTitle . " " . $supervisorFirstName . " " . $supervisorLastName;
                                }
                            }
                        } else {
                            echo "Error: " . $query . "<br>" . $connection->error;
                        }

                        return $supervisorName;
                    }

                    // Closes connection
                    $connection->close();

                ?>

                </tbody>
            </table>
            <br><br><br>

    </div>
     
    <!-- Includes footer -->
    <?php include "includes/footer.php" ?>

</body>

</html>
