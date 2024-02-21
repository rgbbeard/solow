<!doctype html>
<html>
	<head>
		<title>Home - MyWebsite</title>
		<?php include "templates/common/headers.php"; ?>
		<style>
			
		</style>
	</head>
	<body>
		<?php echo $params["navbar"]; ?>
		<main>
			<?php echo $params["module_jumbotron"]; ?>
			<?php echo $params["module_services"]; ?>
			<?php echo $params["module_contacts"]; ?>
		</main>
	</body>
</html>