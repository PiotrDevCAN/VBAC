/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class sendPesEmailBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ sendPesEmailBox.constructor');

        super(parent);
        this.listenforSendPesEmail();
        this.listenforConfirmSendPesEmail();

        console.log('--- Function --- sendPesEmailBox.constructor');
    }

    listenforSendPesEmail() {
        $(document).on("click", ".btnSendPesEmail", function (e) {
            $(this).addClass("spinning");
            var data = $(this).data();
            $.ajax({
                url: "ajax/pesEmailDetails.php",
                type: "GET",
                data: {
                    emailaddress: data.emailaddress,
                    country: data.country,
                    cnum: data.cnum,
                    workerid: data.workerid,
                    recheck: data.recheck,
                },
                success: function (result) {
                    $(".btnSendPesEmail").removeClass("spinning");
                    var resultObj = JSON.parse(result);
                    if (resultObj.success == true) {
                        $("#pesEmailFirstName").val(data.firstname);
                        $("#pesEmailLastName").val(data.lastname);
                        $("#pesEmailAddress").val(data.emailaddress);
                        $("#pesEmailCountry").val(data.country);
                        $("#pesEmailOpenSeat").val(data.openseat);
                        $("#pesEmailCnum").val(resultObj.cnum);
                        $("#pesEmailWorkerId").val(resultObj.workerId);
                        $("#pesEmailRecheck").val(resultObj.recheck);
                        $("#pesEmailAttachments").val(""); // clear it out the first time.                        
                        console.log(resultObj.attachmentFileNames);
                        if (resultObj.attachmentFileNames == null) {
                            $("#pesEmailAttachments").val("");
                        } else {
                            var arrayLength = resultObj.attachmentFileNames.length;
                            for (var i = 0; i < arrayLength; i++) {
                                var attachments = $("#pesEmailAttachments").val();
                                $("#pesEmailAttachments").val(
                                    resultObj.attachmentFileNames[i] + "\n" + attachments
                                );
                            }
                        }
                        $("#pesEmailFilename").val(resultObj.filename);
                        $("#pesEmailFilename").css("background-color", "#eeeeee");
                        $("#confirmSendPesEmail").prop("disabled", false);
                        $("#confirmSendPesEmailModal").modal("show");
                    } else {
                        $("#pesEmailFirstName").val(data.firstname);
                        $("#pesEmailLastName").val(data.lastname);
                        $("#pesEmailAddress").val(data.emailaddress);
                        $("#pesEmailCountry").val(data.country);
                        $("#pesEmailOpenSeat").val(data.openseat);
                        $("#pesEmailCnum").val(resultObj.cnum);
                        $("#pesEmailWorkerId").val(resultObj.workerId);
                        $("#pesEmailRecheck").val("");
                        $("#pesEmailAttachments").val(""); // clear it out the first time.
                        if (resultObj.attachmentFileNames) {
                            var arrayLength = resultObj.attachmentFileNames.length;
                            for (var i = 0; i < arrayLength; i++) {
                                var attachments = $("#pesEmailAttachments").val();
                                $("#pesEmailAttachments").val(
                                    resultObj.attachmentFileNames[i] + "\n" + attachments
                                );
                            }
                        }
                        if (resultObj.warning.filename) {
                            $("#pesEmailFilename").val(resultObj.warning.filename);
                            $("#pesEmailFilename").css("background-color", "red");
                        }
                        $("#confirmSendPesEmail").prop("disabled", true);
                        $("#confirmSendPesEmailModal").modal("show");
                    }
                },
            });
        });
    }

    listenforConfirmSendPesEmail() {
        var $this = this;
        $(document).on("click", "#confirmSendPesEmail", function (e) {
            $("#confirmSendPesEmail").addClass("spinning");
            var firstname = $("#pesEmailFirstName").val();
            var lastname = $("#pesEmailLastName").val();
            var emailAddress = $("#pesEmailAddress").val();
            var country = $("#pesEmailCountry").val();
            var openseat = $("#pesEmailOpenSeat").val();
            var cnum = $("#pesEmailCnum").val();
            var workerId = $("#pesEmailCnum").val();
            var recheck = $("#pesEmailRecheck").val();
            $.ajax({
                url: "ajax/sendPesEmail.php",
                type: "POST",
                data: {
                    firstname: firstname,
                    lastname: lastname,
                    emailaddress: emailAddress,
                    country: country,
                    openseat: openseat,
                    cnum: cnum,
                    workerid: workerId,
                    recheck: recheck,
                },
                success: function (result) {
                    $("#confirmSendPesEmail").removeClass("spinning");

                    var resultObj = JSON.parse(result);
                    if (typeof $this.tableObj.table != "undefined") {
                        // We came from the PERSON PORTAL
                        $this.tableObj.table.ajax.reload();
                    }
                    $('.pesComments[data-cnum="' + cnum + '"]').html(
                        "<small>" + resultObj.comment + "</small>"
                    );
                    $('.pesStatusField[data-cnum="' + cnum + '"]').text(
                        resultObj.pesStatus
                    );
                    $('.pesStatusField[data-cnum="' + cnum + '"]')
                        .siblings(".btnSendPesEmail")
                        .remove();
                    $("#confirmSendPesEmailModal").modal("hide");
                },
            });
        });
    }
}

export { sendPesEmailBox as default };