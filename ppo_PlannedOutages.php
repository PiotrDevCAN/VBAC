<?php
use itdq\PlannedOutages;

echo "<div class='container'>";

echo "<div id='messagePlaceholder'>";
echo "</div>";

$plannedOutages = new PlannedOutages();
include ('UserComms/responsiveOutages_V2.php');
$plannedOutages->displayOutages();
echo "</div>";