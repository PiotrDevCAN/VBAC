<?php
namespace itdq;

/**
 *
 * @author gb001399
 *
 */
class NavbarDivider extends NavbarItem {

    protected $classes;

    function __construct($classes=null){
        $this->classes = 'navbarMenuOption ' . $classes;
    }


    function createItem(){
        ?>
        <li role="separator" class="divider <?=$this->classes?>"></li>
        <?php
    }
}