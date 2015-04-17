<?php
require 'includes/db_connection.php';

try {
    if (isset($_POST['delete_pk'])) {
        $sql = sprintf("DELETE FROM distros WHERE id = '%s'", $_POST['delete_pk']);
    } elseif (!isset($_POST['pk'])) {
        $id = uniqid();
        $sql = sprintf(
            "INSERT INTO distros (id, name, desc)".
            "VALUES (\"%s\", \"%s\", \"%s\")",
            $id,
            $_POST['name'],
            $_POST['desc']
        );

        $response = array(
                      "id"=>   $id,
                      "name"=> $_POST['name'],
                      "desc"=> $_POST['desc']
        );
        echo json_encode($response);
    } else {
        $sql = sprintf(
            "UPDATE distros SET %s=\"%s\" WHERE id=\"%s\"",
            $_POST['name'],
            $_POST['value'],
            $_POST['pk']
        );
    }

    $stmt = $db->prepare($sql);
    $stmt->execute();

} catch (PDOException $e) {
    echo $e->getMessage();
}
