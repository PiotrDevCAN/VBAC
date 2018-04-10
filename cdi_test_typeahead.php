<?php



?>

<div class='container'>
<div id="custom-templates">
  <input class="typeahead" type="text" placeholder="Find IBMer">
</div>
</div>

<script type="text/javascript">
$(document).ready(function(){

	var bluepages = new Bloodhound({
	      datumTokenizer: Bloodhound.tokenizers.whitespace,
		  queryTokenizer:  function (d){
		      console.log(d);
			  return  Bloodhound.tokenizers.whitespace(d);
	      }, 
	      identify: function(obj) {
			  console.log(obj)
		      return obj;
		  },	      
		  remote: {
		    url: 'http://w3-services1.w3-969.ibm.com/myw3/unified-profile/v1/search/user?query=%QUERY&searchConfig=optimized_search',
		    wildcard: '%QUERY',
		    filter: function(data) {
		        // assume data is an array of strings e.g. ['one', 'two', 'three']
		        console.log(data);
		        console.log(data.results);
		        return $.map(data.results, function(obj) { return { value: obj.callupName, role: obj.role }; });
		      },
		  }
		});

	$('#custom-templates .typeahead').typeahead(null, {
		  name: 'bluepages',
		  display: 'value',
		  source: bluepages,		 
		  templates: {
		    empty: [
		      '<div class="empty-message">',
		        'unable to find any Best Picture winners that match the current query',
		      '</div>'
		    ].join('\n'),
		    suggestion: Handlebars.compile('<div><strong>{{value}}</strong><br/>{{role}}</div>')
		  }
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
	width: 422px;
}
.tt-suggestion {
	font-size: 22px;  /* Set suggestion dropdown font size */
	padding: 3px 20px;
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