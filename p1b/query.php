<html>
<head>
	<title>1B</title>
</head>
<body>
	<form action="." method="GET">
		<textarea name="query" cols="60" rows="8"></textarea>
		<input type="submit" name="submit">
	</form>

	<?php
		$db = new mysqli('localhost', 'cs143', '', 'TEST');

		if($db->connect_errno > 0){
		    die('Unable to connect to database [' . $db->connect_error . ']');
		}

		if(isset($_GET['query'])) {
			$query=$_GET["query"];

		$result = $db->query($query);
		
		$i = 0;
		if ($result->num_rows > 0) {
			echo "<table>";
			echo "<th>";
			while($i < mysqli_num_fields($result)) {
				
			}
			echo <"</th>";
			while($row = $result->fetch_assoc()) {
				echo "<tr>";

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