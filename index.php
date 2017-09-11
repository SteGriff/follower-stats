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

	$username = null;
	$friends = null;
	if (!empty($_GET['username']) && isset($_GET['username']))
	{
		$username = strtolower($_GET['username']);
		$friends = getConnectionInfo($username);
		?>
		
	<table>
	<thead>
	<th>Username</th>
	<th>Name</th>
	<th>Follows Me</th>
	</thead>
	<tbody>

	<?

	foreach($friends as $f)
	{	
		$datestr = date_string($f['joined']);
		
		echo "<tr>
		<td class='username'>@{$f['screen_name']}</td>
		<td class='name'>{$f['name']}</td>
		<td class='follows_me'>{$f['follows_me']}</td>
		</tr>";
	}
	?>

	</tbody>
	</table>
	
	<div class="overview">

	</div>
		
	<?
	}
	else
	{
	?>
	
	<form action="index.php" method="GET">
		<label>Enter your twitter handle: @<input name="username" placeholder="stegriff"></label>
		<button type="submit">Go</button>
	</form>
	
	<?
	}
	?>

	</main>
	
</body>
</html>


