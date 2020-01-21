<!-- Page shows account details and allow user to change password and view information -->
<!DOCTYPE html>
<html lang="en">

<head>

	<!-- Database connection and title -->
	<?php include "includes/connect.php" ?>
  <title>Account - SPAS</title>

</head>

<body>

	<!-- Includes navbar -->
	<?php include "includes/nav.php" ?>

  	<!-- Page Content -->
  	<div class="container">

  	  	<!-- Page Header -->
  	    <br>
  	    <h2>My Account</h2>
  	    <br>

  	    <!-- Main section - account page -->
  	    <div class="row">

  	    	<form name="changePassword" action="php/changePassword.php" method="post" enctype="multipart/form-data">
  	    	    <div class="control-group form-group">
              		<div class="controls">
                	    <label>Current Password</label>
                			<input type="password" class="form-control" name="currentPassword" required data-validation-required-message="">
              		</div>
            		</div>

            		<div class="control-group form-group">
              		<div class="controls">
                			<label>New Password</label>
                			<input type="password" class="form-control" name="newPassword" required data-validation-required-message="">
              		</div>
            		</div>

            		<div class="control-group form-group">
              		<div class="controls">
                			<label>Confirm New Password</label>
                			<input type="password" class="form-control" name="confirmNewPassword" required data-validation-required-message="">
              		</div>
            		</div>

            		<button type="submit" class="btn btn-primary" id="changePasswordButton">Change Password</button>

          </form>

  	    </div>

	  </div>

	<br><br>
  
  	<!-- Includes footer -->
	<?php include "includes/footer.php" ?>

</body>

</html>

