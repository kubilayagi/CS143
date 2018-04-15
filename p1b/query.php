<!DOCTYPE html>
<html>
<head>
	<title>1B</title>
	<style>
		table, td, th {
			border: 1px solid #ffffff;
		}
		body {
			background: #001a33;
            color: white;
		}
		button {
			color: green;
		}
        textarea {
			background: #ffcc00;
            border: 5px solid #b3daff;
		}
	</style>
</head>
<body>
	<h1>Movie Database Query</h1>
	<form action="query.php" method="GET">
		<textarea name="query" cols="60" rows="8"></textarea>
		<input type="submit" name="submit">
	</form>

	<?php
		$db = new mysqli('localhost', 'cs143', '', 'CS143');

		if($db->connect_errno > 0){
		    die('Unable to connect to database [' . $db->connect_error . ']');
		}

		if(isset($_GET['query'])) {
			$query=$_GET["query"];
		}

		$result = $db->query($query);

		echo "<h2>Results from MySQL:</h2>";
		
		$numcols = mysqli_num_fields($result);
        $array = [];
		if ($result->num_rows > 0) {
			echo "<table>";
			
			echo "<tr>";
			while($property = mysqli_fetch_field($result)) {	//save the attribute names in an array so we can use them later
                $array[] = $property->name;    //push back equivalent in php, equiv: array_push($array, $property->name);
				echo "<th>" . $property->name . "</th>";	//print out the names of the attributes as titles
			}
			echo "</tr>";

			while($row = $result->fetch_assoc()) {		//go row by row through the results of the mysql query
				echo "<tr>";
                $i = 0;
                while($i < sizeof($array)) {
                	if (is_null($row[$array[$i]])) {
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

		$result->free();
		$db->close();
	?>

</body>
</html>

<!-- https://www.w3schools.com/php/php_mysql_select.asp -->
<!-- http://php.net/manual/en/mysqli-result.fetch-assoc.php -->
<!--https://stackoverflow.com/questions/14629636/mysql-field-name-to-the-new-mysqli-->
