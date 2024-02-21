<?php
Use Router\Router;
?>
<!doctype html>
<html>
	<head>
		<title>Services - MyWebsite</title>
		<?php include "templates/common/headers.php"; ?>
	</head>
	<body>
		<?php echo $params["navbar"]; ?>
		<main>
			<section class="fsss-container">
				<?php
				if(!empty($params["services"])) {
					foreach($params["services"] as $id => $service) {
						echo "<div class='service'>
							<!--img src='https://placehold.co/600x500'/-->
							<div class='overlay'>
								<article>
									<h7>" . $service["name"] . "</h7>
									<h3>" . $service["description"] . "</h3>
									<a href='" . Router::generate_link("service", [$id]) . "' class='btn btn-flat btn-info btn-lg'>Scopri</a>
								</article>	
							</div>
						</div>";
					}
				} else {
					echo "<p>Nessun servizio disponibile</p>";
				}
				?>
			</section>
		</main>
	</body>
</html>