/**
 *
 */

let buttonCommon = await cacheBustImport('./modules/functions/buttonCommon.js');
let actions = await cacheBustImport('./modules/actions/person/personManualPesUpdateActions.js');

class manualPESStatusUpdate {

	table;

	constructor() {
		console.log('+++ Function +++ manualPESStatusUpdate.constructor');

		this.initialiseUpdateStatusTable();
		this.listenForPerson();
		// this.listenForPesStatus();
		this.listenForUpdatePerson();
		this.listenForClosingSaveFeedbackModal();
		this.listenForTableRowSelect();

		// pass table to actions
		const Actions = new actions(this);

		$('.select2').select2();

		console.log('--- Function --- manualPESStatusUpdate.constructor');
	}

	listenForPerson() {
		$(document).on('change', '#person', function () {
			// $('#pesStatus').attr('disabled', false);
			$('#updatePerson').attr('disabled', false);
		});
	}

	/*
	listenForPesStatus() {
		$(document).on('change', '#pesStatus', function () {
			$('#updatePerson').attr('disabled', false);
		});
	}
	*/

	listenForClosingSaveFeedbackModal() {
		$(document).on('hidden.bs.modal', '#showUpdateResultModal', function (e) {
			location.reload();
		});
	}

	listenForUpdatePerson() {
		$(document).on('click', '#updatePerson', function (event) {
			$(this).addClass('spinning').attr('disabled', true);
			event.preventDefault();
			console.log(event);
			var formData = $('#updateStatus').serialize();
			$.ajax({
				type: 'post',
				url: 'ajax/setPesProgressing.php',
				data: formData,
				success: function (response) {
					var resultObj = JSON.parse(response);
					console.log(resultObj);

					$('.spinning').removeClass('spinning').attr('disabled', false);

					var message = resultObj.success ? "<br />Status Update Successful" : "<br />Status Update Failed";
					message += "<br />" + resultObj.messages;
					$('#updateReport').html(message);
					$('#showUpdateResultModal').modal('show');
				}
			});
		});
	}

	initialiseUpdateStatusTable() {
		console.log("initialiseUpdateStatusTable");

		// Setup - add a text input to each footer cell
		$("#updateStatusTable tfoot th").each(function () {
			var title = $(this).text();
			$(this).html(
				'<input type="text" id="footer' +
				title +
				'" placeholder="Search ' +
				title +
				'" />'
			);
		});
		// Show DataTable
		$('#updateStatusTable').show();
		// DataTable
		this.table = $("#updateStatusTable").DataTable({
			ajax: {
				url: "ajax/populateUpdateStatusTable.php",
				type: "POST",
				beforeSend: function (jqXHR, settings) {
					$.each(xhrPool, function (idx, jqXHR) {
						console.log('abort jqXHR');
						jqXHR.abort();  // basically, cancel any existing request, so this one is the only one running
						xhrPool.splice(idx, 1);
					});
					xhrPool.push(jqXHR);
				}
			},
			columns: [
				{ data: "CNUM" },
				{ data: "WORKER_ID" },
				{ data: "FIRST_NAME" },
				{ data: "LAST_NAME" },
				{ data: "EMAIL_ADDRESS" },
				{ data: "KYN_EMAIL_ADDRESS" },
				{ data: "PES_STATUS" },
			],
			order: [[1, "asc"]],
			responsive: true,
			processing: true,
			dom: "Blfrtip",
			buttons: [
				"colvis",
				$.extend(true, {}, buttonCommon, {
					extend: "excelHtml5",
					exportOptions: {
						orthogonal: "sort",
						stripHtml: true,
						stripNewLines: false,
					},
					customize: function (xlsx) {
						var sheet = xlsx.xl.worksheets["sheet1.xml"];
						var now = new Date();
						$("c[r=A1] t", sheet).text("Ventus Squads : " + now);
					},
				}),
				$.extend(true, {}, buttonCommon, {
					extend: "csvHtml5",
					exportOptions: {
						orthogonal: "sort",
						stripHtml: true,
						stripNewLines: false,
					},
				}),
				$.extend(true, {}, buttonCommon, {
					extend: "print",
					exportOptions: {
						orthogonal: "sort",
						stripHtml: true,
						stripNewLines: false,
					},
				}),
			],
		});

		// Apply the search
		this.table.columns().every(function () {
			var that = this;
			$("input", this.footer()).on("keyup change", function () {
				if (that.search() !== this.value) {
					that.search(this.value).draw();
				}
			});
		});
	}

	listenForTableRowSelect() {
		$(document).on('click', '#updateStatusTable tbody tr', function (event) {
			if ($(this).hasClass('selected')) {
				$(this).removeClass('selected');
			} else {
				// $this.table.$('tr.selected').removeClass('selected');
				$(this).addClass('selected');
			}
		});
	}
}

const ManualPESStatusUpdate = new manualPESStatusUpdate();

export { ManualPESStatusUpdate as default };