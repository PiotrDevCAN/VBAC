<?php
use itdq\PlannedOutages;
use itdq\IconRolesTable;
include ('php/menuconf.php');
include ('itdq/PlannedOutages.php');
include ('itdq/DbTable.php');
$plannedOutages = new PlannedOutages();
include ('UserComms/responsiveOutages_V2.php');

?>
       <nav class="navbar navbar-default navbar-fixed-top">
          <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
            <?php
              if (!empty($navBarImage) && !empty($navBarBrand)){
               echo "<span class='navbar-brand'><img src='$navBarImage'></span>";
              }
               echo "<a class='navbar-brand' href='$navBarBrand[1]'>$navBarBrand[0]</a>";
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

              $pageDetails = explode("/", $_SERVER['PHP_SELF']);
              $page = isset($pageDetails[2]) ? $pageDetails[2] : $pageDetails[1];

              foreach ($navBar_data as $navBarItem) {
                   $label = $navBarItem[0];
                   $link = $navBarItem[1];
                   switch ($link) {
                        case 'divider':
                              echo "<li class='divider'></li>";
                          break;
                          case 'dropDown':
                              echo "<li class='dropdown ' id='dropDown_" . str_replace(" ","_",$label) . "' >";
                              echo "<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'>$label";
                              echo "</a>";
                              echo "<ul class='dropdown-menu' role='menu'>";
                              break;
                          case 'endOfDropDown':
                              echo "</ul>";
                              echo "</li>";
                              break;

                          default:
                              $class = trim($link)==trim($page) ? " class='active' " : null;
                              echo "<li $class id='" . str_replace(" ","_",$label) . "' ><a href=\"$link\">$label";
                              if($label=='Planned Outages'){
                                  echo "&nbsp;" . $plannedOutages->getBadge();
                              }
                              echo "</a></li>";
                              echo "<script>$('.active').closest('li.dropdown').addClass('active');</script>";
                          break;
                      }
                  }

               echo "</ul>";
              ?>

              <ul class="nav navbar-nav navbar-right">
              </ul>

              <p class='nav navbar-nav navbar-right ' style='color:white'>User Level is:<scan id='userLevel'></scan><br/>Powered by CDI</p>
            </div><!-- /.navbar-collapse -->
          </div><!-- /.container-fluid -->
        </nav>


  <?php

//   $isAdmin = employee_in_group($_SESSION['adminBg'], $GLOBALS['ltcuser']['mail']);
//   $validUser = employee_in_group($_SESSION['userBg'], $GLOBALS['ltcuser']['mail']);
//   $isItdq = employee_in_group($_SESSION['itdqBg'], $GLOBALS['ltcuser']['mail']);
//  $isPmo = employee_in_group($_SESSION['pmoBg'], $GLOBALS['ltcuser']['mail']);

  $isAdmin = true;
  $validUser= true;
  $isItdq = true;
  $isPmo = true;


  ?>
  <script>
  $(document).ready(function () {
	  surpressMenuOptions(     <?=$isAdmin ? 'true' : 'false';?>
      , <?=$validUser ? 'true' : 'false';?>
      , <?=$isItdq ? 'true':'false';?>
      );

	  <?=$validUser ? '$("#userLevel").html("Valid User");' : '$("#userLevel").html("Not Defined");';?>
	  <?=$isAdmin ? '$("#userLevel").html("Admin User");' : null;?>


  });
  </script>


