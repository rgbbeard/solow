<?php
Use Router\Router;
?>
<nav id='navbar' class='navbar'>
	<div class="container">
		<div class="nav-logo-container">
			<a href="<?php echo Router::generate_link("admin"); ?>">
				<img 
					src="<?php echo $params["img"];?>/logo_min_white.png"
					title="MyWebsite"
					alt="Immagine non disponibile" />
			</a>
		</div>
		<div class="nav-main-menu">
			<?php foreach($params["menu_items"] as $name => $item) {
				if($item["visible"]) {
					$link = Router::generate_link($name);
					$selected = $name === Router::get_current_route() ? "selected" : "";

					echo "<a href='$link'>
						<li class='navbar-item $selected'>
						" . $item["display"] . "	
						</li>
					</a>";
				}
			}?>
		</div>
	</div>
</nav>