<?php
date_default_timezone_set('America/Sao_Paulo');
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "clinicavet";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function tratarLinkImagem($url) {
    if (empty($url) || strpos($url, 'assets/') === 0) {
        return !empty($url) ? $url : 'assets/img/vet.png';
    }

    if (strpos($url, 'drive.google.com') !== false) {
        preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $url, $matches);
        if (isset($matches[1])) {
            return 'https://drive.google.com/uc?export=view&id=' . $matches[1];
        }
    }
    return $url;
}
?>