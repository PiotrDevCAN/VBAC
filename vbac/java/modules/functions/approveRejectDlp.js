function approveRejectDlp(button, table, approveReject) {
	var cnum = $(button).data("cnum");
	var workerId = $(button).data("workerid");
	var hostname = $(button).data("hostname");
	$.ajax({
		url: "ajax/dlpApproveReject.php",
		data: {
			cnum: cnum,
			workerid: workerId,
			hostname: hostname,
			approveReject: approveReject
		},
		type: "POST",
		success: function (result) {
			var resultObj = JSON.parse(result);
			console.log(resultObj);
			table.ajax.reload();
		},
	});
}

export { approveRejectDlp as default };