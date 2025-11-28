<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("location:index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $stmt = mysqli_prepare($koneksi, "DELETE FROM nilai WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    header("Location: nilai.php");
    exit;
}

header("Location: nilai.php");
exit;
?>