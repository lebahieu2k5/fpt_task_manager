<?php

date_default_timezone_set('Asia/Ho_Chi_Minh');
ini_set('default_charset', 'UTF-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
