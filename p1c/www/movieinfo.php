<!DOCTYPE html>
<html>
<head>
	<title>Movie</title>
	<link rel="stylesheet" type="text/css" href="./styles.css">
</head>
<body>
	<ul>
		<li><a href="./index.php">Home Page</a></li>
		<li><a href="./search.php">Search Page</a></li>
		<li><a href="./actorinfo.php">Actor Info</a></li>
		<li><a class="active" href="./movieinfo.php">Movie Info</a></li>
		<li><a href="./addpersoninfo.php">Add Actor or Director</a></li>
		<li><a href="./addmovieinfo.php">Add Movie Info</a></li>
		<li><a href="./addcomments.php">Add Comments</a></li>
		<li><a href="./add_actor_to_movie.php">Add Actor to Movie Relation</a></li>
		<li><a href="./add_director_to_movie.php">Add Director to Movie Relation</a></li>
	</ul>
	
<div class="container">
	<h1>Movie Information Page:</h1>

	<?php
		$db = new mysqli('localhost', 'cs143', '', 'CS143');

		if($db->connect_errno > 0){
		    die('Unable to connect to database [' . $db->connect_error . ']');
		}

		if(isset($_GET['identifier'])) {
			$id=$_GET["identifier"];
			$movie = "SELECT title, year, company, rating FROM Movie WHERE id=" . $id;		//need to add genre to this query
			$actors = "SELECT Actor.id as aid, CONCAT(Actor.first, \" \", Actor.last) as Name, MovieActor.role as Role FROM (Actor INNER JOIN MovieActor ON Actor.id = MovieActor.aid) WHERE MovieActor.mid=" . $id;
			$director = "SELECT CONCAT(Director.first, \" \", Director.last) as Name FROM (Director INNER JOIN MovieDirector ON Director.id = MovieDirector.did) WHERE MovieDirector.mid=" . $id;
			$genre = "SELECT genre FROM MovieGenre WHERE mid=" . $id;
			$review = "SELECT name, time, rating, comment FROM Review WHERE mid=" . $id;
			$avg = "SELECT avg(rating) as avg_rating FROM Review WHERE mid=" . $id;

			$movieinfo = $db->query($movie);
			$actorinfo = $db->query($actors);
			$directorinfo = $db->query($director);
			$genreinfo = $db->query($genre);
			$reviewinfo = $db->query($review);
			$avg_review = $db->query($avg);

			//movie info
			echo "<h4>Movie Infomrmation is:</h4>";
			$movieoutput = $movieinfo->fetch_assoc();
			$directoroutput = $directorinfo->fetch_assoc();
			$avgoutput = $avg_review->fetch_assoc();

			echo "Title: " . $movieoutput["title"] . " (" . $movieoutput["year"] . ")";
			echo "<br>";
			echo "Producer: " . $movieoutput["company"];
			echo "<br>";
			echo "MPAA Rating: " . $movieoutput["rating"];
			echo "<br>";
			echo "Director: " . $directoroutput["Name"];
			echo "<br>";
			echo "Genre: ";
			while ($genreoutput = $genreinfo->fetch_assoc()) {
				echo $genreoutput["genre"] . " ";
			}

			echo "<br>";

			//actor info
			$numcols = mysqli_num_fields($actorinfo);
	        $array = [];
			if ($actorinfo->num_rows > 0) {
				echo "<table>";
				echo "<tr>";
				echo "<th>Name</th>";
				echo "<th>Role</th>";
				echo "</tr>";

				while($row = $actorinfo->fetch_assoc()) {		//go row by row through the results of the mysql query, fetch_assoc gets a row and turns it into an array
					echo "<tr>";
	                	if (is_null($row["Role"])) {	//manually print N/A if the field is null to look like sample
	                		echo "<td><a href=\"./actorinfo.php?identifier=" . $row["aid"] . "\">" . $row["Name"] . "</a></td>";
	                		echo "<td>N/A</td>";
	                	}
	                    else {
	                    	echo "<td><a href=\"./actorinfo.php?identifier=" . $row["aid"] . "\">" . $row["Name"] . "</a></td>";
	                    	echo "<td>\"" . $row["Role"] . "\"</td>";	//$row essentially acts as dictionary, use name of attribute to get value for current row
	                    }
	                    echo "</tr>";
	                }
					
				echo "</table>";
			}
			else {
				echo "No results found.";
			}
					//review info
			echo "<br>";
			echo "<h2>Average Review: " . round($avgoutput["avg_rating"], 1) . "/5</h2>";
			echo "<h4>Reviews:</h4>";
			if ($reviewinfo->num_rows > 0) {
				echo "<table>";

				while($row = $reviewinfo->fetch_assoc()) {	
					echo "<tr><td>";
		                echo $row[time];
		                if (is_null($row[name])){
		                	echo "<h2>An anonymous user says: </h2>";
		                } else {
		                	echo "<h2>" . $row[name] . " says: </h2>";
		                }
		                echo "<p>" . $row[comment] . "</p>";
		                echo "<h5>Rating: " . $row[rating] . "/5</h5>";
					echo "</td></tr>";
				}
				echo "</table>";
			}
			else {
				echo "No reviews for this movie.";
			}

			echo "<br>";
			echo "<a href=\"./addcomments.php?movie_id=" . $id . "\">Add a review...</a>";

			$actorinfo->free();
			$movieinfo->free();
			$db->close();
		}

	?>

	
	<form method="get" action="./search.php">
		<h3>Search:</h3>
		<input type="text" name="search" placeholder="Search...">
		<br><br>
		<input type="submit" name="submitbutton" value="Submit">
	</form>
</div>
	

</body>
</html>