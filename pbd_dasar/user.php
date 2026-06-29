<?php
require("../sistem/koneksi.php");
session_start();

if (!isset($_SESSION['iduser'])) {
    header("Location: login.php");
    exit();
}

$hub = open_connection();

// Tambah / Edit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($hub, $_POST['username']);
    $password = mysqli_real_escape_string($hub, $_POST['password']);
    $jenisuser = mysqli_real_escape_string($hub, $_POST['jenisuser']);
    $level = mysqli_real_escape_string($hub, $_POST['level']);
    $status = mysqli_real_escape_string($hub, $_POST['status']);
    $idprodi = (int)$_POST['idprodi'];

    if (isset($_POST['iduser']) && $_POST['iduser'] != '') {
        // Edit
        $iduser = (int)$_POST['iduser'];
        // Jika password diisi, update password juga. Jika kosong, biarkan password lama.
        if (!empty($password)) {
             $query = "UPDATE user SET username='$username', password='$password', jenisuser='$jenisuser', level='$level', status='$status', idprodi=$idprodi WHERE iduser=$iduser";
        } else {
             $query = "UPDATE user SET username='$username', jenisuser='$jenisuser', level='$level', status='$status', idprodi=$idprodi WHERE iduser=$iduser";
        }
    } else {
        // Tambah
        $query = "INSERT INTO user (username, password, jenisuser, level, status, idprodi) VALUES ('$username', '$password', '$jenisuser', '$level', '$status', $idprodi)";
    }
    mysqli_query($hub, $query);
    header("Location: user.php");
}

// Hapus
if (isset($_GET['delete'])) {
    $iduser = (int)$_GET['delete'];
    mysqli_query($hub, "DELETE FROM user WHERE iduser=$iduser");
    header("Location: user.php");
}

// Ambil data untuk Edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $iduser = (int)$_GET['edit'];
    $result = mysqli_query($hub, "SELECT * FROM user WHERE iduser=$iduser");
    $edit_data = mysqli_fetch_assoc($result);
}

// Ambil semua data user, dijoin dengan dt_prodi jika diperlukan untuk tampil
$query_users = "SELECT u.*, p.nmprodi FROM user u LEFT JOIN dt_prodi p ON u.idprodi = p.idprodi";
$result_users = mysqli_query($hub, $query_users);

// Ambil semua data prodi untuk dropdown
$query_prodi = "SELECT * FROM dt_prodi";
$result_prodi = mysqli_query($hub, $query_prodi);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Data User</title>
</head>
<body>
    <h2>Kelola Data User</h2>
    <a href="index.php">Kembali ke Menu Utama</a><br><br>

    <form method="POST" action="">
        <input type="hidden" name="iduser" value="<?php echo $edit_data ? $edit_data['iduser'] : ''; ?>">
        
        <label>Username:</label><br>
        <input type="text" name="username" value="<?php echo $edit_data ? $edit_data['username'] : ''; ?>" required><br>
        
        <label>Password:</label><br>
        <input type="password" name="password" placeholder="<?php echo $edit_data ? 'Kosongkan jika tidak ingin mengubah' : ''; ?>" <?php echo $edit_data ? '' : 'required'; ?>><br>
        
        <label>Jenis User:</label><br>
        <select name="jenisuser" required>
            <option value="0" <?php echo ($edit_data && $edit_data['jenisuser'] == '0') ? 'selected' : ''; ?>>User-Client (0)</option>
            <option value="1" <?php echo ($edit_data && $edit_data['jenisuser'] == '1') ? 'selected' : ''; ?>>User-Admin (1)</option>
        </select><br>
        
        <label>Level:</label><br>
        <select name="level" required>
            <option value="00" <?php echo ($edit_data && $edit_data['level'] == '00') ? 'selected' : ''; ?>>User-Client (00)</option>
            <option value="01" <?php echo ($edit_data && $edit_data['level'] == '01') ? 'selected' : ''; ?>>Level 01</option>
            <option value="10" <?php echo ($edit_data && $edit_data['level'] == '10') ? 'selected' : ''; ?>>Super-Admin (10)</option>
            <option value="11" <?php echo ($edit_data && $edit_data['level'] == '11') ? 'selected' : ''; ?>>Admin (11)</option>
        </select><br>
        
        <label>Status:</label><br>
        <select name="status" required>
            <option value="T" <?php echo ($edit_data && $edit_data['status'] == 'T') ? 'selected' : ''; ?>>Online (T)</option>
            <option value="F" <?php echo ($edit_data && $edit_data['status'] == 'F') ? 'selected' : ''; ?>>Offline (F)</option>
        </select><br>

        <label>Prodi:</label><br>
        <select name="idprodi" required>
            <option value="">-- Pilih Prodi --</option>
            <?php while($row_prodi = mysqli_fetch_assoc($result_prodi)) { ?>
                <option value="<?php echo $row_prodi['idprodi']; ?>" <?php echo ($edit_data && $edit_data['idprodi'] == $row_prodi['idprodi']) ? 'selected' : ''; ?>>
                    <?php echo $row_prodi['nmprodi']; ?>
                </option>
            <?php } ?>
        </select><br><br>
        
        <input type="submit" value="<?php echo $edit_data ? 'Update' : 'Simpan'; ?>">
        <?php if ($edit_data) { ?>
            <a href="user.php">Batal</a>
        <?php } ?>
    </form>

    <br>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID User</th>
            <th>Username</th>
            <th>Jenis User</th>
            <th>Level</th>
            <th>Status</th>
            <th>Prodi</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result_users)) { ?>
        <tr>
            <td><?php echo $row['iduser']; ?></td>
            <td><?php echo $row['username']; ?></td>
            <td><?php echo $row['jenisuser'] == '1' ? 'User-Admin' : 'User-Client'; ?></td>
            <td><?php echo $row['level']; ?></td>
            <td><?php echo $row['status'] == 'T' ? 'Online' : 'Offline'; ?></td>
            <td><?php echo $row['nmprodi'] ? $row['nmprodi'] : 'N/A'; ?></td>
            <td>
                <a href="user.php?edit=<?php echo $row['iduser']; ?>">Edit</a> | 
                <a href="user.php?delete=<?php echo $row['iduser']; ?>" onclick="return confirm('Yakin ingin menghapus?');">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
<?php mysqli_close($hub); ?>
