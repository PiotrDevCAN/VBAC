<?php
namespace itdq;

/**
 *
 * @author gb001399
 *
 */
class NavbarOption extends NavbarItem
{
    protected $label;
    protected $link;
    protected $classes;

    function __construct($label,$link,$classes=null){
        $this->label   = $label;
        $this->link    = $link;
        $this->classes = 'navbarMenuOption ' . $classes;
    }

    function createItem(){
        ?><li class='<?=$this->classes?>'
              id='<?=str_replace(" ","_",$this->label);?>'
              data-pagename='<?=$this->link?>'>
          <a href="<?=$this->link?>"><?=$this->label?></a>
          </li>
            <?php
    }
}

?>