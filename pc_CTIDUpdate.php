<?php
use vbac\personTable;
use vbac\dlpRecord;
use vbac\odcAccessTable;

set_time_limit(0);
ob_start();
?>

<style>
#drop-area {
  border: 2px dashed #ccc;
  border-radius: 20px;
  width: 480px;
  font-family: sans-serif;
  margin: 100px auto;
  padding: 20px;
}
#drop-area.highlight {
  border-color: purple;
}
p {
  margin-top: 0;
}
.my-form {
  margin-bottom: 10px;
}
.button {
  display: inline-block;
  padding: 10px;
  background: #ccc;
  cursor: pointer;
  border-radius: 5px;
  border: 1px solid #ccc;
}
.button:hover {
  background: #ddd;
}
#fileElem {
  display: none;
}
</style>

<div class='container'>
<div class='row'>
<div class='col-sm-offset-2 col-sm-8'>
<h2>CT ID Data Upload</h2>

<p>The update process requires two CNUM and NEW_CT_ID columns to be provided in an Excel spreadsheet.</p>

<div id="drop-area">
  <form class="my-form">
    <p>To upload and CT ID Data XLS file simply drag and drop it onto the dashed region</p>
    <input type="file" id="fileElem" multiple accept="image/*" onchange="handleFiles(this.files)">
    <label class="button" for="fileElem">or Select File here</label>
  </form>
</div>
</div>
</div>
</div>
<script>

let dropArea = document.getElementById('drop-area');
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
  dropArea.addEventListener(eventName, preventDefaults, false)
})

function preventDefaults (e) {
  e.preventDefault()
  e.stopPropagation()
};
['dragenter', 'dragover'].forEach(eventName => {
	  dropArea.addEventListener(eventName, highlight, false)
});
['dragleave', 'drop'].forEach(eventName => {
	  dropArea.addEventListener(eventName, unhighlight, false)
})

function highlight(e) {
	dropArea.classList.add('highlight')
}

function unhighlight(e) {
	dropArea.classList.remove('highlight')
}

dropArea.addEventListener('drop', handleDrop, false)

function handleDrop(e) {
	let dt = e.dataTransfer
	let files = dt.files

	handleFiles(files)
}

function handleFiles(files) {
	([...files]).forEach(uploadFile)
}

function uploadFile(file) {
	var url = 'ajax/upload_ctid.php';
	var xhr = new XMLHttpRequest();
	var formData = new FormData();

	xhr.open('POST', url, true);

	xhr.addEventListener('readystatechange', function(e) {
		if (xhr.readyState == 4 && xhr.status == 200) {
			// Done. Inform the user
			var responseText = xhr.responseText;
			responseText += "<br/>Upload to DB2 starting.<br/>This can take several minutes (load runs at circa 4 Rows/Sec)<br/>";
			responseText += "<i class='fa fa-spinner fa-spin' style='font-size:24px'></i>";
			$('#drop-area').html(responseText);
			var filename = file.name;
			console.log(filename);			  
			$.ajax({
				url: "ajax/copyCTIDDataXlsxIntoDb2.php",
				type: 'POST',
				data: {filename: filename},
				success: function(result){
					console.log(result);
					$('#drop-area').html(result);
				}
			});
				
		} else if (xhr.readyState == 4 && xhr.status != 200) {
			// Error. Inform the user
			var responseText = xhr.responseText;
			responseText += "<br/>Error has occured, inform support";
			$('#drop-area').html(responseText);
		}
	})

	formData.append('file', file)
	xhr.send(formData)
}
</script>