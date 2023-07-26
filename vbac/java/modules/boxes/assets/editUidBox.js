/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class editUidBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ editUidBox.constructor');

        super(parent);
        this.listenForEditUid();
        this.listenForSaveEditUid();

        console.log('--- Function --- editUidBox.constructor');
    }

    listenForEditUid() {
        $(document).on("click", ".btnEditUid", function (e) {
            $("#asset").val($(this).data("asset"));
            $("#userid").val($(this).data("requestee"));

            var primaryUid = $(this).data("primaryuid");
            var secondaryUid = $(this).data("secondaryuid");

            var primaryTitle = $(this).data("primarytitle");
            $("#primaryLabel").text(primaryTitle);
            $("#primaryUid").attr("placeholder", primaryTitle);

            if (primaryUid) {
                $("#primaryUid").val(primaryUid);
            } else {
                $("#primaryUid").val("");
            }

            var secondaryTitle = $(this).data("secondarytitle");
            if (secondaryTitle) {
                $("#secondaryLabel").text(secondaryTitle);
                $("#secondaryUid").attr("placeholder", secondaryTitle);
                $("#secondaryUidFormGroup").show();
            } else {
                $("#secondaryLabel").text("null");
                $("#secondaryUid").attr("placeholder", "null");
                $("#secondaryUidFormGroup").hide();
            }

            if (secondaryUid) {
                $("#secondaryUid").val(secondaryUid);
            } else {
                $("#secondaryUid").val("");
            }

            $("#reference").val($(this).data("reference"));
            $("#editUidModal").modal("show");
        });
    }

    listenForSaveEditUid() {
        var $this = this;
        $(document).on("click", "#saveEditUid", function (e) {
            $("#saveEditUid").addClass("spinning");
            var formData = $("#editUidForm").serialize();
            console.log(formData);
            $.ajax({
                url: "ajax/saveEditUid.php",
                type: "POST",
                data: formData,
                success: function (result) {
                    console.log(result);
                    var resultObj = JSON.parse(result);
                    $("#saveEditUid").removeClass("spinning");
                    $("#editUidModal").modal("hide");
                    $this.tableObj.table.ajax.reload();
                    $this.parent.countRequestsForPortal();
                },
            });
        });
    }
}

export { editUidBox as default };