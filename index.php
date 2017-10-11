<!DOCTYPE HTML>
<html>
<head>
<title>FS - Who I'm following</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="style.css">
</head>
<body>
	<h1>Who I'm following</h1>

	<main>
	
	<?php
	include 'twitter-helper.php';

	init();

	$username = 'robouncle';
	$enable = isset($_GET['enable']) && ($_GET['enable'] == $password);
	
	$following = 0;
	$followbacks = 0;
	
	$friends = [];
	if ($enable)
	{
		$friends = getFriendConnections($username);
		//usort($friends, "follows_me");
	}
	?>
		
	<table>
	<thead>
	<th>ID</th>
	<th>Username</th>
	<th>Name</th>
	<th>Follows Me</th>
	<th>Extra Data</th>
	</thead>
	<tbody>

	<?

	foreach($friends as $f)
	{			
		$following += 1;
		$followbacks += $f['follows_me'];
		
		echo "<tr>
		<td>{$f['id']}</td>
		<td class='username'>@{$f['screen_name']}</td>
		<td class='name'>{$f['name']}</td>
		<td class='follows_me'>{$f['follows_me']}</td>
		<td>{$f['connections']}</td>
		</tr>";
	}
	?>

	</tbody>
	</table>

	<p>Following back/Following = <? echo "$followbacks/$following"; ?></p>
	
	<pre>
		<? echo getRateLimitJson(); ?>
	</pre>
	</main>
	
</body>
</html>


