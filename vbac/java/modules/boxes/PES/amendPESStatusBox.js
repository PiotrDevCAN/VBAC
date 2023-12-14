/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class amendPESLevelBox extends box {

	constructor(parent) {
		console.log('+++ Function +++ amendPESLevelBox.constructor');

		super(parent);
		this.listenForEditPesStatus();
		this.listenForAmendPesStatusModalShown();
		this.listenForSavePesStatus();

		console.log('--- Function --- amendPESLevelBox.constructor');
	}

	listenForEditPesStatus() {
		$(document).on("click", ".btnPesStatus", function (e) {
			var cnum = $(this).data("cnum");
			var workerId = $(this).data("workerid");
			var notesid = $(this).data("notesid");
			var email = $(this).data("email");
			var revalidationStatus = $(this).data("revalidationstatus");
			var status = $(this).data("pesstatus");

			if (typeof $(this).data("passportfirst") != "undefined") {
				var passportFirst = $(this).data("passportfirst");
				var passportSurname = $(this).data("passportsurname");
				$("#psm_passportFirst").val($.trim(passportFirst));
				$("#psm_passportSurname").val($.trim(passportSurname));
				$("#psm_passportFirst").prop("disabled", false);
				$("#psm_passportSurname").prop("disabled", false);
			} else {
				$("#passportNameDetails").hide();
				$("#psm_passportFirst").prop("disabled", true);
				$("#psm_passportSurname").prop("disabled", true);
			}

			notesid = notesid.trim() != "" ? notesid : email;
			$("#psm_notesid").val(notesid);
			$("#psm_cnum").val(cnum);
			$("#psm_worker_id").val(workerId);
			$("#psm_revalidationstatus").val(revalidationStatus);
			$('#psm_status').val(status);

			$("#amendPesStatusModal").modal("show");
		});
	}

	listenForAmendPesStatusModalShown() {
		$(document).on('shown.bs.modal', '#amendPesStatusModal', function (e) {
			$("#psm_status").select2();
			$("#psm_detail").val("");
			$("#pes_date").datepicker({
				dateFormat: "dd-mm-yy",
				altField: "#pes_date_db2",
				altFormat: "yy-mm-dd",
				maxDate: 0,
			});
		});
	}

	listenForSavePesStatus() {
		var $this = this;
		$(this).attr("disabled", true);
		$(document).on('submit', '#psmForm', function (e) {
			$("#savePesStatus").attr("disabled", true).addClass("spinning");
			var form = document.getElementById("psmForm");
			var formValid = form.checkValidity();
			if (formValid) {
				var allDisabledFields = $("input:disabled");
				$(allDisabledFields)
					.not("#psm_passportFirst")
					.not("#psm_passportSurname")
					.attr("disabled", false);
				var formData = $("#amendPesStatusModal form").serialize();
				$(allDisabledFields).attr("disabled", true);
				$.ajax({
					url: "ajax/savePesStatus.php",
					data: formData,
					type: "POST",
					success: function (result) {
						var resultObj = JSON.parse(result);
						$("#savePesStatus")
							.attr("disabled", false)
							.removeClass("spinning");

						var success = resultObj.success;
						var cnum = resultObj.cnum;

						var message = resultObj.success ? "<br />PES Status Update Successful" : "<br />PES Status Update Failed";
						message += "<br />" + resultObj.messages;
						$('#messageModalBody').html(message);
						$('#messageModal').modal('show');

						if (!success) {
							if (typeof $this.tableObj.table != "undefined") {
								// We came from the PERSON PORTAL
								$this.tableObj.table.ajax.reload();
							}
						} else {
							if (typeof $this.tableObj.table != "undefined") {
								// We came from the PERSON PORTAL
								$this.tableObj.table.ajax.reload();
							} else {
								// We came from the PES TRACKER
								var pesStatusField = resultObj.formattedPesStatusField;
								$("#pesTrackerTable tr." + cnum)
									.children(".pesStatusCell:first")
									.html(pesStatusField.display);
							}
						}
						$("#amendPesStatusModal").modal("hide");
					},
				});
			}
			return false;
		});
	}
}

export { amendPESLevelBox as default };