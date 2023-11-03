/**
 *
 */

class OKTAUserEntry {

    table;
    responseObj;

    constructor() {
        this.prepareSelect2();
        this.listenForEMAIL_ADDRESSChange();
        this.listenForEditRecord();
        this.listenForDeleteRecord();
        this.listenForSaveOktaUser();
        this.listenForResetForm();
    }

    prepareSelect2() {
        $("#GROUP").select2();
    }

    listenForEMAIL_ADDRESSChange() {
        $(document).on('change', '#EMAIL_ADDRESS', function () {
            var email = $('#EMAIL_ADDRESS').val().trim().toLowerCase();

            var IBMRegex = RegExp('ibm.com$');
            var oceanRegex = RegExp('ocean.ibm.com$');
            var kyndrylRegex = RegExp('kyndryl.com$');

            var ibmEmailAddress = IBMRegex.test(email);
            var oceanEmailAddress = oceanRegex.test(email);
            var kyndrylEmailAddress = kyndrylRegex.test(email);

            if (kyndrylEmailAddress) {
                $("input[name='Submit']").attr('disabled', false);
                $('#EMAIL_ADDRESS').css("background-color", "LightGreen");
                $("#IBMNotAllowed").hide();
            } else {
                $('input[name="Submit"]').attr('disabled', true);
                $('#EMAIL_ADDRESS').css("background-color", "LightPink");
                $("#IBMNotAllowed").hide();
                if (ibmEmailAddress || oceanEmailAddress) {
                    if ($('#EMAIL_ADDRESS').val() !== $("#originalEMAIL_ADDRESS").val()) {
                        $("#IBMNotAllowed").show();
                    }
                }
            }
        });
    }

    listenForEditRecord() {
        $(document).on("click", ".editRecord", function () {
            $("#GROUP").val($(this).data("groupname")).trigger('change');
            $("#EMAIL_ADDRESS").val($(this).data("emailid"));
            $("#mode").val("edit");
        });
    }

    listenForDeleteRecord() {
        $(document).on("click", ".deleteRecord", function () {
            var groupId = $(this).data('groupid');
            var userId = $(this).data('userid');
            $.ajax({
                url: "ajax/deleteOktaUser.php",
                type: 'POST',
                data: {
                    GROUP_ID: groupId,
                    USER_ID: userId
                },
                success: function (result) {
                    try {
                        var resultObj = JSON.parse(result);
                        var success = resultObj.success;
                        var messages = resultObj.messages;
                        if (success) {
                            messages = 'Record deleted';
                        }
                        $('#messageModalBody').html(messages);
                        $('#messageModal').modal('show');
                        $('.spinning').removeClass('spinning').attr('disabled', false);
                    } catch (e) {
                        $('#messageModalBody').html(
                            "<p>Save has encountered a problem</p><p>" +
                            e +
                            "</p>"
                        );
                        $('#messageModal').modal('show');
                        $('.spinning').removeClass('spinning').attr('disabled', false);
                    }
                }
            });
        });
    }

    listenForSaveOktaUser() {
        var $this = this;
        $(document).on('click', '#saveOktaEntry', function (e) {
            e.preventDefault();
            $('#saveOktaEntry').addClass('spinning').attr('disabled', true);
            var disabledFields = $(':disabled:not(:submit)');
            $(disabledFields).removeAttr('disabled');
            var formData = $('#oktaEntryForm').serialize();
            $(disabledFields).attr('disabled', true);
            $.ajax({
                url: "ajax/saveOktaUser.php",
                type: 'POST',
                data: formData,
                success: function (result) {
                    try {
                        var resultObj = JSON.parse(result);
                        var success = resultObj.success;
                        var messages = resultObj.messages;
                        if (success) {
                            messages = 'Save successful';
                        }
                        $('#messageModalBody').html(messages);
                        $('#messageModal').modal('show');
                        $('.spinning').removeClass('spinning').attr('disabled', false);
                        $('#GROUP').val('').trigger('change');
                        $('#EMAIL_ADDRESS').val('');                        
                    } catch (e) {
                        $('#messageModalBody').html(
                            "<p>Save has encountered a problem</p><p>" +
                            e +
                            "</p>"
                        );
                        $('#messageModal').modal('show');
                        $('.spinning').removeClass('spinning').attr('disabled', false);
                    }
                }
            });
            // e.preventDefault();
        });
    }

    listenForResetForm() {
        $(document).on('click', '#resetOktaEntry', function () {
            $('#GROUP').val('').trigger('change');
            $('#EMAIL_ADDRESS').val('');
            $('#saveOktaEntry').val('Submit');
            $('#mode').val('Define');
        });
    }
}

const OktaUserEntry = new OKTAUserEntry();

export { OktaUserEntry as default };