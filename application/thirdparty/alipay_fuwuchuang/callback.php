<?php

file_put_contents('/tmp/log.txt', date('Y-m-d H:i:s').print_r($GLOBALS['HTTP_RAW_POST_DATA'], true)."\n", FILE_APPEND);
file_put_contents('/tmp/log.txt', date('Y-m-d H:i:s').'post='.print_r($_POST, true)."\n", FILE_APPEND);
echo "ok";
