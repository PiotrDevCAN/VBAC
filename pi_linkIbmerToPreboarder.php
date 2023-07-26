<?php
use vbac\personRecord;
use itdq\FormClass;
use itdq\Loader;
use vbac\allTables;

set_time_limit(0);

?>
<div class='container'>
<div class='row'>
<div class='col-sm-2'></div>
<div class='col-sm-8'>
<h2>Link Reg to Pre-Boarder</h2>
</div>
</div>

<div class='row'>
<div class='col-sm-2'></div>
<div class='col-sm-8'>

<?php
$mode = personRecord::$modeDEFINE;
$person = new personRecord();
$person->displayLinkForm($mode);
?>
</div>
<div class='col-sm-2'></div>
</div>
</div>