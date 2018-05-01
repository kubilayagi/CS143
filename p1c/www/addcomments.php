<!DOCTYPE html>
<html>
<head>
	<title>Add Comments</title>
	<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
	<ul>
		<li><a href="index.php">Home Page</a></li>
		<li><a href="search.php">Search Page</a></li>
		<li><a href="actorinfo.php">Actor Info</a></li>
		<li><a href="movieinfo.php">Movie Info</a></li>
		<li><a href="addpersoninfo.php">Add Actor or Director</a></li>
		<li><a href="addmovieinfo.php">Add Movie Info</a></li>
		<li><a href="add_actor_to_movie.php">Add Actor to Movie Relation</a></li>
		<li><a href="add_director_to_movie.php">Add Director to Movie Relation</a></li>
	</ul>
	<div class="container">
	<h1>What's on Your Mind?</h1>
	<?php
        $mid;
		$db = new mysqli('localhost', 'cs143', '', 'CS143');

		if($db->connect_errno > 0){
		    die('Unable to connect to database [' . $db->connect_error . ']');
		}

		if(isset($_GET['movie_id'])) {
			$mid=$_GET["movie_id"];
			//get title from the movie => put it in the select part of the form
			$titlequery = "SELECT title FROM Movie WHERE id=$mid";
			$movietitle = mysqli_query($db, $titlequery);
			$titlerow = $movietitle->fetch_assoc();
			$actualmovietitle=(string) $titlerow["title"];
		} else if(isset($_POST['movie_id'])) {
			$mid=$_POST["movie_id"];
			//get title from the movie => put it in the select part of the form
			$titlequery = "SELECT title FROM Movie WHERE id=$mid";
			$movietitle = mysqli_query($db, $titlequery);
			$titlerow = $movietitle->fetch_assoc();
			$actualmovietitle=(string) $titlerow["title"];
		}

	?>

	<form action="#" method="POST">
		<h3>Movie Title:</h3>
		<select name="movie_id">
			<option value="<?= $mid ?>"><?= $actualmovietitle ?></option>
		</select>
		<h4>Your Name:</h4>
		<input type="text" name="name">
		<h4>Rating:</h4>
		<select name="rating">
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
		</select>
		<br>
		<h4>Your Thoughts:</h4>
		<textarea placeholder="no more than 500 characters" name="body" rows="10" cols="50"></textarea>
		<br>
		<input type="submit" name="submit">
	</form>

	<?php
		if(isset($_POST['submit'])) {
			//get title from the movie => put it in the select part of the form

			if(isset($_POST['body'])) {
				$body = mysqli_real_escape_string($db, $_POST['body']);
			}
			if(isset($_POST['name'])) {
				$name = mysqli_real_escape_string($db, $_POST['name']);
			}
			if(isset($_POST['movie_id'])) {
				$mid = mysqli_real_escape_string($db, $_POST['movie_id']);
			}
			if(isset($_POST['rating'])) {
				$rating = mysqli_real_escape_string($db, $_POST['rating']);
			}

			if ($mid == "" || $rating == "") {
				echo "You need to specify a rating and movie id!";
			}
			else {
				$query = "INSERT INTO Review (name, time, mid, rating, comment) VALUES (";
				if ($name == "") {
					$query .= "NULL, ";
				}
				else {
					$query .= "'$name', ";
				}

				$query .= "CURRENT_TIME(), ";

				$query .= "'$mid', '$rating', ";

				if ($body == "") {
					$query .= "NULL";
				}
				else {
					$query .= "'$body'";
				}

				$query .= ")";
				if(mysqli_query($db, $query)){
				    echo "Successful add.";
				    //echo $query;
				} else{
				    echo "Could not execute query";
				}
			}

			echo "<a href=\"movieinfo.php?identifier=$mid\">Check out your Review on the Movie Info Page</a>";

		}

		$db->close();
	?>

</div>

</body>
</html>