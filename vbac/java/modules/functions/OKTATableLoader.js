/**
 *
 */

function OKTATableLoader(tableId, groupName) {
	return new Promise((onFulfilled, onReject) => {

		if (!tableId) {
			onReject("No Table Id provided");
		}

		if (!groupName) {
			onReject("No Group Name provided");
		}

		$('#' + tableId)
			.on('error.dt', function () {
				onReject(new Error('DT failed'));
			});

		// DataTable
		let table = $('#' + tableId).DataTable({
			initComplete: (settings, json) => {
				console.log('DataTables ' + tableId + ' ' + groupName + ' has finished its initialisation.');
				onFulfilled(table);
			},
			autoWidth: false,
			processing: true,
			responsive: false,
			dom: 'Blfrtip',
			ajax: {
				"url": "ajax/populateOktaGroupMembers.php",
				"type": "POST",
				"data": {
					"group": groupName
				}
			},
			columns: [
				{ data: "NAME", "defaultContent": "" },
				{ data: "EMAIL_ADDRESS", "defaultContent": "" },
			]
		});
	})
}

export { OKTATableLoader as default };