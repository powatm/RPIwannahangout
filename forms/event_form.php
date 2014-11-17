<?php
	require_once '../database_access.php';
	
	// a function to populate the form if it's been submitted already and is coming back
	// likely because of invalid data
	function getData($formName) {
		if(isset($_POST[$formName]))
			return $_POST[$formName];
		else
			return "";
	}
	// checks if a validation specific CSS class should be applied
	function getCSSClass($formName) {
		if(isset($failures) && in_array($failures, $formName))
			return ".has-error";
	}

	// set up vars to hold form info
	$title = '';
	$start_time = '';
	$end_time = '';
	$location = '';
	$description = '';
	$needcar = '';

	if(isset($_POST["submit"]) && $_POST["submit"] == "submit") {
		// populate vars
		if(isset($_POST["title"])) {
			$title = htmlspecialchars($_POST["title"]);
		}
		if(isset($_POST["start_time"])) {
			$start_time = htmlspecialchars($_POST["start_time"]);
		}
		if(isset($_POST["end_time"])) {
			$end_time = htmlspecialchars($_POST["end_time"]);
		}
		if(isset($_POST["location"])) {
			$location = htmlspecialchars($_POST["location"]);
		}
		if(isset($_POST["description"])) {
			$description = htmlspecialchars($_POST["description"]);
		}
		if(isset($_POST["need_car"])) {
			$needcar = htmlspecialchars($_POST["need_car"]);
		} else {
			$needcar = 0;
		}
		// set up event object
		$event = new Event();
		$event->setTitle("this is a test");
		$event->setStartTime($start_time);
		$event->setEndTime($end_time);
		$event->setLocation($location);
		$event->setDescription($description);
		$event->setNeedCar($needcar);
		// this is just a cludge for now until sessions and users are set up
		$user_query = new UserQuery();
		$user = $user_query->findPk(1); //this is the test user
		$event->setCreator($user);

		if (!$event->validate()) {
			$failures = array();
		    foreach ($event->getValidationFailures() as $failure) {
		        echo '<p class="error_message">Property '.$failure->getPropertyPath().": ".$failure->getMessage()."</p>";
		        array_push($failures, $failure->getPropertyPath());
		    }
		}
		else {
			$event->save();
		    // echo "Everything's all right!";
		    $event_id = $event->getEventId();
		    header("Location:../event_details.php?event_id=$event_id&new=1");
		    // http_redirect("../event_details.php?event_id=$event_id");
		}
	} // end if for was submitted
?>
<!DOCTYPE html>
<html>
<head>
	<?php include_once '../basic_includes/head_includes.php' ?>
	<link rel="stylesheet" href="../assets/css/style.css">
    <title>RPI Wanna Hangout</title>
</head>
<body>
	<?php include '../basic_includes/navbar.php' ?>

	<div class="panel panel-default">
		<div class="panel-heading">Event Creation Form</div>
		<div class="panel-body">
			<form role="form" id="event_creation_form" action="event_form.php" method="post">
				<div class="form-group <?php getCSSClass('title') ?>">
					<label for="title">Title</label>
					<input class="form-control" type="text" id="title" name="title" value="<?php getData('title') ?>" placeholder="Title">
				</div>
				<div class="form-group <?php getCSSClass('start_time') ?>">
					<label for="start_time">Start Time</label>
					<input class="form-control" type="datetime-local" id="start_time" name="start_time" value="<?php getData('start_time') ?>">
				</div>
				<div class="form-group <?php getCSSClass('end_time') ?>">
					<label for="end_time">End Time</label>
					<input class="form-control" type="datetime-local" id="end_time" name="end_time" value="<?php getData('end_time') ?>">
				</div>
				<div class="form-group <?php getCSSClass('location') ?>">
					<label for="location">Location</label>
					<input class="form-control" type="text" id="location" name="location" placeholder="Location" value="<?php getData('location') ?>">
				</div>
				<div class="checkbox <?php getCSSClass('need_car') ?>">
					<label>
						<input type="checkbox" name="need_car" value="1" <?php if(getData('need_car')) echo "checked"; ?> >Requires car
					</label>
					
				</div>
				<div class="form-group <?php getCSSClass('description') ?>">
					<label for="description">Description</label>
					<textarea class="form-control" rows="3" name="description" placeholder="Description" form="event_creation_form"></textarea>
				</div>
				<button type="submit" name="submit" value="submit" class="btn btn-default">Submit</button>
			</form>
		</div>
	</div>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<!-- <script src="assets/bootstrap/js/bootstrap.min.js"></script> -->
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>
</html>