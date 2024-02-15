<?php
namespace vbac\emails;

use vbac\interfaces\notificationEmail;
use vbac\personRecord;

class offboardingEmail implements notificationEmail {

    private static $offboardingEmail = 'Please initiate OFFBOARDING for the following individual:\n
      Name : &&name&&
      Serial: &&cnum&&
      Email Address : &&email&&
      Notes Id : &&notesid&&

      Projected End Date : &&projectedEndDate&&

      Country working in : &&country&&
      LoB : &&lob&&
      Employee Type:&&type&&
      Functional Mgr: &&functionalMgr&&';

    private static $offboardingEmailPattern = array(
      '/&&name&&/',
      '/&&cnum&&/',
      '/&&email&&/',
      '/&&notesid&&/',
      '/&&projectedEndDate&&/',
      '/&&country&&/',
      '/&&lob&&/',
      '/&&type&&/',
      '/&&functionalMgr&&/',
    );

    function send(personRecord $person){
      
    }
}