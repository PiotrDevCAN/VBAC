/*
 *
 *
 *
 */

let infoBox = await cacheBustImport('./modules/boxes/person/revalidationInfoBox.js');
let box = await cacheBustImport('./modules/boxes/box.js');

class completeOffboardingBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ completeOffboardingBox.constructor');

        super(parent);
        this.listenForOffBoardingCompleted();

        console.log('--- Function --- completeOffboardingBox.constructor');
    }

    listenForOffBoardingCompleted() {
        var $this = this;
        $(document).on("click", ".btnOffboarded", function (e) {
            var button = this;
            var data = $(button).data();
            $(button).addClass("spinning").attr("disabled", true);
            $.ajax({
                url: "ajax/completeOffboarding.php",
                type: "POST",
                data: {
                    cnum: data.cnum
                },
                success: function (result) {
                    var resultObj = JSON.parse(result);
                    var message = "";
                    var panelclass = "";
                    if (resultObj.completed == true) {
                        message += "<div class=panel-heading><h3 class=panel-title>Success</h3>";
                        message += "<br/><h4>Offboarding has been completed</h4></br>";
                        panelclass = "panel-success";
                    } else {
                        message += "<div class=panel-heading><h3 class=panel-title>Failure</h3>";
                        message += "<br/><h4>Offboarding has <b>NOT</b> been completed</h4></br>";
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

export { completeOffboardingBox as default };