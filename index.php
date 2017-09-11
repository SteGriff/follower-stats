<!DOCTYPE HTML>
<html>
<head>
<title>Follower Stats</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="style.css">
</head>
<body>
	<h1>Follower Stats</h1>

	<main>
	
	<?php
	include 'twitter-helper.php';

	init();

	$username = 'robouncle';
	$enable = isset($_GET['enable']) && ($_GET['enable'] == $password);
	
	$friends = [];
	if ($enable)
	{
		$friends = getConnectionInfo($username);
	}
	?>
		
	<table>
	<thead>
	<th>Username</th>
	<th>Name</th>
	<th>Follows Me</th>
	<th>Extra Data</th>
	</thead>
	<tbody>

	<?

	foreach($friends as $f)
	{			
		echo "<tr>
		<td class='username'>@{$f['screen_name']}</td>
		<td class='name'>{$f['name']}</td>
		<td class='follows_me'>{$f['follows_me']}</td>
		<td>{$f['connections']}</td>
		</tr>";
	}
	?>

	</tbody>
	</table>

	</main>
	
</body>
</html>


