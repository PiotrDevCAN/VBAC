<?php

namespace vbac\interfaces;

interface notificationEmail
{
    function send($resultSetOnly = false);
}