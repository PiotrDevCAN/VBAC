/**
 *
 */

class formMessageArea {

	constructor() {
		console.log('+++ Function +++ formMessageArea.constructor');

		console.log('--- Function --- formMessageArea.constructor');
	}
    
	showMessageArea() {
		$('#formMessageArea').html("<h4>Data loading...<span class='glyphicon glyphicon-refresh spinning'></span></h4>");
		$('#formMessageAreaWrapper').show();
	}

	clearMessageArea() {
		$('#formMessageArea').html("");
		$('#formMessageAreaWrapper').hide();
	}
}

const FormMessageArea = new formMessageArea();

export { FormMessageArea as default };