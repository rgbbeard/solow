<?php
use Router\Router;
?>
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
		echo "<a href='" . Router::generate_link("services") . "'>
			<div class='block'>
				<h3>Vedi tutti i servizi</h3>
			</div>
		</a>";
	} else {
		echo "<p>Nessun servizio disponibile</p>";
	}
	?>
</section>