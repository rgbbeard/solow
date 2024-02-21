<?php 
$http = $params["response"]->httpr;
?>
<!doctype html>
<html>
	<head>
		<title><?php echo $params["title"]; ?> - MyWebsite</title>
		<?php include "templates/common/headers.php"; ?>
		<link href="<?php echo $params["css"] . "error.css";?>" rel="stylesheet"/>
	</head>
	<body>
		<?php echo $params["navbar"]; ?>
		<main>
			<article>
				<h20 class="text-center"><?php echo $http->code; ?></h20>
				<h3 class="text-center"><?php echo $http->message; ?></h3>
			</article>
		</main>
	</body>
</html>