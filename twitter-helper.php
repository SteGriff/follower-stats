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

function getFriendConnections($username)
{
	//$filename = "data/$username-friends.json";
	
	//Get all friends IDs
	$ids = getFriends($username);
	$friends = getFriendships($ids);

	//Log the result
	// $json = json_encode($friends);
	// file_put_contents($filename, $json);

	return $friends;
}

function getFollowerConnections($username)
{
	// $filename = "data/$username-followers.json";
	
	//Get all followers IDs
	$ids = getFollowers($username);
	//file_put_contents('getFollowConnections_ids', json_encode($ids));
	
	$friends = getFriendships($ids);

	//Log the result
	// $json = json_encode($friends);
	// file_put_contents($filename, $json);

	return $friends;
}

function getRateLimitJson()
{
	global $twitter;
	$request = ['resources' => 'friends,followers,users'];
	$response = $twitter->request('application/rate_limit_status', 'GET', $request);
	return json_encode($response, JSON_PRETTY_PRINT);
}

function getFriends($screen_name)
{
	global $twitter;
	
	$request = ['screen_name' => $screen_name, 'stringify_ids' => 'true'];
	$response = $twitter->request('friends/ids', 'GET', $request);
	return $response->ids;
}

function getFollowers($id, $is_screen_names = true)
{
	global $twitter;
	
	$search_criteria = $is_screen_names ? 'screen_name' : 'user_id';
	
	$request = [$search_criteria => "$id", 'stringify_ids' => 'true'];
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
		try
		{
			$chunk_friends = $twitter->request('friendships/lookup', 'GET', $request);
			$friends = array_merge($friends, $chunk_friends);
		}
		catch (Exception $ex)
		{
			//Rate limit met on this chunk,
			// let's give up and stick with what we've got
			break;
		}
	}

	//Log raw data
	//$filename = "data/raw.json";
	//$json = json_encode($friends);
	//file_put_contents($filename, $json);
	
	//Filter, sort, and return
	// This transforms it from a PHP class to an associative array
	$data = formatFriends($friends);
	
	return $data;
}

function formatFriends($friends)
{
	// For formatting data from friendships/lookup
	
	$data = [];
	foreach($friends as $u)
	{
		$i_follow = in_array('following', $u->connections);
		$follows_me = in_array('followed_by', $u->connections);
		$follow_requested = in_array('following_requested', $u->connections);
		$connections_csv = implode(',', $u->connections);
		
		$obj = [
			'id' => $u->id_str,
			'screen_name' => $u->screen_name, 
			'name' => $u->name,
			'follows_me' => $follows_me,
			'i_follow' => $i_follow,
			'follow_requested' => $follow_requested,
			'connections' => $connections_csv
		];
		
		$data[$u->screen_name] = $obj;
	}
	
	// echo "formatFriends output\r\n";
	// var_dump($data);
	
	return $data;
}

/*
	Users, not connections
*/

function getAllFollowersInfo($username)
{
	$friends = [];

	//Get all followers IDs
	$ids = getFollowers($username);
	
	//Get mostly-hydrated users (objects shrunk using formatUser)
	$friends = getUsers($ids);
	
	return $friends;
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
		
		try
		{
			//Send the request and get back array of users
			$users = $twitter->request('users/lookup', 'GET', $request);

			//Get the interesting fields from the users
			$users_good_bits = formatUser($users);
			
			//Merge it together with previous results
			$data = array_merge($data, $users_good_bits);
		}
		catch (Exception $ex)
		{
			//Rate limit, leave with what we got so far
			break;
		}
	}

	return $data;
}

function formatUser($users)
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
			'followers_count' => $u->followers_count,
			'following_count' => $u->friends_count,
			'deficit' => $u->friends_count > $u->followers_count,
			'time_zone' => $u->time_zone,
			'location' => $u->location
		];
		$data[] = $person;
	}
	return $data;
}

/*
	Follow and unfollow actions
*/

function follow($screen_name)
{
	global $twitter;
	$request = ['screen_name' => $screen_name];
	$response = $twitter->request('friendships/create', 'POST', $request);
	return $response;
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
