AntLog
======

SQL log
-------
````php
<?php

$storage = new \Davajlama\AntLog\Storage\FileStorage(__DIR__ . '/path/to/log/directory');
\Davajlama\AntLog\AntLog::create($storage);


\Davajlama\AntLog\AntLog::logSql($sql, $time);

````

Parse logs
----------
````
./vendor/bin/antlog /path/to/log/directory
````

Result
------
```
==================  STATS  ==================
Total queries count: 9
Unique queries: 4
Unique sessions: 1
Unique runners: 5
Unique apis: 5
Unique patterns: 9

==================  TOP RUNNERS  ==================
0.049s / 2q 5fcb7c06513c3 /api/all?filter={"type":""}&offset=0
...

==================  TOP QUERIES  ==================
0.0452s : SELECT `articles`.* LIMIT 20 /api/all?filter={"type":""}&offset=0
...

==================  TOP SAME QUERIES by time  ==================
0.0884s : SELECT `articles`.* DESC LIMIT @value
...

==================  TOP SAME QUERIES by count  ==================
5x : SELECT `profiles`.* FROM `profiles` WHERE `profiles`.`id` = @value LIMIT @value
...
```
