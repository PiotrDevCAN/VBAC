function initiatePes(cnum, table) {
	var $this = this;
	$.ajax({
		url: "ajax/initiatePes.php",
		data: { cnum: cnum },
		type: "POST",
		success: function (result) {
			var resultObj = JSON.parse(result);
			$(document).on('hidden.bs.modal', '#savingBoardingDetailsModal', function () {
				// When they close the modal this time, reload the page.
				$("#savingBoardingDetailsModal").off("hidden.bs.modal"); // only do this once.
			});
			if (resultObj.success == true) {
				var message =
					"<div class=panel-heading><h3 class=panel-title>Success</h3>" +
					resultObj.messages;
				$("#savingBoardingDetailsModal .panel").html(message);
				$("#savingBoardingDetailsModal .panel").addClass("panel-success");
				$("#savingBoardingDetailsModal .panel").removeClass(
					"panel-danger"
				);
			} else {
				var message =
					"<div class=panel-heading><h3 class=panel-title>Error</h3>" +
					resultObj.messages;
				$("#savingBoardingDetailsModal .panel").html(message);
				$("#savingBoardingDetailsModal .panel").addClass("panel-danger");
				$("#savingBoardingDetailsModal .panel").removeClass(
					"panel-success"
				);
			}
			$("#savingBoardingDetailsModal").modal("show");
			// $("#initiatePes").removeClass("spinning");
			// $("#initiatePes").attr("disabled", true);
			$(".btnPesInitiate").removeClass("spinning");
			$(".btnPesInitiate").attr("disabled", true);
			if (typeof table != "undefined") {
				table.ajax.reload();
			}
		},
	});
}

export { initiatePes as default };