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

function getConnectionInfo($username)
{
	$filename = "data/$username.json";
	$friends = null;
	
	//If cached result exists, load it
	if (file_exists($filename))
	{
		$cached = file_get_contents($filename);
		$friends = json_decode($cached, true);
	}
	else
	{
		//Send a new req to Twitter API
		
		//Get all friends IDs
		$ids = getFriends($username);
		
		//Get own ID as well
		//$ids[] = getMyID($username);
		
		$friends = getFriendships($ids);
		
		//Save the result
		$json = json_encode($friends);
		file_put_contents($filename, $json);
	}

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

function getMyID($screen_name)
{
	global $twitter;
	$request = ['screen_name' => $screen_name, 'include_entities' => 'false'];
	$self = $twitter->request('users/show', 'GET', $request);

	return $self->id_str;
}

function getFriendships($ids)
{
	global $twitter;

	$friends = [];

	//Passed in $ids is an array of user id strings
	// We chunk it into 100s because that's the limit for users/lookup
	$id_chunks = array_chunk($ids, 100, true);

	//For each chunk of 100
	foreach($id_chunks as $id_chunk)
	{
		//Turn the id array into a CSV string
		$ids_csv = implode(',', $id_chunk);
		
		//Prepare the request object
		$request = ['user_id' => $ids_csv];
		
		//Send the request and get back array of users
		$users = $twitter->request('friendships/lookup', 'GET', $request);

		//Merge it together with previous results
		$friends = array_merge($friends, $users);
	}

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
		
		$obj = [
			'screen_name' => $u->screen_name, 
			'name' => $u->name,
			'follows_me' => $follows_me
		];
		
		$data[] = $obj;
	}
	
	return $data;
}

function getUsers($ids)
{
	global $twitter;

	$data = [];

	//Passed in $ids is an array of user id strings
	// We chunk it into 100s because that's the limit for users/lookup
	$id_chunks = array_chunk($ids, 100, true);
	
	//For each chunk of 100
	foreach($id_chunks as $id_chunk)
	{
		//Turn the id array into a CSV string
		$ids_csv = implode(',', $id_chunk);
		
		//Prepare the request object
		$request = ['user_id' => $ids_csv];
		
		//Send the request and get back array of users
		$users = $twitter->request('users/lookup', 'GET', $request);

		//Get the interesting fields from the users
		$users_good_bits = good_bits($users);
		
		//Merge it together with previous results
		$data = array_merge($data, $users_good_bits);
	}

	return $data;
}

function good_bits($users)
{
	$data = [];
	foreach ($users as $u)
	{
		$joined = date_parse($u->created_at);
		
		$person = [
			'screen_name' => $u->screen_name, 
			'name' => $u->name,
			'joined' => $joined,
			'id' => $u->id_str,
			'follows_me' => $u->following,
			'num_followers' => $u->followers_count,
			'num_i_follow' => $u->friends_count,
			'surplus' => $u->followers_count >= $u->friends_count,
			'ratio' => $u->followers_count / $u->friends_count
		];
		$data[] = $person;
	}
	return $data;
}

function follows_me($a,$b) {
	return $a['follows_me']<=$b['follows_me'];
}

function date_string($joined)
{
	return "{$joined['year']}-{$joined['month']}-{$joined['day']} {$joined['hour']}:{$joined['minute']}";
}
