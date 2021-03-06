<!-- Page for editing supervisor details -->
<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Includes required scripts. -->
    <?php include "../../includes/header.php" ?>
    <?php include "../../includes/userscript.php" ?>
    <?php include "../../includes/connect.php" ?>

    <title>Editing Supervisor - SPAS</title>

    <?php

        $supervisorID = $_POST['supervisorID'];

        $query = "SELECT * FROM supervisor WHERE supervisorID = '$supervisorID'";
        $result = $connection->query($query);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $supervisorTitle = $row["supervisorTitle"];
                $firstName = $row["firstName"];
                $lastName = $row["lastName"];
                $activeSupervisor = $row["activeSupervisor"];
                $officeNumber = $row["officeNumber"];
                $emailAddress = $row["emailAddress"];
                $admin = $row["admin"];
            }
        }

    ?>

</head>

<body>

    <!-- Ensures admin is logged in -->
    <?php
        if ($loggedIn == true) {
            if ($userType == "admin") {
    ?>

    <!-- Includes navigation bar -->
    <?php include "../../includes/systemnav.php" ?>

    <!-- Script used to edit supervisors -->
    <?php

        $editedSupervisorTitle = $editedLastName = $editedLastName = $editedOfficeNumber = $editedEmailAddress = $editedActive = $editedAdmin = "";

        if (isset($_POST['submit'])) {
            $editedSupervisorTitle = $_POST['supervisorTitle'];
            $editedFirstName = $_POST['firstName'];
            $editedLastName = $_POST['lastName'];
            $editedActive = $_POST['activeSupervisor'];
            $editedOfficeNumber = $_POST['officeNumber'];
            $editedEmailAddress = $_POST['emailAddress'];
            $editedAdmin = $_POST['admin'];

            if($editedActive == "Yes") {
                $activeSupervisor = 1;
            } else if ($editedActive == "No") {
                $activeSupervisor = 0;
            } else {
                $activeSupervisor = 0;
            }

            if($editedAdmin == "Yes") {
                $adminRow = 1;
            } else if ($editedAdmin == "No") {
                $adminRow = 0;
            } else {
                $adminRow = 0;
            }

            // Form validation
            if (strlen($editedFirstName) > 250) {
                echo '<div class="alert alert-danger" role="alert">
                            Error: First name is too long!
                      </div>';

            } else if (strlen($editedLastName) > 50) {
                echo '<div class="alert alert-danger" role="alert">
                            Error: Last name is too long!
                      </div>';

            } else {

                $query = "UPDATE supervisor SET supervisorTitle = ?, firstName = ?, lastName = ?, activeSupervisor = ?, officeNumber = ?, emailAddress = ?, admin = ?, lastUpdated = now() WHERE supervisorID = ?";

                if($statement = mysqli_prepare($connection, $query)) {
                    mysqli_stmt_bind_param($statement, "sssissii", $editedSupervisorTitle, $editedFirstName, $editedLastName, $activeSupervisor, $editedOfficeNumber, $editedEmailAddress, $adminRow, $supervisorID);
                    mysqli_stmt_execute($statement);
                    header("Refresh:0.01; url=supervisorlist.php");
                } else {
                    echo '<div class="alert alert-danger" role="alert">
                                Error: Could not update supervisor! Please contact an administrator.
                          </div>'; 
                }

                mysqli_stmt_close($statement);

            }

        }

        $connection->close();

    ?>

    <!-- Page content includes add course form. -->
    <div class="container">

        <!-- Page Heading/Breadcrumbs -->
        <center>
            <h1 class="mt-4 mb-3">Editing Supervisor</h1>
        </center>

        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="../admin.php">Admin</a>
            </li>
            <li class="breadcrumb-item">
                <a href="../systemmanagement.php">System Management</a>
            </li>
            <li class="breadcrumb-item">
                <a href="supervisorlist.php">Supervisor List</a>
            </li>
            <li class="breadcrumb-item active">Editing Supervisor</li>
        </ol>

        <!-- Editing Supervisor Form -->
        <div class="row">

            <div class="col-lg-8 mb-4">

                <form name="supervisorForm" action="editingsupervisor.php" method="post" enctype="multipart/form-data">

                    <input type="hidden" name="supervisorID" value="<?php echo $supervisorID; ?>">

                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Supervisor Title</label>
                            <select name="supervisorTitle">
                                <option>Dr</option>
                                <option>Professor</option>
                                <option>Mr</option>
                                <option>Mrs</option>
                                <option>Miss</option>
                            </select>
                        </div>
                    </div>

                    <div class="control-group form-group">
                        <div class="controls">
                            <label>First Name</label>
                            <input type="text" class="form-control" name="firstName" value="<?php echo $firstName; ?>" required>
                        </div>
                    </div>

                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Last Name</label>
                            <input type="text" class="form-control" name="lastName" value="<?php echo $lastName; ?>" required>
                        </div>
                    </div>

                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Office Number</label>
                            <input type="text" class="form-control" name="officeNumber" value="<?php echo $officeNumber; ?>" required>
                        </div>
                    </div>

                    <div class="control-group form-group">
                        <div class="controls">
                            <label>Email Address</label>
                            <input type="text" class="form-control" name="emailAddress" value="<?php echo $emailAddress; ?>" required>
                        </div>
                    </div>

                    <div class="control-group form-group">
                        <div class="form-check">
                            <label>Active Supervisor?</label><br>
                            <label class="radio-inline"><input value="Yes" type="radio" name="activeSupervisor" checked>Yes</label>
                            <label class="radio-inline"><input value="No" type="radio" name="activeSupervisor">No</label>
                        </div>
                    </div>

                    <div class="control-group form-group">
                        <div class="form-check">
                            <label>System Administrator?</label><br>
                            <label class="radio-inline"><input value="Yes" type="radio" name="admin">Yes</label>
                            <label class="radio-inline"><input value="No" type="radio" name="admin" checked>No</label>
                        </div>
                    </div>

                    <button name="submit" type="submit" class="btn btn-primary" id="addButton">Edit</button>
          
                </form>

            </div>

        </div>

    </div>

    <?php include "../../includes/footer.php" ?>

    <?php
        
            } else if ($userType == "supervisor" or $userType == "student") {
                // Invalid Permissions
                header("Refresh:0.01; url=../../error/permissionerror.php");

            } else {
                // Invalid user type
                header("Refresh:0.01; url=../../error/usertypeerror.php");
            }
        } else {
            header("Refresh:0.01; url=../../login.php");
        }

    ?>

</body>

</html>