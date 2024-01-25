let post = await cacheBustImport('./modules/dataSources/fetch/post.js');

const initiatePesFetch = async (cnum, workerId, table) => {

	const url = 'ajax/initiatePes.php';
	let params = {
		cnum: cnum,
		workerid: workerId
	};

	const data = post(url, params)
		.then((resultObj) => {

			// var resultObj = JSON.parse(result);
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
			$(".btnPesInitiate").removeClass("spinning");
			$(".btnPesInitiate").attr("disabled", true);
			if (typeof table != "undefined") {
				table.ajax.reload();
			}

		})
		.catch((error) => {
			alert('catch 1');
			console.log(error);
		})
		.finally(() => {
			console.log('Execute always ');
		});

	return data;
};

export default initiatePesFetch;