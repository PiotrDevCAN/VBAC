/**
 *
 */
$(document).ready(function () {
	var workerAPI = new Bloodhound({
		datumTokenizer: Bloodhound.tokenizers.whitespace,
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			url: '/ajax/getEmployeesList.php?query=%QUERY',
			wildcard: '%QUERY',
			filter: function (data) {
				var dataObject = $.map(data.data, function (obj) {
					obj.value = obj.displayName;
					obj.role = obj.businessTitle;
					obj.preferredIdentity = obj.displayName;
					obj.jobresponsibilities = obj.businessTitle;
					obj.notesEmail = 'No longer available';
					obj.mail = obj.email;
					return obj;
				});
				// console.log(dataObject);
				return dataObject;
			},
		}
	});

	$('.typeahead').typeahead(null, {
		name: 'workerAPI',
		display: 'value',
		displayKey: 'value',
		limit: 3,
		source: workerAPI,
		templates: {
			empty: [
				'<div class="empty-messagexx">',
				'unable to find any Kyndryl employees that match the current query',
				'</div>'
			].join('\n'),
			suggestion: Handlebars.compile('<div> <img src="./public/img/no-img.jpg" alt="Profile" height="42" width="42"> <strong>{{value}}</strong><br/><small>{{preferredIdentity}}<br/>{{role}}</small></div>')
		}
	});

	/*
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
	*/

	/*
	$('.typeaheadNotesId').typeahead(null, {
		name: 'notesId',
		display: 'value',
		displayKey: 'value',
		limit: 3,
		source: notesId,
		templates: {
			empty: [
				'<div class="empty-messagexx">',
				'unable to find any Kyndryl employees that match the current query',
				'</div>'
			].join('\n'),
			suggestion: Handlebars.compile('<div> <img src="./public/img/no-img.jpg" alt="Profile" height="42" width="42"> <strong>{{value}}</strong><br/><small>{{preferredIdentity}}<br/>{{role}}</small></div>')
		}
	});
	*/
});