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

        function get_page_mod_time() {
            $incls = get_included_files();
            $incls = array_filter($incls, "is_file");
            $mod_times = array_map('filemtime', $incls);
            $mod_time = max($mod_times);
            
            return $mod_time;
        }

        // $hash = `git log -1 --pretty=%h`;
        $hashMain = date("d F Y", get_page_mod_time());
        $hash = sha1($hashMain);
        $hash = substr($hash, 0, 7);

        ?>
        </ul>

		<p class='nav navbar-nav navbar-right userLevel '>User Level is:<scan id='userLevel'></scan><br/>Powered by SRE (<?=$hash;?>)</p>
        <ul class="nav navbar-nav navbar-right">
        <li class='accessCdi accessPmo accessFm accessUser'
               id='Help_Page'
               data-pagename='pa_helpPage.php'><a href="pa_helpPage.php">Help/Feedback</a></li>
	    </ul>

        </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
        </nav>
        <?php
    }
}
?>