<?php
use vbac\personPortalLiteRecord;
use vbac\personPortalLiteTable;
use vbac\allTables;
use vbac\personTable;

$personRecord = new personPortalLiteRecord();
// $personTable = new personPortalLiteTable(allTables::$PERSON_PORTAL_LITE);
$personTable = new personPortalLiteTable(allTables::$PERSON);
$headerCells = $personTable->headerRowForFullDatatable();

?>
<div class='container'>
<h1 id='portalTitle'>Person Portal - Lite</h1>
<p>Records of Active employees only</p>
</div>
</div>

<div class='container-fluid'>
<h3>Person Database</h3>

<button id='reportAction' 		  class='btn btn-primary btn-sm '>Action Mode</button>
<!-- <button id='reportOffboarding' 	class='btn btn-primary btn-sm '>Offboarding Report</button> -->
<!-- <button id='reportOffboarded' 	class='btn btn-primary btn-sm '>Offboarded Report</button> -->
<button id='reportPes'    		  class='btn btn-primary btn-sm '>PES Report</button>
<button id='reportRevalidation' class='btn btn-primary btn-sm '>Revalidation Report</button>
<button id='reportMgrsCbn'      class='btn btn-primary btn-sm '>Mgrs CBN Report</button>
<button id='reportSquads'       class='btn btn-primary btn-sm '>Squad Details</button>
<button id='reportAll'  		    class='btn btn-primary btn-sm '>Show all Columns</button>
&nbsp;
<button id='reportRemoveOffb' class='btn btn-warning btn-sm '>Hide Offboarding</button>
<button id='reportReload'  		class='btn btn-warning btn-sm '>Reload Data</button>
<button id='reportReset'  		class='btn btn-warning btn-sm '>Reset</button>
<div id='personDatabaseDiv'   class='portalDiv'>
<table id='personTable'       class='table table-striped table-bordered compact' cellspacing='0' width='100%' style='display: none;'>
<thead>
<?=$headerCells;?>
</thead>
<tbody>
</tbody>
<tfoot>
<?=$headerCells;?>
</tfoot>
</table>
</div>
</div>

<?php
$person = new personPortalLiteRecord();
$person->amendPesStatusModal();
$person->amendPesLevelModal();
$person->editAgileSquadModal();
$person->portalReportSaveModal();
$person->confirmChangeFmFlagModal();
$person->confirmSendPesEmailModal();
$person->confirmOffboardingModal();
$person->confirmOffboardingInfoModal();
include_once 'includes/modalSavingBoardingDetails.html';
include_once 'includes/modalEditPerson.html';
include_once 'includes/modalEditEmailAddress.html';
include_once 'includes/modalEditCtid.html';
// include_once 'includes/modalConfirmOffboarding.html';
?>

<script type="text/javascript">

document.isCdi = <?= $_SESSION['isCdi'] ? "'yes'" : "'no'";?>;
document.isFm = <?= $_SESSION['isFm'] ? "'yes'" : "'no'";?>;
document.tableType = '<?=personTable::PORTAL_ONLY_ACTIVE ?>';

</script>
<style>
  .toolTipDetails {
    width:  600px;
    max-width: 600px;
    overflow:auto;
  } 
  .toolTipDetails p {
      margin: 0;
      font-weight: bold;
  }
</style>