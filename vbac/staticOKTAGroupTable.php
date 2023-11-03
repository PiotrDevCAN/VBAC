<?php
namespace vbac;

use itdq\DbTable;

/**
 *
 * @author gb001399
 *
 */
class staticOKTAGroupTable
{
    function displayPills() {
        ?>
        <ul class="nav nav-pills">
        <?php
        $i = 0;
        $allGroups = $GLOBALS['site']['allGroups'];
        foreach ($allGroups as $key => $group){
            $active = ($i == 0) ? 'active' : null;
            $groupName = str_replace('_', ' ', $key);
            if (strlen($groupName) == 3) {
                $groupName = strtoupper($groupName);
            } else {
                $groupName = ucwords($groupName);
            }
            ?>
            <li class="<?=$active;?>"><a data-toggle="pill" href="#<?=$key;?>-tab"><?=$groupName;?></a></li>
            <?php
            $i++;
        }
        ?>
        </ul>
        <?php
    }

    function displayPillsTables() {
        ?>
        <div class="tab-content">
        <?php
        $i = 0;
        $allGroups = $GLOBALS['site']['allGroups'];
        foreach ($allGroups as $key => $group){
            $active = ($i == 0) ? 'active' : null;
            ?>
            <div id="<?=$key;?>-tab" class="tab-pane fade in <?=$active;?>">
                <table id='<?=$key;?>MembersTable' class='dataTable' data-group='<?=$key;?>'>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email Address</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Name</th>
                            <th>Email Address</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php
            $i++;
        }
        ?>
        </div>
        <?php
    }
}