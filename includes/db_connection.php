<?php
require_once 'config.php';

if (file_exists($cfg['db']['file'])) {
    $db = new PDO("sqlite:".$cfg['db']['file']);
} else {
    touch($cfg['db']['file']);
    $db = new PDO("sqlite:".$cfg['db']['file']);
    $db->exec('create table distros (id INTEGER PRIMARY KEY, icon, name, description)');
}
