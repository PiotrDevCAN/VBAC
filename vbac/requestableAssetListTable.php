<?php
namespace vbac;

use itdq\DbTable;
use itdq\AuditTable;
use itdq\AuditRecord;

class requestableAssetListTable extends DbTable {


    const RETURN_EXCLUDE_DELETED = true;
    const RETURN_INCLUDE_DELETED = false;
    const RETURN_WITH_BUTTONS    = true;
    const RETURN_WITHOUT_BUTTONS = false;

    function returnAsArray($excludeDeleted=true,$withButtons=true){
        $data = array();

        $predicate = $excludeDeleted ? " AND LISTING_ENTRY_REMOVED is null " : null;

        $sql  = " SELECT * FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName ;
        $sql .= " WHERE 1=1 " . $predicate;
        $sql .= " ORDER BY ASSET_TITLE ";

        $rs = db2_exec($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        } else {
            while(($row=db2_fetch_assoc($rs))==true){
                $trimmedData = array_map('trim', $row);
                $trimmedData['APPLICABLE_ONSHORE']              = trim($trimmedData['APPLICABLE_ONSHORE'])=='1'         ? 'Yes' : 'No';
                $trimmedData['APPLICABLE_OFFSHORE']             = trim($trimmedData['APPLICABLE_OFFSHORE'])=='1'        ? 'Yes' : 'No';
                $trimmedData['REQUEST_BY_DEFAULT']              = trim($trimmedData['REQUEST_BY_DEFAULT'])=='1'         ? 'Yes' : 'No';
                $trimmedData['BUSINESS_JUSTIFICATION_REQUIRED'] = trim($trimmedData['BUSINESS_JUSTIFICATION_REQUIRED'])=='1' ? 'Yes' : 'No';
                $trimmedData['RECORD_DATE_ISSUED_TO_IBM']       = trim($trimmedData['RECORD_DATE_ISSUED_TO_IBM'])=='1'  ? 'Yes' : 'No';
                $trimmedData['RECORD_DATE_ISSUED_TO_USER']      = trim($trimmedData['RECORD_DATE_ISSUED_TO_USER'])=='1' ? 'Yes' : 'No';
                $trimmedData['RECORD_DATE_RETURNED']            = trim($trimmedData['RECORD_DATE_RETURNED'])=='1'       ? 'Yes' : 'No';
                $trimmedData['ORDER_IT_REQUIRED']               = trim($trimmedData['ORDER_IT_REQUIRED'])=='1'       ? 'Yes' : 'No';

                AuditTable::audit("Diag:" . __METHOD__ . __LINE__ . ":" . print_r($trimmedData,true), AuditTable::RECORD_TYPE_DETAILS);

                $rowWithButtonsAdded = $withButtons ?  $this->addButtons($trimmedData) : $trimmedData ;
                $data[] = $rowWithButtonsAdded;
            }
        }


        return $data;
    }


    function addButtons($row){
        // save some fields before we change them
        $created = trim($row['LISTING_ENTRY_CREATED']);
        $createdBy = trim($row['LISTING_ENTRY_CREATED_BY']);

        $removed   = trim($row['LISTING_ENTRY_REMOVED']);
        $removedBy = trim($row['LISTING_ENTRY_REMOVED_BY']);

        if($GLOBALS['isPmo'] || $GLOBALS['isCdi']){
            $row['LISTING_ENTRY_CREATED']  = "<button type='button' class='btn btn-default btn-xs btnEditAsset' aria-label='Left Align' ";
            $row['LISTING_ENTRY_CREATED'] .= "data-asset='" . trim($row['ASSET_TITLE']) . "' ";
            $row['LISTING_ENTRY_CREATED'] .= "data-prereq='" .trim($row['ASSET_PREREQUISITE']) . "' ";
            $row['LISTING_ENTRY_CREATED'] .= "data-uidpri='" .trim($row['ASSET_PRIMARY_UID_TITLE']) . "' ";
            $row['LISTING_ENTRY_CREATED'] .= "data-uidsec='" .trim($row['ASSET_SECONDARY_UID_TITLE']) . "' ";
            $row['LISTING_ENTRY_CREATED'] .= "data-onshore='" .trim($row['APPLICABLE_ONSHORE']) . "' ";
            $row['LISTING_ENTRY_CREATED'] .= "data-offshore='" .trim($row['APPLICABLE_OFFSHORE']) . "' ";
            $row['LISTING_ENTRY_CREATED'] .= "data-dtetoibm='" .trim($row['RECORD_DATE_ISSUED_TO_IBM']) . "' ";
            $row['LISTING_ENTRY_CREATED'] .= "data-dtetousr='" .trim($row['RECORD_DATE_ISSUED_TO_USER']) . "' ";
            $row['LISTING_ENTRY_CREATED'] .= "data-dteret='" .trim($row['RECORD_DATE_RETURNED']) . "' ";
            $row['LISTING_ENTRY_CREATED'] .= "data-just='" .trim($row['BUSINESS_JUSTIFICATION_REQUIRED']) . "' ";
            $row['LISTING_ENTRY_CREATED'] .= "data-prompt='" . urldecode(trim($row['PROMPT'])) . "' ";
            $row['LISTING_ENTRY_CREATED'] .= "data-type='" . urldecode(trim($row['ORDER_IT_TYPE'])) . "' ";
            $row['LISTING_ENTRY_CREATED'] .= "data-orderitreq='" . urldecode(trim($row['ORDER_IT_REQUIRED'])) . "' ";

            $row['LISTING_ENTRY_CREATED'] .= " > ";
            $row['LISTING_ENTRY_CREATED'] .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
            $row['LISTING_ENTRY_CREATED'] .= " </button> ";
            if(empty($removed)){
                $row['LISTING_ENTRY_CREATED'] .= "<button type='button' class='btn btn-default btn-xs btnDeleteAsset' aria-label='Left Align' ";
                $row['LISTING_ENTRY_CREATED'] .= "data-asset='" . trim($row['ASSET_TITLE']) . "' ";
                $row['LISTING_ENTRY_CREATED'] .= "data-deleter='" . trim($_SESSION['ssoEmail']) . "' ";
                $row['LISTING_ENTRY_CREATED'] .= " > ";
                $row['LISTING_ENTRY_CREATED'] .= "<span class='glyphicon glyphicon-trash ' aria-hidden='true'></span>";
                $row['LISTING_ENTRY_CREATED'] .= " </button> ";
            }
            $row['LISTING_ENTRY_CREATED'] .= $createdBy . "<br/><span style='font-size:x-small'>" . $created ."</span>";
        } else {
            $row['LISTING_ENTRY_CREATED'] .= $createdBy . "<br/><span style='font-size:x-small'>" . $created ."</span>";
        }

        $row['LISTING_ENTRY_REMOVED'] = $removedBy . "<br/><span style='font-size:x-small'>" . $removed;

        unset($row['LISTING_ENTRY_CREATED_BY']);
        unset($row['LISTING_ENTRY_REMOVED_BY']);
        return $row;
    }

    static function flagAsDeleted($assetTitle,$deletedBy){
        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$REQUESTABLE_ASSET_LIST;
        $sql .= " SET LISTING_ENTRY_REMOVED= current timestamp, LISTING_ENTRY_REMOVED_BY='" . db2_escape_string($deletedBy) . "' ";
        $sql .= " WHERE ASSET_TITLE='" . db2_escape_string($assetTitle) . "' ";

        $rs = db2_exec($GLOBALS['conn'], $sql);
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        return true;
    }

}