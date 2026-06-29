<?php
require("../sistem/koneksi.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hub = open_connection();
    $username = mysqli_real_escape_string($hub, $_POST['username']);
    $password = $_POST['password']; // Sebaiknya pakai hashing seperti MD5 atau password_hash, tapi untuk simpelnya ini dulu.

    $query = "SELECT * FROM user WHERE username = '$username' AND password = '$password' AND status = 'T'";
    $result = mysqli_query($hub, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['iduser'] = $row['iduser'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['level'] = $row['level'];
        $_SESSION['idprodi'] = $row['idprodi'];
        header("Location: index.php");
    } else {
        $error = "Username atau Password salah atau akun tidak aktif.";
    }
    mysqli_close($hub);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login UAP</title>
</head>
<body>
    <h2>Login</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST" action="">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>
        
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        
        <input type="submit" value="Login">
    </form>
</body>
</html>
