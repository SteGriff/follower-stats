<?php

	/*
		File utils
	*/
	function get_or_create_list_file($filename)
	{
		logline("Decode or create $filename");
		$entities = [];
		if (file_exists($filename))
		{
			logline("$filename exists; decoding...");
			$data = file_get_contents($filename);
			$entities = parse_list($data);
		}
		else
		{
			logline("Creating...");
			//Create empty file
			touch($filename);
		}
		return $entities;
	}
	
	/*
		Convert between list of newline-separated strings and an array
	*/
	function array_to_list_file($array)
	{
		return implode("\n", $array);
	}
	
	function parse_list($data)
	{
		$array = preg_split("/\r\n|\n|\r/", $data);
		return $array;
	}
	
	/*
		Convert between comma-separated IDs and array
	*/
	function array_to_csv($array)
	{
		return implode(',', $array);
	}
	
	function parse_csv($csv)
	{
		return explode(',', $csv);
	}
	
	/*
		Logging
	*/
	function logline($x)
	{
		global $log;
		echo "$x\r\n";
		$log .= "$x\r\n";
	}
	