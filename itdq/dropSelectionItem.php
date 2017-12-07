<?php
namespace itdq;
/*
 * 
 * These Objects are used to define the Items displayed on the Drop Selection Panel 
 * 
 */

class DropSelectionItem {
	protected $label;
	protected $first;
	protected $column;
	protected $data;
	protected $type;
	protected $operator;
	protected $state;
	
	
	function __construct($label,$first,$column,$data,$type='char',$operator='=',$state=null){
		$this->label = $label;
		$this->first = $first;
		$this->column = $column;
		$this->data = $data;
		$this->type = $type;
		$this->operator = $operator;
		$this->state = $state;		
	}
	
	function label(){
		return $this->label;
	}
	
	function first(){
		return $this->first;
	}
	
	function column(){
		return $this->column;		
	}
	
	function data(){
		return $this->data;
	}
	
	function type(){
		return $this->type;
	}
	
	function operator(){
		return $this->operator;
	}
	
	function state(){
		return $this->state;
	}
}
?>