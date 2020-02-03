<?php

namespace Korus\Test;

use Exception;
use RedBeanPHP\R as R;
use RedBeanPHP\RedException\SQL;

class WorkDayPause extends WorkDay
{
    const
        _type = 'workdaypause';

    protected
        $workday,
        $bean;

    /**
     * WorkDayPause constructor.
     *
     * @param WorkDay $workday
     * @param int|bool $id
     * @throws Exception
     */
    function __construct($workday, $id = false)
    {
        if ($workday->bean && !$workday->bean->isEmpty()) {
            $this->workday = $workday;
            if ($id)
                $this->bean = $this->workday->bean->ownWorkdaypauseList[$id];
            else
                $this->bean = reset($this->workday->bean->withCondition(
                    '(`date_start` > ? AND `date_stop` IS NULL) ORDER BY `date_start` DESC LIMIT 1',
                    [R::isoDateTime(strtotime($this->workday->bean->date_start))]
                )->ownWorkdaypauseList);
        } else
            throw new Exception('Error. Workday is missing.');
    }

    /**
     * Workday pause start.
     *
     * @param bool $t
     * @return WorkDayPause
     * @throws SQL
     * @throws Exception
     */
    public function start($t = false)
    {
        if ($t === false)
            $t = time();

        if (!$this->bean || $this->bean->isEmpty()) {
            $this->bean = R::dispense(self::_type);
            $this->bean->date_start = R::isoDateTime($t);
            $this->workday->bean->ownWorkdaypauseList[] = $this->bean;
            $this->workday->save();
        } else
            throw new Exception('Error. The workday already paused.');

        return $this;
    }

    /**
     * Workday pause stop.
     *
     * @param bool $t
     * @return WorkDayPause
     * @throws SQL
     * @throws Exception
     */

    public function stop($t = false)
    {
        if ($t === false)
            $t = time();

        if (!$this->workday->bean->isEmpty() && $this->bean && !$this->bean->isEmpty()) {
            $this->bean->date_stop = R::isoDateTime($t);
            $this->workday->save();
        } else
            throw new Exception('Error. The workday is not paused.');

        return $this;
    }
}