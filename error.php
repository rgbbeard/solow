<!doctype html>
<html>
	<head>
		<title>Error - MyWebsite</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" type="image/png" sizes="32x32" href="res/img/favicon.png"/>
		<link href="res/css/main.css" rel="stylesheet"/>
		<link href="res/css/debugger.css" rel="stylesheet"/>
		<link href="res/css/error.css" rel="stylesheet"/>
	</head>
	<body>
		<main>
			<article>
				<h20 class="text-center"><?php echo $http->code; ?></h20>
				<h3 class="text-center"><?php echo $http->message; ?></h3>
			</article>
		</main>
	</body>
</html>