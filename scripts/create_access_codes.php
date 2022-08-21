<?php

require __DIR__ . '/../require/db_connect.php';

function create_code($segments = 3, $segment_length = 4) {
    $permited = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < $segments; $i++) {
        if ($i) $code .= '-';
        $code .= substr(str_shuffle($permited), 0, $segment_length);
    }
    return $code;
}

function create_access_code() {
    do {
        $code = create_code();
        $exists = exists('select * from access_codes where code = \'' . $code . '\'');
    } while ($exists);
    execute('insert into access_codes (code) values (\'' . $code . '\')');
    return $code;
}

for($i = 0; $i < 10; $i++) {
    echo create_access_code() . PHP_EOL;
}