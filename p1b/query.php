<!DOCTYPE html>
<html>
<head>
	<title>1B</title>
	<style>
		table {
			border: 1px solid #000000;
		}
		td, th {
		    border: 1px solid #000000;
		    text-align: left;
		}
	</style>
</head>
<body>
	<form action="." method="GET">
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

		$result = $db->query($query);
		
		$i = 0;
		$numcols = mysqli_num_fields($result);
		if ($result->num_rows > 0) {
			echo "<table>";
			
			echo "<tr>";
			while($i < $numcols) {
				echo "<th>" . $row[$i] . "</th>";
				$i++;
			}
			echo "</tr>"

			while($row = $result->fetch_assoc()) {
				echo "<tr>";
				$j = 0;
				while($j < $numcols) {
					echo "<td>" . $row[$j] . "</td>";
				}
				echo "</tr>";
			}
			echo "</table>"
		}
		else {
			echo "No results found.";
		}

		$db->close();
	?>

</body>
</html>

<!-- https://www.w3schools.com/php/php_mysql_select.asp -->
<!-- http://php.net/manual/en/mysqli-result.fetch-assoc.php -->
