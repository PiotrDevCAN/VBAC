<?php
namespace itdq;


class slack {

    protected $url;

    const CHANNEL_SM_CDI      = 'sm_cognitive_delivery';
    const CHANNEL_SM_CDI_AUDIT = 'sm-cdi-audit';
    const CHANNEL_GENERAL     = 'general';

    const CHANNEL_RTB_WINTEL_OFFSHORE = 'rtb-wintel_offshore';
    const CHANNEL_BAU_D_ELT = 'bau_d_elt';
//     const CHANNEL_POLYTEST    = 'polytest';
//     const CHANNEL_ID_POLYTEST = 'G010VEL63UN';
    const CHANNEL_ID_BAU_D_ELT = 'G010TNY2VG8';
    const CHANNEL_ID_SM_CDI_AUDIT = 'CC3EY7P6V';


    function __construct(){
        $this->url[self::CHANNEL_SM_CDI]              = 'https://hooks.slack.com/services/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';
        $this->url[self::CHANNEL_SM_CDI_AUDIT]        = 'https://hooks.slack.com/services/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';
        $this->url[self::CHANNEL_GENERAL]             = 'https://hooks.slack.com/services/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';
        $this->url[self::CHANNEL_BAU_D_ELT]           = 'https://hooks.slack.com/services/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';

        $this->url[self::CHANNEL_RTB_WINTEL_OFFSHORE] = 'https://hooks.slack.com/services/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';
//        $this->url[self::CHANNEL_POLYTEST]            = 'https://hooks.slack.com/services/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';

    }

    function sendMessageToChannel($message=null,$channel=null){


        $url = $this->url[$channel];
        $ch = curl_init( $url );

        $messageToSlack = '{"text":"' . $message . '"}';

        var_dump($messageToSlack);

        curl_setopt( $ch, CURLOPT_POSTFIELDS,$messageToSlack );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Content-Length: ' . strlen($messageToSlack)));

        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_POST, true);
        # Send request.
        $result = curl_exec($ch);
        return $result;
    }

    function slackJoinChannel($channel){
        $url = "https://slack.com/api/conversations.join";
        $ch = curl_init( $url );

        $messageToSlack = 'token=' . self::TOKEN_VENTUS_SRE . '&channel=' . $channel ;

        curl_setopt( $ch, CURLOPT_POSTFIELDS,$messageToSlack );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/x-www-form-urlencoded','Content-Length: ' . strlen($messageToSlack)));

        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_POST, true);
        # Send request.

        $result = curl_exec($ch);

        return $result;
    }


    function slackApiPostMessage($channel,$text){
        // https://slack.com/api/chat.postMessage?token=xoxb-xxxxxxxxxxxxxxxxxxxxxxxxxx&channel=polytest&text=Emoji%20This&pretty=1(

        $url = "https://slack.com/api/chat.postMessage";
        $ch = curl_init( $url );

        $tokenVentusSre = $_ENV['token_ventus_sre'];

        $messageToSlack = 'token=' . $tokenVentusSre . '&channel=' . $channel . '&text=' . urlencode($text);

        curl_setopt( $ch, CURLOPT_POSTFIELDS,$messageToSlack );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/x-www-form-urlencoded','Content-Length: ' . strlen($messageToSlack)));

        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_POST, true);
        # Send request.

        $result = curl_exec($ch);

        return $result;

    }

    function slackAddReaction($channel,$name,$timestamp){
        //  https://slack.com/api/reactions.add?token=xoxb-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx&channel=C8DLE1DFH&name=thumbsup&timestamp=1585225528.000700&pretty=1

        $url = "https://slack.com/api/reactions.add";
        $ch = curl_init( $url );

        $tokenVentusSre = $_ENV['token_ventus_sre'];

        $messageToSlack = 'token=' . $tokenVentusSre . '&channel=' . $channel . '&name=' . $name . '&timestamp=' . $timestamp;

        curl_setopt( $ch, CURLOPT_POSTFIELDS,$messageToSlack );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/x-www-form-urlencoded','Content-Length: ' . strlen($messageToSlack)));

        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_POST, true);
        # Send request.

        $result = curl_exec($ch);

        return $result;

    }

    function slackPostMessageWithEmoji($channel, $text ,array $names){
        $postResult = $this->slackApiPostMessage($channel, $text);
        $postResultObj = json_decode($postResult);

        if($postResultObj->ok){
            foreach ($names as $name) {
                $reactionResult = $this->slackAddReaction($channel, $name,$postResultObj->ts );
                $reactionResultObj = json_decode($reactionResult);
                if(!$reactionResultObj->ok){
                    echo "<pre>";
                    var_dump($postResultObj);
                    throw new \Exception("Adding Reaction " . $name . " to Slack channel " . $channel . " Failed");
                }
            }
        } else {
            echo "<pre>";
            var_dump($postResultObj);
            throw new \Exception("Write to Slack channel " . $channel . " Failed");
        }
    }
}
