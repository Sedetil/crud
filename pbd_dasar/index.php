<?php
session_start();
if (!isset($_SESSION['iduser'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Menu Utama - UAP</title>
</head>
<body>
    <h2>Selamat Datang, <?php echo $_SESSION['username']; ?></h2>
    <p>Level Akses: <?php echo $_SESSION['level']; ?></p>
    
    <ul>
        <li><a href="prodi.php">Kelola Data Prodi</a></li>
        <li><a href="user.php">Kelola Data User</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</body>
</html>
