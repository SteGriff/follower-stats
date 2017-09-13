<?php

	function get_or_create_list_file($filename)
	{
		logline("Decode or create $filename");
		$entities = [];
		if (file_exists($filename))
		{
			logline("Exists; decoding...");
			$data = file_get_contents($filename);
			logline("Content: $data");
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
	
	function array_to_list_file($array)
	{
		return implode("\n", $array);
	}
	
	function parse_list($data)
	{
		$array = preg_split("/\r\n|\n|\r/", $data);
		return $array;
	}
	
	function logline($x)
	{
		global $log;
		echo "$x\r\n";
		$log .= "$x\r\n";
	}
	