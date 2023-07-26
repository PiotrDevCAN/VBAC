/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class editDelegateBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ editDelegateBox.constructor');

        super(parent);
        this.listenForDeleteDelegate();

        console.log('--- Function --- editDelegateBox.constructor');
    }

    listenForDeleteDelegate() {
        var $this = this;
        $(document).on("click", ".btnDeleteDelegate", function (e) {
            $(this).addClass("spinning");
            var cnum = $(this).data("cnum");
            var delegateCnum = $(this).data("delegate");
            $.ajax({
                url: "ajax/deleteDelegate.php",
                type: "POST",
                data: { cnum: cnum, delegateCnum: delegateCnum },
                success: function (result) {
                    $(".btnDeleteDelegate").removeClass("spinning");
                    var resultObj = JSON.parse(result);
                    console.log(resultObj);
                    $this.tableObj.table.ajax.reload();
                },
            });
        });
    }
}

export { editDelegateBox as default };