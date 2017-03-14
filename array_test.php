<?php

$array = ['dog', 'cat'];
$more = ['rat', 'fish'];
$array = array_merge($array, $more);

header('content-type: text/plain;');
var_dump($array);

?>