/**
 *
 */

class formMessageArea {

	constructor() {
		console.log('+++ Function +++ formMessageArea.constructor');

		console.log('--- Function --- formMessageArea.constructor');
	}
    
	showMessageArea() {
		$('#formMessageAreaWrapper').show();
	}

	clearMessageArea() {
		$('#formMessageAreaWrapper').hide();
	}
}

const FormMessageArea = new formMessageArea();

export { FormMessageArea as default };