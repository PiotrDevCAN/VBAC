/**
 *
 */

class modalMessageArea {

	constructor() {
		console.log('+++ Function +++ modalMessageArea.constructor');

		console.log('--- Function --- modalMessageArea.constructor');
	}
    
	showMessageArea() {
		$('.messageArea').html("<h4>Data loading...<span class='glyphicon glyphicon-refresh spinning'></span></h4>");
	}

	clearMessageArea() {
		$('.messageArea').html("");
	}
}

const ModalMessageArea = new modalMessageArea();

export { ModalMessageArea as default };