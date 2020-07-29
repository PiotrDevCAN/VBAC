<?php
namespace vbac;

use itdq\DbTable;
use itdq\Loader;

/*
 *
 * CREATE TABLE ROB_DEV.STATIC_SUBPLATFORM ( WORK_STREAM_ID INTEGER, SUB_PLATFORM CHAR(50) );
 * CREATE UNIQUE INDEX ROB_DEV.SD_SP_PK ON ROB_DEV.STATIC_SUBPLATFORM (WORK_STREAM_ID ASC,SUB_PLATFORM ASC);
 */

class staticDataSubPlatformTable extends DbTable
{

    static function prepareJsonObjectForSubPlatformSelect(){
        $loader = new Loader();
        // $allSubPlatform = $loader->loadIndexed('WORK_STREAM_ID','SUB_PLATFORM',allTables::$STATIC_SUBPLATFORM);
        $allWorkstreams = $loader->loadIndexed('WORKSTREAM_ID','WORKSTREAM',allTables::$STATIC_WORKSTREAMS);
        $allSubPlatformsByWorkstream = array();
        $platformWithinStream= array();

        foreach ($allWorkstreams as $workstream => $workstream_id) {
            $predicate = " WORK_STREAM_ID='$workstream_id' ";
            $allSubPlatformsByWorkstream[$workstream_id] = $loader->load('SUB_PLATFORM',allTables::$STATIC_SUBPLATFORM,$predicate);
        }


        foreach ($allSubPlatformsByWorkstream as $work_stream_id => $subPlatforms) {
            foreach ($subPlatforms as $subPlatform) {
                $options = new \stdClass();
                $options->id   = trim($subPlatform);
                $options->text = trim($subPlatform);
                $platformWithinStream[$work_stream_id][] = $options;
            }
        }
        ?>
        <script>
		var platformWithinStream = <?= json_encode($platformWithinStream);?>;
		var workstreamDetails = <?= json_encode($allWorkstreams); ?>;
        </script>
        <?php

    }

}

