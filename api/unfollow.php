<?php
	require_once 'twitter-helper.php';
	require_once 'api_utils.php';
	
	init();

	$username = 'robouncle';
	$enable = isset($_POST['password']) && ($_POST['password'] == $password);
	
	if (!enable)
	{
		exit('Bad password');
	}
	
	$unfollow_list = "../data/unfollow.json";
	$exile_list = "../data/exile.json";
	$people_to_unfollow = decode_or_create_json_file($unfollow_list);
	$exiles = decode_or_create_json_file($exile_list);
		
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
	
	