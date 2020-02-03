<?php

namespace Korus\Test;

require_once __DIR__ . '/autoload.php';

$c = new TimeMan();

// Fill demo data
$c->FillDemoData(true);

// Start workday for user 2
d($c->user(1)->workday()->start(mktime(8, 45)));


// The workday for user 2 was started by fill demo data

// Some workday pause for user 2
d($c->user(2)->workday()->pause()->start(mktime(10, 9)));
d($c->user(2)->workday()->pause()->stop(mktime(10, 35)));
d($c->user(2)->workday()->pause()->start(mktime(13, 1)));
d($c->user(2)->workday()->pause()->stop(mktime(13, 48)));

// Stop workday for user 2
d($c->user(2)->workday()->stop(mktime(18, 7)));


// Start workday with lateness for user 3
d($c->user(3)->workday()->start(mktime(8, 10)));


// Set all lateness for all users (for cron task)
d($c->fillLateness());