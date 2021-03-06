<!-- Page is a template for all project pages, page gets copied on project creation -->
<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Includes required scripts. -->
    <?php include "../includes/connect.php" ?>
    <?php include "../includes/header.php" ?>
    <?php include "../includes/userscript.php" ?>

    <?php

        // Gets the project id from the current file name and assigns empty variables for use later.
        $projectID = basename(__FILE__, '.php');

        // Query to get the project data based on the ID.
        $query = "SELECT * FROM project where projectID='$projectID'";
        $result = $connection->query($query);

        // Gets all data and assigns to variables.
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $projectTitle = $row["projectTitle"];
                $projectBrief = $row["projectBrief"];
                $projectCode = $row["projectCode"];

                $supervisorRow = getSupervisorDetails($connection, $row["supervisorID"]);

                $supervisorOffice = $supervisorRow['officeNumber'];
                $supervisorEmail = $supervisorRow['emailAddress'];

                $supervisorTitle = $supervisorRow['supervisorTitle'];
                $supervisorFirstName = $supervisorRow['firstName'];
                $supervisorLastName = $supervisorRow['lastName'];
                $supervisorName = $supervisorTitle . " " . $supervisorFirstName . " " . $supervisorLastName;
            }
        } else {
            echo '<div class="alert alert-danger" role="alert">
                        Error: We could not retrieve the project data! Please contact an administrator. 
                  </div>';
        }

        $allocationQuery = "SELECT * FROM management";
        $allocationResult = $connection->query($allocationQuery);
        // Gets if the projects have been allocated and stores in a variable.
        if ($allocationResult->num_rows > 0) {
            while($allocationRow = $allocationResult->fetch_assoc()) {
                $projectsAllocated = $allocationRow["projectsAllocated"];
            }
        } else {
             echo '<div class="alert alert-danger" role="alert">
                        Error: We could not load the allocation data! Please contact an administrator. 
                   </div>';
        }


        // Function uses the ID to get the supervisor name from the supervisor table.
        function getSupervisorDetails($connection, $supervisorID) {

            $query = "SELECT * FROM supervisor WHERE supervisorID='$supervisorID'";
            $result = $connection->query($query);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    return $row;
                }
            } else {
                echo '<div class="alert alert-danger" role="alert">
                            Error: We could not retrieve the supervisor data! Please contact an administrator. 
                      </div>';
            }

            return $row;
        }

        // Function used to get courses which are relevant to the project.
        function getRelevantCourses($connection, $projectID) {

            $courses = array();

            // Query joins projectCourse and course tables to get course details specific to the current project.
            $query = "SELECT * FROM projectCourse INNER JOIN course ON projectCourse.courseID = course.courseID WHERE projectCourse.projectID = '$projectID'";
            $result = $connection->query($query);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $courseName = $row['courseName'];
                    array_push($courses,$courseName);
                }
            } else {
                echo '<div class="alert alert-danger" role="alert">
                            Error: We could not retrieve the course data! Please contact an administrator. 
                      </div>';
            }

            return $courses;
        }

        // Gets relevant courses specific to the project selected.
        $relevantCourses = getRelevantCourses($connection, $projectID);

    ?>

    <!-- Sets title to project title -->
    <title><?php echo $projectTitle; ?> - SPAS</title>

</head>

<body>

    <!-- Includes main navbar -->
    <?php include "../includes/mainnav.php" ?>

    <!-- Script adds a selected project to the database -->
    <?php

    if ($loggedIn == true AND $userType == "student") {
        $studentQuery = "SELECT * FROM student WHERE studentID = '$loggedInStudentID'";
        $studentResult = $connection->query($studentQuery);

        if ($studentResult->num_rows > 0) {
            while($studentRow = $studentResult->fetch_assoc()) {
                $firstChoice = $studentRow["projectFirstChoice"];
                $secondChoice = $studentRow["projectSecondChoice"];
                $thirdChoice = $studentRow["projectThirdChoice"];
            }
        } else {
            echo '<div class="alert alert-danger" role="alert">
                        Error: Problem getting student data, please contact an administrator!
                  </div>';
        }
    }

    if (isset($_POST['remove'])) {
        $studentID = $_POST['studentID'];
        $choiceNumber = $_POST['choiceNumber'];

        if ($choiceNumber == '1') {
            $selectionRow = "projectFirstChoice";
        } else if ($choiceNumber == '2') {
            $selectionRow = "projectSecondChoice";
        } else if ($choiceNumber == '3') {
            $selectionRow = "projectThirdChoice";
        } else {
            $selectionRow = "";
        }

        $query = "UPDATE student SET $selectionRow = NULL WHERE studentID = '$studentID'";

        if ($connection->query($query) === TRUE) {
            echo '<div class="alert alert-success" role="alert">
                        Successfully removed selection!
                  </div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">
                        Error: Could not remove selection!
                  </div>';
        }
    }

    
    if (isset($_POST['add'])) {

        $projectID = $_POST['projectID'];
        $studentID = $_POST['studentID'];
        $choiceNumber = $_POST['choiceNumber'];

        if ($choiceNumber == "1") {

            // Checks if the user has already selected the project.
            if ($firstChoice == $projectID or $secondChoice == $projectID or $thirdChoice == $projectID) {
                echo '<div class="alert alert-warning" role="alert">
                            You have already selected this project!
                      </div>';
            } else {

                $firstChoiceQuery = "UPDATE student SET projectFirstChoice = '$projectID' WHERE studentID = '$studentID'";

                if ($connection->query($firstChoiceQuery) === TRUE) {
                    echo '<div class="alert alert-success" role="alert">
                            Selected project as first choice!
                          </div>';     
                } else {
                    echo '<div class="alert alert-danger" role="alert">
                                Error: Problem getting student data, please contact an administrator!
                          </div>';
                }
            }

        } else if ($choiceNumber == "2") {
            
            // Checks if the user has already selected the project.
            if ($secondChoice == $projectID or $firstChoice == $projectID or $thirdChoice == $projectID) {
                echo '<div class="alert alert-warning" role="alert">
                            You have already selected this project!
                      </div>';
            } else {
                $secondChoiceQuery = "UPDATE student SET projectSecondChoice='$projectID' WHERE studentID='$studentID'";

                if ($connection->query($secondChoiceQuery) === TRUE) {
                    echo '<div class="alert alert-success" role="alert">
                                Selected project as second choice!
                          </div>';                         
                } else {
                    echo '<div class="alert alert-danger" role="alert">
                                Error: Problem getting student data, please contact an administrator!
                          </div>';                    
                }
            }

        } else if ($choiceNumber == "3") {

            // Checks if the user has already selected the project.
            if ($thirdChoice == $projectID or $secondChoice == $projectID or $firstChoice == $projectID) {
                echo '<div class="alert alert-warning" role="alert">
                            You have already selected this project!
                      </div>';
            } else {
                $thirdChoiceQuery = "UPDATE student SET projectThirdChoice='$projectID' WHERE studentID='$studentID'";

                if ($connection->query($thirdChoiceQuery) === TRUE) {
                    echo '<div class="alert alert-success" role="alert">
                            Selected project as third choice!
                          </div>';                    
                } else {
                    echo '<div class="alert alert-danger" role="alert">
                                Error: Problem getting student data, please contact an administrator!
                          </div>';                    
                }
            }

        } else {
            echo '<div class="alert alert-warning" role="alert">
                        You have selected the maximum amount of projects, please remove one before selecting more!
                  </div>';           
        }
    }

    ?>


    <!-- Main Page Content -->
    <div class="container">

        <!-- Page Heading and breadcrumbs -->
        <h1 class="mt-4 mb-3"><?php echo $projectTitle; ?></h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="../index.php">Project List</a>
            </li>
            <li class="breadcrumb-item active">
                <?php echo $projectTitle; ?>
            </li>
        </ol>

        <!-- First row contains two columns - project and supervisor info -->
        <div class="row">

            <!-- Left row of content - shows project info: title, supervisor, course, project code -->
            <div class="col-md-6 border">

                <br>

                <p><b>Title: </b><?php echo $projectTitle; ?></p><br>
                <p><b>Supervisor: </b><?php echo $supervisorName; ?></p><br>
                <p><b>Relevant Courses:</b>
                <?php
                    // Loops through each course in the loop and prints out the course.
                    $i = 0;
                    $len = count($relevantCourses);
                    foreach ($relevantCourses as $course) {
                        if ($i==$len-1) {
                            echo $course;
                        } else {
                            echo $course . ", "; 
                        }
                        $i++;
                    } 
                ?> 
                </p>

                <br>

                <p><b>Project Code: </b><?php echo $projectCode; ?></p>

                <div class="col-md-5">

	                <!-- Checks if user is logged in as a student, if so, display select project buttons -->
	    			<?php
	                    if ($loggedIn == true) {
	    	                if ($userType == "student") {
                                if ($projectsAllocated == 0) {
	    	        ?>

                	                <!-- Each button posts to the same php script and a hidden value posts the choice number -->
                	                <?php if ($projectID == $firstChoice) { ?>
                                        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST" role="form">
                                            <input type="hidden" name="studentID" value="<?php echo $loggedInStudentID; ?>">
                                            <input type="hidden" name="choiceNumber" value="1">
                	                        <center><button name="remove" type="submit" class="btn btn-danger btn-block">Remove First Choice</button></center>
                                        </form>
                	                <?php } else { ?>
                	                    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST" role="form">
                	                        <input type="hidden" name="projectID" value="<?php echo $projectID; ?>">
                	                        <input type="hidden" name="studentID" value="<?php echo $loggedInStudentID; ?>">
                	                        <input type="hidden" name="choiceNumber" value="1">
                	                        <center><button name="add" type="submit" class="btn btn-success btn-block">Select First Choice</button></center>
                	                    </form>
                	                <?php } ?>

                	                <br>

                	                <?php if ($projectID == $secondChoice) { ?>
                                        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST" role="form">
                                            <input type="hidden" name="studentID" value="<?php echo $loggedInStudentID; ?>">
                                            <input type="hidden" name="choiceNumber" value="1">
                	                        <center><button name="remove" type="submit" class="btn btn-danger btn-block">Remove Second Choice</button></center>
                                        </form>
                	                <?php } else { ?>
                	                    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST" role="form">
                	                        <input type="hidden" name="projectID" value="<?php echo $projectID; ?>">
                	                        <input type="hidden" name="studentID" value="<?php echo $loggedInStudentID; ?>">
                	                        <input type="hidden" name="choiceNumber" value="2">
                	                        <center><button name="add" type="submit" class="btn btn-success btn-block">Select Second Choice</button></center>
                	                    </form>
                	                <?php } ?>

                	                <br>
                	                
                	                <?php if ($projectID == $thirdChoice) { ?>
                                        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST" role="form">
                                            <input type="hidden" name="studentID" value="<?php echo $loggedInStudentID; ?>">
                                            <input type="hidden" name="choiceNumber" value="1">
                	                        <center><button name="remove" type="submit" class="btn btn-danger btn-block">Remove Third Choice</button></center>
                                        </form>
                	                <?php } else { ?>
                	                    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST" role="form">
                	                        <input type="hidden" name="projectID" value="<?php echo $projectID; ?>">
                	                        <input type="hidden" name="studentID" value="<?php echo $loggedInStudentID; ?>">
                	                        <input type="hidden" name="choiceNumber" value="3">
                	                        <center><button name="add" type="submit" class="btn btn-success btn-block">Select Third Choice</button></center>
                	                    </form>
                	                <?php } ?>

                	                <br>

	                <?php 
                                }
	    	            	}
	    	            }
	                ?>

	            </div>

            </div>

            <br><hr><br>

            <!-- Right row of content - shows supervisor details -->
            <div class="col-md-6 border">

                <br>

                <h4>Supervisor Information</h4>

                <br>

                <p><b>Name: </b><?php echo $supervisorName; ?></p><br>
                <p><b>Office: </b><?php echo $supervisorOffice; ?></p><br>
                <p><b>Email Address: </b><?php echo $supervisorEmail; ?></p>

            </div>

        </div>

        <!-- Creates a line below the two sections -->
        <br>
        <hr>
        <br>

        <!-- Bottom row of content - shows project brief -->
        <div class="row">

            <div class="col-md-12 border">

                <br>

                <center>
                    <h4>Project Brief</h4>
                </center>

                <br>

                <!-- Project brief uses wordwrap function in order to keep the text on the screen -->
                <p><?php echo wordwrap($projectBrief, 200, "<br>", true); ?>

                <br>

            </div>

        </div>

        <br>
        <br>

    </div>

    <!-- Closes connection -->
    <?php $connection->close(); ?>

    <!-- Includes footer -->
    <?php include "../includes/footer.php" ?>

</body>

</html>