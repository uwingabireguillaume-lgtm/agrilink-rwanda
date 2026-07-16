<?php
require_once __DIR__ . '/../config/bootstrap.php';

$_SESSION = [];
session_destroy();

session_start();
setFlash('success', 'You have been logged out.');
redirectTo('/index.php');
