/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');
// let initiatePes = await cacheBustImport('./modules/functions/initiatePes.js');
let initiatePes = await cacheBustImport('./modules/functions/initiatePesFetch.js');

class pesInitiateBox extends box {

    child;

    constructor(parent, child) {
        console.log('+++ Function +++ pesInitiateBox.constructor');

        super(parent);
        this.child = child;
        if (this.constructor == pesInitiateBox) {
            throw new Error('Cannot create a instance of Abstract class');
        }
        this.listenForInitiatePes();

        console.log('--- Function --- pesInitiateBox.constructor');
    }

    listenForInitiatePes() {
        var $this = this;
        $(document).on("click", $this.child.initButton, function (e) {
            $(this).addClass("spinning");
            if ($this.child.cnumFieldId == 'this' || $this.child.workerIdFieldId == 'this') {
                var cnum = $(this).data("cnum");
                var workerId = $(this).data("workerid");
            } else {
                var cnum = $($this.child.cnumFieldId).val();
                var workerId = $($this.child.workerIdFieldId).val();
            }
            initiatePes(cnum, workerId, $this.table);
        });
    }
}

export { pesInitiateBox as default };