<?php
namespace itdq;


class slack {
    
    protected $url;
    
    const CHANNEL_SM_CDI = 'sm_cognitive_delivery';
    
    function __construct(){
        $this->url[self::CHANNEL_SM_CDI] = 'https://hooks.slack.com/services/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';
    }
    
    function sendMessageToChannel($message,$channel){
        if(empty($this->url[trim($channel)])){
            throw new Exception($channel . " unknown channel, message can't be sent");
        }
        
        $url = $this->url[trim($channel)];
        $ch = curl_init( $url );
        
        $messageToSlack = '{"text":"' . trim($message) . '"}';
        
        curl_setopt( $ch, CURLOPT_POSTFIELDS,$messageToSlack );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Content-Length: ' . strlen($messageToSlack)));
        
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_POST, true);
        # Send request.
        $result = curl_exec($ch);
        
        // # Print response.
        echo "<pre>$result</pre>";
        echo "completed.";
        
        
        echo curl_errno($ch);
        echo curl_error($ch);
        
        echo var_dump($messageToSlack);
        
        
        
        return $result;
    }
}
