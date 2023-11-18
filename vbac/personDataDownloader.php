<?php
namespace vbac;

class personDataDownloader
{
    private $type;
    private $email;

    function __construct($type = '') {
        
        $this->type = $type;
        $this->email = $_SESSION['ssoEmail'];
    
    }

    function getFile() {

        if(!isset($this->type)){
            $this->type = 'unknown';
        }

        $processName = 'Aurora Person Table Extract Script';
        $processFile = 'downloadPersonDetailsProcess.php';
        $additionalParams = ' ' . $this->email . ' ' . str_replace(" ", "_", $this->type);

        $job = new batchJob($processName, $processFile, $additionalParams);
        $job->run();
    }
}