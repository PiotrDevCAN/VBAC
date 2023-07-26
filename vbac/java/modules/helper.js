/**
 *
 */

class Helper {

	constructor() {
		$.fn.dataTable.ext.errMode = 'none';
	}

	listenForMessageModallShown() {
		$(document).on('shown.bs.modal', '#messageModal', function (e) {
			$(this).css("z-index", "999999");
		});
	}

	listenForMessageModallHidden() {
		$(document).on('hidden.bs.modal', '#messageModal', function (e) {

		});
	}

	listenForErrorMessageModalShown() {
		$(document).on('shown.bs.modal', '#errorMessageModal', function (e) {
			$(this).css("z-index", "999999");
		});
	}

	listenForErrorMessageModalHidden() {
		$(document).on('hidden.bs.modal', '#errorMessageModal', function (e) {

		});
	}

	lockButton(button) {
		$(button).addClass('spinning').attr('disabled', true);
	}

	unlockButton() {
		$('.spinning').removeClass('spinning').attr('disabled', false);
	}

	highlightOnGreen(el) {
		$(el).css("background-color", "LightGreen");
	}

	highlightOnRed(el) {
		$(el).css("background-color", "LightPink");
	}

	lockSubmitButton() {
		$(':submit').attr('disabled', true);
	}

	unlockSubmitButton() {
		$(':submit').attr('disabled', false);
	}
}

// Workaround to get textStatus from ajax request
$.ajaxPrefilter(function (options, originalOptions, jqXHR) {
	jqXHR.fail(function (jqXHR, textStatus, errorThrown) {
		var message = options.url + ": ";
		var suppress = false;
		if (textStatus == 'parsererror') {
			message += "Parsing request has failed - " + errorThrown;
		} else if (errorThrown == 'timeout') {
			message += "Request time out.";
		} else if (errorThrown == 'abort') {
			message += "Request was aborted.";
			suppress = true;
		} else if (jqXHR.status === 0) {
			message += "No connection.";
			suppress = true;
		} else if (jqXHR.status) {
			message += "HTTP Error " + jqXHR.status + " - " + jqXHR.statusText + ".";
		} else {
			message += "Unknown error.";
		}
		console.warn(message);
		// console.log(options);
		// console.log(originalOptions);
		// console.error(message);
		if (suppress !== true) {
			//	handle errors here. What errors	            :-)!
			$('#errorMessageBody').html("<h2>Json call errored. Tell Piotr</h2><p>" + message + "</p>");
			helper.unlockButton();
			$(".modal").modal('hide');
			$('#errorMessageModal').modal('show');
		}
	});
});

// // Register a handler to be called when Ajax requests complete. This is an AjaxEvent.
// $(document).ajaxComplete(function( event, jqxhr, settings ) {
// 	console.log('ajaxComplete');
// });

// // Register a handler to be called when Ajax requests complete with an error. This is an Ajax Event.
// $(document).ajaxError(function( event, jqXHR, settings, thrownError ) {
// 	console.log('ajaxError');
// });

// Attach a function to be executed before an Ajax request is sent. This is an Ajax Event.
// $(document).ajaxSend(function( event, jqxhr, settings ) {
// 	console.log('ajaxSend');
// });

// // Register a handler to be called when the first Ajax request begins. This is an Ajax Event.
// $(document).ajaxStart(function( event, jqxhr, settings ) {
// 	console.log('ajaxStart');
// });

// // Register a handler to be called when all Ajax requests have completed. This is an Ajax Event.
// $(document).ajaxStop(function( event, jqxhr, settings ) {
// 	console.log('ajaxStop');
// });

// // Attach a function to be executed whenever an Ajax request completes successfully. This is an Ajax Event.
// $(document).ajaxSuccess(function( event, jqxhr, settings ) {
// 	console.log('ajaxSuccess');
// });

const helper = new Helper();

helper.listenForMessageModallShown();
helper.listenForMessageModallHidden();
helper.listenForErrorMessageModalShown();
helper.listenForErrorMessageModalHidden();

export { helper as default };