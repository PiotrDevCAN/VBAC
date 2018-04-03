<?php
namespace vbac;

use itdq\DbTable;
use itdq\DbRecord;
use itdq\FormClass;
use itdq\Loader;


class assetRequestsTable extends DbTable{
    
    const RETURN_WITH_BUTTONS = true;
    const RETURN_WITHOUT_BUTTONS = false;
    
    public $currentVarb;
    
    private $preparedUpdateUidsStmt;
    
    private static $portalHeaderCells = array('REFERENCE','CT_ID','PERSON','ASSET','STATUS','JUSTIFICATION','REQUESTOR','APPROVER',
        'LOCATION','PRIMARY_UID','SECONDARY_UID','DATE_ISSUED_TO_IBM','DATE_ISSUED_TO_USER','DATE_RETURNED',
        'ORDERIT_VARB_REF','ORDERIT_NUMBER','ORDERIT_STATUS','ORDERIT_TYPE', 'COMMENT');
    

//     function saveRecord(assetRequestRecord $record, $populatedColumns, $nullColumns, $commit){
//         parent::saveRecord($record, $populatedColumns, $nullColumns, $commit);
//     }
    
    static function portalHeaderCells(){
        $headerCells = null;
       // $widths = array(5,5,5,5,10,10,10,10,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
         foreach (self::$portalHeaderCells as $key => $value) {
//                 $width = 'width="' . $widths[$key] . '"';
                $headerCells .= "<th>";
                $headerCells .= str_replace("_", " ", $value);
                $headerCells .= "</th>";
         }
         return $headerCells;
    }

    static function returnForPortal($predicate=null,$withButtons=true){
        $sql  = " SELECT ";
//        $sql .= " concat('000000',AR.REQUEST_REFERNCE) as car,";
        $sql .= " AR.REQUEST_REFERENCE as reference, ";
        $sql .= " P.CT_ID as CT_ID, P.EMAIL_ADDRESS, P.NOTES_ID, AR.ASSET_TITLE as ASSET, STATUS, ";
        $sql .= " BUSINESS_JUSTIFICATION as JUSTIFICATION, REQUESTOR_EMAIL as REQUESTOR_EMAIL, REQUESTED as REQUESTED_DATE,  ";
        $sql .= " APPROVER_EMAIL, APPROVED as APPROVED_DATE, ";
        $sql .= " USER_LOCATION as LOCATION, ";
        $sql .= " PRIMARY_UID, SECONDARY_UID, DATE_ISSUED_TO_IBM, DATE_ISSUED_TO_USER, DATE_RETURNED,   ";
        $sql .= " ORDERIT_VARB_REF, ORDERIT_NUMBER, ORDERIT_STATUS, ";
        $sql .= " RAL.ORDER_IT_TYPE as ORDERIT_TYPE ";
        $sql .= " ,RAL.ASSET_PRIMARY_UID_TITLE ";
        $sql .= " ,RAL.ASSET_SECONDARY_UID_TITLE ";
        $sql .= " , COMMENT ";
        $sql .= " FROM " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " ON AR.CNUM = P.CNUM ";      
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$REQUESTABLE_ASSET_LIST . " as RAL ";
        $sql .= " ON TRIM(RAL.ASSET_TITLE) = TRIM(AR.ASSET_TITLE) ";
        $sql .= " WHERE 1=1 ";
        $sql .= $predicate;  
        
        $rs = db2_exec($_SESSION['conn'],$sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
        }

        $data = array();

        while(($preTrimmed=db2_fetch_assoc($rs))==true){
            
            $row = array_map('trim', $preTrimmed);
                          
            
            $reference = $row['REFERENCE'];
            
            $row['REFERENCE'] = trim($row['ORDERIT_NUMBER']) . ":" . $reference;
            $row['REFERENCE'] = empty($row['ORDERIT_VARB_REF']) ? $row['REFERENCE'] : $row['REFERENCE'] . "<br/><small>" . $row['ORDERIT_VARB_REF'] . "</small>";
            
            
            
            $status = trim($row['STATUS']);
            $statusWithVarb = trim($row['ORDERIT_VARB_REF']) != null ? $status . " (" . trim($row['ORDERIT_VARB_REF']) . ") " : $status;
            
            $approveButton  = "<button type='button' class='btn btn-default btn-xs btnAssetRequestApprove btn-success' aria-label='Left Align' ";
            $approveButton .= "data-reference='" .trim($row['REFERENCE']) . "' ";
            $approveButton .= "data-requestee='" .trim($row['EMAIL_ADDRESS']) . "' ";
            $approveButton .= "data-asset='"     .trim($row['ASSET']) . "' ";
            $approveButton .= "data-orderitstatus='".trim($row['ORDERIT_STATUS']) . "' ";
            $approveButton .= "data-toggle='tooltip' data-placement='top' title='Approve the request'";
            $approveButton .= " > ";
            $approveButton .= "<span class='glyphicon glyphicon-ok ' aria-hidden='true'></span>";
            $approveButton .= " </button> ";
            
            $approveButton = $withButtons ? $approveButton : '';
            
            $rejectButton  = "<button type='button' class='btn btn-default btn-xs btnAssetRequestReject btn-danger' aria-label='Left Align' ";
            $rejectButton .= "data-reference='" .trim($row['REFERENCE']) . "' ";
            $rejectButton .= "data-requestee='" .trim($row['EMAIL_ADDRESS']) . "' ";
            $rejectButton .= "data-asset='"     .trim($row['ASSET']) . "' ";
            $rejectButton .= "data-orderitstatus='".trim($row['ORDERIT_STATUS']) . "' ";
            $rejectButton .= "data-toggle='tooltip' data-placement='top' title='Reject the request'";
            $rejectButton .= " > ";
            $rejectButton .= "<span class='glyphicon glyphicon-remove ' aria-hidden='true'></span>";
            $rejectButton .= " </button> ";
            
            $rejectButton = $withButtons ? $rejectButton : '';
            
            $pmoOrFm = ($_SESSION['isFm'] || $_SESSION['isPmo']);            
            $notTheirOwnRecord = ( trim(strtolower($row['EMAIL_ADDRESS'])) != trim(strtolower($_SESSION['ssoEmail'])));               
            
            $allowedtoApproveReject = ( $pmoOrFm && $notTheirOwnRecord);      
            
            
            switch (true) {
                case $status == assetRequestRecord::$STATUS_APPROVED:
                    $button = $rejectButton;
                break;
                case $status == assetRequestRecord::$STATUS_CREATED:
                    $button = $rejectButton . $approveButton;
                    break;
                case $status == assetRequestRecord::$STATUS_REJECTED:
                    $button = $approveButton;
                    break;
                default:
                    $button = null;
                break;
            }
          
            $button = $withButtons ? $button : '';
     
            $row['STATUS'] = $allowedtoApproveReject  ? $button . $statusWithVarb : $statusWithVarb;
            
            $row['PERSON'] = $row['NOTES_ID'];
            if($withButtons){
                unset($row['EMAIL_ADDRESS']);
                unset($row['NOTES_ID']);
            }

                       
            $row['APPROVER'] = $row['APPROVER_EMAIL'] . "<br/><small>" . $row['APPROVED_DATE'] . "</small>";
            if($withButtons){
                unset($row['APPROVER_EMAIL']);
                unset($row['APPROVED_DATE']);
            }
            
            $row['REQUESTOR'] = $row['REQUESTOR_EMAIL'] . "<br/><small>" . $row['REQUESTED_DATE'] . "</small>";
            if($withButtons){
                unset($row['REQUESTOR_EMAIL']);
                unset($row['REQUESTOR_DATE']);
            }
            
            
            $editUidButton  = "<button type='button' class='btn btn-default btn-xs btnEditUid btn-primary' aria-label='Left Align' ";
            $editUidButton .= "data-reference='" .$reference . "' ";
            $editUidButton .= "data-requestee='" .trim($row['PERSON']) . "' ";
            $editUidButton .= "data-asset='"     .trim($row['ASSET']) . "' ";
            $editUidButton .= "data-primarytitle='".trim($row['ASSET_PRIMARY_UID_TITLE']) . "' ";
            $editUidButton .= "data-secondarytitle='".trim($row['ASSET_SECONDARY_UID_TITLE']) . "' ";
            $editUidButton .= "data-toggle='tooltip' data-placement='top' title='Update UID'";
            $editUidButton .= " > ";
            $editUidButton .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
            $editUidButton .= " </button> ";
            
            $editUidButton = $withButtons ? $editUidButton : '';
            
            
            $primaryUid = empty($row['PRIMARY_UID']) ? "<i>unknown</i>" : $row['PRIMARY_UID'];
            
            $row['PRIMARY_UID'] = !empty($row['ASSET_PRIMARY_UID_TITLE']) ? $editUidButton . $primaryUid :  "<i>Not Applicable</i>";
            $row['SECONDARY_UID'] = !empty($row['ASSET_SECONDARY_UID_TITLE']) ? $row['SECONDARY_UID'] :  "<i>Not Applicable</i>";
              
            $data[] = $row;
        }

        return $data;

    }
    
    private function getNextVarb(){
        $sql  = " INSERT INTO " . $_SESSION['Db2Schema'] . "." . allTables::$ORDER_IT_VARB_TRACKER;
        $sql .= " ( CREATED_BY ) VALUES ('" . db2_escape_string($_SESSION['ssoEmail']) . "' )" ;
        
        $rs = db2_exec($_SESSION['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        $varbRef = db2_last_insert_id($_SESSION ['conn']);
        
        $nextVarb = 'vARB' . substr('000000' . $varbRef ,-5);
        $this->currentVarb = $nextVarb;
        return $nextVarb;      
    }
    
    private function eligibleForOrderItPredicate($orderItType=0){
        /*
         *   ORDERIT_VARB_REF is null - Has not previously been exported.
         *   ORDER_IT_NUMBER is null  - Hasn't already been raised by the individual
         *   RAL.ORDER_IT_TYPE = '" . db2_escape_string($orderItGroup) . "'  - the ASSET_TITLE has a TYPE that matches the type we're processing
         *   AR.STATUS='" . assetRequestRecord::$STATUS_APPROVED . "'  - It's approved for processing
         *   
         *   '" . db2_escape_string($orderItType) . "' == 1  - It's a TYPE1 - ie it doesn't need a CT ID
         *   or P.CONTRACTOR_ID is not null                  - It's not a TYPE 1 - so it does need a CT ID, so CONTRACTOR ID can't be empty.
         *   
         *           
         */
        $predicate  = "";
        $predicate .= "   AND ORDERIT_VARB_REF is null and ORDERIT_NUMBER is null and RAL.ORDER_IT_TYPE = '" . db2_escape_string($orderItType) . "' AND AR.STATUS='" . assetRequestRecord::$STATUS_APPROVED . "' ";
        $predicate .= "   AND ('" . db2_escape_string($orderItType) . "' = '1' or P.CT_ID is not null)";
        
        return $predicate;
    }
    
    
    function getRequestsForOrderIt($orderItType, $first=false){    
        
        
        $nextVarb = $this->getNextVarb();   
        
        $commitState  = db2_autocommit($_SESSION['conn'],DB2_AUTOCOMMIT_OFF);
        
        $sql =  "UPDATE " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS ;
        $sql .= " SET ORDERIT_VARB_REF = '$nextVarb', STATUS='" . assetRequestRecord::$STATUS_EXPORTED . "' ";
        $sql .= " WHERE REQUEST_REFERENCE in ";
        $sql .= " (SELECT REQUEST_REFERENCE FROM " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR ";
        $sql .= "  LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$REQUESTABLE_ASSET_LIST . " AS RAL ";
        $sql .= "  ON RAL.ASSET_TITLE = AR.ASSET_TITLE ";
        $sql .= "  LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= "  ON AR.CNUM = P.CNUM ";    
        $sql .= "   WHERE 1=1 ";
        $sql .= $this->eligibleForOrderItPredicate($orderItType);
        $sql .= "   ORDER BY REQUEST_REFERENCE asc ";
        $sql .= "   FETCH FIRST 20 ROWS ONLY) ";
    
        $rs = db2_exec($_SESSION['conn'],$sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
       
        $sql = " SELECT ORDERIT_VARB_REF, REQUEST_REFERENCE, ";
        $sql .= " P.CT_ID as CT_ID, ";
        $sql .= " P.CTB_RTB as CTB_RTB, ";
        $sql .= " P.TT_BAU as TT_BAU, ";
        $sql .= " P.LOB as LOB, ";
        $sql .= " ASSET_TITLE, ";
        $sql .= " CASE when P.EMAIL_ADDRESS is null then P.NOTES_ID else P.EMAIL_ADDRESS end as IDENTITY, ";
        $sql .= " case when BUSINESS_JUSTIFICATION is null then 'N/A' else BUSINESS_JUSTIFICATION end as JUSTIFICATION, ";
        $sql .= " STATUS,  USER_LOCATION, REQUESTOR_EMAIL, date(REQUESTED) as REQUESTED,  APPROVER_EMAIL, DATE(APPROVED) as APPROVED, current date as EXPORTED ";
        $sql .= " FROM " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " ON AR.CNUM = P.CNUM ";      
        $sql .= " WHERE ORDERIT_VARB_REF = '" . $nextVarb . "' ";        
        $sql .= " ORDER BY ASSET_TITLE, REQUEST_REFERENCE desc";
  
        $data = array();
//         $data[] = "";
        $data[] = $first ? '"VARB","REQUEST","CT ID","CTB/RTB","TT/BAU","LOB","ASSET TITLE","EMAIL","JUSTIFICATION","STATUS","LOCATION","REQUESTOR","REQUESTED","APPROVER","APPROVED","EXPORTED"' : null;
        
        $rs2 = db2_exec($_SESSION['conn'],$sql);    
        if(!$rs2){
            db2_rollback($_SESSION['conn']);
            DbTable::displayErrorMessage($rs2, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        while(($row=db2_fetch_assoc($rs2))==true){    
            $trimmedData = array_map('trim', $row);
            $data[] = '"' . implode('","',$trimmedData) . '" ';
        }
        
        $requestData = '';        
        foreach ($data as $request){
            $requestData .= $request . "\n";            
        }
        
//         $base64Encoded = base64_encode($requestData);        
        
        db2_commit($_SESSION['conn']);
        db2_autocommit($_SESSION['conn'],$commitState);
 
        return $requestData;
    }
    
    
    function exportForOrderIT($orderItGroup = 0){
        $rows = $this->getRequestsForOrderIt($orderItGroup);
        return $rows;
    }
    
    
    function countApprovedForOrderItType($orderItType = 0){
        $sql  = " SELECT COUNT(*) as REQUESTS ";
        $sql .= " FROM " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " as AR ";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$REQUESTABLE_ASSET_LIST . " AS RAL ";
        $sql .= " ON RAL.ASSET_TITLE = AR.ASSET_TITLE ";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " ON AR.CNUM = P.CNUM "; 
        $sql .= " WHERE 1=1 ";
        $sql .= $this->eligibleForOrderItPredicate($orderItType);
       
        $rs = db2_exec($_SESSION['conn'],$sql);
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        $row = db2_fetch_assoc($rs);
        return $row['REQUESTS'];        
    }
    
    function approveRejectModal(){
        ?>
       <!-- Modal -->
    <div id="approveRejectModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
          <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Approve/Reject</h4>
          </div>
          <div class="modal-body" >
          
          	<form class="form-horizontal" role="form" id='assetRequestApproveRejectForm' onSubmit='return false;' >
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="approveRejectRequestReference">Reference</label>
                    <div class="col-sm-10">
        				<input class='form-control' id='approveRejectRequestReference' name='approveRejectRequestReference'
                				value=''
                				type='text' disabled 
                		>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label"
                          for="approveRejectRequestee" >Requestee</label>
                    <div class="col-sm-10">
       					<input class='form-control' id='approveRejectRequestee'  name='approveRejectRequestee'
                			   value=''
                			   type='text' disabled 
                			>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label"
                          for="approveRejectAssetTitle" >Asset</label>
                    <div class="col-sm-10">
          				<input class='form-control' id='approveRejectAssetTitle' name='approveRejectAssetTitle'
                			value=''
                			type='text' disabled
                			>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
						<input  data-toggle="toggle" type="checkbox" class='toggle' data-width='250' data-on="<?=assetRequestRecord::$STATUS_APPROVED?>" data-off="<?=assetRequestRecord::$STATUS_REJECTED?>" id='assetRequestApprovalToggle' name='assetRequestApproval' value='Yes' data-onstyle='success' data-offstyle='warning'>
                    </div>
                  </div>                  
                  
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
						 <textarea class='form-control justification' rows='4' style='min-width: 100%' id='approveRejectRequestComment' name='approveRejectRequestComment' placeholder='Please provide comment if rejecting' min='0' max='500' ' ></textarea><span disabled>500 characters max</span>
                    </div>
                  </div>  
                </form>
          </div>
          <div class='modal-footer'>
          		<?php 
                $form = new FormClass();
                $allButtons = null;
                $confirmButton =  $form->formButton('submit','Submit','assetRequestApproveRejectConfirm',null,'Confirm','btn btn-primary');
                $allButtons[] = $confirmButton;
                $form->formBlueButtons($allButtons);
                $form->formHiddenInput('assetRequestApproverRejector',$_SESSION['ssoEmail'],'assetRequestApproverRejector');
                ?>
	      		<button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
          	</div>
          	<input type=-'hidden' val='' id='approveRejectRequestOrderItStatus' name='approveRejectRequestOrderItStatus' />
          </form>
          </div>
        </div>
      </div>
    <?php
    }
    
    
    
    function exportResultsModal(){
        ?>
       <!-- Modal -->
    <div id="exportResultsModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
          <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Export Results</h4>
            </div>
             <div class="modal-body" >
             </div>
             <div class='modal-footer'>
             <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
             </div>
             </form>
            </div>
        </div>
      </div>
    <?php
    }
    
    function editUidModal(){
        ?>
       <!-- Modal -->
    <div id="editUidModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
          <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Edit UID</h4>
            </div>
             <div class="modal-body" >
             	<form class="form-horizontal" role="form" id='editUidForm' onSubmit='return false;' >
             	
				<div class="form-group">
                    <label class="col-sm-2 control-label"
                          for="userid" >User</label>
                    <div class="col-sm-10">
          				<input class='form-control' id='userid' name='userid'
                			value=''
                			type='text' disabled
                			>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label"
                          for="asset" >Asset</label>
                    <div class="col-sm-10">
          				<input class='form-control' id='asset' name='asset'
                			value=''
                			type='text' disabled
                			>
                    </div>
                  </div>
   
                  <div class="form-group">               
                  <label class="col-sm-2 control-label" id='primaryLabel'
                          for="asset" >Primary UID</label>
                    <div class="col-sm-10">
          				<input class='form-control' id='primaryUid' name='primaryUid'
                			value=''
                			type='text'
                			placeholder='Primary UID'
                			>
                    </div>
                  </div>
                 <div class="form-group" id='secondaryUidFormGroup' style='display:none' >
                 <label class="col-sm-2 control-label" id='secondaryLabel' 
                          for="asset" >Secondary UID</label>
                    <div class="col-sm-10">
          				<input class='form-control' id='secondaryUid' name='secondaryUid'
                			value=''
                			type='text'
                			placeholder='Secondary UID'
                			>
                    </div>
                  </div>
                  <input id='reference' name='reference' value='' type='hidden' />             	
             	</form>
             </div>
             <div class='modal-footer'>
             <?php
                $form = new FormClass();
                $allButtons = null;
                $submitButton = $form->formButton('submit','Submit','saveEditUid','enabled','Save','btn btn-primary');
                $allButtons[] = $submitButton;
                $form->formBlueButtons($allButtons);
                $form->formHiddenInput('user',$GLOBALS['ltcuser']['mail'],'user');
            ?>  
             
             
             <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
             </div>
             </form>
            </div>
        </div>
      </div>
    <?php
    }
    
    
    
    
    function mapVarbToOrderItModal(){
        ?>
       <!-- Modal -->
    <div id="mapVarbToOrderItModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
          <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Map vARB to OrderIt</h4>
            </div>
             <div class="modal-body" >
             </div>
             <div class='modal-footer'>
             <?php
                $form = new FormClass();
                $allButtons = null;
                $submitButton =   $form->formButton('submit','Submit','saveMapVarbToOrderIT','enabled','Save','btn btn-primary');
                $allButtons[] = $submitButton;
                $form->formBlueButtons($allButtons);
                $form->formHiddenInput('mapper',$GLOBALS['ltcuser']['mail'],'mapper');
            ?>             
             <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
             </div>
            </div>
        </div>
      </div>
    <?php
    }
    
    function mapVarbToOrderITForm(){
        $unmappedVarb = $this->getUnmappedVarb();     
        ?>
        <form id='mapVarbToOrderItForm'  class="form-horizontal"
        	onsubmit="return false;">

		<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Map VARB to Order IT</h3>
		</div>

		<div class="panel-body">
        	<div class='form-group required'>
        		<div class='col-sm-5'>
                <select class='form-control select select2 '
                			  id='unmappedVarb'
                              name='unmappedVarb'
                              required
                      >
                    <option value=''></option>
                    <?php
                    foreach ($unmappedVarb as $varb){                            
                            ?><option value='<?=trim($varb);?>'><?=$varb?></option><?php
                        };
                        ?>
				</select>
            	</div>
         		<div class='col-sm-2 align-middle'>
         		<h4 class='text-center align-middle'>Maps to Order IT</h4>
         		</div>           	
         		<div class='col-sm-5'>
        			<input type="number" name='ORDERIT_NUMBER' id=orderItNumber' placeholder="Order IT Number" min="999999" max="9999999" class='form-control' required >
         		</div>            	
            	
            	
            	
        	</div>
        	<div class='form-group required'>
        	<div class='col-sm-12'>
        		<table class='table table-striped table-bordered ' cellspacing='0' width='90%' id='requestsWithinVarb'>
        		<thead><tr><th>Inc</th><th>Ref</th><th>Email</th><th>Asset</th><th>Primary UID</th><th>Secondary UID</th></tr></thead>
        		<tbody>
        		</tbody>
        		</table>
        	</div>
        </div>
        </div>
        <div class='panel-footer'>        
        </div>
        </div>
        </form>
    	<?php 
    }    
        
        
    function getUnmappedVarb(){
        $sql = " SELECT distinct ORDERIT_VARB_REF ";
        $sql .= " FROM " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql .= " WHERE ORDERIT_VARB_REF is not null and ORDERIT_NUMBER is null and STATUS = 'Exported' ";
        $sql .= " ORDER BY ORDERIT_VARB_REF asc ";
        
        $rs = db2_exec($_SESSION['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            return false;
        }
        
        
        while(($row=db2_fetch_assoc($rs))==true){
            $data[]=$row['ORDERIT_VARB_REF'];
        }
        return $data;       
    
    }
    
    function getCnumAndAssetForReference($reference){
        
        $sql = " SELECT CNUM, ASSET_TITLE ";
        $sql .= " FROM " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql .= " WHERE REQUEST_REFERENCE= '" . db2_escape_string($reference) . "' ";
        
        
        $rs = db2_exec($_SESSION['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        $row = db2_fetch_assoc($rs);
        return array('cnum'=>$row['CNUM'],'assetTitle'=>$row['ASSET_TITLE']);
    
    }
    
    
    
    function getAssetRequestsForVarb($varb){
        $sql = " SELECT REQUEST_REFERENCE as REFERENCE, P.EMAIL_ADDRESS as PERSON, AR.ASSET_TITLE as ASSET, AR.CNUM, PRIMARY_UID, SECONDARY_UID, ";
        $sql .= " ASSET_PRIMARY_UID_TITLE, ASSET_SECONDARY_UID_TITLE ";
        $sql .= " FROM " . $_SESSION['Db2Schema'] . "." . $this->tableName . " as AR ";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql .= " ON AR.CNUM = P.CNUM ";
        $sql .= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$REQUESTABLE_ASSET_LIST . " as RAL ";
        $sql .= " ON RAL.ASSET_TITLE = AR.ASSET_TITLE ";
        
        $sql .= " WHERE ORDERIT_VARB_REF='" . db2_escape_string($varb) . "' ";
        
        $rs = db2_exec($_SESSION['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        $data = array();
        while(($row=db2_fetch_assoc($rs))==true){
            $row['INCLUDED'] = "<input type='checkbox' name='request[]' value='" . $row['REFERENCE'] . "' checked />";
            $row['PRIMARY_UID'] = !empty($row['ASSET_PRIMARY_UID_TITLE']) ?  "<input type='text' name='primaryUid[".$row['REFERENCE'] . "]' placeholder='" . $row['ASSET_PRIMARY_UID_TITLE'] . "' value='" . $row['PRIMARY_UID'] . "' />" : null;
            $row['SECONDARY_UID'] = !empty($row['ASSET_SECONDARY_UID_TITLE']) ?  "<input type='text' name='secondaryUid[" .$row['REFERENCE'] . "]' placeholder='" . $row['ASSET_SECONDARY_UID_TITLE'] . "' value='" . $row['SECONDARY_UID'] . "'  />" : null;
            
            unset($row['CNUM']);            
            $data[] = $row;
        }
    
        return $data;
    }
    
    function saveVarbToOrderItMapping($orderIt, $varb, array $request){
        
        $requestList = "'" . implode("','", $request) . "'";
        
        $autoCommit = db2_autocommit($_SESSION['conn'],DB2_AUTOCOMMIT_OFF);
        
        $sql  = " UPDATE ";
        $sql .= $_SESSION['Db2Schema'] . "." . $this->tableName ;
        $sql .= " SET ORDERIT_NUMBER='" . db2_escape_string($orderIt) . "' ";
        $sql .= ",STATUS='" . assetRequestRecord::$STATUS_RAISED_ORDERIT . "' ";
        $sql .= ",ORDERIT_STATUS='" . assetRequestRecord::$STATUS_ORDERIT_RAISED . "' ";
        $sql .= " WHERE ORDERIT_VARB_REF='" . db2_escape_string($varb) . "' and STATUS='" . assetRequestRecord::$STATUS_EXPORTED . "' ";
        $sql .= " AND REQUEST_REFERENCE in (" . $requestList . ") " ;
 
       
        // echo __METHOD__ . __LINE__ . $sql;
        
        $rs = db2_exec($_SESSION['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            return false;
        }
        
        // Anything they didn't select gets reset for next time.
        
        $sql  = " UPDATE ";
        $sql .= $_SESSION['Db2Schema'] . "." . $this->tableName ;
        $sql .= " SET STATUS='" . assetRequestRecord::$STATUS_APPROVED . "' ";
        $sql .= ", ORDERIT_VARB_REF = null ";
        $sql .= ", ORDERIT_STATUS = '" . assetRequestRecord::$STATUS_ORDERIT_YET . "' ";
        $sql .= ", ORDERIT_NUMBER = null ";
        $sql .= " WHERE ORDERIT_VARB_REF='" . db2_escape_string($varb) . "' and STATUS='" . assetRequestRecord::$STATUS_EXPORTED . "' ";
        
        
        // echo __METHOD__ . __LINE__ .  $sql;
        
        $rs = db2_exec($_SESSION['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            return false;
        }
        
        db2_commit($_SESSION['conn']);
        
        db2_autocommit($_SESSION['conn'],$autoCommit);
        
        return true;
    }
    
    
    static function setStatus($reference, $status, $comment=null){
       
        if(!empty($comment)){        
            $now = new \DateTime();
            $sql = " SELECT COMMENT FROM " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS . " WHERE REQUEST_REFERENCE='" . db2_escape_string($reference) . "' ";
            $rs = db2_exec($_SESSION['conn'], $sql);
           
            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__,__METHOD__, $sql);
                return false;
            }
        
            $row = db2_fetch_assoc($rs);
            $existingComment = isset($row['COMMENT']) ?  trim($row['COMMENT']) : null;
            
            $newComment = "<b>" . $now->format('Y-m-d H:i') . "</b>:" . trim($comment) . "<br/>" . $existingComment;     
        } else {
            $newComment = trim($comment);
        }
        
        $sql  = " UPDATE ";
        $sql .= $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS;
        $sql .= " SET STATUS='" . db2_escape_string($status) . "' ";
        $sql .= !empty($newComment) ? ", COMMENT='" . db2_escape_string(substr($newComment,0,500)) . "' " : null;
        $sql .= trim($status)==assetRequestRecord::$STATUS_APPROVED ? ", APPROVER_EMAIL='" . $_SESSION['ssoEmail'] . "' , APPROVED = current timestamp " : null;
        $sql .= " WHERE REQUEST_REFERENCE='" . db2_escape_string($reference) . "' ";
        
        $rs = db2_exec($_SESSION['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__,__METHOD__, $sql);
            return false;
        }
       
        return true;
    }
    
    function prepareUpdateUidsStmt(){
        
        if(empty($this->preparedUpdateUidsStmt)){
            $sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
            $sql .= " SET PRIMARY_UID = ? , SECONDARY_UID = ? ";
            $sql .= " WHERE REQUEST_REFERENCE = ? ";
        
            $preparedUpdateUidsStmt = db2_prepare($_SESSION['conn'], $sql);
        
            if(!$preparedUpdateUidsStmt){
                DbTable::displayErrorMessage($preparedUpdateUidsStmt, __CLASS__, __METHOD__, $sql);
                return false;
            }            
            $this->preparedUpdateUidsStmt = $preparedUpdateUidsStmt;
        }
        
        return $this->preparedUpdateUidsStmt;    
    }
    
    function updateUids($reference, $primaryUid,$secondaryUid=''){
        $stmt = $this->prepareUpdateUidsStmt();       
        $data = array($primaryUid, $secondaryUid, $reference);
        
        $result = db2_execute($stmt,$data);
        
        if(!$result){
            echo db2_stmt_error();
            echo db2_stmt_errormsg();
            DbTable::displayErrorMessage($result, __CLASS__, __METHOD__, 'prepared stmt');
        }        
        return true;       
    
    }
    
    function extractForTracker(){
        
        $requestableAssetTable = new requestableAssetListTable(allTables::$REQUESTABLE_ASSET_LIST);
        $requestableAssets = $requestableAssetTable->returnAsArray(requestableAssetListTable::RETURN_EXCLUDE_DELETED,requestableAssetListTable::RETURN_WITHOUT_BUTTONS);

        foreach ($requestableAssets as $key => $record){
            $requestableAssets[$record['ASSET_TITLE']] = $record;
            unset($requestableAssets[$key]);
        }
        
        $data = $this->returnForPortal(null,self::RETURN_WITHOUT_BUTTONS);
        
        foreach ($data as $key => $record){
            $assetRequests[$record['EMAIL_ADDRESS']][$record['ASSET']] = $record;    
        }
      
        ?>
        
        <div class='container-fluid'>
        
        <table class='table table-striped table-bordered compact' '>        
        <thead>
        <tr>
        <th>IBMer</th>
        <th>CT ID</th>
        <th>Asset</th>
        <th>Order IT Number</th>
        <th>Order IT Status</th>
        <th>VBAC Status</th>
        <th>VBAC Approver</th>
        <th>VBAC Approved</th>
        <th>Location</th>
        <th>Justification</th>
        <th>Primary UID</th>
        <th>Secondary UID</th>
        <th>Date to IBM</th>
        <th>Date to User</th>
        <th>Date Rtnd</th>
        
        </tr>
        </thead>
        <tbody>
        <?php 
            foreach ($assetRequests as $email => $assetRequests){
                foreach ($assetRequests as $asset => $record){
                ?>
                <tr>
                <td><?=$email?></td>
                <td><?=$record['CT_ID']?></td>
                <td><?=$record['ASSET']?></td>
                <td><?=$record['ORDERIT_NUMBER']?></td>
                <td><?=$record['ORDERIT_STATUS']?></td>
                <td><?=$record['STATUS']?></td>
                <td><?=$record['APPROVER_EMAIL']?></td>
                <td><?=$record['APPROVED_DATE']?></td>
                <td style="word-wrap: break-word"><?=$record['LOCATION']?></td>
                <td style="word-wrap: break-word"><?=$record['JUSTIFICATION']?></td>
                <td><?=$record['PRIMARY_UID']?></td>
                <td><?=$record['SECONDARY_UID']?></td>
                <td><?=$record['DATE_ISSUED_TO_IBM']?></td>
                <td><?=$record['DATE_ISSUED_TO_USER']?></td>
                <td><?=$record['DATE_RETURNED']?></td>                
                </tr>
                <?php 
                }
            }
        ?>
        </tbody>
        <tfoot>
        </tfoot>
        </table>
        
        </div>
        <?php 
    }
}