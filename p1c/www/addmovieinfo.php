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

	<h1>Add new Movie</h1>

	<form action="addpersoninfo.php" method="POST">
		<h4>Movie Title</h4>
		<input type="text" placeholder="Text input" name="title">
		<h4>Company</h4>
		<input type="text" placeholder="Text input" name="company">
		<h4>Year</h4>
		<input type="text" placeholder="Text input" name="year">
		<p>ie: 1997</p>
		<h4>MPAA Rating</h4>
		<select name="rating"> <!--Supplement an id here instead of using 'text'-->
			<option value="G" selected>G</option> 
			<option value="PG">PG</option>
			<option value="PG-13">PG-13</option>
			<option value="R">R</option>
			<option value="NC-17">NC-17</option>
			<option value="NR">NR</option>
			<option value="UR">UR</option>
		</select>
		<br>
		Action<input type="checkbox" name="genre[]" id="genre" value="Action">
    	Adult<input type="checkbox" name="genre[]" id="genre" value="Adult">
    	Adventure<input type="checkbox" name="genre[]" id="genre" value="Adventure">
    	Animation<input type="checkbox" name="genre[]" id="genre" value="Animation">
    	Comedy<input type="checkbox" name="genre[]" id="genre" value="Comedy">
    	Crime<input type="checkbox" name="genre[]" id="genre" value="Crime">
    	<br>
    	Documentary<input type="checkbox" name="genre[]" id="genre" value="Documentaryk">
    	Drama<input type="checkbox" name="genre[]" id="genre" value="Dramak">
    	Family<input type="checkbox" name="genre[]" id="genre" value="Family">
    	Fantasy<input type="checkbox" name="genre[]" id="genre" value="Fantasy">
    	Horror<input type="checkbox" name="genre[]" id="genre" value="Horror">
    	Musical<input type="checkbox" name="genre[]" id="genre" value="Musical">
    	<br>
    	Mystery<input type="checkbox" name="genre[]" id="genre" value="Mystery">
    	Romance<input type="checkbox" name="genre[]" id="genre" value="Romance">
    	Sci-Fi<input type="checkbox" name="genre[]" id="genre" value="Sci-Fi">
    	Short<input type="checkbox" name="genre[]" id="genre" value="Short">
    	Thriller<input type="checkbox" name="genre[]" id="genre" value="Thriller">
    	War<input type="checkbox" name="genre[]" id="genre" value="War">
    	Western<input type="checkbox" name="genre[]" id="genre" value="Western">
    	<br>
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
		
		if ($position = "Director") { 
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