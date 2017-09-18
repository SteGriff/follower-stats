<?php

require_once 'twitter-php/src/twitter.class.php';
require_once 'secrets.php';

$twitter = null;

function init()
{
	global $twitter;
	global $consumerKey,
	       $consumerSecret,
	       $accessToken,
	       $accessTokenSecret;
	
	$twitter = new Twitter(
		$consumerKey,
		$consumerSecret,
		$accessToken,
		$accessTokenSecret
	);
}

function getConnections($username_array)
{
	
}

function getFriendConnections($username)
{
	$filename = "data/$username-friends.json";
	
	//Get all friends IDs
	$ids = getFriends($username);
	$friends = getFriendships($ids);

	//Log the result
	$json = json_encode($friends);
	file_put_contents($filename, $json);

	return $friends;
}

function getFollowerConnections($username)
{
	$filename = "data/$username-followers.json";
	
	//Get all followers IDs
	$ids = getFollowers($username);
	$friends = getFriendships($ids);

	//Log the result
	$json = json_encode($friends);
	file_put_contents($filename, $json);

	return $friends;
}

function getRateLimit()
{
	global $twitter;
	$request = ['resources' => 'friends,followers,users'];
	$response = $twitter->request('application/rate_limit_status', 'GET', $request);
}

function getFriends($screen_name)
{
	global $twitter;
	
	$request = ['screen_name' => $screen_name, 'stringify_ids' => 'true'];
	$response = $twitter->request('friends/ids', 'GET', $request);
	return $response->ids;
}

function getFollowers($screen_name)
{
	global $twitter;
	
	$request = ['screen_name' => $screen_name, 'stringify_ids' => 'true'];
	$response = $twitter->request('followers/ids', 'GET', $request);
	return $response->ids;
}

function getMyID($screen_name)
{
	global $twitter;
	$request = ['screen_name' => $screen_name, 'include_entities' => 'false'];
	$self = $twitter->request('users/show', 'GET', $request);

	return $self->id_str;
}

function getFriendships($ids, $is_screen_names = false)
{
	global $twitter;

	$friends = [];

	//Passed in $ids is an array of user id strings
	// We chunk it into 100s because that's the limit for users/lookup
	$id_chunks = array_chunk($ids, 100, true);

	$search_criteria = $is_screen_names ? 'screen_name' : 'user_id';
	
	//For each chunk of 100
	foreach($id_chunks as $id_chunk)
	{
		//Turn the id array into a CSV string
		$ids_csv = implode(',', $id_chunk);
		
		//Prepare the request object
		$request = [$search_criteria => $ids_csv];
		
		//Send the request and get back array of users
		$users = $twitter->request('friendships/lookup', 'GET', $request);

		//Merge it together with previous results
		$friends = array_merge($friends, $users);
	}

	//Log raw data
	$filename = "data/raw.json";
	$json = json_encode($friends);
	file_put_contents($filename, $json);
	
	//Filter, sort, and return
	// This transforms it from a PHP class to an associative array
	$data = formatFriends($friends);
	usort($data, "follows_me");
	
	return $data;
}

function formatFriends($friends)
{
	$data = [];
	foreach($friends as $u)
	{
		$i_follow = in_array('following', $u->connections);
		$follows_me = in_array('followed_by', $u->connections);
		$connections_csv = implode(',', $u->connections);
		
		$obj = [
			'id' => $u->id_str,
			'screen_name' => $u->screen_name, 
			'name' => $u->name,
			'follows_me' => $follows_me,
			'connections' => $connections_csv
		];
		
		$data[$u->screen_name] = $obj;
	}
	
	return $data;
}

function unfollow($screen_name)
{
	global $twitter;
	$request = ['screen_name' => $screen_name];
	$response = $twitter->request('friendships/destroy', 'POST', $request);
	return $response;
}

function follows_me($a,$b) {
	return $a['follows_me']<=$b['follows_me'];
}

function date_string($joined)
{
	return "{$joined['year']}-{$joined['month']}-{$joined['day']} {$joined['hour']}:{$joined['minute']}";
}
