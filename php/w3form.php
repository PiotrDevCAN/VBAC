<?php
require_once 'HTML/QuickForm.php';

class w3_Form extends HTML_QuickForm {
	
	var $_confidential = FALSE;

	// The name of the form element to focus immediately upon load
	var $_focused;

	function w3_Form($formName='', $method='post', $action='', $target='',
		$attributes=null, $trackSubmit = false) {
		// Call parent's constructor
		parent::HTML_QuickForm($formName, $method, $action, $target,
			$attributes, $trackSubmit);
	}

	function setConfidential($status = TRUE) {
		if ($status === TRUE) {
			$this->_confidential = TRUE;
		} elseif ($status === FALSE) {
			$this->_confidential = FALSE;
		} 
	}

	function getConfidential() {
		return $this->_confidential;
	}

	function setFocus($name) {
		$this->_focused = $name;
	}
}
