<?php
require_once 'config.php';

if (file_exists($cfg['db']['file'])) {
    $db = new PDO("sqlite:".$cfg['db']['file']);
} else {
    touch($cfg['db']['file']);
    $db = new PDO("sqlite:".$cfg['db']['file']);

    $db->exec('CREATE TABLE IF NOT EXISTS distros  (id PRIMARY KEY, name, desc)');
    $db->exec('CREATE TABLE IF NOT EXISTS groups   (id PRIMARY KEY, label)');
    $db->exec('CREATE TABLE IF NOT EXISTS packages (id PRIMARY KEY, pkg_name, label, desc,
                                                    distro_id REFERENCES distro(id),
                                                    group_id REFERENCES groups(id))');

    $distro_data = array();
    $distro_data['id']   = uniqid();
    $distro_data['name'] = 'Example Distro';
    $distro_data['desc'] = 'Some information about the distro';

    $distro_insert = $db->prepare('INSERT INTO distros (id, name, desc) VALUES (:id, :name, :desc)');
    $distro_insert->execute($distro_data);

    $grp_data = array();
    $grp_data['id']   = uniqid();
    $grp_data['label'] = 'Example Group';

    $grp_insert = $db->prepare('INSERT INTO groups (id, label) VALUES (:id, :label)');
    $grp_insert->execute($grp_data);

    $pkg_data = array();
    $pkg_data['id']         = uniqid();
    $pkg_data['pkg_name']   = 'example-pkg';
    $pkg_data['label']  = 'My Custom package';
    $pkg_data['desc']  = 'Some information about the package';
    $pkg_data['distro_id'] = $distro_data['id'];
    $pkg_data['group_id']  = $grp_data['id'];

    $pkg_insert = $db->prepare('INSERT INTO packages (id,  pkg_name,  label,  desc,  distro_id,  group_id)
                                             VALUES (:id, :pkg_name, :label, :desc, :distro_id, :group_id)');
    $pkg_insert->execute($pkg_data);
}
