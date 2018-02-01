<?php
$codeDirs = array('ajax','vbac', 'vbac/java', 'batchJobs');

echo "<div class='container'>";

function processFilesInDir($directory, $filesToScan){
    $totalRows = 0;
    $files = 0;
    foreach ($filesToScan as $fileName){
        if(!is_dir($fileName)){
            $files++;
            $rows = 0;
            $myfile = fopen($directory . "/" . $fileName, 'r');
            while(!feof($myfile)) {
                 fgets($myfile);
                 $rows++;
                 $totalRows++;
            }
            fclose($myfile);
            echo "<br/>$fileName : $rows";
        }
    }

    echo "<br/>End of Directory. Files: $files Total Lines of code : $totalRows";
    return array('files'=>$files,"rows"=>$totalRows);
}

$response = array();

foreach ($codeDirs as $directory){
    $filesToScan = scandir($directory);
    echo "<h1>$directory</h2>";
    $response[] = processFilesInDir($directory, $filesToScan);
}

$totalFiles=0;
$totalRow =0;

foreach ($response as $dirDetails){
    $totalFiles += $dirDetails['files'];
    $totalRow += $dirDetails['rows'];
}

Echo "<BR/>Total Files: $totalFiles. Total Rows:$totalRow";


echo "</div>";