<?php
$css = $params["css"];
$js = $params["js"];
$img = $params["img"];
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo $img;?>favicon.png"/>
<link href="<?php echo $css;?>main.css" rel="stylesheet"/>
<link href="<?php echo $css;?>debugger.css" rel="stylesheet"/>
<script src="<?php echo $js;?>navbar_mobile.js"></script>
<script src="<?php echo $js;?>utils.js"></script>
<script src="<?php echo $js;?>requests.js"></script>