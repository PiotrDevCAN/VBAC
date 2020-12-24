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

<div class='col-sm-offset-2 col-sm-8'>
<h5>Drop Tone output here:</h5>
<div id="drop-area" contenteditable style='display:block'>
</div>
<div col-sm-offset-2 col-sm-2'>
<button id='processTone' class='btn btn-primary'>Produce CSV</button>
<button class='btn btn-secondary' ><a id='downloadCSV'  download>Download CSV</a></button>
</div>
</div> 
</div>
</div>



<script>

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
     	}
    });

	
});



</script>
