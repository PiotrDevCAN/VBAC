<?php
use vbac\staticDataTable;
use vbac\staticDataRolesTable;

?>
<div class='container'>
<?php

staticDataTable::setScreenTabs();

staticDataTable::editStaticDataTable();

staticDataRolesTable::editGroupsForRoles();

staticDataTable::amendStaticDataModal();
?>

<script type="text/javascript">
$('document').ready(function() {

	var StaticDataTable = new staticDataTable();

	StaticDataTable.listenForSelectStaticData();
	StaticDataTable.listenForSelectGroupsForRoles();
//	StaticDataTable.listenForSelectGroupDetails();

	StaticDataTable.listenForEditRecord();
	StaticDataTable.listenForNewEntry();
	StaticDataTable.listenForSaveAmendedStaticData();

	StaticDataTable.initialiseDataTable();

});
</script>