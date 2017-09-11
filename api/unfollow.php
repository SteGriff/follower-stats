	<?php
	include 'twitter-helper.php';

	init();

	$username = 'robouncle';
	$enable = isset($_POST['password']) && ($_POST['password'] == $password);
	
	if (!enable)
	{
		exit('Bad password');
	}
	
	$people_to_unfollow = [];
	
	$unfollow_list = "../data/unfollow.json";
	if (file_exists($unfollow_list))
	{
		$unfollow_data = file_get_contents($unfollow_list);
		$people_to_unfollow = json_decode($unfollow_data, true);
	}
	else
	{
		//Create empty file
		touch($unfollow_list);
	}
	
	if (count($people_to_unfollow) > 0)
	{
		
	}

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
	
	