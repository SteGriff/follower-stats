<?php
	require_once '../twitter-helper.php';
	require_once 'api_utils.php';
	
	header('content-type: text/plain');
	
	$log = '';
	$time = time();
	$log_file = "../data/log$time.txt";
	
	init();

	$username = 'robouncle';
	// $enable = isset($_POST['password']) && ($_POST['password'] == $password);
	
	// if (!enable)
	// {
		// exit('Bad password');
	// }
	logline("---- Start Unfollowing [1/2] ----");
	
	//Get the list of people to unfollow and those to never follow again
	// first time we run this, the list is empty and may have to be created
	$unfollow_list = "../data/unfollow.txt";
	$exile_list = "../data/exile.txt";
	$people_to_unfollow = get_or_create_list_file($unfollow_list);
	$exiles = get_or_create_list_file($exile_list);
		
	logline("People to unfollow:");

	// Unfollow everyone on deletion list (if any)
	if (count($people_to_unfollow) > 0)
	{
		foreach($people_to_unfollow as $person)
		{			
			if (trim($person) == '') {
				logline('Skip blank');
				continue;
			}
			
			logline("Unfollowing $person");
			
			$result = unfollow($person);
			
			logline ("Unfollowed $person");
			
			//Add them to exiles list - this is a courtesy to the user
			// so that we don't keep following and unfollowing repeatedly
			$exiles[] = trim($person);
		}
	}
	else
	{
		logline("No one in list to unfollow");
	}
	
	logline('Clear unfollow list');
	// Clear the deletion list
	file_put_contents($unfollow_list, '');

	// Write exiles list
	
	logline('Exiles');
	var_dump($exiles);
	$list = array_to_list_file($exiles);
	logline($list);
	file_put_contents($exile_list, $list);
	
	// Write log
	logline('Write log');
	file_put_contents($log_file, $log);
	
	logline('---- Finished Unfollowing [1/2]----');
	logline('---- Start Targeting [2/2]----');
	
	$friends = getConnectionInfo($username);
	$ids = [];
	
	//Get IDs of people who don't follow back
	foreach($friends as $friend)
	{
		if ($f['follows_me'] < 1)
		{
			$ids[] = $f['id'];
		}
	}
	
	//Look up the length of relationship with the targets
	// filter to only those who I've been following more than a day
	// TODO
	
	/*
		Unfollow Task - every 24 hours (48?)
			Unfollow everyone on deletion list if any (initially empty)
			Clear the deletion list
			Find out people I follow who don't follow back
				Add them to the deletion list
			Quit
			
		Follow Task - Every 2 hours?
			Limit := 10
			For each in MyFollowers
				For each in TheirFollowers
					If They.Follow > They.Followers && Limit > 0 (i.e. they are in follower defecit)
						Follow That Person
						Limit -= 1;
			If Limit == 0 Then Exit
	*/
	
	//$friends = getConnectionInfo($username);
	
	