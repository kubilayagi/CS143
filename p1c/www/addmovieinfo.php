<!DOCTYPE html>
<html>
<head>
	<title>Add Actor and Director Information</title>
	<link rel="stylesheet" type="text/css" href="./styles.css">
</head>
<body>
	<ul>
		<li><a href="./index.php">Home Page</a></li>
		<li><a href="./search.php">Search Page</a></li>
		<li><a href="./actorinfo.php">Actor Info</a></li>
		<li><a href="./movieinfo.php">Movie Info</a></li>
		<li><a href="./addpersoninfo.php">Add Actor or Director</a></li>
		<li><a class="active" href="./addmovieinfo.php">Add Movie Info</a></li>
		<li><a href="./addcomments.php">Add Comments</a></li>
		<li><a href="./add_actor_to_movie.php">Add Actor to Movie Relation</a></li>
		<li><a href="./add_director_to_movie.php">Add Director to Movie Relation</a></li>
	</ul>

	<div class="container">
	<h1>Add new Movie</h1>

	<form action="addmovieinfo.php" method="POST">
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
		<h4>Genre</h4>
		Action<input type="checkbox" name="genre[]" id="genre" value="Action">
    	Adult<input type="checkbox" name="genre[]" id="genre" value="Adult">
    	Adventure<input type="checkbox" name="genre[]" id="genre" value="Adventure">
    	Animation<input type="checkbox" name="genre[]" id="genre" value="Animation">
    	Comedy<input type="checkbox" name="genre[]" id="genre" value="Comedy">
    	Crime<input type="checkbox" name="genre[]" id="genre" value="Crime">
    	Documentary<input type="checkbox" name="genre[]" id="genre" value="Documentary">
    	Drama<input type="checkbox" name="genre[]" id="genre" value="Drama">
    	Family<input type="checkbox" name="genre[]" id="genre" value="Family">
    	Fantasy<input type="checkbox" name="genre[]" id="genre" value="Fantasy">
    	Horror<input type="checkbox" name="genre[]" id="genre" value="Horror">
    	Musical<input type="checkbox" name="genre[]" id="genre" value="Musical">
    	Mystery<input type="checkbox" name="genre[]" id="genre" value="Mystery">
    	Romance<input type="checkbox" name="genre[]" id="genre" value="Romance">
    	Sci-Fi<input type="checkbox" name="genre[]" id="genre" value="Sci-Fi">
    	Short<input type="checkbox" name="genre[]" id="genre" value="Short">
    	Thriller<input type="checkbox" name="genre[]" id="genre" value="Thriller">
    	War<input type="checkbox" name="genre[]" id="genre" value="War">
    	Western<input type="checkbox" name="genre[]" id="genre" value="Western">
    	<br><br>
		<input type="submit" name="submit">
	</form>

	<?php
		$db = new mysqli('localhost', 'cs143', '', 'CS143');
		if($db->connect_errno > 0){
		    die('Unable to connect to database [' . $db->connect_error . ']');
		}

		if (isset($_POST["submit"])) {
			$title="";
			$company=""; //use the max person id table to get this value
			$year=0;
			$rating="";
			$genre="";
			$query="";

			$get_id="SELECT * FROM MaxMovieID";
			$result = mysqli_query($db, $get_id);
			$row = $result->fetch_assoc();
			$id= (string) $row['id'];
			$query = "UPDATE MaxMovieID SET id = id + 1;";
			mysqli_query($db, $query);

			if(isset($_POST['title']))
				$title = mysqli_real_escape_string($db, $_POST['title']);
			//if ($title == "") $title = "NULL";
			
			if(isset($_POST['company'])) 
				$company = mysqli_real_escape_string($db, $_POST['company']);
			//if ($company == "") $company = "NULL";
			
			if(isset($_POST['year'])) 
				$year = mysqli_real_escape_string($db, $_POST['year']);
			//if ($year == "") $year = "NULL";
			
			if(isset($_POST['rating'])) 
				$rating = mysqli_real_escape_string($db, $_POST['rating']);
			//if ($rating == "") $rating = "NULL";
		
			
			//$query = "INSERT INTO Movie (id, title, company, year, rating) VALUES ('$id', '$title', '$company', '$year', '$rating')";
			$query = "INSERT INTO Movie (id, title, company, year, rating) VALUES ('$id', ";
			if ($title == "") {
				$query .= "NULL, ";
			}
			else {
				$query .= "'$title', ";
			}
			if ($company == "") {
				$query .= "NULL, ";
			}
			else {
				$query .= "'$company', ";
			}
			if ($year == "") {
				$query .= "NULL, ";
			}
			else {
				$query .= "'$year', ";
			}
			if ($rating == "") {
				$query .= "NULL";
			}
			else {
				$query .= "'$rating'";
			}

			$query .= ")";


			if(mysqli_query($db, $query)){
			    echo "Successful add.";
			    //echo $query;
			} else{
			    echo "Could not execute query";
			}

			$name = "";
			if(isset($_POST['genre']))
				$name=($_POST['genre']);
			foreach ($name as $genre) {
				$genre = mysqli_real_escape_string($db, $genre);
				$query = "INSERT INTO MovieGenre (mid, genre) VALUES ('$id', '$genre')";
				if(mysqli_query($db, $query)){
				    //echo "Successful add.";
				    //echo $query;
				} else{
				    echo "Could not execute genre insertion";
				}
			}
		}
		$db->close();
	?>
</div>
</body>
</html>