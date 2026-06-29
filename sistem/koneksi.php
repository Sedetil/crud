<?php

function open_connection() {
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "akademik";
    $koneksi = mysqli_connect($host, $username, $password, $dbname);
    return $koneksi;
}

?>