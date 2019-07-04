<?php
use itdq\Trace;

Trace::pageOpening($_SERVER['PHP_SELF']);



Trace::pageLoadComplete($_SERVER['PHP_SELF']);
