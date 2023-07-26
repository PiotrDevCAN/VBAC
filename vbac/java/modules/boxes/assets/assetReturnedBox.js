/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class assetReturnedBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ assetReturnedBox.constructor');

        super(parent);
        this.listenForAssetReturned();
        this.listenForConfirmedAssetReturnedModalShown();
        this.listenForConfirmedAssetReturned();

        console.log('--- Function --- assetReturnedBox.constructor');
    }

    listenForAssetReturned() {
        $(document).on("click", ".btnAssetReturned", function (e) {
            $("#assetRet").val($(this).data("asset"));
            $("#useridRet").val($(this).data("requestee"));

            var primaryUid = $(this).data("primaryuid");
            var secondaryUid = $(this).data("secondaryuid");

            var primaryTitle = $(this).data("primarytitle");
            if (primaryTitle) {
                $("#primaryLabelRet").text(primaryTitle);
                $("#primaryUidRet").attr("placeholder", primaryTitle);
                $("#primaryUidFormGroupRet").show();
            } else {
                $("#primaryLabelRet").text("null");
                $("#primaryUidRet").attr("placeholder", "null");
                $("#primaryUidFormGroupRet").hide();
            }

            if (primaryUid) {
                $("#primaryUidRet").val(primaryUid);
                $("#primaryUidRet").attr("disabled", true);
            } else {
                $("#primaryUidRet").val("");
                $("#primaryUidRet").attr("disabled", false);
            }

            var secondaryTitle = $(this).data("secondarytitle");
            if (secondaryTitle) {
                $("#secondaryLabelRet").text(secondaryTitle);
                $("#secondaryUidRet").attr("placeholder", secondaryTitle);
                $("#secondaryUidFormGroupRet").show();
            } else {
                $("#secondaryLabelRet").text("null");
                $("#secondaryUidRet").attr("placeholder", "null");
                $("#secondaryUidFormGroupRet").hide();
            }

            if (secondaryUid) {
                $("#secondaryUidRet").val(secondaryUid);
                $("#secondaryUidRet").attr("disabled", true);
            } else {
                $("#secondaryUidRet").val("");
                $("#secondaryUidRet").attr("disabled", false);
            }

            $("#referenceRet").val($(this).data("reference"));
            $("#confirmAssetReturned").attr("disabled", false);
            $("#confirmReturnedModal").modal("show");
        });
    }

    listenForConfirmedAssetReturnedModalShown() {
        $(document).on('shown.bs.modal', '#confirmReturnedModal', function (e) {
            $("#date_returned").datepicker({
                dateFormat: "dd M yy",
                altField: "#date_returned_db2",
                altFormat: "yy-mm-dd",
                minDate: -365,
                maxDate: +0,
            });
            var dateReturned = $("#date_returned").datepicker("getDate");
        });
    }

    listenForConfirmedAssetReturned() {
        var $this = this;
        $(document).on("click", "#confirmAssetReturned", function (e) {
            $("#confirmAssetReturned").addClass("spinning");
            $("#confirmAssetReturned").attr("disabled", true);
            var formData = $("#confirmReturnedForm").serialize();
            console.log(formData);
            $.ajax({
                url: "ajax/saveAssetReturned.php",
                type: "POST",
                data: formData,
                success: function (result) {
                    console.log(result);
                    var resultObj = JSON.parse(result);
                    $("#confirmAssetReturned").removeClass("spinning");
                    $("#confirmAssetReturned").attr("disabled", false);
                    $("#confirmReturnedModal").modal("hide");
                    $this.tableObj.table.ajax.reload();
                    $this.parent.countRequestsForPortal();
                },
            });
        });
    }
}

export { assetReturnedBox as default };