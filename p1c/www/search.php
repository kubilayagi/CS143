<!DOCTYPE html>
<html>
<head>
	<title>Search Actors and Movies</title>
	<link rel="stylesheet" type="text/css" href="./styles.css">
</head>
<body>
	<ul>
		<li><a href="./index.php">Home Page</a></li>
		<li><a class="active" href="./search.php">Search Page</a></li>
		<li><a href="./actorinfo.php">Actor Info</a></li>
		<li><a href="./movieinfo.php">Movie Info</a></li>
		<li><a href="./addpersoninfo.php">Add Actor or Director</a></li>
		<li><a href="./addmovieinfo.php">Add Movie Info</a></li>
		<li><a href="./addcomments.php">Add Comments</a></li>
		<li><a href="./add_actor_to_movie.php">Add Actor to Movie Relation</a></li>
		<li><a href="./add_director_to_movie.php">Add Director to Movie Relation</a></li>
	</ul>
<div class="container">
	<h1>Search for Actors and Movies</h1>

	<form action="search.php" method="GET">
		<h3>Explore:</h3>
		<input type="text" placeholder="Search..." name="search">
		<br><br>
		<input type="submit" name="submit">
	</form>

	<?php
		$db = new mysqli('localhost', 'cs143', '', 'CS143');

		if($db->connect_errno > 0){
		    die('Unable to connect to database [' . $db->connect_error . ']');
		}

		
		if(isset($_GET['search'])) {
			$search=$_GET['search'];

			$search_items = explode( " " , $search);

			$actor_query = "select id, concat(a.first, ' ', a.last) as Name, dob as `Date of Birth`
					  from Actor as a
					  where (a.first like '%" . $search_items[0] . "%' or a.last like '%" . $search_items[0] . "%')";
			for ($i = 1; $i < count($search_items); $i++) {
			    $actor_query = $actor_query . " and (a.first like '%" . $search_items[$i] . "%' or a.last like '%" . $search_items[$i] . "%')";
			}
			$actor_query = $actor_query . ";";

			$movie_query = "select id, title as Title, year as Year
					  from Movie as m
					  where m.title like '%" . $search_items[0] . "%'";
			for ($j = 1; $j < count($search_items); $j++) {
			    $movie_query = $movie_query . " and m.title like '%" . $search_items[$j] . "%'";
			}
			$movie_query = $movie_query . ";";
		} else {
			$actor_query = "";
			$movie_query = "";
		}
		if (isset($_GET["submit"])) {
			echo "<h2>Actor Results:</h2>";
			query_print($actor_query, $db, "actor");
			echo "<h2>Movie Results:</h2>";
			query_print($movie_query, $db, "movie");
		}

		function query_print(&$query, &$db, $string) {
			$result = $db->query($query);
			$numcols = mysqli_num_fields($result);
			$array = [];
			if ($result->num_rows > 0) {
				echo "<table class=\"result_table result_table_highlight\">";
				
				echo "<thead><tr>";
				while($property = mysqli_fetch_field($result)) {	//save the attribute names in an array so we can use them later
	                $array[] = $property->name;    //push back equivalent in php, equiv: array_push($array, $property->name);
	                if ($property->name != 'id') 
						echo "<th>" . $property->name . "</th>";	//print out the names of the attributes as titles
				}
				echo "</tr></thead>";
				echo "<tbody>";
				while($row = $result->fetch_assoc()) {		//go row by row through the results of the mysql query, fetch_assoc gets a row and turns it into an array
					echo "<tr>";
	                $i = 1;
	                while($i < sizeof($array)) {
	                	if (is_null($row[$array[$i]])) {	//manually print N/A if the field is null to look like sample
	                		echo "<td>N/A</td>";
	                	}
	                    else {
	                    	echo "<td>" . "<a href=\"./" . $string . "info.php?identifier=" . $row[$array[0]] . "\">" . $row[$array[$i]] . "</a></td>";	//$row essentially acts as dictionary, use name of attribute to get value for current row
	                    }
	                    $i++;
	                }
					echo "</tr>";
				}
				echo "</tbody>";
				echo "</table>";
			}
			else {
				echo "No results found.";
			}

			$result->free();
		}
		echo "</div>";

		$db->close();
	?>
</div>

</body>
</html>