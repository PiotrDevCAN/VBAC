<?php

namespace vbac\reports;

use vbac\interfaces\report;

class downloadablePerson implements report
{    
    const TITLE = 'Aurora Person Table Extract generated from vBAC';
    const SUBJECT = 'Person Table';
    const DESCRIPTION = 'Aurora Person Table Extract generated from vBAC';
    const PREFIX = '';

    public function getReport($resultSetOnly = false)
    {
        return false;
    }

    public function getDetails() {
        $return = array(
            'title' => static::TITLE,
            'subject' => static::SUBJECT,
            'description' => static::DESCRIPTION,
            'prefix' => static::PREFIX
        );
        return $return;
    }
}