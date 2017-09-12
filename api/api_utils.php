<?php

	function decode_or_create_json_file($filename)
	{
		$entities = [];
		if (file_exists($filename))
		{
			$data = file_get_contents($filename);
			$entities = json_decode($data, true);
		}
		else
		{
			//Create empty file
			touch($filename);
		}
		return $entities;
	}
	