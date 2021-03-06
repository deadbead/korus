<?php

namespace Korus\Test;

use Exception;
use RedBeanPHP\R as R;
use RedBeanPHP\RedException\SQL;

class WorkDay extends User
{
    const
        _type = 'workday';

    protected
        $user,
        $bean;

    private
        $isFinish = false;

    /**
     * WorkDay constructor.
     *
     * @param User $user
     * @param int|bool $id
     * @throws Exception
     */
    function __construct($user, $id = false)
    {
        if ($user->bean && !$user->bean->isEmpty()) {
            $this->user = $user;
            if ($id)
                $this->bean = $this->user->bean->ownWorkdayList[$id];
            else
                $this->bean = reset($this->user->bean->withCondition(
                    '`date_start` > ? ORDER BY `date_start` DESC LIMIT 1',
                    [R::isoDateTime(mktime(0, 0, 0))]
                )->ownWorkdayList);

            $this->isFinish = ($this->bean && !$this->bean->isEmpty() && $this->bean->date_end !== null);
        } else
            throw new Exception('Error. User is missing.');
    }

    /**
     * Workday start.
     *
     * @param bool $t
     * @return WorkDay
     * @throws SQL
     * @throws Exception
     */
    public function start($t = false)
    {
        if ($this->isFinish)
            throw new Exception('Error. Workday is finished.');

        if ($t === false)
            $t = time();

        if (!$this->bean || $this->bean->isEmpty()) {
            $this->bean = R::dispense(self::_type);
            $this->bean->date_start = R::isoDateTime($t);
            $this->user->bean->ownWorkdayList[] = $this->bean;
            $this->user->save();
        } else
            throw new Exception("Error. Workday for {$this->user->bean->name} {$this->user->bean->last_name} already started.");

        return $this;
    }

    /**
     * Workday stop.
     *
     * @param bool $t
     * @return WorkDay
     * @throws SQL
     * @throws Exception
     */

    public function stop($t = false)
    {
        if ($this->isFinish)
            throw new Exception('Error. The workday already finished.');

        if ($t === false)
            $t = time();

        if (!$this->user->bean->isEmpty() && $this->bean && !$this->bean->isEmpty()) {
            $this->bean->date_stop = R::isoDateTime($t);
            $this->user->save();
        } else
            throw new Exception("Error. The workday for {$this->user->bean->name} {$this->user->bean->last_name} is not started.");

        return $this;
    }

    public function pause($id = false)
    {
        return new WorkDayPause($this, $id);
    }
}