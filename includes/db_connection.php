<?php
require_once 'config.php';

if (file_exists($cfg['db']['file'])) {
    $db = new PDO("sqlite:".$cfg['db']['file']);
} else {
    touch($cfg['db']['file']);
    $db = new PDO("sqlite:".$cfg['db']['file']);

    $db->exec('CREATE TABLE distros (id INTEGER PRIMARY KEY, name, description)');

    $example_data = array();
    $example_data['id']          = uniqid();
    $example_data['name']        = 'Example Distro';
    $example_data['description'] = 'Some distro';

    $insert = $db->prepare('INSERT INTO distros (id, name, description)
                            VALUES (:id, :name, :description)');

    $insert->exec($example_data);
}
