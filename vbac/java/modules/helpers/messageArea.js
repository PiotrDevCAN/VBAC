/**
 *
 */

class messageArea {

    constructor() {
        console.log('+++ Function +++ messageArea.constructor');

        console.log('--- Function --- messageArea.constructor');
    }

    showMessageArea() {
        $('#messageArea').html("<div class='col-sm-4'></div><div class='col-sm-4'><h4>Form loading...<span class='glyphicon glyphicon-refresh spinning'></span></h4><br/><small>This may take a few seconds</small></div><div class='col-sm-4'></div>");
    }

    clearMessageArea() {
        $('#messageArea').html("");
    }
}

const MessageArea = new messageArea();

export { MessageArea as default };