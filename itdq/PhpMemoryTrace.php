<?php
namespace itdq;

class PhpMemoryTrace
{
    static function reportPeek($file, $line, $errorLog=true, $console=false){
        $_SESSION['peekUsage'] = isset($_SESSION['peekUsage']) ? $_SESSION['peekUsage'] : 0;  // initialize peekUsage
        $peek = memory_get_usage(true);
        if($peek > $_SESSION['peekUsage']){
            if($errorLog) {
                error_log("File:" .  $file . " Line:" .  $line .  " Memory peek:" . memory_get_peak_usage(true)/1024,0);
            }
            if($console){
                echo "<br/>File:" . $file . " Line:" . $line;
                echo "\nMemory limit:" . ini_get('memory_limit');
                echo "\nMemory usage:" . memory_get_peak_usage(true)/1024;
                echo "\nMemory peek:" . memory_get_usage(true)/1024;
            }
            $_SESSION['peekUsage'] = $peek;
        } else {
            if($errorLog) {
                error_log("File:" .  $file . " Line:" .  $line .  " Memory peek: no rapid changes");
            }
            if($console){
                echo "<br/>File:" . $file . " Line:" . $line;
                echo "\nMemory limit: no rapid changes";
            }
        }
    }
}

