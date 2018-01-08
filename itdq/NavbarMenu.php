<?php
namespace itdq;

/**
 *
 * @author gb001399
 *
 */
class NavbarMenu
{
    protected $label;
    protected $class;
    protected $options;

    function __construct($label,$class=null){
        $this->label = $label;
        $this->class = $class;
    }

    function addOption(NavbarItem $navbarOption){
        $this->options[]=$navbarOption;
    }

    function createItem(){
        ?>
        <li class='dropdown navbarMenu' id='dropDown_<?=str_replace(" ","_",$this->label)?>' >
        <a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'><?=$this->label;?></a>
        <ul class='dropdown-menu' role='menu'>
        <?php
            foreach ($this->options as $menuOption){
                $menuOption->createItem();
            }
        ?>
        </ul>
        <?php
    }
}

?>