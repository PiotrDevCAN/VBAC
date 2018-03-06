<?php

$string =  " Hello World this is a string";

var_dump($string);

$encoded = urlencode($string);

var_dump($encoded);

$raw = rawurlencode($string);

var_dump($raw);

$decodeRaw = urldecode($raw);

var_dump($decodeRaw);
