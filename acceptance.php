<?php

/*
	$person = [
		'screen_name' => $u->screen_name, 
		'name' => $u->name,
		'joined' => $joined,
		'id' => $u->id_str,
		'followers_count' => $u->followers_count,
		'following_count' => $u->friends_count,
		'deficit' => $u->friends_count > $u->followers_count,
		'time_zone' => $u->time_zone,
		'location' => $u->location
	];
*/

//Criteria to target a specific twitter market
function accept_market($u)
{
	$locations = ['riyad', 'rkiye', 'saudi', 'baghdad', 'iran', 'iraq', 'stanbul', 'india'];
	$timezones = ['istanbul', 'baghdad', 'riyadh', 'bucharest', 'jakarta', 'kuwait', 'kyiv'];
	foreach($locations as $loc)
	{
		if (stripos($u['location'], $loc) !== false)
		{
			return false;
		}
	}
	
	foreach($timezones as $tz)
	{
		if (stripos($u['time_zone'], $tz) !== false)
		{
			return false;
		}
	}
	
	return true;
}