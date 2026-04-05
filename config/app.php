<?php

declare(strict_types=1);

const APP_NAME = 'DAAWEYE TELEMEDICINE SYSTEM';
const BASE_URL = '/';
const SESSION_NAME = 'daaweeye_session';

if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
