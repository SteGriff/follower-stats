<?php
	require_once '../twitter-helper.php';
	require_once 'api_utils.php';
		
	$log = '';
	$time = time();
	$log_file = "../data/follow-log$time.htm";
	$log_today = "../today/follow-log$time.htm";
	
	init();

	$username = 'robouncle';
	
	$enable = isset($_POST['password']) && ($_POST['password'] == $password);
	
	if (!$enable)
	{
		logline($_POST['password']);
		exit('Bad password');
	}
	$date = date('r', $time);
	logline("Start following back", "h1");
	logline("$time - $date");
	
	$followers = getFollowerConnections($username);
	
	foreach($followers as $f)
	{
		if (!$f['i_follow'])
		{
			$name = $f['screen_name'];
			try
			{
				follow($name);
				logline("Followed $name");
			}
			catch (Exception $ex)
			{
				logline(" - Failed to follow $name - exception thrown");
			}
		}
	}
	
	logline('Finished following back','h2');
	
	// Write log
	logline('Write log');
	file_put_contents($log_file, $log);
	
	//copy($log_file, $log_today);
	