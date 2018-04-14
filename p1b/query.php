<!DOCTYPE html>
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
		if(isset($_GET['query'])) {
			$query=$_GET["query"];

		$result = mysql_query($query);
		echo $result;
	?>



</body>
</html>