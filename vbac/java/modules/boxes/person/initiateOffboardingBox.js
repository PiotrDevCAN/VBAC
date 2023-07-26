/*
 *
 *
 *
 */

let infoBox = await cacheBustImport('./modules/boxes/person/revalidationInfoBox.js');
let box = await cacheBustImport('./modules/boxes/box.js');

class initiateOffboardingBox extends box {

    static modalId = 'confirmOffboardingModal';

    constructor(parent) {
        console.log('+++ Function +++ initiateOffboardingBox.constructor');

        super(parent);
        this.listenForOffboardingModalShown();
        this.listenForBtnOffboarding();
        this.listenForSaveOffboarding();

        console.log('--- Function --- initiateOffboardingBox.constructor');
    }

    resetModal() {
        $('#' + initiateOffboardingBox.modalId + ' .panel')
            .removeClass("panel-danger")
            .removeClass("panel-warning")
            .removeClass("panel-success");
    }

    showModal() {
        $('#' + initiateOffboardingBox.modalId).modal("show");
    }

    hideModal() {
        $('#' + initiateOffboardingBox.modalId).modal("hide");
    }

    listenForOffboardingModalShown() {
        $(document).on('shown.bs.modal', '#' + initiateOffboardingBox.modalId, function (e) {
            $("#offboarding_proposed_leaving_date").datepicker({
                dateFormat: "dd M yy",
                // dateFormat: "d M Y",
                altField: "#offboarding_proposed_leaving_date_db2",
                altFormat: "yy-mm-dd",
            });
        });
    }

    listenForBtnOffboarding() {
        var $this = this;
        $(document).on("click", ".btnOffboarding", function (e) {
            var data = $(this).data();
            $("#offboarding_cnum").val(data.cnum);
            $("#offboarding_proposed_leaving_date").val('');
            $("#offboarding_proposed_leaving_date_db2").val('');
            $this.resetModal();
            $this.showModal();
        });
    }

    listenForSaveOffboarding() {
        var $this = this;
        $(document).on("click", "#saveOffboarding", function () {
            var cnum = $('#offboarding_cnum').val();
            var leavingDate = $('#offboarding_proposed_leaving_date_db2').val();
            var trimmedDate = leavingDate.trim();
            if (trimmedDate !== "") {
                $("#offboarding_proposed_leaving_date").css("background-color", "white");
                var button = this;
                $(button).addClass("spinning").attr("disabled", true);
                $.ajax({
                    url: "ajax/initiateOffboardingFromPortal.php",
                    type: "POST",
                    data: {
                        cnum: cnum,
                        proposedLeavingDate: leavingDate
                    },
                    success: function (result) {
                        var resultObj = JSON.parse(result);
                        var message = "";
                        var panelclass = "";
                        if (resultObj.initiated == true) {
                            message += "<div class=panel-heading><h3 class=panel-title>Success</h3>";
                            message += "<br/><h4>Offboarding has been initiated</h4></br>";
                            panelclass = "panel-success";
                        } else {
                            message += "<div class=panel-heading><h3 class=panel-title>Failure</h3>";
                            message += "<br/><h4>Offboarding has <b>NOT</b> been initiated</h4></br>";
                            panelclass = "panel-danger";
                        }
                        if (resultObj.success != true) {
                            message += "<br/>Other problems were also encountered details follow :";
                            message += resultObj.messages;
                        }
                        $this.hideModal();
                        infoBox.displayMessage(message, panelclass);
                        $this.tableObj.table.ajax.reload();
                    },
                    complete: function (xhr, status) {
                        $(button).removeClass("spinning").attr("disabled", false);
                    }
                });
            } else {
                // can not proceed
                $("#offboarding_proposed_leaving_date").css("background-color", "Red");
            }
        });
    }
}

export { initiateOffboardingBox as default };