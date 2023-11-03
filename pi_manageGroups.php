<?php

use itdq\FormClass;
use itdq\Trace;
use vbac\staticOKTAGroupRecord;
use vbac\staticOKTAGroupTable;

?>
<div class='container'>
<h2>Define Okta Group employee record</h2>
<?php

Trace::pageOpening($_SERVER['PHP_SELF']);

$record = new staticOKTAGroupRecord();
$record->displayForm(FormClass::$modeDEFINE);
?>
</div>

<div class='container'>
	<h2>Manage Okta Groups Assignment</h2>
	<?php
		$table = new staticOKTAGroupTable();
		$table->displayPills();
		$table->displayPillsTables();
	?>
</div>
<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);