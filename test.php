<?php

// $string = "some words font-size:16px more words table width=\"50%\"";
// echo $string;


// $newString = preg_replace(array('/width="[0-9]{2}%"/','/[0-9]{2}px/'), array('width="100%"','8px'), $string);


$string = 	'{"subject":"PES Request - Fred Smith","sent":false,"locked":false,"status":"Email queued for delivery","prevStatus":"first"}';

$newString = preg_replace(array('/"([A-z]+)":/','/,/'), array('<b>\1</b>:','<br/>'), $string);


echo "<br/>";
echo $string;
echo "<br/>";
echo $newString;