<!DOCTYPE html>
<html>
<head>
	<title>Add Actor-Movie Relation</title>
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
		$actors="SELECT id, CONCAT(first, \" \", last) as name, dob FROM Actor";
		$titleinfo = $db->query($titles);
		$actorinfo = $db->query($actors);

	?>
	<form action="add_actor_to_movie.php" method="POST">
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

		<h4>Actor:</h4>
		<select name="actorname">
			<option value="NULL"></option>
			<?php
				while($row = $actorinfo->fetch_assoc()) {
					$name=$row["name"];
					$dob=$row["dob"];
					$aid=$row["id"];
					echo "<option value=\"$aid\">" . $name . " (" . $dob . ")</option>";
				}
			?>
		</select>

		<h4>Role:</h4>
		<input type="text" name="role">
		<input type="submit" name="submit">
	</form>

	<?php 
		$aid_select;
		$mid_select;
		if(isset($_POST['submit'])) {
			$mid_select = $_POST['movietitle'];
			$aid_select = $_POST['actorname'];
			$role = $_POST['role'];
			//TODO: sanitize this
		}

		echo $mid_select;
		echo $aid_select;
		echo $role;

		if(isset($_POST['submit']) && isset($_POST['movietitle']) && isset($_POST['actorname'])) {
            $insert = "INSERT INTO MovieActor (mid, aid, role) VALUES ('$mid_select', '$aid_select', '$role')";
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