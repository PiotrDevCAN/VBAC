<?php
use itdq\Loader;
use itdq\BluePages;

ob_start();

$loader = new Loader();
$allCnum = $loader->load('CNUM',"CNUM4BP");

$chunkedCnum = array_chunk($allCnum, 500);
$detailsFromBp = "&manager&worklocation&employeetype&notesid";
$justNotesid = "&notesid";

$employeetype = '';
$worklocation = '';
$manager = '';
$notesid = '';

echo "<div class='container-fluid'>";
ob_start();
foreach ($chunkedCnum as $key => $cnumList){
    set_time_limit(20);
    $jsonObjects[$key] = BluePages::getDetailsFromCnumSlapMulti($cnumList, $detailsFromBp);
    set_time_limit(20);

    foreach ($jsonObjects[$key]->search->entry as $bpEntry){
        $serial = substr($bpEntry->dn,4,9);
        foreach ($bpEntry->attribute as $details){
            $name = trim($details->name);
            $$name = trim($details->value[0]);
        }
        $notesIdLookup[$serial] = str_replace(array('CN=','OU=','O='),array('','',''),$notesid);
        $managerSerial = substr($manager,4,9);
        $managers[$managerSerial] = $managerSerial;
        $worklocations[substr($worklocation,8,3)] = substr($worklocation,8,3);
    }
}

$missingManagers = array_diff($managers, $allCnum);
$chunkedManagers = array_chunk($missingManagers,500);

foreach ($chunkedManagers as $key => $missingMgrs){
    $missingMgrNotesids[] = BluePages::getDetailsFromCnumSlapMulti($missingMgrs, $detailsFromBp);
}

foreach ($missingMgrNotesids as $missingMgrNotesid ) {
    foreach ($missingMgrNotesid->search->entry as $bpEntry){
        $serial = substr($bpEntry->dn,4,9);
        foreach ($bpEntry->attribute as $details){
            $name = trim($details->name);
            $$name = trim($details->value[0]);
        }
        $notesIdLookup[$serial] = str_replace(array('CN=','OU=','O='),array('','',''),$notesid);
    }
}

// Lookup locations now.

$json = Bluepages::lookupLocations($worklocations);


$allLocations = $json->search->entry;

$workloc = '';
$c = '';
$l = '';

$allLocationDetails = array();
foreach ($allLocations as $location){
    $workloc = '';
    $c = '';
    $l = '';
    $address1 = '';
    foreach ($location->attribute as $attribute){
        $name = $attribute->name;
        $$name = $attribute->value[0];
    }
    $allLocationDetails[$workloc] = array('country'=>$c,'city'=>$l,'address'=>$address1);
}


ob_clean();

echo "<table id='bpDetails' class='table table-striped table-bordered compact' ><thead><th>Serial</th><th>Notes Id</th><th>Manager</th><th>Manager Notesid</th><th>Employee Type</th><th>Work Location</th><th>Address</th></thead>";
echo "<tbody>";

foreach ($jsonObjects as $key => $jsonObject){
    foreach ($jsonObject->search->entry as $bpEntry){
        $serial = substr($bpEntry->dn,4,9);
        echo "<tr><td>" . $serial . "</td>";
        foreach ($bpEntry->attribute as $details){
                $name = $details->name;
                $$name = $details->value[0];
        }

        $managerSerial = substr($manager, 4,9);
        $workLocationCode = substr($worklocation,8,3);
        $notesid = str_replace( array('CN=','OU=','O='),array('','',''),$notesid);
        $managerNotesid = isset($notesIdLookup[$managerSerial]) ? $notesIdLookup[$managerSerial] : "Not known";
        $managersToFind[] = $managerSerial;

        echo "<td>". $notesid . "</td><td>". $managerSerial . "</td><td>" . $managerNotesid . "</td><td>" . $employeetype . "</td><td>" . $workLocationCode . "</td>";
        echo "<td style='font-size:75%'>";
        echo isset($allLocationDetails[$workLocationCode]['address']) ? $allLocationDetails[$workLocationCode]['address']  : null;
        echo "<br/>";
        echo isset($allLocationDetails[$workLocationCode]['city']) ? $allLocationDetails[$workLocationCode]['city']  : null;
        echo "<br/>";
        echo isset($allLocationDetails[$workLocationCode]['country']) ? $allLocationDetails[$workLocationCode]['country']  : null;
        echo "</td>";
        echo "</tr>";
    }
}

echo "</tbody>";
echo "<tfoot><th>Serial</th><th>Notes Id</th><th>Manager</th><th>Manager Notesid</th><th>Employee Type</th><th>Work Location</th><th>Address</th></tfoot>";
echo "</table>";
echo "</div>";
?>


<script type="text/javascript">


var table;


$(document).ready(function() {

    // Setup - add a text input to each footer cell
    $('#bpDetails tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );

	table = $('#bpDetails').DataTable({
		autoWidth: false,
		deferRender: true,
		responsive: false,
		processing: true,
		responsive: true,
		colReorder: true,
		dom: 'Blfrtip',
	    buttons: [
              	'colvis',
              	'excelHtml5',
              	'csvHtml5',
              	'print'
          	],
	});

	console.log(table);


	table.columns().every( function () {
    	var that = this;
    	$( 'input', this.footer() ).on( 'keyup change', function () {
	        if ( that.search() !== this.value ) {
            	that
	                .search( this.value )
                	.draw();
        		}
    		} );
	});

});



</script>

