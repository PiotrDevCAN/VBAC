<?php
namespace itdq;

/**
 *
 * @author gb001399
 *
 */
class NavbarDivider extends NavbarItem {

    function createItem($classes=null){
        ?>
        <li class="divider navbarMenuOption <?=$classes?>"></li>
        <?php
    }
}