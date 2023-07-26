/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class revalidationBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ revalidationBox.constructor');

        super(parent);
        this.listenForOffboardingModalShown();

        console.log('--- Function --- revalidationBox.constructor');
    }

    resetModal() {
        $("#confirmOffboardingModal .panel")
            .removeClass("panel-danger")
            .removeClass("panel-warning")
            .removeClass("panel-success");
    }

    showModal() {
        $("#confirmOffboardingModal").modal("show");
    }

    displayMessage(message, panelclass) {
        this.resetModal();
        $("#confirmOffboardingModal .panel").html(message);
        $("#confirmOffboardingModal .panel").addClass(panelclass);
        this.showModal();
        this.tableObj.table.ajax.reload();
    }

    listenForOffboardingModalShown() {
        $(document).on('shown.bs.modal', '#confirmOffboardingModal', function (e) {
            $("#offboarding_proposed_leaving_date").datepicker({
                dateFormat: "dd M yy",
                // dateFormat: "d M Y",
                altField: "#offboarding_proposed_leaving_date_db2",
                altFormat: "yy-mm-dd",
            });
        });
    }
}

export { revalidationBox as default };