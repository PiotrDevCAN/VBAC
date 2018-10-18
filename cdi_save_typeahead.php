<?php

?>

<div class='container'>
<div id="custom-templates">
  <input class="typeahead" type="text" placeholder="Find IBMer">
</div>
</div>

<script type="text/javascript">
$(document).ready(function(){

	var bestPictures = new Bloodhound({
	      datumTokenizer: Bloodhound.tokenizers.whitespace, 
		  queryTokenizer: Bloodhound.tokenizers.whitespace,
		  remote: {
		    url: 'http://w3-services1.w3-969.ibm.com/myw3/unified-profile/v1/search/user?query=%QUERY&searchConfig=optimized_search',
		    wildcard: '%QUERY'
		  }
		});

	$('#custom-templates .typeahead').typeahead(null, {
		  name: 'best-pictures',
		  display: 'userId',
		  source: bestPictures,		 
		  templates: {
		    empty: [
		      '<div class="empty-message">',
		        'unable to find any Best Picture winners that match the current query',
		      '</div>'
		    ].join('\n'),
		    suggestion: Handlebars.compile('<div><strong>xxx</strong></div>')
		  }
		});


	
});  
</script>
<style type="text/css">
#custom-templates .empty-message {
  padding: 5px 10px;
 text-align: center;
}
</style>
