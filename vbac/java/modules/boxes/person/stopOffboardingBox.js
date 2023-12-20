/*
 *
 *
 *
 */

let infoBox = await cacheBustImport('./modules/boxes/person/revalidationInfoBox.js');
let box = await cacheBustImport('./modules/boxes/box.js');

class stopOffboardingBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ stopOffboardingBox.constructor');

        super(parent);
        this.listenForStopOffBoarding();

        console.log('--- Function --- stopOffboardingBox.constructor');
    }

    listenForStopOffBoarding() {
        var $this = this;
        $(document).on("click", ".btnStopOffboarding", function (e) {
            var button = this;
            var data = $(button).data();
            $(button).addClass("spinning").attr("disabled", true);
            $.ajax({
                url: "ajax/stopOffboarding.php",
                type: "POST",
                data: {
                    cnum: data.cnum,
                    workerid: data.workerid
                },
                success: function (result) {
                    var resultObj = JSON.parse(result);
                    var message = "";
                    var panelclass = "";
                    if (resultObj.stopped == true) {
                        message += "<div class=panel-heading><h3 class=panel-title>Success</h3>";
                        message += "<br/><h4>Offboarding has been stopped</h4></br>";
                        panelclass = "panel-success";
                    } else {
                        message += "<div class=panel-heading><h3 class=panel-title>Failure</h3>";
                        message += "<br/><h4>Offboarding has <b>NOT</b> been stopped</h4></br>";
                        panelclass = "panel-danger";
                    }
                    if (resultObj.success != true) {
                        message += "<br/>Other problems were also encountered details follow :";
                        message += resultObj.messages;
                    }
                    infoBox.displayMessage(message, panelclass);
                    $this.tableObj.table.ajax.reload();
                },
                complete: function (xhr, status) {
                    $(button).removeClass("spinning").attr("disabled", false);
                }
            });
        });
    }
}

export { stopOffboardingBox as default };