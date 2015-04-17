<?php
require 'includes/db_connection.php';

try {
    if (isset($_POST['delete_pk'])) {
        $sql = $db->prepare("DELETE FROM packages WHERE id = ?");
        $sql->execute(array($_POST['delete_pk']));
    } elseif (!isset($_POST['pk'])) {
        $id = uniqid();
        $sql = $db->prepare("INSERT INTO packages (id, distro_id, group_id, pkg_name, label, desc) VALUES (?,?,?,?,?,?)");
        $sql->execute(array(
                        $id,
                        $_POST['distro_id'],
                        $_POST['group_id'],
                        $_POST['pkg_name'],
                        $_POST['label'],
                        $_POST['desc']
        ));
        $sql = $db->prepare("SELECT distros.name, groups.label as 'group' FROM packages JOIN distros ON packages.distro_id = distros.id JOIN groups ON group_id = groups.id WHERE packages.id = ? LIMIT 1");
        $sql->execute(array($id));
        $row = $sql->fetch();
        $response = array(
                      "id"          => $id,
                      "name"        => $row['name'],
                      "group"       => $row['group'],
                      "group_id"    => $_POST['group_id'],
                      "pkg_name"    => $_POST['pkg_name'],
                      "label"       => $_POST['label'],
                      "desc"        => $_POST['desc']
                      );
        echo json_encode($response);
    } else {
        $sql = $db->prepare("UPDATE packages SET $_POST[name] = ? WHERE id = ?");
        $sql->execute(array($_POST['value'], $_POST['pk']));
    }

} catch (PDOException $e) {
    echo $e->getMessage();
}
