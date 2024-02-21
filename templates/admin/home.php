<?php 
$css = $params["css"];
?>
<!doctype html>
<html>
	<head>
		<title>Administration - MyWebsite</title>
		<?php include "templates/common/headers.php"; ?>
		<link href="<?php echo $css . "admin.css"; ?>" rel="stylesheet">
	</head>
	<body>
		<?php echo $params["navbar"]; ?>
		<main>
			<div class="admin-service">
				<h3>Gestione socials</h3>
				<a href="#" class="btn btn-flat btn-info">Vai</a>
			</div>
			<div class="admin-service">
				<h3>Gestione immagini</h3>
				<a href="#" class="btn btn-flat btn-info">Vai</a>
			</div>
			<div class="admin-service">
				<h3>Gestione contatti</h3>
				<a href="#" class="btn btn-flat btn-info">Vai</a>
			</div>
		</main>
	</body>
</html>