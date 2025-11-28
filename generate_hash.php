<?php
$password_plain = '123456';
$password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

echo "Password: " . $password_plain . "<br>";
echo "Hash: " . $password_hash . "<br>";
echo "<br>Copy hash di atas dan masukkan ke database kolom 'password' untuk user.";
?>