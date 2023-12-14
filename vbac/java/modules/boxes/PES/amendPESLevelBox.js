/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');
let pesDescriptionHover = await cacheBustImport('./modules/functions/pesDescriptionHover.js');

class amendPESLevelBox extends box {

	constructor(parent) {
		console.log('+++ Function +++ amendPESLevelBox.constructor');

		super(parent);
		this.listenForEditPesLevel();
		this.listenForAmendPesLevelModalShown();
		this.listenForSavePesLevel();

		console.log('--- Function --- amendPESLevelBox.constructor');
	}

	listenForEditPesLevel() {
		var $this = this;
		$(document).on("click", ".btnPesLevel", function (e) {
			var cnum = $(this).data("cnum");
			var workerId = $(this).data("workerid");
			var notesid = $(this).data("notesid");
			var email = $(this).data("email");
			var pesDateclrd = $(this).data("pesdatecleared");
			var pesDateRechk = $(this).data("pesdaterecheck");
			var level = $(this).data("peslevel");

			notesid = notesid.trim() != "" ? notesid : email;
			$("#plm_notesid").val(notesid);
			$("#plm_cnum").val(cnum);
			$("#plm_worker_id").val(workerId);
			$('#plm_level').val(level);

			if (pesDateclrd) {
				$("#pes_cleared_date").val(pesDateclrd.trim());
				$("#pes_cleareD_date_db2").val(pesDateclrd.trim());
			}

			if (pesDateRechk) {
				$("#pes_old_recheck_date").val(pesDateRechk.trim());
				$("#pes_old_recheck_date_db2").val(pesDateRechk.trim());
			}

			pesDescriptionHover();

			$("#amendPesLevelModal").modal("show");
		});
	}

	listenForAmendPesLevelModalShown() {
		$(document).on('shown.bs.modal', '#amendPesLevelModal', function (e) {
			$("#plm_level").select2();
			$("#pes_cleared_date").datepicker({
				// dateFormat: "dd M yy",
				dateFormat: "d M Y",
				altField: "#pes_cleared_date_db2",
				altFormat: "yy-mm-dd",
				maxDate: 0,
			});
			$("#pes_old_recheck_date").datepicker({
				// dateFormat: "dd M yy",
				dateFormat: "d M Y",
				altField: "#pes_old_recheck_date_db2",
				altFormat: "yy-mm-dd",
				maxDate: 0,
			});
			// $("#pes_recheck_date").datepicker({
			// 	// dateFormat: "dd M yy",
			// 	dateFormat: "d M Y",
			// 	altField: "#pes_recheck_date_db2",
			// 	altFormat: "yy-mm-dd",
			// 	maxDate: 0,
			// });
		});
	}

	listenForSavePesLevel() {
		var $this = this;
		$(this).attr("disabled", true);
		$(document).on('submit', '#plmForm', function (e) {
			$("#savePesLevel").attr("disabled", true).addClass("spinning");
			var form = document.getElementById("plmForm");
			var formValid = form.checkValidity();
			if (formValid) {
				var allDisabledFields = $("input:disabled");
				$(allDisabledFields)
					.attr("disabled", false);
				var formData = $("#amendPesLevelModal form").serialize();
				$(allDisabledFields).attr("disabled", true);
				$.ajax({
					url: "ajax/savePesLevel.php",
					data: formData,
					type: "POST",
					success: function (result) {
						var resultObj = JSON.parse(result);
						$("#savePesLevel")
							.attr("disabled", false)
							.removeClass("spinning");

						var success = resultObj.success;
						var cnum = resultObj.cnum;

						var message = resultObj.success ? "<br /><b>PES Level Update Successful</b>" : "<br /><b>PES Level Update Failed</b>";
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
								var pesLevelField = resultObj.formattedPesLevelField;
								$("#pesTrackerTable tr." + cnum)
									.children(".pesLevelCell:first")
									.html(pesLevelField.display);
							}
						}
						$("#amendPesLevelModal").modal("hide");
					},
				});
			}
			return false;
		});
	}
}

export { amendPESLevelBox as default };