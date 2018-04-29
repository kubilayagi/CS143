<!DOCTYPE html>
<html>
<head>
	<title>Add Director-Movie Relation</title>
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

	<?php
		$db = new mysqli('localhost', 'cs143', '', 'CS143');

		if($db->connect_errno > 0){
		    die('Unable to connect to database [' . $db->connect_error . ']');
		}

		$titles="SELECT id, title, year FROM Movie";
		$directors="SELECT id, CONCAT(first, \" \", last) as name, dob FROM Director";
		$titleinfo = $db->query($titles);
		$directorinfo = $db->query($directors);

	?>
	<form action="add_director_to_movie.php" method="POST">
		<h4>Movie Title:</h4>
		<select name="movietitle">
			<option value="NULL"></option>
			<?php
				while($row = $titleinfo->fetch_assoc()) {
					$title=$row["title"];
					$year=$row["year"];				
					$mid=$row["id"];
					echo "<option value=\"$mid\">" . $title . " (" . $year . ")</option>";
				}
			?>
		</select>

		<h4>Director:</h4>
		<select name="directorname">
			<option value="NULL"></option>
			<?php
				while($row = $directorinfo->fetch_assoc()) {
					$name=$row["name"];
					$dob=$row["dob"];
					$did=$row["id"];
					echo "<option value=\"$did\">" . $name . " (" . $dob . ")</option>";
				}
			?>
		</select>

		<input type="submit" name="submit">
	</form>

	<?php 
		$did_select;
		$mid_select;
		if(isset($_POST['submit'])) {
			$mid_select = $_POST['movietitle'];
			$did_select = $_POST['directorname'];
		}

		echo $mid_select;
		echo $did_select;
		echo $role;

		if(isset($_POST['submit']) && isset($_POST['movietitle']) && isset($_POST['directorname'])) {
			$insert = "INSERT INTO MovieDirector (mid, did) VALUES ('$mid_select', '$did_select')";
			if(mysqli_query($db, $insert)){
			    echo "Successful add ";
			    echo $insert;
			} else{
			    echo "Could not execute $query" . mysqli_error($link);
			}
        }


		
	?>

</body>
</html>