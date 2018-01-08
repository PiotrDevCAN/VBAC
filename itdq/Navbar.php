<?php
namespace itdq;
/**
 * @author gb001399
 *
 */
class Navbar
{
    protected $navbar;
    protected $navbarDropDowns;
    protected $navbarImage;
    protected $navbarBrand;
    protected $navbarSearch;

    protected $menuItems;


    function __construct($image,$brand,$search=false){
        $this->navbarImage = $image;
        $this->navbarBrand = $brand;
        $this->navbarSearch = $search;
   }

    function addMenu(NavbarMenu $navbarMenu){
        $this->menuItems[] = $navbarMenu;
    }

    function addOption(NavbarOption $navbarOption){
        $this->menuItems[] = $navbarOption;
    }


    function createNavbar($page){
        ?>
        <nav class="navbar navbar-default navbar-fixed-top">
          <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
            <?php
              if (!empty($this->navBarImage) && !empty($this->navBarBrand)){
               echo "<span class='navbar-brand'><img src='$this->navBarImage'></span>";
              }
              echo "<a class='navbar-brand' href='" . $this->navbarBrand[1] . "'>" . $this->navbarBrand[0] . "</a>";
            ?>

               <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
            </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="navbar-collapse">
        <ul class="nav navbar-nav">
        <?php
        foreach ($this->menuItems as $menu){
            $menu->createItem();
        }
        ?>
        </ul>
        <ul class="nav navbar-nav navbar-right">
        </ul>

        <p class='nav navbar-nav navbar-right ' style='color:white'>User Level is:<scan id='userLevel'></scan><br/>Powered by CDI</p>
        </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
        </nav>
        <?php
    }


}
?>