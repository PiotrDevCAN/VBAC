/**
 *
 */
$(document).ready(function () {
	var bluepages = new Bloodhound({
		datumTokenizer: Bloodhound.tokenizers.whitespace,
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			url: '/ajax/getEmployeesList.php?query=%QUERY',
			wildcard: '%QUERY',
			filter: function (data) {
				var dataObject = $.map(data.data, function (obj) {
					var mail = typeof (obj.email) == 'undefined' ? 'unknown' : obj.email;
					return {
						value: obj.displayName,
						role: obj.businessTitle,
						preferredIdentity: obj.displayName,
						cnum: obj.cnum,
						notesEmail: 'Unknown',
						mail: mail
					};
				});
				console.log(dataObject);
				return dataObject;
			},
		}
	});

	var notesId = new Bloodhound({
		datumTokenizer: Bloodhound.tokenizers.whitespace,
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			url: '/ajax/getEmployeesList.php?query=%QUERY',
			wildcard: '%QUERY',
			filter: function (data) {
				var dataObject = $.map(data.data, function (obj) {
					var mail = typeof (obj.email) == 'undefined' ? 'unknown' : obj.mail;
					return {
						value: obj.email,
						cnum: obj.cnum,
						role: obj.businessTitle,
						preferredIdentity: obj.displayName
					};
				});
				return dataObject;
			},
		}
	});

	$('.typeahead').typeahead(null, {
		limit: 3,
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
			suggestion: Handlebars.compile('<div> <img src="./public/img/no-img.jpg" alt="Profile" height="42" width="42"> <strong>{{value}}</strong><br/><small>{{preferredIdentity}}<br/>{{role}}</small></div>')
		}
	});

	$('.typeaheadNotesId').typeahead(null, {
		limit: 3,
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
			suggestion: Handlebars.compile('<div> <img src="./public/img/no-img.jpg" alt="Profile" height="42" width="42"> <strong>{{value}}</strong><br/><small>{{preferredIdentity}}<br/>{{role}}</small></div>')
		}
	});
});