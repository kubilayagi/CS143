<!DOCTYPE html>
<html>
<head>
	<title>Movie</title>
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
	

	<h1>Movie Infomrmation Page:</h1>

	<?php
		$db = new mysqli('localhost', 'cs143', '', 'CS143');

		if($db->connect_errno > 0){
		    die('Unable to connect to database [' . $db->connect_error . ']');
		}

		if(isset($_GET['identifier'])) {
			$id=$_GET["identifier"];
		}

		$movie = "SELECT title, year, company, rating FROM Movie WHERE id=" . $id;		//need to add genre to this query
		$actors = "SELECT CONCAT(Actor.first, \" \", Actor.last) as Name, MovieActor.role as Role FROM (Actor INNER JOIN MovieActor ON Actor.id = MovieActor.aid) WHERE MovieActor.mid=" . $id;
		$director = "SELECT Director.first, Director.last FROM (Director INNER JOIN MovieDirector ON Director.id = MovieDirector.did) WHERE MovieDirector.mid=" . $id;
		$genre = "SELECT genre FROM MovieGenre WHERE mid=" . $id;

		$movieinfo = $db->query($movie);
		$actorinfo = $db->query($actors);
		$directorinfo = $db->query($director);
		$genreinfo = $db->query($genre);

		//movie info
		echo "<h4>Movie Information is:</h4>";
		$movieoutput = $movieinfo->fetch_assoc();
		$directoroutput = $directorinfo->fetch_assoc();
		$genreoutput = $genreinfo->fetch_assoc();
		echo "Title: " . $movieoutput["title"] . " (" . $movieoutput["year"] . ")";
		echo "<br>";
		echo "Producer: " . $movieoutput["company"];
		echo "<br>";
		echo "MPAA Rating: " . $movieoutput["rating"];
		echo "<br>";
		echo "Director: " . $directoroutput["Director.first"] . " " . $directoroutput["Director.last"];
		echo "<br>";
		echo "Genre: " . $genreoutput["genre"];
		echo "<br>";

		//actor info
		$numcols = mysqli_num_fields($actorinfo);
        $array = [];
		if ($actorinfo->num_rows > 0) {
			echo "<table>";
			
			echo "<tr>";
			while($property = mysqli_fetch_field($actorinfo)) {	//save the attribute names in an array so we can use them later
                $array[] = $property->name;    //push back equivalent in php, equiv: array_push($array, $property->name);
				echo "<th>" . $property->name . "</th>";	//print out the names of the attributes as titles
			}
			echo "</tr>";

			while($row = $actorinfo->fetch_assoc()) {		//go row by row through the results of the mysql query, fetch_assoc gets a row and turns it into an array
				echo "<tr>";
                $i = 0;
                while($i < sizeof($array)) {
                	if (is_null($row[$array[$i]])) {	//manually print N/A if the field is null to look like sample
                		echo "<td>N/A</td>";
                	}
                    else {
                    	echo "<td>" . $row[$array[$i]] . "</td>";	//$row essentially acts as dictionary, use name of attribute to get value for current row
                    }
                    $i++;
                }
				echo "</tr>";
			}
			echo "</table>";
		}
		else {
			echo "No results found.";
		}


		//review info

		$actorinfo->free();
		$movieinfo->free();
		$db->close();
	?>

	<h4>Search:</h4>
	<form method="get" action="./search.php">
		<input type="text" name="search" placeholder="Search...">
		<br>
		<input type="submit" name="submitbutton" value="Submit">
	</form>

	

</body>
</html>