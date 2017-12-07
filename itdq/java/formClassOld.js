/**
 * 
 */

var FormClass = {
		
	init : function(){
		FormClass.enableTinyMce();	
		
//		$('form').bind('form-pre-serialize', function(e) {
//		    tinyMCE.triggerSave();
//		});
		
		
	},	

	enableTinyMce : function() {
		console.log('enableTinyMce');
		tinymce.init({
			selector : 'textarea.tinyMce',
			statusbar : false,
			plugins : [
					'advlist autolink lists link charmap print preview anchor',
					'searchreplace visualblocks code fullscreen',
					'insertdatetime table contextmenu paste' ],
			toolbar : 'bold italic'
		});

	},

	enableTinyMceForClass : function(classId) {
		console.log('enableTinyMceForClass' + classId);
		tinymce.init({
			selector : 'textarea.' + classId,
			statusbar : false,
			plugins : [ 'advlist autolink lists link charmap print preview anchor',
					'searchreplace visualblocks code fullscreen',
					'insertdatetime table contextmenu paste' ],
			toolbar : 'bold italic | bullist numlist outdent indent'
		});
	},

	enableTinyMceReadOnly : function(classId) {		
		console.log('enableTinyMceReadOnly:' + classId);
	    tinymce.init({
	        selector: 'textarea.' + classId,
	        toolbar: false,
	        menubar: false,
	        preview_styles: false,
	        statusbar:false,
            readonly : 1       
	    
	    });
	},
	
	disableTinyMce : function(classId){
		console.log('disableTinyMce:' + classId);
		//tinymce.remove('textarea.' + classId);
		
		$("."+classId).each(function() { $(this).tinymce().remove();});
		
	},
	
	
	disableTinyMceForId : function(id) {
		tinymce.EditorManager.execCommand('mceRemoveEditor', true, id);
	},
	
	
	
	setTinyMceElementBackgroundColor : function(elementId, color) {
		color = typeof color !== 'undefined' ? color : '#eeeeee';
		var editor = tinyMCE.get(elementId);
		editor.getBody().style.backgroundColor = color;
	}
	

}



$( document ).ready(function() {
    FormClass.init();
});
