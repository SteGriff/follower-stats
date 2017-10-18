<?php
	require_once '../twitter-helper.php';
	require_once '../acceptance.php';
	require_once 'api_utils.php';
		
	$log = '';
	$time = time();
	$log_file = "../data/prospect-log$time.htm";
	$log_today = "../today/prospect-log$time.htm";
	
	init();

	$username = 'robouncle';
	
	$enable = isset($_POST['password']) && ($_POST['password'] == $password);
	
	if (!$enable)
	{
		logline($_POST['password']);
		exit('Bad password');
	}
	$date = date('r', $time);
	logline('Start organic following', 'h1');
	logline("$time - $date");
	
	//Get Limit from POST or use default
	$default_limit = 10;
	$limit = (isset($_POST['limit']) && is_numeric($_POST['limit']))
		? $_POST['limit']
		: $default_limit;
	
	logline("Limit is $limit");
	
	//Get initial referring Follower ID from POST 
	// to override usual routine of picking three at random
	$referrer = isset($_POST['referrer'])
		? $_POST['referrer']
		: null;
		
	logline($referrer ? "Injected referrer: $referrer" : "No injected referrer");
	
	if ($referrer)
	{
		$followers = [$referrer];
		$selected_follower_indices = [0];
	}
	else
	{
		//Array of IDs
		$followers = getFollowers($username);
		
		//Take 3 random followers (or use our follower count if it is < 3!)
		$take = min(count($followers), 3);
		logline("Plan to use $take referrers");
		
		$selected_follower_indices = array_rand($followers, $take);
		
		$actual_referrer_count = count($selected_follower_indices);
		logline("Got $actual_referrer_count referrers");
	}
	
	//Get the list of people to never follow again as an array of usernames
	$exile_list = "../data/exile.txt";
	$exiles = get_or_create_list_file($exile_list);
		
	foreach($selected_follower_indices as $follower_index)
	{
		$follower_id = $followers[$follower_index];
		logline("Selected referrer: $follower_id", 'h2');
		
		//Get the IDs of people who follow this follower
		$ids = getFollowers($follower_id, false);
		$their_followers = getUsers($ids);
		
		foreach($their_followers as $peep)
		{
			$peep_name = $peep['screen_name'];
			if ($peep['deficit'])
			{
				if (!in_array($peep_name, $exiles))
				{
					if (accept_market($peep))
					{
						try
						{
							follow($peep_name);
							logline(" + Followed $peep_name ($limit)");
							$limit -= 1;
						}
						catch (Exception $ex)
						{
							logline(" - Failed to follow $peep_name - exception thrown");
						}
					}
					else
					{
						logline("Skip $peep_name - failed market acceptance");
					}
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
	
	//copy($log_file, $log_today);
	
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