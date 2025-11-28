<?php
$koneksi = mysqli_connect("localhost", "root", "", "pertemuan_9");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
