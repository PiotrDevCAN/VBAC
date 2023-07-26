/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class restartPesBox extends box {

	constructor(parent) {
		console.log('+++ Function +++ restartPesBox.constructor');

		super(parent);
		this.listenForRestartPes();

		console.log('--- Function --- restartPesBox.constructor');
	}

	listenForRestartPes() {
		var $this = this;
		$(document).on("click", ".btnPesRestart", function (e) {
			$(this).addClass("spinning");
			var cnum = $(this).data("cnum");
			var notesid = $(this).data("notesid");
			var email = $(this).data("email");
			var now = new Date();
			var passportFirst = $(this).data("passportfirst");
			var passportSurname = $(this).data("psm_passportSurname");
			$.ajax({
				url: "ajax/restartPes.php",
				data: {
					psm_cnum: cnum,
					psm_status: "Restart Requested",
					psm_detail: "PES Restart Requested",
					PES_DATE_RESPONDED: now.toLocaleDateString("en-US"),
					psm_passportFirst: passportFirst,
					psm_passportSurname: passportSurname,
				},
				type: "POST",
				success: function (result) {
					var resultObj = JSON.parse(result);
					if (typeof $this.tableObj.table != "undefined") {
						// We came from the PERSON PORTAL
						$this.tableObj.table.ajax.reload();
					} else {
						// We came from the PES TRACKER
						var cnum = resultObj.cnum;
						var formattedEmail = resultObj.formattedEmailField;
						$("#pesTrackerTable tr." + cnum)
							.children(".formattedEmailTd:first")
							.children(".formattedEmailDiv:first")
							.html(formattedEmail);
					}
				},
			});
		});
	}
}

export { restartPesBox as default };