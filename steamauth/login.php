<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'config.php';
require 'OpenID.php';

$domain = 'http://localhost/steamauth/';
$return = 'http://localhost/steamauth/login.php';

$openid = new LightOpenID($domain);

try {
    if (!isset($_GET['openid_assoc_handle'])) {
        $openid->setIdentity('https://steamcommunity.com/openid');
        $openid->setReturnUrl($return);
        $openid->setRealm('http://localhost/');
        header('Location: ' . $openid->authUrl());
        exit;
    }

    if (isset($_GET['openid_mode']) && $_GET['openid_mode'] === 'cancel') {
        echo 'Sisse logimine katkesti.';
        exit;
    }

    if ($openid->validate()) {
        $id = $openid->getIdentity();
        preg_match('/^https:\/\/steamcommunity\.com\/openid\/id\/(7\d{15,25}+)$/', $id, $matches);
        $_SESSION['steamid'] = $matches[1];

        header('Location: ../dashboard.php');
        exit;
    } else {
        echo 'Steamiga logimise viga.';
    }

} catch (Exception $e) {
    echo '--Takenncs osa -- openid viga: ' . $e->getMessage();
}
?>
