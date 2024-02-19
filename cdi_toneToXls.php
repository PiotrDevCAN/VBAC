<?php



?>

<style>

#drop-area {
  border: 2px dashed #ccc;
  border-radius: 20px;
  width: 680px;
  font-family: sans-serif;
  padding: 20px;
  background-color:#eeeeee 
}
</style>


 
<div class='container'>

<div class='row'>

<div id='toneTableDiv' class='col-sm-offset-2 col-sm-8' style='display:none'>

<table id='toneTable' class='table table-stripped'>
<thead><tr><th>Text</th><th>Anger</th><th>Fear</th><th>Joy</th><th>Sadness</th><th>Analytical</th><th>Confident</th><th>Tentative</th></tr><template></template></thead>
<tbody></tbody>
<tfoot><tr><th>Text</th><th>Anger</th><th>Fear</th><th>Joy</th><th>Sadness</th><th>Analytical</th><th>Confident</th><th>Tentative</th></tr><template></template></tfoot>
</table>

</div> 
</div>


<div class='row'>

<div id="drop-areaDiv" class='col-sm-offset-2 col-sm-8' style='display:block'>
<h5>Drop Tone output here:</h5>
<div id="drop-area" contenteditable style='display:block'>
</div>
<div class='col-sm-offset-2 col-sm-2'>
<button id='processTone' class='btn btn-primary'>Process json</button>

</div>
</div> 
</div>
</div>

<style>
.normalanger { background-color:#ffa197; }
.stronganger { background-color:#e80521; 
               color:#ffffff; }


.normalanalytical { background-color:#19a3f7; } 
.stronganalytical { background-color:#075cd8; 
               color:#ffffff; }
               
.normalconfident { background-color:#a779d8; } 
.strongconfident { background-color:#592684; 
               color:#ffffff; }               
               
.normalfear { background-color:#7db258; } 
.strongfear { background-color:#325e2b; 
               color:#ffffff; }  
               
.normaltentative { background-color:#94ffef; } 
.strongtentative { background-color:#1ae5cd ; 
               color:#000000; }                 

</style>

<script type="text/javascript">

var buttonCommon = {
		exportOptions: {
	    	format: {
	        	body: function ( data, row, column, node ) {
	            	  return data ? data.replace( /<br\s*\/?>/ig, "\n").replace(/(&nbsp;|<([^>]+)>)/ig, "") : data ;
	        	}
	    	}
		}
	}



$(document).on('click','#processTone',function(e){

	var toneText = $('#drop-area').text();

    $.ajax({
    	url: "ajax/produceXlsFromToneData.php",
    	type: 'POST',
    	data: {tonetext:toneText},
    	success: function(result){
    		var resultObj = JSON.parse(result);
    		var response = resultObj.response;
    		var messages = resultObj.messages;   
			if(messages){
				$('#drop-area').text(messages);  
			} else {
				$('#drop-area').text('Now click Download CSV');
			}
     		$('#downloadCSV').attr('href','/ajax/toneAnalysis.csv');
     		$('#processTone').attr('disabled',true);

			resultObj.tablerows.forEach(function(value, index, array){
					$('#toneTable > tbody').append(value);
				});
	
			$('#toneTable').DataTable({
				responsive: true,
				dom: 'Blfrtip',
		    	buttons: [
		            'colvis',
		            $.extend( true, {}, buttonCommon, {
		                extend: 'excelHtml5',
		                exportOptions: {
		                    orthogonal: 'sort',
		                    stripHtml: true,
		                    stripNewLines:false
		                },
		                 customize: function( xlsx ) {
		                     var sheet = xlsx.xl.worksheets['sheet1.xml'];
		                 }
		        }),
		        $.extend( true, {}, buttonCommon, {
		            extend: 'csvHtml5',
		            exportOptions: {
		                orthogonal: 'sort',
		                stripHtml: true,
		                stripNewLines:false
		            }
		        }),
		        $.extend( true, {}, buttonCommon, {
		            extend: 'print',
		            exportOptions: {
		                orthogonal: 'sort',
		                stripHtml: true,
		                stripNewLines:false
		            }
		        })
		        ]
				});

			$('#toneTableDiv').show();
			$('#drop-areaDiv').hide();
     		
     	}
    });

	
});



</script>
