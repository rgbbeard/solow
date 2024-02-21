<?php
use Router\Router;
?>
<section class="fsfxim-container">
	<img src="<?php echo $params["img"] . "cartongesso/34.jpeg"; ?>"/>
	<div class="overlay">
		<article>
			<h7 class="text-uppercase white-color">costruiamo il futuro insieme</h7>
			<a href="<?php echo Router::generate_link("about"); ?>" class="btn btn-flat btn-info btn-lg">Contattaci</a>
		</article>
	</div>
</section>