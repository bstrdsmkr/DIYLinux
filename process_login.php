<?php
require 'password.php';
require_once 'config.php';

$user = $_POST['uname'];
$pass = $_POST['pass'];

define(LDAP_OPT_DIAGNOSTIC_MESSAGE, 0x0032);

$ldap_conn = ldap_connect($cfg['ldap']['server_url']) or die("Failed to connect to LDAP server.");
$user_dn = sprintf($cfg['ldap']['userdn'], $user);

ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);

function is_in_group($ad, $userdn, $groupdn) {
    $attributes = array('members');
    $result = ldap_read($ad, $userdn, "(memberof={$groupdn})", $attributes);
    if ($result === false) { return false; };
    $entries = ldap_get_entries($ad, $result);
    return ($entries['count'] > 0);
}

$binding = ldap_bind($ldap_conn, $user_dn, $pass);
if (!$binding) {
    header("HTTP/1.1 401 Unauthorized");
    echo "Invalid Login";
} else {
    session_start();
    $_SESSION['login'] = true;
    if (is_in_group($ldap_conn, $userdn, $cfg['ldap']['admin_group_dn'])) {
        $_SESSION['role'] = "admin";
    } else {
        $_SESSION['role'] = "user";
    }
    echo "wizard.php";
}
