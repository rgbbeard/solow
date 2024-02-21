<?php
$service = end($params["service"]);
?>
<!doctype html>
<html>
	<head>
		<title>Service - <?php echo $service["name"]; ?> - MyWebsite</title>
		<?php include "templates/common/headers.php"; ?>
	</head>
	<body>
		<?php echo $params["navbar"]; ?>
		<div class="container services">
			<h1><?php  echo $service["name"]; ?></h1>
			<p><?php  echo $service["description"]; ?></p>
		</div>
	</body>
</html>