/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class editWorkLocation extends box {

    constructor(parent) {
        console.log('+++ Function +++ editWorkLocation.constructor');

        super(parent);
        this.listenForEditLocation();
        this.listenForDeleteLocation();

        console.log('--- Function --- editWorkLocation.constructor');
    }

    listenForEditLocation() {
        $(document).on("click", ".btnEditLocation", function () {
            $("#ID").val($(this).data("id"));
            $("#COUNTRY").val($(this).data("countryid")).trigger("change");
            $("#CITY").val($(this).data("cityid")).trigger("change");
            $("#ADDRESS").val($(this).data("address")).trigger("change");
            $("#ONSHORE").val($(this).data("onshore")).trigger("change");
            $("#CBC_IN_PLACE").val($(this).data("cbcinplace")).trigger("change");
            $("#mode").val("edit");
        });
    }

    listenForDeleteLocation() {
        var $this = this;
        $(document).on("click", ".btnDeleteLocation", function (e) {
            $(this).addClass("spinning");
            var id = $(this).data("id");
            $.ajax({
                url: "ajax/deleteLocation.php",
                type: "POST",
                data: {
                    id: id,
                },
                success: function (result) {
                    $(".btnDeleteLocation").removeClass("spinning");
                    var resultObj = JSON.parse(result);
                    console.log(resultObj);
                    $this.tableObj.table.ajax.reload();
                },
            });
        });
    }
}

export { editWorkLocation as default };