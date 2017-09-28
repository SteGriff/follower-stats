<?php
	require_once '../twitter-helper.php';
	require_once 'api_utils.php';
		
	$log = '';
	$time = time();
	$log_file = "../data/prospect-log$time.txt";
	
	init();

	$username = 'robouncle';
	
	$enable = isset($_POST['password']) && ($_POST['password'] == $password);
	
	if (!$enable)
	{
		logline($_POST['password']);
		exit('Bad password');
	}
	logline('Start organic following', 'h1');
	
	$limit = 10;
	logline("Limit is $limit");
	
	$followers = getFollowers($username);
	
	//Take 3 random followers (or use our follower count if it is < 3!)
	$take = min(count($followers), 3);
	logline("Plan to use $take referrers");
	
	$selected_followers = array_rand($followers, $take);
	
	$actual_referrer_count = count($selected_followers);
	logline("Got $actual_referrer_count referrers");
	
	//Get the list of people to never follow again as an array of usernames
	$exile_list = "../data/exile.txt";
	$exiles = get_or_create_list_file($exile_list);
		
	foreach($selected_followers as $follower)
	{
		//Get the IDs of people who follow this follower
		$follower_name = $follower['screen_name'];
		$ids = getFollowers($follower_name);

		logline("Selected follower: $follower_name", 'h2');
				
		$their_followers = getUsers($ids);
		
		foreach($their_followers as $peep)
		{
			$peep_name = $peep['screen_name'];
			if ($peep['deficit'])
			{
				if (!in_array($peep_name, $exiles))
				{
					$peep['referrer'] = $follower_name;
					logline(" > Recommend $peep_name ($limit)");
					$limit -= 1;
				}
				else
				{
					logline("Skip $peep_name - exiled");
				}
			}
			else
			{
				logline("Skip $peep_name - too popular");
			}
			
			//If we have hit our quota of people to follow
			if ($limit == 0)
			{
				logline("Limit reached!", 'h2');
				//Break out of both foreach loops
				break 2;
			}
		}
	}
	
	logline('Finished organic following', 'h1');
	
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