<?php
namespace itdq;


class slack {

    protected $url;

    const CHANNEL_SM_CDI      = 'sm_cognitive_delivery';
    const CHANNEL_SM_CDI_AUDIT = 'sm-cdi-audit';
    const CHANNEL_GENERAL     = 'general';
    const CHANNEL_RTB_WINTEL_OFFSHORE = 'rtb-wintel_offshore';
    const CHANNEL_BAU_D_ELT = 'bau_d_elt';
    const CHANNEL_ID_BAU_D_ELT = 'G010TNY2VG8';
    const CHANNEL_ID_SM_CDI_AUDIT = 'CC3EY7P6V';

    function __construct(){

    }

    function sendMessageToChannel($message=null,$channel=null){
        return true;
    }

    function slackJoinChannel($channel){
        return true;
    }

    function slackApiPostMessage($channel,$text){
        return true;
    }

    function slackAddReaction($channel,$name,$timestamp){
        return true;
    }

    function slackPostMessageWithEmoji($channel, $text ,array $names){
        return true;
    }
}