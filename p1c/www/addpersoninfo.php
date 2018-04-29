<!DOCTYPE html>
<html>
<head>
	<title>Add Actor and Director Information</title>
	<link rel="stylesheet" type="text/css" href="./styles.css">
</head>
<body>
	<a href="./index.php">Home Page</a>
	<a href="./search.php">Search Page</a>
	<a href="./actorinfo.php">Actor Info</a>
	<a href="./movieinfo.php">Movie Info</a>
	<a href="./addpersoninfo.php">Add Actor or Director</a>
	<a href="./addmovieinfo.php">Add Movie Info</a>
	<a href="./addcomments.php">Add Comments</a>
	<a href="./add_actor_to_movie.php">Add Actor to Movie Relation</a>
	<a href="./add_director_to_movie.php">Add Director to Movie Relation</a>

	<h1>Add new Actor/Director</h1>

	<form action="addpersoninfo.php" method="POST">
		<input type="radio" name="position" value="Actor"> Actor
		<input type="radio" name="position" value="Director"> Director
		<h4>First Name</h4>
		<input type="text" placeholder="Text input" name="first">
		<h4>Last Name</h4>
		<input type="text" placeholder="Text input" name="last">
		<br>
		<input type="radio" name="sex" value="Male">Male
		<input type="radio" name="sex" value="Female">Female
		<h4>Date of Birth</h4>
		<input type="text" placeholder="Text input" name="dob">
		<p>ie: 1997-05-05</p>
		<h4>Date of Die</h4>
		<input type="text" placeholder="Text input" name="dod">
		<p>(leave blank if still alive)</p>
		<input type="submit">
	</form>

	<?php
		$db = new mysqli('localhost', 'cs143', '', 'CS143');
		if($db->connect_errno > 0){
		    die('Unable to connect to database [' . $db->connect_error . ']');
		}

		$position="";
		$id=0; //use the max person id table to get this value
		$first="";
		$last="";
		$sex="";
		$dob="";
		$dod="";
		$query="";

		$get_id="SELECT * FROM MaxPersonID";
		$result = mysqli_query($db, $get_id);
		$row = $result->fetch_assoc();
		$id= (string) $row['id'];
		$query = "UPDATE MaxPersonID SET id = id + 1;";
		mysqli_query($db, $query);


		if(isset($_POST['position'])) {
			$position = mysqli_real_escape_string($db, $_POST['position']);
		}
		if(isset($_POST['first'])) {
			$first = mysqli_real_escape_string($db, $_POST['first']);
		}
		if(isset($_POST['last'])) {
			$last = mysqli_real_escape_string($db, $_POST['last']);
		}
		if(isset($_POST['sex'])) {
			$sex = mysqli_real_escape_string($db, $_POST['sex']);
		}
		if(isset($_POST['dob'])) {
			$dob = mysqli_real_escape_string($db,$_POST['dob']);
		}
		if(isset($_POST['dod'])) {
			$dod = mysqli_real_escape_string($db,$_POST['dod']);
		}
		if ($dod == "") $dod = "NULL";
		
		if ($position == "Director") { 
			$query = "INSERT INTO $position (id, last, first, dob, dod) VALUES ('$id', '$last', '$first', '$dob', $dod)";
		} else {
			$query = "INSERT INTO $position (id, last, first, sex, dob, dod) VALUES ('$id', '$last', '$first', '$sex', '$dob', $dod)";
		}
		if(mysqli_query($db, $query)){
		    echo "Successful add";
		    echo $query;
		} else{
		    echo "Could not execute $query" . mysqli_error($link);
		}
		


		$db->close();
	?>

</body>
</html>