<html>
<head><title>Calculator</title></head>

<body>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
		<input type="text" name="expr"><input type="submit" value="Calculate">
	</form>
	<?php
		if(isset($_GET['expr'])) {
			$expr=$_GET["expr"];
			$expr_cleaned=str_replace(' ', '', $expr);
			$valid_expr=preg_match("/^[-+*.\/, 0-9]+$/",$expr_cleaned);
			$divide_zero=preg_match("/\/[0]/",$expr_cleaned);

			if($expr_cleaned=="") {

			} elseif(! $valid_expr){
				?>
				<h3>Result</h3>
				<?php
				echo "Invalid Expression!";
			} elseif($divide_zero) {
				?>
				<h3>Result</h3>
				<?php
				echo "Division by zero error!";
			} else {
				?>
				<h3>Result</h3>
				<?php

				$err=@eval("\$result=$expr_cleaned;");
				if($err) {
					echo "Invalid Expression!";
				} else {
					echo $expr . " = " . $result;
				}
			}
		}
	?>
</body>
</html>