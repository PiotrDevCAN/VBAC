<?php
$target_dir = "odc_uploads/";
$target_file = $target_dir . basename($_FILES["file"]["name"]);
$uploadOk = 1;
$fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));


ob_clean();


echo $fileType;

var_dump($_FILES);


// Check if file already exists
if (file_exists($target_file)) {
    $uploadOk = unlink($target_file);
    echo $uploadOk ? "Previous File deleted." : "Problem deleting previous file";

}
// Check file size
if ($_FILES["file"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($fileType != "xls" && $fileType != "xlsx" ) {
        echo "Sorry, only XLS & XLSX files are allowed.";
        $uploadOk = 0;
    }
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>