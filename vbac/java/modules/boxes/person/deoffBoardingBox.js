/*
 *
 *
 *
 */

let infoBox = await cacheBustImport('./modules/boxes/person/revalidationInfoBox.js');
let box = await cacheBustImport('./modules/boxes/box.js');

class deoffBoardingBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ deoffBoardingBox.constructor');

        super(parent);
        this.listenForDeoffBoarding();

        console.log('--- Function --- deoffBoardingBox.constructor');
    }

    listenForDeoffBoarding() {
        var $this = this;
        $(document).on("click", ".btnDeoffBoarding", function (e) {
            var button = this;
            var data = $(button).data();
            $(button).addClass("spinning").attr("disabled", true);
            $.ajax({
                url: "ajax/deoffBoarding.php",
                type: "POST",
                data: {
                    cnum: data.cnum,
                    workerid: data.workerid
                },
                success: function (result) {
                    var resultObj = JSON.parse(result);
                    var message = "";
                    var panelclass = "";
                    if (resultObj.deoffboarded == true) {
                        message += "<div class=panel-heading><h3 class=panel-title>Success</h3>";
                        message += "<br/><h4>Offboarded has been reversed.</h4></br>";
                        panelclass = "panel-success";
                    } else {
                        message += "<div class=panel-heading><h3 class=panel-title>Failure</h3>";
                        message += "<br/><h4>Offboarding has <b>NOT</b> been reversed</h4></br>";
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

export { deoffBoardingBox as default };