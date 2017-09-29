<?php
	require_once '../twitter-helper.php';
	require_once 'api_utils.php';
	
	header('content-type: text/plain');
	
	$log = '';
	$time = time();
	$log_file = "../data/unfollow-log$time.htm";
	$log_today = "../today/unfollow-log$time.htm";
	
	init();

	$username = 'robouncle';
	$enable = isset($_POST['password']) && ($_POST['password'] == $password);
	
	if (!$enable)
	{
		logline($_POST['password']);
		exit('Bad password');
	}
	$date = date('r', $time);
	logline("Start unfollowing [1/2]");
	logline("$time - $date");
	
	/*
		Part 1/2
	*/
	
	//Get the list of people to unfollow and those to never follow again
	// first time we run this, the list is empty and may have to be created
	$unfollow_list = "../data/unfollow.txt";
	$exile_list = "../data/exile.txt";
	
	//These are arrays of usernames
	$people_to_unfollow = get_or_create_list_file($unfollow_list);
	$exiles = get_or_create_list_file($exile_list);
		
	logline('Checking people to unfollow...');

	// Unfollow everyone on deletion list (if any)
	if (count($people_to_unfollow) > 0)
	{
		logline('Get friendship connections...');
		
		//Check they still don't follow (get connections using username list)
		$friendships = getFriendships($people_to_unfollow, true);
		
		// logline(" >> ALL FRIENDSHIPS:");
		// var_dump($friendships);
		
		logline('Got connections. Begin:', 'h2');
		
		foreach($people_to_unfollow as $person)
		{			
			if (trim($person) == '') {
				logline('Skip blank');
				continue;
			}
			
			//var_dump($friendships[$person]);
			$follows_back = $friendships[$person]['follows_me'];
			//logline("Check $person. follows_me = $follows_back");
			
			if ($follows_back)
			{
				logline("Skipped $person - they now follow back!");
			}
			else
			{
				try
				{
					unfollow($person);
					logline ("Unfollowed $person");
					//Add them to exiles list - this is a courtesy to the user
					// so that we don't keep following and unfollowing repeatedly
					$exiles[] = trim($person);
				}
				catch (Exception $ex)
				{
					logline (" - Failed to unfollow $person - exception thrown");
				}
			}

		}
	}
	else
	{
		logline("No one in list to unfollow!");
	}
	
	logline('Clear unfollow list');
	// Clear the deletion list
	file_put_contents($unfollow_list, '');

	// Write exiles list
	
	$exiles_count = count($exiles);
	logline("There are $exiles_count exiles", 'h2');
	
	$list = array_to_list_file($exiles);
	//logline($list);
	file_put_contents($exile_list, $list);
	
	/*
		Part 2/2
	*/
	
	logline('Finished unfollowing [1/2]', 'h2');
	logline('Start targeting [2/2]', 'h2');
	
	$friends = getFriendConnections($username);
	$names = [];
	$limit = 100;
	
	//Get names of people who don't follow back
	foreach($friends as $f)
	{
		if (!$f['follows_me'])
		{
			$name = $f['screen_name'];
			$names[] = $name;
			$limit--;
			logline("Targeted $name ($limit)");
		}
		if ($limit == 0)
		{
			logline("Limit reached.");
			break;
		}
	}
	
	logline("Saving unfollow list.");
	$list_content = array_to_list_file($names);
	file_put_contents($unfollow_list, $list_content);
	
	logline('Finished targeting [2/2]', 'h2');
	
	// Write log
	logline('Write log');
	file_put_contents($log_file, $log);
	
	//copy($log_file, $log_today);
	
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
		
	