<?php
require_once("HTML/Table.php");

class w3_Table extends HTML_Table {

	var $row_header = 0;
	var $row_rule = 0;
	var $header_attr;

	function w3_Table($title, $header, $attr = array()) {
		// basic table attributes
		$table_attr = array(
			'class' => 'basic-table',
			'cellspacing' => 1,
			'cellpadding' => 0,
		);
		if (isset($attr['width'])) {
			$table_attr['width'] = $attr['width'];
		}
		if (isset($attr['id'])) {
			$table_attr['id'] = $attr['id'];
			$this->table_id = $attr['id'];
		}

		// initalize the table
		parent::HTML_Table($table_attr);
		$this->setAutoGrow(true);
		$this->setAutoFill("&#160;");

		// title
		$this->setCaption($title, array('style' => 'white-space: nowrap;'));

		// Create the header row. We'll fill it later
		$this->header_attr = array(
			'class' => 'blue-med-dark',
			);
		$this->row_header = $this->addRow($header, $this->header_attr, 'TH', 
			TRUE);

	}

	function addRow($content = array(), $attributes = array(), $type = 'TD',
		$inTR = FALSE) {

		$row = parent::addRow(array_values($content), $attributes, $type, 
			$inTR);
		$col_count = sizeof($content);
		$col = 0;
		foreach ($content as $cell_data) {
			$this->updateCellAttributes($row, $col, 
				$this->_cell_attr($cell_data));
			$col++;
		}
		return $row;
	}

	// The available classes come from
	// http://w3.ibm.com/ui/v8/css/tables.css

	function setHeaderClass($class = NULL) {
		if ($class) $this->header_attr['class'] = $class;
		else unset( $this->header_attr['class'] );
	}

	function setHeaderStyle($style = NULL) {
		if ($style) $this->header_attr['style'] = $style;
		else unset( $this->header_attr['style'] );

	}

	/* This function is used on small data sets. That is, not for
	   database results
	 */
	function setData(&$data) {
		if (!is_array($data)) {
			$data = array($data); 
		}
		$this->total_items = sizeof($data);
		foreach ($data as $row) {
			$this->addRow($row);
		}
	}

	/* This function requires an ADODB <http://adodb.sf.net> database
	   object.
	 */
	function fetchADODBData(&$db, $sql, $inputarr = FALSE) {
		if (!is_object($db) || !$db->IsConnected()) {
			return 0;
		}
		$result = $db->GetAll($sql, $inputarr);
		if (!$result) {
			return 0;
		} 
		foreach ($result as $row) {
			$this->addRow($row);
		}
		return count($result);
	}

	function addRule() {
		if (!$this->row_rule) {
			$this->row_rule = $this->addRow();
			$this->setCellContents($this->row_rule, 0, 
				'<div class="hrule-dots">&nbsp;</div>');
		}
	}

	function toHtml() {
		// header
		// set row attributes
		$this->setRowAttributes($this->row_header, $this->header_attr, TRUE);
		// set field attributes
		$this->header_attr['nowrap'] = 'nowrap';
		$this->setRowAttributes($this->row_header, $this->header_attr, FALSE);

		// content shading
		$this->altRowAttributes(1, null, array('class' => 'even'), TRUE);

		// dotted rule for bottom of table
		$this->addrule();

		// set colspan for dotted rule
		$col_count = $this->getColCount();
		$this->setCellAttributes($this->row_rule, 0, 
			array('style' => 'padding: .3em 0;', 'colspan' => $col_count));

		// For every row after the dotted rule, 
		// make the background 'odd' (white)
		$numRows = $this->getRowCount();
		for ($row = $this->row_rule; $row <= $numRows; $row++) {
			$this->setRowAttributes($row, array('class'=>'odd'), TRUE);
		}

		// return the table
		return parent::toHtml();
	}

	function getID() {
		return $this->table_id;
	}

	function _cell_attr ($data="") {
		if ($data == "") 
			return array();
		if (is_numeric($data))
			return array('class' => 'number');
		if (strtotime($data) != "-1") 
			return array('class' => 'date');
		if(strpos($data, '<input ') !== FALSE)
			return array('class' => 'center');
		if(strpos($data, '<select ') !== FALSE)
			return array('class' => 'center');
		if(strpos($data, '<textarea ') !== FALSE)
			return array('class' => 'center');
		return array();
	}
}


/* for backwards-compatibility only ... do not use in new code */
function w3table($data) {
	$table_attr = array();

	if (isset($data['width'])) {
		$table_attr['width'] = $data['width'];
	} else {
		$table_attr['width'] = '100%';
	}
	if (isset($data['id'])) {
		$table_attr['id'] = $data['id'];
	}

	$table = new w3_Table($data['title'], $data['head'], $table_attr);

	if (isset($data['color'])) {
		$table->setHeaderClass($data['color']);
		$table->setHeaderStyle();
	}

	foreach ($data['content'] as $row) {
		$table->addRow($row);
	}
	return $table->toHtml();
}

?>