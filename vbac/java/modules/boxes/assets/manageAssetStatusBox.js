/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class manageAssetStatusBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ manageAssetStatusBox.constructor');

        super(parent);
        this.listenForAssetRequestApprove();
        this.listenForAssetRequestReject();
        this.listenForAssetRequestApproveRejectConfirm();
        this.listenForAssetRequestApproveRejectToggle();

        console.log('--- Function --- manageAssetStatusBox.constructor');
    }
    
    listenForAssetRequestApprove() {
        $(document).on("click", ".btnAssetRequestApprove", function (e) {
            $("#approveRejectRequestReference").val($(this).data("reference"));
            $("#approveRejectRequestee").val($(this).data("requestee"));
            $("#approveRejectAssetTitle").val($(this).data("asset"));
            $("#approveRejectRequestOrderItStatus").val(
                $(this).data("orderitstatus")
            );
            $("#approveRejectRequestStatus").val($(this).data("status"));
            $("#approveRejectRequestIsPmo").val($(this).data("ispmo"));
            $("#assetRequestApprovalToggle").prop("checked", true).change();

            $("#approveRejectRequestComment").val("").attr("required", false);
            $("#approveRejectModal").modal("show");
        });
    }

    listenForAssetRequestReject() {
        $(document).on("click", ".btnAssetRequestReject", function (e) {
            $("#approveRejectRequestReference").val($(this).data("reference"));
            $("#approveRejectRequestee").val($(this).data("requestee"));
            $("#approveRejectAssetTitle").val($(this).data("asset"));
            $("#approveRejectRequestOrderItStatus").val(
                $(this).data("orderitstatus")
            );
            $("#assetRequestApprovalToggle").prop("checked", false).change();

            console.log($("#assetRequestApprovalToggle"));

            $("#approveRejectRequestComment").val("").attr("required", true);
            $("#approveRejectModal").modal("show");
        });
    }

    listenForAssetRequestApproveRejectConfirm() {
        var $this = this;
        $(document).on("click", "#assetRequestApproveRejectConfirm", function (e) {
            var form = document.getElementById("assetRequestApproveRejectForm");
            var formValid = form.checkValidity();
            if (formValid) {
                $("#approveRejectModal").modal("hide");
                var allDisabledFields = $(
                    "#assetRequestApproveRejectForm input:disabled"
                );
                $(allDisabledFields).attr("disabled", false);
                var reference = $("#approveRejectRequestReference").val();
                var comment = $("#approveRejectRequestComment").val();
                var orderItStatus = $("#approveRejectRequestOrderItStatus").val();
                var status = $("#approveRejectRequestStatus").val();
                var isPmo = $("#approveRejectRequestIsPmo").val();
                var approveReject = $("#assetRequestApprovalToggle").is(":checked");
                var raisedInOrderIt =
                    orderItStatus == "Raised with LBG" ? true : false;
                var iamApproval = status == "Awaiting IAM Approval" && isPmo == 1;
                switch (true) {
                    case iamApproval && approveReject && raisedInOrderIt:
                        console.log("true and true and false");
                        // It's already Raised with LBG - and has now been approved BY IAM.
                        var status = "Approved for LBG";
                        var orderitstatus = "Raised with LBG";
                        break;
                    case approveReject && raisedInOrderIt:
                        console.log("true and true");
                        // It was already Raised with LBG - and now it's approved - so pass it to IAM for their approval next
                        // var status = 'Approved for LBG';
                        var status = "Awaiting IAM Approval";
                        var orderitstatus = "Raised with LBG";
                        break;
                    case approveReject && !raisedInOrderIt:
                        console.log("true and false");
                        // It's NOT already Raised with LBG - and has now been approved.
                        //var status = 'Awaiting IAM Approval';
                        var status = "Approved for LBG";
                        var orderitstatus = "Yet to be raised";
                        break;
                    case !approveReject && raisedInOrderIt:
                        console.log("false and true");
                        // It was already Raised with LBG - but has now been rejected in vbac.
                        var status = "Rejected in vBAC";
                        var orderitstatus = "Raised with LBG";
                        break;
                    case !approveReject && !raisedInOrderIt:
                        console.log("false and false");
                        // It's NOT Raised with LBG - but has now been rejected in vbac, so we WON'T Raise it in Order IT.
                        var status = "Rejected in vBAC";
                        var orderitstatus = "Not to be raised";
                        break;
                    default:
                        break;
                }

                $(allDisabledFields).attr("disabled", true);

                $.ajax({
                    url: "ajax/updateAssetRequestStatus.php",
                    type: "POST",
                    data: {
                        reference: reference,
                        status: status,
                        orderitstatus: orderitstatus,
                        comment: comment,
                        ispmo: isPmo,
                    },
                    success: function (result) {
                        console.log(result);
                        var resultObj = JSON.parse(result);
                        $("#approveRejectModal").modal("hide");
                        $this.tableObj.table.ajax.reload();
                        $this.parent.countRequestsForPortal();
                    },
                });
            } else {
                $('#messageModalBody').html("<p>Please complete justification</p>");
                $('#messageModal').modal('show');
            }
        }
        );
    }

    listenForAssetRequestApproveRejectToggle() {
        $(document)
            .off("change.varb")
            .on("change.varb", "#assetRequestApprovalToggle", function (e) {
                var comment = $("#assetRequestApprovalComment");
                comment.prop("required", !comment.prop("required"));
            });
    }
}

export { manageAssetStatusBox as default };