<?php
$js = $params["js"];
$socials = $params["socials"];
$class = !empty($socials) ? "halved" : "";
?>
<style>

</style>
<section class="fscontacts-container <?php echo $class; ?>">
	<h1 class="text-uppercase text-center">Contattaci, daremo forma ai tuoi sogni!</h1>
	<form id="login_form" action="" method="post">
		<div class="input-group">
			<label for="name">Nome</label>
			<input id="name" type="text" name="name" placeholder="Nome" required/>
		</div>
		<div class="input-group">
			<label for="surname">Cognome</label>
			<input id="surname" type="text" name="surname" placeholder="Cognome" required/>
		</div>
		<div class="input-group">
			<label for="email">Recapito e-mail</label>
			<input id="email" type="text" name="email" placeholder="E-mail"/>
		</div>
		<div class="input-group">
			<label for="phone">Recapito telefonico</label>
			<input id="phone" type="text" name="phone" placeholder="Telefono fisso o mobile"/>
		</div>
		<button class="btn btn-flat btn-info">Invia</button>
	</form>
</section>
<?php 
	if(!empty($socials)) {
		?>
			<section class="fcsocials-container <?php echo $class; ?>">
				
			</section>
		<?php
	}
?>
<script src='<?php echo $js;?>contacts.js'></script>