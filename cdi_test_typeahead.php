<?php



?>

<div class='container'>
<div id="custom-templates" class='col-sm-6'>
  <input class="typeahead" type="text" placeholder="Find IBMer">
</div>
<div id="custom-templates" class='col-sm-6'>
<div class='form-group'>
  <input id='notesId' type="text" placeholder="Notes Id">
</div>
<div class='form-group'>
  <input id='serial' type="text" placeholder="Serial">
</div>
<div class='form-group'>  
  <input id='role' type="text" placeholder="Role">
  </div>
<div class='form-group'>
  <input id='email' type="text" placeholder="Email">
  </div>
</div>


</div>

<!-- <script type="text/javascript" src="https://unpkg.com/@azure/msal-browser@2.28.1/lib/msal-browser.js" crossorigin=""></script> -->
<!-- <script type="module" src="vbac/java/test.js"></script> -->

<script type="text/javascript" src="https://alcdn.msauth.net/browser/2.35.0/js/msal-browser.js" crossorigin=""></script>
<script type="text/javascript" src="vbac/java/msal/authConfig.js"></script>
<script type="text/javascript" src="vbac/java/msal/auth.js"></script>

<style type="text/css">

#custom-templates .empty-message {
  padding: 5px 10px;
 text-align: center;
}

.bs-example {
	font-family: sans-serif;
	position: relative;
	margin: 100px;
}
.typeahead, .tt-query, .tt-hint {
	border: 2px solid #CCCCCC;
	border-radius: 8px;
	font-size: 22px; /* Set input font size */
	height: 30px;
	line-height: 30px;
	outline: medium none;
	padding: 8px 12px;
	width: 396px;
}
.typeahead {
	background-color: #FFFFFF;
}
.typeahead:focus {
	border: 2px solid #0097CF;
}
.tt-query {
	box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
}
.tt-hint {
	color: #999999;
}
.tt-menu {
	background-color: #FFFFFF;
	border: 1px solid rgba(0, 0, 0, 0.2);
	border-radius: 8px;
	box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
	margin-top: 12px;
	padding: 8px 0;
	width: 322px;
}
.tt-suggestion {
	font-size: 12px;  /* Set suggestion dropdown font size */
	padding: 3px 5px;
	border: 1px solid rgba(0, 0, 0, 0.2);
}
.tt-suggestion:hover {
	cursor: pointer;
	background-color: #0097CF;
	color: #FFFFFF;
}
.tt-suggestion p {
	margin: 0;
}
</style>