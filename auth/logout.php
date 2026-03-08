<?php

    require_once '../includes/config.php';

    session_start();

    $_SESSION = [];

    session_destroy();

    header("Location: " . SITE_URL . "/index.php");
    exit;

?>