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

<script type="text/javascript">
$(document).ready(function(){

	var selectedUser;

	var bluepages = new Bloodhound({
	      datumTokenizer: Bloodhound.tokenizers.whitespace,
		  queryTokenizer:  Bloodhound.tokenizers.whitespace, 
	      identify: function(obj) {
			  console.log(obj)
		      return obj;
		  },	      
		  remote: {
			//  http://unified-profile.w3ibm.mybluemix.net
			//	http://w3-services1.w3-969.ibm.com
			//  http://w3-services1.w3-969.ibm.com/myw3/unified-profile/v1/search/user
			//  https://unified-profile-search-service.us-south-k8s.intranet.ibm.com/search
		    //  https://unified-profile-search-service.us-south-k8s.intranet.ibm.com/search?query=%QUERY&searchConfig=optimized_search
			url: 'https://w3-unifiedprofile-search.dal1a.cirrus.ibm.com/search?query=%QUERY&searchConfig=optimized_search',
			wildcard: '%QUERY',
		    filter: function(data) {  
			        
		        var dataObject = $.map(data.results, function(obj) {
					console.log(obj.mail);			        
					 var mail = typeof(obj.mail)=='undefined' ? 'unknown' : obj.mail[0];
			         return { value: obj.nameFull, role: obj.role, preferredIdentity: obj.preferredIdentity, cnum:obj.id, notesEmail:obj.notesEmail, mail:mail }; });
		        console.log(dataObject);  
			    return dataObject;
		      },
		  }
		});

	$('#custom-templates .typeahead').typeahead(null, {
		limit : 3,
		name: 'bluepages',
		display: 'value',
		displayKey: 'value',
		source: bluepages,		 
		templates: {
		empty: [
			'<div class="empty-message">',
			'unable to find any IBMers that match the current query',
			'</div>'
			].join('\n'),
		suggestion: Handlebars.compile('<div> <img src="https://w3-unifiedprofile-api.dal1a.cirrus.ibm.com/v3/image/{{cnum}}?type=bp&def=blue&s=50" alt="Profile" height="42" width="42"> <strong>{{value}}</strong><br/><small>{{preferredIdentity}}<br/>{{role}}</small></div>')
		}
	});

 	$('.typeahead').bind('typeahead:select', function(ev, suggestion) {
 		 $('#notesId').val(suggestion.notesEmail);
 		 $('#serial').val(suggestion.cnum);
 		 $('#role').val(suggestion.role);
 		 $('#email').val(suggestion.mail);
		console.log(suggestion.mail);		
 		});
	
	
});  
</script>
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