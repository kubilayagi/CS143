<!DOCTYPE html>
<html>
<head>
	<title>Actor/Director</title>
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

	<h1>Actor Information Page</h1>

	<?php
		$db = new mysqli('localhost', 'cs143', '', 'CS143');

		if($db->connect_errno > 0){
		    die('Unable to connect to database [' . $db->connect_error . ']');
		}

		if(isset($_GET['identifier'])) {
			$id=$_GET["identifier"];
            

			$actor = "SELECT CONCAT(first, \" \", last) AS Name, sex, dob, dod FROM Actor WHERE id=" . $id;
			$actorinfo = $db->query($actor);
			$actoroutput = $actorinfo->fetch_assoc();

			$movies = "SELECT Movie.title as `Movie Title`, MovieActor.role as Role FROM (Movie INNER JOIN MovieActor ON Movie.id = MovieActor.mid) WHERE MovieActor.aid=" . $id;
			$movieinfo = $db->query($movies);

			//actor information
			echo "<h3>Actor Information is:</h3>";
			echo "<table>";
			//header
			echo "<tr>";
			echo "<th>Name</th><th>Sex</th><th>Date Of Birth</th><th>Date Of Death</th>";
			echo "</tr>";
			//actual info
			echo "<tr>";
			echo "<td>" . $actoroutput["Name"] . "</td><td>" . $actoroutput["sex"] . "</td><td>" . $actoroutput["dob"] . "</td>";
			if (is_null($actoroutput["dod"])) {
				echo "<td>Still Alive</td>";
			}
			else {
				echo "<td>" . $actoroutput["Name"] . "</td>";
			}
			echo "</tr>";
			echo "</table>";

            echo "<br>";
			echo "<h3>Actor's Movies and Roles:</h3>";


			//movies they've been in
			$numcols = mysqli_num_fields($movieinfo);
	        $array = [];
			if ($movieinfo->num_rows > 0) {
				echo "<table>";
				
				echo "<tr>";
				while($property = mysqli_fetch_field($movieinfo)) {	//save the attribute names in an array so we can use them later
	                $array[] = $property->name;    //push back equivalent in php, equiv: array_push($array, $property->name);
					echo "<th>" . $property->name . "</th>";	//print out the names of the attributes as titles
				}
				echo "</tr>";

				while($row = $movieinfo->fetch_assoc()) {		//go row by row through the results of the mysql query, fetch_assoc gets a row and turns it into an array
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


		}
	?>

	<h4>Search:</h4>
	<form method="get" action="./search.php">
		<input type="text" name="search" placeholder="Search...">
		<br>
		<input type="submit" name="submitbutton" value="Submit">
	</form>

</body>
</html>