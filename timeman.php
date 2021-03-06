<?php

namespace Korus\Test;

use RedBeanPHP\R as R;
use RedBeanPHP\RedException\SQL;

class TimeMan
{
    static
        $connect = false,
        $serverTimeZoneOffset;
    private
        $current_user = false;

    /**
     *  Fill demo data
     *
     * @param bool $clean
     * @throws SQL
     */

    function FillDemoData($clean = false)
    {
        if (self::Init()) {
            R::freeze(false);

            if ($clean) // Clean database
                R::nuke();

            // Set types
            R::store(R::dispense(['_type' => 'profile',
                'login' => 'string',
                'name' => 'string',
                'last_name' => 'string',
                'offset' => 'string',
            ]));
            R::wipe('profile');

            // Demo User 1
            R::store(R::dispense(['_type' => 'profile',
                'login' => 'user_1',
                'name' => 'Иван',
                'last_name' => 'Петров',
                'offset' => '+0300',
                'ownWorkdayList' => [
                    ['_type' => 'workday',
                        'date_start' => '2020-01-01 08:55:37',
                        'date_stop' => '2020-01-01 18:01:29',
                        'ownWorkdaypauseList' => [
                            ['_type' => 'workdaypause',
                                'date_start' => '2020-01-01 10:31:53',
                                'date_stop' => '2020-01-01 10:47:22',
                            ],
                            ['_type' => 'workdaypause',
                                'date_start' => '2020-01-01 13:07:17',
                                'date_stop' => '2020-01-01 13:50:32',
                            ],
                            ['_type' => 'workdaypause',
                                'date_start' => '2020-01-01 15:33:34',
                                'date_stop' => '2020-01-01 15:46:27',
                            ],
                        ]
                    ],
                ],
                'ownLatenessList' => [
                    ['_type' => 'lateness',
                        'date' => '2019-05-07',
                    ],
                ]
            ]));

            // Demo User 2
            R::store(R::dispense(['_type' => 'profile',
                'login' => 'user_2',
                'name' => 'Петр',
                'last_name' => 'Сидоров',
                'offset' => '+0300',
                'ownWorkdayList' => [
                    ['_type' => 'workday',
                        'date_start' => '2020-01-01 08:55:37',
                        'date_stop' => '2020-01-01 18:57:49',
                        'ownWorkdaypauseList' => [
                            ['_type' => 'workdaypause',
                                'date_start' => '2020-01-01 10:31:44',
                                'date_stop' => '2020-01-01 18:58:39',
                            ],
                            ['_type' => 'workdaypause',
                                'date_start' => '2020-01-01 13:50:27',
                                'date_stop' => '2020-01-01 14:37:29',
                            ],
                            ['_type' => 'workdaypause',
                                'date_start' => '2020-01-01 15:36:37',
                                'date_stop' => '2020-01-01 16:17:19',
                            ],
                        ]
                    ],
                    ['_type' => 'workday',
                        'date_start' => R::isoDateTime(mktime(9, 26)),
                    ],
                ]
            ]));

            // Demo User 3
            R::store(R::dispense(['_type' => 'profile',
                'login' => 'user_3',
                'name' => 'Сидор',
                'last_name' => 'Иванов',
                'offset' => '+0200',
                'ownWorkdayList' => [
                    ['_type' => 'workday',
                        'date_start' => '2020-01-01 08:55:37',
                        'date_stop' => '2020-01-01 18:57:49',
                        'ownWorkdaypauseList' => [
                            ['_type' => 'workdaypause',
                                'date_start' => '2020-01-01 10:31:44',
                                'date_stop' => '2020-01-01 18:58:39',
                            ],
                            ['_type' => 'workdaypause',
                                'date_start' => '2020-01-01 13:50:27',
                                'date_stop' => '2020-01-01 14:37:29',
                            ],
                            ['_type' => 'workdaypause',
                                'date_start' => '2020-01-01 15:36:37',
                                'date_stop' => '2020-01-01 16:17:19',
                            ],
                        ]
                    ],
                    ['_type' => 'workday',
                        'date_start' => '2020-01-02 08:55:37',
                        'date_stop' => '2020-01-02 18:57:49',
                        'ownWorkdaypauseList' => [
                            ['_type' => 'workdaypause',
                                'date_start' => '2020-01-01 10:31:44',
                                'date_stop' => '2020-01-01 18:58:39',
                            ],
                            ['_type' => 'workdaypause',
                                'date_start' => '2020-01-01 13:50:27',
                                'date_stop' => '2020-01-01 14:37:29',
                            ],
                            ['_type' => 'workdaypause',
                                'date_start' => '2020-01-01 15:36:37',
                                'date_stop' => '2020-01-01 16:17:19',
                            ],
                        ]
                    ],
                ]

            ]));

            R::freeze(true);
        }
    }

    /**
     *  Database connection initial
     *
     * @return bool
     */

    static function Init()
    {
        self::$serverTimeZoneOffset = intval(substr(date('P'), 1, 2));

        if (!self::$connect)
            R::setup('mysql:host=localhost;dbname=korus', 'root');

        self::$connect = R::testConnection();

        R::freeze(true);

        return self::$connect;
    }

    public function user($uid = false)
    {
        if ($uid !== false)
            $this->current_user = $uid;

        return new User($this->current_user);
    }

    public function fillLateness()
    {
        if (self::Init())
            return R::exec('INSERT INTO `lateness`
SELECT NULL id, CONVERT(w.date_start, DATE) date, p.id profile_id
FROM `workday` w
LEFT JOIN `lateness` l ON w.profile_id = l.profile_id AND CONVERT(w.date_start, DATE) = l.date
JOIN `profile` p ON p.id = w.profile_id
WHERE l.date IS NULL AND HOUR(DATE_ADD(w.date_start, INTERVAL TIMESTAMPDIFF(HOUR, UTC_TIMESTAMP(), NOW())-SUBSTRING(p.offset, 2, 2) HOUR)) >= 9
GROUP BY p.id, CONVERT(w.date_start, DATE)');
    }
}