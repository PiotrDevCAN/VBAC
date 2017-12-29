<?php
namespace itdq;

class EmailLogTable  extends DbTable {

    function returnAsArray($startDate,$endDate){
        $rows = array();
        $rows[] = array('id','to','subject','message','response','status','sent timestamp','status_timestamp');
        $rows[] = array('id2','to2','subject2','message2','response2','status2','sent timestamp2','status_timestamp2');

        //$data = array('data'=>$rows);

        return $rows;

    }

}