<?php
$pesEmail = 'Hello &&firstName&&,';
$pesEmail.= '<p>We have recently made changes to our LBG contracts, this includes the requirement for PES revalidation.  It is now within 8 weeks of your revalidation date.</p>';
$pesEmail.= '<p>We have been advised that you are <b>offboarding/offboarded</b> from the <b>vBAC</b> system.  This means that you are no longer working on the <b>Ventus</b> contract but <b>may still be working</b>  on the LBG Account.</p>';
$pesEmail.= '<p>If you have offboarded  - you do not need to go through this revalidation process, however your clearance will be revoked and you will be required to go through <b>full PES</b> if you return to an LBG contract in the future.   If we <b>do not hear</b> from you within <b>5 working days</b> we will assume that you do not want to move forward and your PES will be <b>revoked.</b></p>';
$pesEmail.= '<p>If you would like to <b>retain</b> your LBG PES (either because you are still working on the account or you would like to have it for future use), please contact us and we will progress you further.</p>';
$pesEmail.= '<p>If you have any questions, you do not have any of the listed documents or are unsure about the process please contact the PES Team.</p>';
$pesEmail.= '<p>Many Thanks for your cooperation</p>';
$pesEmail.= '<h3>Lloyds PES Team</h3>';
$pesEmailPattern = array('/&&firstName&&/');

