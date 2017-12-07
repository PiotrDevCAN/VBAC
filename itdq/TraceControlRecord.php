<?php
namespace itdq;
/**
 * Trace control records control the level of tracing performed by the application.
 * 
 * It is down to the develop to put suitable TRACE statements in the code, but using Trace Control the end user/debugger
 * can determine which of the Trace statements they wish to see.
 * 
 * Tracing can be "inclusive" - ie ALL TRace Statements will write to the Trace Table, EXCEPT those specifically EXLUDED
 * or
 * it can be "exclusive" - ie ONLY those Trace Statements specfically INCLUDED will write to the TRACE table.
 *
 * 2012-02-01 methodTimnigs and classTimings added. Use in place of methodInclude/classInclude if you just want the traceTimings comments to be traced.
 *
 * @author GB001399
 * @package itdqlib
 *
 *
 */
class TraceControlRecord extends DbRecord {
	protected $TRACE_CONTROL_TYPE;
	protected $TRACE_CONTROL_VALUE;
	protected $trace_class_name;
	protected $trace_method_name;

	
	const CONTROL_TYPE_CLASS_TIMINGS = 'classTimings';
	const CONTROL_TYPE_CLASS_INCLUDE = 'classInclude';
	const CONTROL_TYPE_CLASS_EXCLUDE = 'classExclude';
	const CONTROL_TYPE_METHOD_TIMINGS = 'methodTimings';
	const CONTROL_TYPE_METHOD_INCLUDE = 'methodInclude';
	const CONTROL_TYPE_METHOD_EXCLUDE = 'methodExclude';
	
	public static $controlTypeHeadings = array(TraceControlRecord::CONTROL_TYPE_CLASS_EXCLUDE => 'Classes Excluded',
	    TraceControlRecord::CONTROL_TYPE_CLASS_INCLUDE => 'Classes Included',
	    TraceControlRecord::CONTROL_TYPE_CLASS_TIMINGS => 'Classes Timed',
	    TraceControlRecord::CONTROL_TYPE_METHOD_EXCLUDE => 'Methods Excluded',
	    TraceControlRecord::CONTROL_TYPE_METHOD_INCLUDE => 'Methods Included',
	    TraceControlRecord::CONTROL_TYPE_METHOD_TIMINGS => 'Methods Timed',
	);
	
	
	private static $controlTypes = array(TraceControlRecord::CONTROL_TYPE_CLASS_EXCLUDE,TraceControlRecord::CONTROL_TYPE_CLASS_INCLUDE,TraceControlRecord::CONTROL_TYPE_CLASS_TIMINGS,TraceControlRecord::CONTROL_TYPE_METHOD_EXCLUDE,TraceControlRecord::CONTROL_TYPE_METHOD_INCLUDE,TraceControlRecord::CONTROL_TYPE_METHOD_TIMINGS);
	
	function __construct($pwd=null){
		Trace::traceComment(null,__METHOD__);		
		parent::__construct ( $pwd );
		$this->fcFormName = 'TraceControl';
	}
	
	function displayForm($mode){
		Trace::traceComment(null,__METHOD__);	
        $this->modeSetup($mode);
		
		$loader = new Loader();
		
		$allClasses = Trace::allApplicationsClasses('dpulse',true);	
		sort($allClasses);
		$allFunctions = array('Methods...');
		
 		echo "<div class='panel panel-primary'>";
        echo "<div class='panel-heading'>Trace Control</div>";	
        echo "<div class='container'>";
        
        echo "<div class='form-horizontal'>";
        $onChange = "onChange='var traceControlRec = new TraceControlRecord(); traceControlRec.populateMethodSelect()'";
		$this->formSelect( self::$controlTypes, 'Control Type', 'TRACE_CONTROL_TYPE', $this->chkState,null,$onChange);	
		$this->formSelect($allClasses, 'Class', 'trace_class_name',$this->state,null,$onChange);
        $this->formSelect($allFunctions, 'Method', 'trace_method_name','disabled' ,null,'Method...','blue-med');

       
        echo "<input type='hidden' name='mode' value='insert'> ";    
        $this->displaySaveReset();
        
        echo "</div>"; // form-horiztonal
        echo "</div>"; // container
        echo "</div>"; // panel
        
	}
	

	
}
?>
