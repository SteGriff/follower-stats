<?php
	require_once '../twitter-helper.php';
	require_once 'api_utils.php';
		
	$log = '';
	$time = time();
	$log_file = "../data/follow-log$time.txt";
	
	init();

	$username = 'robouncle';
	
	$enable = isset($_POST['password']) && ($_POST['password'] == $password);
	
	if (!$enable)
	{
		exit('Bad password');
	}
	logline("Start following back", "h1");
	
	$followers = getFollowerConnections($username);
	
	foreach($followers as $f)
	{
		if (!$f['i_follow'])
		{
			$name = $f['screen_name'];
			follow($name);
			logline("Followed $name");
		}
	}
	
	logline('Finished following back','h2');
	logline('---- Start organic following [2/2]----');
	
	// Write log
	logline('Write log');
	file_put_contents($log_file, $log);
		
	