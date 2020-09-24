/**
 *
 */
$(document).ready(function(){
	var bluepages = new Bloodhound({
	      datumTokenizer: Bloodhound.tokenizers.whitespace,
		  queryTokenizer: Bloodhound.tokenizers.whitespace,
		  remote: {
			//    http://unified-profile.w3ibm.mybluemix.net
			//	  http://w3-services1.w3-969.ibm.com
			url: 'https://w3-services1.w3-969.ibm.com/myw3/unified-profile/v1/search/user?query=%QUERY&searchConfig=optimized_search',
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
	
	var notesId = new Bloodhound({
	      datumTokenizer: Bloodhound.tokenizers.whitespace,
		  queryTokenizer: Bloodhound.tokenizers.whitespace,
		  remote: {
			//    http://unified-profile.w3ibm.mybluemix.net
			//	  http://w3-services1.w3-969.ibm.com
			url: 'https://w3-services1.w3-969.ibm.com/myw3/unified-profile/v1/search/user?query=%QUERY&searchConfig=optimized_search',
		    wildcard: '%QUERY',
		    filter: function(data) {

		        var dataObject = $.map(data.results, function(obj) {
					console.log(obj.mail);
					 var mail = typeof(obj.mail)=='undefined' ? 'unknown' : obj.mail[0];
			         return { value: obj.notesEmail, cnum:obj.id, role: obj.role, preferredIdentity: obj.preferredIdentity }; });
		        console.log(dataObject);
			    return dataObject;
		      },
		  }
		});
	

	$('.typeahead').typeahead(null, {
		  limit : 3,
		  name: 'bluepages',
		  display: 'value',
		  displayKey: 'value',
		  source: bluepages,
		  templates: {
		    empty: [
		      '<div class="empty-messagexx">',
		        'unable to find any IBMers that match the current query',
		      '</div>'
		    	].join('\n'),
		  	suggestion: Handlebars.compile('<div> <img src="https://w3-services1.w3-969.ibm.com/myw3/unified-profile-photo/v1/image/{{cnum}}?type=bp&def=blue&s=50" alt="Profile" height="42" width="42"> <strong>{{value}}</strong><br/><small>{{preferredIdentity}}<br/>{{role}}</small></div>')
		  }
		});
	
	$('.typeaheadNotesId').typeahead(null, {
		  limit : 3,
		  name: 'notesId',
		  display: 'value',
		  displayKey: 'value',
		  source: notesId,
		  templates: {
		    empty: [
		      '<div class="empty-messagexx">',
		        'unable to find any IBMers that match the current query',
		      '</div>'
		    	].join('\n'),
		  	suggestion: Handlebars.compile('<div> <img src="https://w3-services1.w3-969.ibm.com/myw3/unified-profile-photo/v1/image/{{cnum}}?type=bp&def=blue&s=50" alt="Profile" height="42" width="42"> <strong>{{value}}</strong><br/><small>{{preferredIdentity}}<br/>{{role}}</small></div>')
		  }
		});
	
	

// 	$('.typeahead').bind('typeahead:select', function(ev, suggestion) {
// 		 $('#notesId').val(suggestion.notesEmail);
// 		 $('#serial').val(suggestion.cnum);
// 		 $('#role').val(suggestion.role);
// 		 $('#email').val(suggestion.mail);
//		console.log(suggestion.mail);
// 		});


});