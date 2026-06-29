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
    $kdprodi = mysqli_real_escape_string($hub, $_POST['kdprodi']);
    $nmprodi = mysqli_real_escape_string($hub, $_POST['nmprodi']);
    $akreditasi = mysqli_real_escape_string($hub, $_POST['akreditasi']);

    if (isset($_POST['idprodi']) && $_POST['idprodi'] != '') {
        // Edit
        $idprodi = $_POST['idprodi'];
        $query = "UPDATE dt_prodi SET kdprodi='$kdprodi', nmprodi='$nmprodi', akreditasi='$akreditasi' WHERE idprodi=$idprodi";
    } else {
        // Tambah
        $query = "INSERT INTO dt_prodi (kdprodi, nmprodi, akreditasi) VALUES ('$kdprodi', '$nmprodi', '$akreditasi')";
    }
    mysqli_query($hub, $query);
    header("Location: prodi.php");
}

// Hapus
if (isset($_GET['delete'])) {
    $idprodi = $_GET['delete'];
    mysqli_query($hub, "DELETE FROM dt_prodi WHERE idprodi=$idprodi");
    header("Location: prodi.php");
}

// Ambil data untuk Edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $idprodi = $_GET['edit'];
    $result = mysqli_query($hub, "SELECT * FROM dt_prodi WHERE idprodi=$idprodi");
    $edit_data = mysqli_fetch_assoc($result);
}

// Ambil semua data
$result = mysqli_query($hub, "SELECT * FROM dt_prodi");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Data Prodi</title>
</head>
<body>
    <h2>Kelola Data Prodi</h2>
    <a href="index.php">Kembali ke Menu Utama</a><br><br>

    <form method="POST" action="">
        <input type="hidden" name="idprodi" value="<?php echo $edit_data ? $edit_data['idprodi'] : ''; ?>">
        
        <label>Kode Prodi:</label><br>
        <input type="text" name="kdprodi" value="<?php echo $edit_data ? $edit_data['kdprodi'] : ''; ?>" required><br>
        
        <label>Nama Prodi:</label><br>
        <input type="text" name="nmprodi" value="<?php echo $edit_data ? $edit_data['nmprodi'] : ''; ?>" required><br>
        
        <label>Akreditasi:</label><br>
        <select name="akreditasi" required>
            <option value="A" <?php echo ($edit_data && $edit_data['akreditasi'] == 'A') ? 'selected' : ''; ?>>A</option>
            <option value="B" <?php echo ($edit_data && $edit_data['akreditasi'] == 'B') ? 'selected' : ''; ?>>B</option>
            <option value="C" <?php echo ($edit_data && $edit_data['akreditasi'] == 'C') ? 'selected' : ''; ?>>C</option>
            <option value="-" <?php echo ($edit_data && $edit_data['akreditasi'] == '-') ? 'selected' : ''; ?>>-</option>
        </select><br><br>
        
        <input type="submit" value="<?php echo $edit_data ? 'Update' : 'Simpan'; ?>">
        <?php if ($edit_data) { ?>
            <a href="prodi.php">Batal</a>
        <?php } ?>
    </form>

    <br>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Kode Prodi</th>
            <th>Nama Prodi</th>
            <th>Akreditasi</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['idprodi']; ?></td>
            <td><?php echo $row['kdprodi']; ?></td>
            <td><?php echo $row['nmprodi']; ?></td>
            <td><?php echo $row['akreditasi']; ?></td>
            <td>
                <a href="prodi.php?edit=<?php echo $row['idprodi']; ?>">Edit</a> | 
                <a href="prodi.php?delete=<?php echo $row['idprodi']; ?>" onclick="return confirm('Yakin ingin menghapus?');">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
<?php mysqli_close($hub); ?>
