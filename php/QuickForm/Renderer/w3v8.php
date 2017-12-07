<?php
require_once 'HTML/QuickForm/Renderer/Default.php';

class HTML_QuickForm_Renderer_w3v8 
	extends HTML_QuickForm_Renderer_Default {

	var $_formTemplate = 
		"\n<form{attributes}>\n<div>\n{hidden}<table border=\"0\" >\n{content}\n</table>\n</div>\n</form>";

	var $_headerTemplate = 
		"\n<tr>\n\t<th colspan=\"3\">{header}</th>\n</tr>\n";

	var $_elementTemplate = 
		"<tr>\n\t<td class=\"col1\"><!-- BEGIN required -->*<!-- END required --></td>\n\t<td class=\"col2\"><!-- BEGIN label -->{label}<!-- END label --></td>\n\t<td class=\"col3\"><!-- BEGIN error --><span class=\"alert-stop\">{error}</span><br /><!-- END error -->\t{element}</td>\n</tr>\n";

	var $_submitTemplate = 
		"\n<tr>\n\t<td colspan=\"3\" align=\"right\">\n\t{element}\n\t</td>\n</tr>\n";

	var $_groupTemplateSubmit = "\n<br />\n<div class=\"hrule-solid\"></div>\n<div style=\"padding-top:3px\">\n<span class=\"button-blue\">\n{content}\n</span>\n</div>\n";
	#FIXME: the <style> tag is not allowed in the body of an xhtml doc
	var $_tableStyle = "\n<style type=\"text/css\">\n<!--\ntd.col1 { padding: .4em 0em .3em 0em; }\ntd.col2 { padding: .3em 1em .3em 0em; }\ntd.col3 { padding: .3em 1em; }\n-->\n</style>\n";

	function HTML_QuickForm_Renderer_w3v8() {
		$this->setGroupTemplate($this->_groupTemplateSubmit, 'submit');
		$this->setElementTemplate($this->_submitTemplate, 'submit');
		parent::HTML_QuickForm_Renderer_Default();
	}
	
	function renderElement(&$element, $required, $error) {
		$element->_generateId();
		parent::renderElement($element, $required, $error);

		if ($this->_inGroup) {
			$this->_html = str_replace('<!-- BEGIN label -->', 
				'', $this->_html);
			$this->_html = str_replace('<!-- END label -->', 
				'', $this->_html);
		} else {
			$this->_html = str_replace('<!-- BEGIN label -->', 
				'<label for="'.$element->getAttribute('id').'">', 
				$this->_html);
			$this->_html = str_replace('<!-- END label -->', 
				'</label>', 
				$this->_html);
		}
	}

	function finishForm(&$form) {
		#FIXME: the <style> tag is not allowed in the body of an xhtml doc
		$html = $this->_tableStyle;
		$conf = FALSE;
		$required = FALSE;
		$errors = FALSE;

		if ($form->getConfidential()) {
			$conf = TRUE;
		}
		if (!empty($form->_required) && !$form->_freezeAll) {
			$required = TRUE;
		}
		if (!empty($form->_errors)) {
			$errors = TRUE;
		}

		if ($conf || $required || $errors) {
			$html .= "<br />\n";
		}

		// add confidential statement if required
		if ($conf) {
			$html .= "<p class=\"confidential\">IBM Confidential</p>\n";
		}

		// add a required note, if one is needed
		if ($required) {
			$html .= "<p>Required fields are marked with an asterisk (*) and must be filled in to complete the form.</p>\n";
		}

		// shows errors if they exist
		if ($errors) {
			$html .= "<div class=\"alert-stop\">An error occurred. Please check below for errors, such as a required field not filled in.</div>\n";
		}

		$html .= "<br />\n";

		// add form attributes
		$html .= str_replace('{attributes}', $form->getAttributes(true), $this->_formTemplate);

		// add hidden elements
		if (strpos($this->_formTemplate, '{hidden}')) {
			$html = str_replace('{hidden}', $this->_hiddenHtml, $html);
		} else {
			$this->_html .= $this->_hiddenHtml;
		}
		$this->_hiddenHtml = '';

		// add content
		$this->_html = str_replace('{content}', $this->_html, $html);

		// add a validation script
		if ('' != ($script = $form->getValidationScript())) {
			$this->_html = $script . "\n" . $this->_html;
		}

		if (!empty($form->_focused)) {
			$this->_html .= ' 
				<script type="text/javaScript">
				<!--
				document.' . $form->_attributes['name']. '.' . $form->_focused . '.focus();
				-->
				</script>
				';
		}
	}
}