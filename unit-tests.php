<?php

require 'twitter-helper.php';

require 'api/api_utils.php';

function write($x)
{
	echo "$x\r\n";
}

write('array_to_csv');
$arrayOfIds = [];
$arrayOfIds[] = '123';
$arrayOfIds[] = '456';
$arrayOfIds[] = '789';
$expected = '123,456,789';
$actual = array_to_csv($arrayOfIds);
assert($expected == $actual);

write('parse_csv');
$input = '123,456,789';
$expected = $arrayOfIds;
$actual = parse_csv($input);
assert($expected == $actual);

