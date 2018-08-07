<?php
namespace itdq;


class slack {
    
    protected $url;
    
    const CHANNEL_SM_CDI = 'sm_cognitive_delivery';
    const CHANNEL_SM_CDI_AUDIT = 'sm_cdi_audit';
    
    function __construct(){
        $this->url[self::CHANNEL_SM_CDI] = 'https://hooks.slack.com/services/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';
        $this->url[self::CHANNEL_SM_CDI_AUDIT] = 'https://hooks.slack.com/services/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';
    }
    
    function sendMessageToChannel($message,$channel){
        if(empty($this->url[trim($channel)])){
            throw new \Exception($channel . " unknown channel, message can't be sent");
        }
        
        $url = $this->url[trim($channel)];
        $ch = curl_init( $url );
        
        $messageToSlack = '{"text":"' . trim($message) . '[' . $_SERVER['environment'] . ']"}';
        
        curl_setopt( $ch, CURLOPT_POSTFIELDS,$messageToSlack );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Content-Length: ' . strlen($messageToSlack)));
        
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_POST, true);
        # Send request.
        $result = curl_exec($ch);
        return $result;
    }
}
