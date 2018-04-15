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
		echo $result;
	?>

</body>
</html>