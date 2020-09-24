<?php
$filenames =  array('recheck_L1_Core4.php','recheck_L1_India_Non_Core4.php','recheck_L1_UK.php'
                   ,'recheck_L2_Core4.php','recheck_L2_India_Non_Core4.php','recheck_L2_UK.php'
                   ,'recheck_offboarded.php'
    
);

echo "<div class='container'>";


foreach ($filenames as $filename) {
    include_once 'emailBodies/' . $filename;

    echo "<h3>$filename</h3>"; 
    echo $pesEmail;
    echo "<hr>";
}


echo "</div>";

