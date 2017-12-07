/**
 *
 */

function Form() {

	var dataTableElements;
	var currentXmlDoc;

	this.init = function(){
		console.log('+++ Function +++ Form.init');
		//this.enableTinyMceForClass();	 - Caused problems on the ActionForms page when it was initialsed here.

		$('select').select2();

		console.log('--- Function --- Form.init');

	},

	this.enableTinyMceForClass = function(classId, callback) {
		console.log(callback);

		callback = (typeof callback === 'undefined') ? null : callback;


		console.log(callback);

		console.log('+++ function +++ enableTinyMceForClass:' + classId);
		classId = (typeof classId === 'undefined') ? 'tinyMce' : classId;
		tinymce.init({
			selector : 'textarea.' + classId,
			statusbar : false,
			plugins : [ 'advlist autolink lists link charmap print preview anchor',
					'searchreplace visualblocks code fullscreen textcolor',
					'insertdatetime table contextmenu paste' ],
			toolbar : 'bold italic | bullist numlist outdent indent  |  forecolor backcolor',
			init_instance_callback : callback,
			relative_urls: false,
			convert_urls: false



//		    setup: function(editor) {
//		        editor.on('remove', function(e) {
//		            console.log('+++tinyMce remove event+++', e);
//		        });
//		    },


		});
		console.log('--- function --- enableTinyMceForClass:' + classId);
	},

	this.enableTinyMceReadOnly = function(classId) {
		console.log('+++ function +++ enableTinyMceReadOnly:' + classId);
		classId = (typeof classId === 'undefined') ? 'tinyMceRo' : classId;
	    tinymce.init({
	        selector: 'textarea.' + classId,
	        toolbar: false,
	        menubar: false,
	        preview_styles: false,
	        statusbar:false,
            readonly : 1,
			relative_urls: false,
			convert_urls: false

	    });
	    console.log('--- function --- enableTinyMceReadOnly:' + classId);
	},

	this.disableTinyMce = function(classId){
		console.log('disableTinyMce:' + classId);
		$("."+classId).each(function() { $(this).tinymce().remove();});

	},


	this.disableTinyMceForId = function(id) {
		tinymce.EditorManager.execCommand('mceRemoveEditor', true, id);
	},



	this.setTinyMceElementBackgroundColor = function(elementId, color) {
		console.log('+++ setTinyMceElementBackgroundColor +++');
		color = typeof color !== 'undefined' ? color : '#eeeeee';
		var editor = tinyMCE.get(elementId);

		console.log('editor');

		editor.getBody().style.backgroundColor = color;
	},


	this.setSelectionBox = function (selectElementId, selectValue, disableSelectBox, select2Field){

		disableSelectBox = typeof disableSelectBox=='undefined' ? true : disableSelectBox;
		select2Field = typeof select2Field=='undefined' ? true : select2Field;
		var isSelect2 = false;

		if(select2Field ){
			if( $('#'+selectElementId).data('select2')){
				isSelect2 = true;
			};
			console.log(selectElementId);
			console.log($('#'+selectElementId));
			console.log(isSelect2);

			if(isSelect2){
				$('#'+selectElementId).select2('destroy');
		 	}
		}

		var entryFound = false;
		var selectElement = document
				.getElementById(selectElementId);
		for (var i = 0; i < selectElement.length; i++) {
			if ((selectElement[i].value.trim() == selectValue.trim()) && !entryFound) {
				selectElement.selectedIndex = i;
				console.log('Selected '	+ selectElement[i].value + ' Index:' + i);
				selectElement.disabled = disableSelectBox;
				entryFound = true;
			}
		}
		if(select2Field && isSelect2){
			$('#'+selectElementId).select2();
		}
		return entryFound;

	},


	this.setRadioButtons = function(radioButtonsId,buttonValue,disableRadioButtons){

		disableRadioButtons = disableRadioButtons=='undefined' ? true : disableRadioButtons;

		var buttons = document.getElementsByName(radioButtonsId);
		for (var i = 0; i < buttons.length; i++) {
			if (buttons[i].value == buttonValue.trim()) {
				buttons[i].disabled = false;
				buttons[i].checked = true;
				buttons[i].disabled = disableRadioButtons;
			} else {
				buttons[i].disabled = disableRadioButtons;
			}
		}

	},


	this.enableDataTables = function(selector){
		console.log('+++ form.enableDataTables +++');
		selector = (typeof selector === 'undefined') ? '.table' : selector;
		console.log(selector);
		dataTableElements = $(selector).dataTable({
			'responsive' : true,
			'pagingType' : 'full_numbers',
			'type' : 'tinymce',
            'dom': 'BC<\"clear\">lfrtip',
            'buttons': [
                      'copyHtml5','csvHtml5', 'pdfHtml5'
                  ],

      		'fnDrawCallback' : function(o) {
    			form = new Form();
    			form.enableTinyMceReadOnly();
    			form.enableTinyMceForClass();
    		},


			"fnPreDrawCallback" : function(oSettings) {
				tinymce.remove('.tinyMceRo');
				tinymce.remove('.tinyMce');
				return true;
			},

		});

		console.log(dataTableElements);
		console.log('--- form.enableDataTables ---');
	},

	this.populateFormDivFromXmlDoc = function(formId, xmlDocId){
		console.log('+++ function +++ form.populateFormDivFromXmlDoc');
		var formElement = document.getElementById(formId);
		var dataFromXmlDoc = this.currentXmlDoc.getElementsByTagName(xmlDocId);
		var data = dataFromXmlDoc[0].innerHTML;
		var D1 = data.replace('<![CDATA[','');
		var D2 = D1.replace(']]>','');
		formElement.innerHTML = (typeof data === 'undefined') ? '<p>Error in populateFormDivFromXmlDoc' : D2;
		console.log('--- function --- form.populateFormDivFromXmlDoc');
	},

	this.logAjaxDiagnostics = function(diagnosticTagName){
		diagnosticsTagName = (typeof diagnosticsTagName === 'undefined') ? 'diagnostics' : diagnosticsTagName;
		var diagnostics = this.currentXmlDoc.getElementsByTagName("diagnostics");
		console.log(diagnostics);
		return diagnostics;
	},


	this.showGlyphiconSpinner = function(elementId){
		console.log('+++ function +++ showGlyphiconSpinner:' + elementId);
		element = document.getElementById(elementId);
		element.style.display = 'block';
		element.innerHTML = "<span class='glyphicon glyphicon-refresh glyphicon-refresh-animate spinningsm' aria-hidden='true'></span>";
		console.log('--- function --- showGlyphiconSpinner');

	},

	this.clearGlyphiconSpinner = function(elementId, hide=true){
		console.log('+++ function +++ clearGlyphiconSpinner:' + elementId);
		element = document.getElementById(elementId);
		hide ? element.style.display = 'hide' : null;
		element.innerHTML = "";
		console.log('--- function --- clearGlyphiconSpinner');

	},


	this.getSelectedValue = function(selectElementId){
		var selectBox = document.getElementById(selectElementId);
		var selectIndex = selectBox.selectedIndex;
		var selectedValue = selectBox[selectIndex].value;
		selectedValue = (typeof selectedValue === 'undefined') ? false : selectedValue;
		return selectedValue;
	},


	this.displayAjaxError = function(errorMessage){
		$('#ajaxErrorMessage').html(errorMessage);
		$('#ajaxError').modal('show');
	},

	this.displayError = function(errorMessage){
		$('#errorMessage').html(errorMessage);
		$('#error').modal('show');
	},


	this.setCookie = function (cname, cvalue, exdays) {
	    var d = new Date();
	    d.setTime(d.getTime() + (exdays*24*60*60*1000));
	    var expires = "expires="+d.toUTCString();
	    document.cookie = cname + "=" + cvalue + "; " + expires;
	},

	this.getCookie = function (cname) {
	    var name = cname + "=";
	    var ca = document.cookie.split(';');
	    for(var i=0; i<ca.length; i++) {
	        var c = ca[i];
	        while (c.charAt(0)==' ') c = c.substring(1);
	        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
	    }
	    return "";
	}


}


$( document ).ready(function() {
	var masterForm = new Form();
    masterForm.init();
});
