<?php
	require_once '../twitter-helper.php';
	require_once 'api_utils.php';
	
	header('content-type: text/plain');
	
	$log = '';
	$time = time();
	$log_file = "../data/follow-log$time.txt";
	
	init();

	$username = 'robouncle';
	$limit = 10;
	
	// $enable = isset($_POST['password']) && ($_POST['password'] == $password);
	
	// if (!enable)
	// {
		// exit('Bad password');
	// }
	logline("---- Start following back [1/2] ----");
	
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
	
	logline('---- Finished following back [1/2]----');
	logline('---- Start organic following [2/2]----');
	
	//Get the list of people to never follow again
	$exile_list = "../data/exile.txt";
	
	//These is an array of usernames
	$exiles = get_or_create_list_file($exile_list);
	
	
	// Write log
	logline('Write log');
	file_put_contents($log_file, $log);
	
	
	/*
		Follow Task - Every 2 hours?
			Limit := 10
			For each in MyFollowers
				For each in TheirFollowers
					If They.Follow > They.Followers && Limit > 0 (i.e. they are in follower defecit)
						Follow That Person
						Limit -= 1;
			If Limit == 0 Then Exit
	*/
		
	