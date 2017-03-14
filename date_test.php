<?php

$date_strings = [
				'Wed May 23 06:01:13 +0000 2007',
				'Tue Feb 20 14:35:54 +0000 2007',
				'Tue Nov 17 10:00:00 +0000 2015',
				'Thu Jun 12 10:00:00 +0000 2014'
				];

$dates = [];

foreach($date_strings as $ds)
{
	$dates[] = date_parse($ds);
}

header('content-type: text/plain;');

//var_dump($dates);
asort($dates);

foreach($dates as $d)
{
	echo "{$d['year']}-{$d['month']}-{$d['day']}\r\n";
}

?>