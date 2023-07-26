/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class editAssetBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ editAssetBox.constructor');

        super(parent);
        this.listenForEditButton();
        this.listenForDeleteButton();

        console.log('--- Function --- editAssetBox.constructor');
    }
    listenForEditButton() {
        $(document).on("click", ".btnEditAsset", function () {
            $(this).data("dtetoibm") == "Yes"
                ? $("#RecordDateToIbm").bootstrapToggle("on")
                : $("#RecordDateToIbm").bootstrapToggle("off");
            $(this).data("dtetousr") == "Yes"
                ? $("#RecordDateToUser").bootstrapToggle("on")
                : $("#RecordDateToUser").bootstrapToggle("off");
            $(this).data("dteret") == "Yes"
                ? $("#RecordDateReturned").bootstrapToggle("on")
                : $("#RecordDateReturned").bootstrapToggle("off");
            $(this).data("onshore") == "Yes"
                ? $("#applicableOnShore").bootstrapToggle("on")
                : $("#applicableOnShore").bootstrapToggle("off");
            $(this).data("offshore") == "Yes"
                ? $("#applicableOffShore").bootstrapToggle("on")
                : $("#applicableOffShore").bootstrapToggle("off");
            $(this).data("orderitreq") == "Yes"
                ? $("#OrderItRequired").bootstrapToggle("on")
                : $("#OrderItRequired").bootstrapToggle("off");
            $(this).data("just") == "Yes"
                ? $("#businessJustification").bootstrapToggle("on")
                : $("#businessJustification").bootstrapToggle("off");
            $(this).data("just") == "Yes"
                ? $("#promptDiv").show()
                : $("#promptDiv").hide();
            $(this).data("just") == "Yes"
                ? $("#prompt").attr("required", true)
                : $("#prompt").attr("required", false);

            var promptnat = $(this).data("prompt");
            var promptdec = decodeURIComponent(promptnat);
            var promptenc = encodeURIComponent(promptnat);
            var type = $(this).data("type");

            $("#prompt").val(promptnat);
            $("#asset_primary_uid_title").val($(this).data("uidpri"));
            $("#asset_secondary_uid_title").val($(this).data("uidsec"));
            $("#asset_title").val($(this).data("asset")).attr("disabled", true);
            $("#saveRequestableAsset").val("Update");
            $("#ORDER_IT_TYPE").val(type);

            var prereq = $(this).data("prereq");

            $('option[value="' + prereq + '"]')
                .prop("selected", true)
                .trigger("change");
        });
    }

    listenForDeleteButton() {
        var $this = this;
        $(document).on("click", ".btnDeleteAsset", function () {
            $.ajax({
                url: "ajax/deleteRequestableAsset.php",
                type: "POST",
                data: {
                    ASSET_TITLE: $(this).data("asset"),
                    DELETED_BY: $(this).data("deleter"),
                },
                success: function (result) {
                    console.log(result);
                    $this.tableObj.table.ajax.reload();
                },
            });
        });
    }
}

export { editAssetBox as default };