<?php
namespace itdq;


class slack {
    
    protected $url;
    
    const CHANNEL_SM_CDI = 'sm_cognitive_delivery';
    const CHANNEL_SM_CDI_AUDIT = 'sm_cdi_audit';
    const CHANNEL_GENERAL = 'general';
    
    function __construct(){
        $this->url[self::CHANNEL_SM_CDI]       = 'https://hooks.slack.com/services/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';
        $this->url[self::CHANNEL_SM_CDI_AUDIT] = 'https://hooks.slack.com/services/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';
        $this->url[self::CHANNEL_GENERAL]      = 'https://hooks.slack.com/services/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';
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
