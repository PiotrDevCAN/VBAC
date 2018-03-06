<?php

$projectedEndDate = DateTime::createFromFormat('Y-m-d', '2018-12-31');


var_dump($projectedEndDate);


$projectedEndDate = DateTime::createFromFormat('Y-m-d', '2018-31-12');


var_dump($projectedEndDate);
