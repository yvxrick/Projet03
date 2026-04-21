<?php
require "../phpdotenv/vendor/autoload.php";
$dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT']);
$dotenv->load();
