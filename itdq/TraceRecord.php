<?php
namespace itdq;


class TraceRecord extends DbRecord {
    
    protected $daysToKeep;
    
    
    function displayConfirmDeletionDays($mode=null){
        $mode = !empty($mode) ? FormClass::$modeDISPLAY : $mode;      
        
        Trace::traceComment(null,__METHOD__);
        if ($mode == FormClass::$modeDISPLAY) {
            $state = 'READONLY';
            $chkState = 'DISABLED';
            $notEditable = 'READONLY';
        } else {
            $state = null;
            $chkState = null;
            $notEditable = 'READONLY';
        }
    
        echo "<div class='panel panel-primary'>";
        echo "<div class='panel-heading'>Confirm Deletion Days</div>";
        echo "<div class='container'>";
    
        echo "<div class='form-horizontal'>";
    
    
        $daysToKeepList = array(1,2,3,4,5,6,7,14,21,28,31,62,93,180,365);
         
        $this->formSelect( $daysToKeepList, 'Days to Keep', 'daysToKeep');
    
        echo "<input type='hidden' name='mode' value='delete'> ";
    
        echo "<div class='form-group'>";
        echo "<div class='col-sm-2'>";
        echo "</div>";
        echo "<div class='btn-group col-sm-8' >";
        echo "<button type='submit' class='btn btn-default'>Save</button>";
        echo "<button type='reset' class='btn btn-default'>Reset</button>";
        echo "</div>"; // btn-group
        echo "</div>"; // form-group
    
        echo "</div>"; // form-horiztonal
        echo "</div>"; // container
        echo "</div>"; // panel
    }
}