<?php

namespace vbac\interfaces;

use vbac\personRecord;

interface notificationEmail
{
    function send(personRecord $person);
}