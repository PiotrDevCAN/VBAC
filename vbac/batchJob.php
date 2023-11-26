<?php
namespace vbac;

use Exception;
use Cocur\BackgroundProcess\BackgroundProcess;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class batchJob
{
    const SCRIPT_DIR = '/var/www/html/batchJobs/';
    const PROCESS_DIR = 'processesCLI/';
    
    private $email;
    private $processName;
    private $processFile;
    private $cmd;

    function __construct($processName = '', $processFile = '', $additionalParams = null) {

        $this->email = $_SESSION['ssoEmail'];
        $this->processName = $processName;
        $this->processFile = $processFile;
        $this->cmd =  'php -d auto_prepend_file=' . batchJob::SCRIPT_DIR 
        . 'php/siteheader.php -d auto_append_file=' . batchJob::SCRIPT_DIR 
        . 'php/sitefooter.php -f ' . batchJob::SCRIPT_DIR 
        . batchJob::PROCESS_DIR 
        . $this->processFile
        . $additionalParams;
    }

    function run() {
        try {
            // $cmd = 'nohup '.$this->cmd.' > /dev/null 2>&1 & echo $!';
            // $cmd = 'nohup '.$this->cmd.' > nohup.out & > /dev/null';

            $process = new BackgroundProcess($this->cmd);
            $process->run('nohup.out');

            sleep(5);
            if ($process->isRunning()) {
                echo 'running';
            } else {
                echo 'stopped';
            }

            echo $this->processName." has succeed to be executed: ".$this->email.PHP_EOL;
            error_log($this->processName." has succeed to be executed: ".$this->email.PHP_EOL);
        } catch (Exception $exception) {
            echo $exception->getMessage();
            echo $this->processName." has failed to be executed: ".$this->email.PHP_EOL;
            error_log($this->processName." has failed to be executed: ".$this->email.PHP_EOL);
        }
    }
}