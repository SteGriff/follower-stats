<?php

require 'twitter-helper.php';
require 'api/api_utils.php';

header('content-type: text/plain');

function write($x)
{
	echo "$x\r\n";
}

function test($condition)
{
	if ($condition)
	{
		write("PASS");
	}
	else
	{
		write("FAIL");
		exit;
	}
}

write('array_to_csv');
$arrayOfIds = [];
$arrayOfIds[] = '123';
$arrayOfIds[] = '456';
$arrayOfIds[] = '789';
$expected = '123,456,789';
$actual = array_to_csv($arrayOfIds);
test($expected == $actual);

write('parse_csv');
$input = '123,456,789';
$expected = $arrayOfIds;
$actual = parse_csv($input);
test($expected == $actual);

