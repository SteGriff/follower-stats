<!DOCTYPE HTML>
<html>
<head>
<title>FS - Rate limits</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="style.css">
</head>
<body>
	<h1>Rate Limits</h1>

	<main>
	
	<pre>
		<?php
			require 'twitter-helper.php';
			init();
			echo getRateLimitJson();
		?>
	</pre>
	
	</main>
	
</body>
</html>


