/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class amendOrderItNumberBox extends box {

	constructor(parent) {
		console.log('+++ Function +++ amendOrderItNumberBox.constructor');

		super(parent);
		this.listenForAmendOrderIt();
		this.listenForSaveAmendedOrderIt();

		console.log('--- Function --- amendOrderItNumberBox.constructor');
	}

	listenForAmendOrderIt() {
		$(document).on("click", ".btnAmendOrderItNumber", function (e) {
			console.log("wish to amend Order IT number");
			var reference = $(this).data("reference");
			var currentOit = $(this).data("orderit");
			$("#amendOrderItRequestReference").val(reference);
			$("#amendOrderItCurrent").val(currentOit);
			$("#amendOrderItNewOrderIt").val("");
			$("#amendOrderItModal").modal("show");
		});
	}

	listenForSaveAmendedOrderIt() {
		var $this = this;
		$(document).on("click", "#confirmedSaveOrderIt", function (e) {
			$("#confirmedSaveOrderIt").addClass("spinning");
			$("#confirmedSaveOrderIt").attr("disabled", true);
			var reference = $("#amendOrderItRequestReference").val();
			var currentOit = $("#amendOrderItCurrent").val();
			var newOit = $("#amendOrderItNewOrderIt").val();
			$.ajax({
				url: "ajax/saveAmendedOrderIt.php",
				type: "POST",
				data: {
					reference: reference,
					currentOit: currentOit,
					newOit: newOit,
				},
				success: function (result) {
					console.log(result);
					var resultObj = JSON.parse(result);
					$this.tableObj.table.ajax.reload();
					$("#confirmedSaveOrderIt").removeClass("spinning");
					$("#confirmedSaveOrderIt").attr("disabled", false);
					var reference = $("#amendOrderItRequestReference").val("");
					var currentOit = $("#amendOrderItCurrent").val("");
					var newOit = $("#amendOrderItNewOrderIt").val("");
					$("#amendOrderItModal").modal("hide");
				},
			});
		});
	}
}

export { amendOrderItNumberBox as default };