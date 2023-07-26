/*
 *
 *
 *
 */

class revalidationInfoBox {

    static modalId = 'confirmOffboardingInfoModal';

    constructor(parent) {
        console.log('+++ Function +++ revalidationInfoBox.constructor');

        console.log('--- Function --- revalidationInfoBox.constructor');
    }

    resetModal() {
        $('#' + revalidationInfoBox.modalId + ' .panel')
            .removeClass("panel-danger")
            .removeClass("panel-warning")
            .removeClass("panel-success");
    }

    showModal() {
        $('#' + revalidationInfoBox.modalId).modal("show");
    }

    hideModal() {
        $('#' + revalidationInfoBox.modalId).modal("hide");
    }

    displayMessage(message, panelclass) {
        this.resetModal();
        $('#' + revalidationInfoBox.modalId + ' .panel').html(message);
        $('#' + revalidationInfoBox.modalId + ' .panel').addClass(panelclass);
        this.showModal();
    }
}

const RevalidationInfoBox = new revalidationInfoBox();

export { RevalidationInfoBox as default };