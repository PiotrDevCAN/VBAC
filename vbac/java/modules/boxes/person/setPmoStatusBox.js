/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class setPmoStatusBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ setPmoStatusBox.constructor');

        super(parent);
        this.listenForSetPmoStatus();

        console.log('--- Function --- setPmoStatusBox.constructor');
    }

    listenForSetPmoStatus() {
        var $this = this;
        $(document).on("click", ".btnSetPmoStatus", function (e) {
            $(this).addClass("spinning");
            var data = $(this).data();
            $.ajax({
                url: "ajax/setPmoStatus.php",
                type: "POST",
                data: data,
                success: function (result) {
                    var resultObj = JSON.parse(result);
                    $this.tableObj.table.ajax.reload();
                },
            });
        });
    }
}

export { setPmoStatusBox as default };