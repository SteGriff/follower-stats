<!DOCTYPE HTML>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">
*{margin:0;padding:0;box-sizing:border-box;}
html{
	font-family: sans-serif;
}

h1
{
	width: 100%;
	font-size: 300%;
	font-weight: 100;
	background-color: #55ACEE;
	margin: 0 0 20px 0;
	padding: 20px;
	color: #fff;
}

main
{
	display: block;
	padding: 20px;
}

p.summary
{
    font-size: 120%;
    margin-bottom: 20px;
}

table
{
	width: 100%;
}
tr.user
{
	font-weight: bold;
	line-height: 2em;
}

div.overview
{
	font-size: 200%;
	margin: 20px 0;
}
</style>
<script type="text/javascript">

</script>
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


