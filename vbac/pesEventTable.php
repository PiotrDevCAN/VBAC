<?php
namespace vbac;

use itdq\DbTable;
use itdq\Loader;
use vbac\pesEventRecord;

class pesEventTable extends DbTable{


    static function displayPesEventsTable(){
        $allEvents = pesEventRecord::$pesEvents;

//         select p.cnum, p.email_address
//         , case when PE1.EVENT = 'Consent Form' then pe1.EVENT_COMMENT else null end as CONSENT_FORM_COMMENT
//         , case when PE1.EVENT = 'Consent Form' then pe1.EVENT_TIMESTAMP else null end as CONSENT_FORM_TIMESTAMP
//         , case when PE1.EVENT = 'Consent Form' then pe1.EVENT_UPDATER else null end as CONSENT_FORM_UPDATER
//         , case when PE2.EVENT = 'Right to Work' then pe2.EVENT_COMMENT else null end as RIGHT_TO_WORK_COMMENT
//         , case when PE2.EVENT = 'Right to Work' then pe2.EVENT_TIMESTAMP else null end as RIGHT_TO_WORK_TIMESTAMP
//         , case when PE2.EVENT = 'Right to Work' then pe2.EVENT_UPDATER else null end as RIGHT_TO_WORK_UPDATER

//         from rob_dev.person as P
//         left join rob_dev.pes_events as PE1
//         on P.CNUM = PE1.CNUM and PE1.EVENT = 'Consent Form'

//         left join rob_dev.pes_events as PE2
//         on P.CNUM = PE2.CNUM and PE2.EVENT = 'Right to Work'
//         where p.cnum = '001399866'


        $sql = " SELECT P.CNUM, P.EMAIL_ADDRESS ";
        $headerCells = "<th>CNUM</th><th>EMAIL ADDRESS</th>";

        foreach ($allEvents as $element => $event_title) {
            $sql .= ", case when PE$element.EVENT = '$event_title' then pe$element.EVENT_COMMENT else null end as " . str_replace(" ", "_", strtoupper($event_title)) . "_COMMENT ";
            $sql .= ", case when PE$element.EVENT = '$event_title' then pe$element.EVENT_TIMESTAMP else null end as " . str_replace(" ", "_", strtoupper($event_title)) . "_TIMESTAMP ";
            $sql .= ", case when PE$element.EVENT = '$event_title' then pe$element.EVENT_UPDATER else null end as " . str_replace(" ", "_", strtoupper($event_title)) . "_UPDATER";
            $headerCells.= "<th>" .strtoupper($event_title) . "</th>";
        }



        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";

        foreach ($allEvents as $element => $event_title) {
            $sql .= " left join " . $GLOBALS['Db2Schema'] . "." . \vbac\allTables::$PES_EVENTS . " as PE$element ";
            $sql .= " ON P.CNUM = PE$element.CNUM AND PE$element.EVENT='$event_title' ";
        }

        $sql.= " WHERE P.PES_STATUS IN ('" . personRecord::PES_STATUS_REQUESTED . "','" . personRecord::PES_STATUS_RECHECK_REQ . "','" . personRecord::PES_STATUS_MOVER . "') ";

        $rs = db2_exec($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            throw new \Exception('Error in ' . __METHOD__ . " running $sql");
        }

        ?>

		<table id='pesTrackerTable' class='table table-striped table-bordered compact'  style='width:100%'>
		<thead>
		<tr><?=$headerCells;?><th>Comment</th></tr></thead>
		<tbody>
		<?php

        while(($row=db2_fetch_assoc($rs))==true){
            ?><tr><td><?=$row['CNUM']?></td><td><?=$row['EMAIL_ADDRESS']?></td><?php
            foreach ($allEvents as $event_title) {
                $commentTitle = str_replace(" ", "_", strtoupper($event_title)) . "_COMMENT";
                $timestampTitle = str_replace(" ", "_", strtoupper($event_title)) . "_TIMESTAMP";
//              $updaterTitle = strtoupper($event_title)) . "_UPDATER";
                ?>
                <td style="white-space:nowrap">
				<button class='btn btn-success btn-sm btnPesStageCleared accessPes accessCdi' data-toggle="tooltip" data-placement="top" title="Cleared" ><span class="glyphicon glyphicon-ok-sign" ></span></button>
  				<button class='btn btn-warning btn-sm btnPesStageProvisional accessPes accessCdi' data-toggle="tooltip"  title="Stage Cleared Provisionally"><span class="glyphicon glyphicon-alert" ></span></button>
				<button class='btn btn-warning btn-sm btnPesStageNotApplicable accessPes accessCdi' data-toggle="tooltip"  title="Not applicable"><span class="glyphicon glyphicon-remove-sign" ></span></button>
                 </td>

                <?php
            }
            ?><td><textarea rows="3" cols="20"></textarea></td><?php
            ?></tr><?php
        }
        ?>

        </tbody>
		<tfoot><tr><?=$headerCells;?></tr></tfoot>
		</table>
		<?php


    }

}