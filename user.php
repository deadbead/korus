<?php

namespace Korus\Test;

use RedBeanPHP\R as R;

class User extends TimeMan
{
    const
        _type = 'profile';

    protected
        $bean;

    function __construct($id = false)
    {
        if ($id && self::Init()) {
            $this->bean = R::load(self::_type, $id);
        }
    }

    public function workday($id = false)
    {
        return new WorkDay($this, $id);
    }

    protected function localDateTime($t = false)
    {
        if ($t === false)
            $t = time();

        if (!$this->bean->isEmpty())
            return $t + (intval(substr($this->bean->offset, 1, 2)) - self::$serverTimeZoneOffset) * 3600;
    }

    protected function save()
    {
        R::store($this->bean);
    }
}