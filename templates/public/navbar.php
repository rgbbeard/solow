<?php
Use Router\Router;
$img = $params["img"];
?>
<nav id='navbar' class='navbar'>
	<div class="container">
		<div class="nav-logo-container">
			<a href="<?php echo Router::generate_link("home"); ?>">
				<img 
					src="<?php echo $img;?>/logo_min_white.png" 
					title="MyWebsite"
					alt="Immagine non disponibile"/>
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
<nav id='navbar-mobile' class='navbar'>
	<div class="container">
		<button id="menu-trigger">
			<img src="<?php echo $params["img"] . "icons/cookie.svg"; ?>"/>
		</button>
		<div class="nav-logo-container">
			<a href="<?php echo Router::generate_link("home"); ?>">
				<img 
					src="<?php echo $img;?>/logo_min_white.png" 
					title="MyWebsite"
					alt="Immagine non disponibile"/>
			</a>
		</div>
		<div class="nav-main-menu">
			<button id="nav-trigger">
				<img src="<?php echo $params["img"] . "icons/close.svg"; ?>"/>
			</button>
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