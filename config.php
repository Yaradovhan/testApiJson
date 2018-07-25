<?php

function dd($param)
{
    echo "<pre>";
    var_dump($param);
    echo "</pre>";
}


define('DSN_PG', 'pgsql');
define('DSN_MY', 'mysql');
define('DATABASE', 'dbuser');
define('USER', 'dbuser');
define('PASSWORD', 'dbuser');
