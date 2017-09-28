<!DOCTYPE HTML>
<html>
<head>
<title>Followers</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="style.css">
</head>
<body>
	<h1>Follow-back targets</h1>

	<main>
	
	<?php
	include 'twitter-helper.php';

	init();

	$username = 'robouncle';
	$enable = isset($_GET['enable']) && ($_GET['enable'] == $password);
	
	$followers = 0;
	$i_follow = 0;
	
	$friends = [];
	if ($enable)
	{
		$friends = getFollowerConnections($username);
	}
	?>
		
	<table>
	<thead>
	<th>Username</th>
	<th>Name</th>
	<th>Follows Me</th>
	<th>I Follow</th>
	<th>Extra Data</th>
	</thead>
	<tbody>

	<?

	foreach($friends as $f)
	{			
		$followers += 1;
		$i_follow += $f['i_follow'];
		
		echo "<tr>
		<td class='username'>@{$f['screen_name']}</td>
		<td class='name'>{$f['name']}</td>
		<td class='follows_me'>{$f['follows_me']}</td>
		<td>{$f['i_follow']}</td>
		<td>{$f['connections']}</td>
		</tr>";
	}
	?>

	</tbody>
	</table>

	<p>I follow/Followers = <? echo "$i_follow/$followers"; ?></p>
	</main>
	
</body>
</html>


