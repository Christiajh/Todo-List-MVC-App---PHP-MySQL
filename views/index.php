<?php
// Konfigurasi database
$host = 'localhost';
$dbname = 'AppTodolistPhp';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Proses tambah aktivitas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'create') {
    $activity = trim($_POST['activity'] ?? '');
    $status = isset($_POST['status']) && $_POST['status'] == 1 ? 1 : 0; // 1 = selesai, 0 = belum

    if (!empty($activity)) {
        $stmt = $pdo->prepare("INSERT INTO todo (activity, status, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$activity, $status]);
    }
    header("Location: index.php");
    exit;
}

// Ambil semua data
$stmt = $pdo->query("SELECT * FROM todo ORDER BY created_at DESC");
$todos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Todo List</title>
    <link href="assets/vendor/bootstrap-5.3.6-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container p-5">
    <div class="card">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Todo List</h2>
                <!-- Tombol Tambah yang memunculkan form -->
                <button id="btnTambah" class="btn btn-primary">Tambah</button>
            </div>

            <!-- Form Tambah, awalnya disembunyikan -->
            <div id="formTambah" class="mb-4" style="display:none;">
                <form method="POST" action="index.php?action=create" class="row g-3 align-items-center">
                    <div class="col-md-5">
                        <input type="text" name="activity" class="form-control" placeholder="Aktivitas" required>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select" required>
                            <option value="0" selected>Belum Selesai</option>
                            <option value="1">Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>

            <hr>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Aktivitas</th>
                        <th>Status</th>
                        <th>Tanggal Dibuat</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($todos)) : ?>
                        <?php foreach ($todos as $todo) : ?>
                            <tr>
                                <td><?= htmlspecialchars($todo['activity']) ?></td>
                                <td>
                                    <?php if ($todo['status']) : ?>
                                        <span class="badge bg-success">Selesai</span>
                                    <?php else : ?>
                                        <span class="badge bg-danger">Belum Selesai</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $todo['created_at'] ?></td>
                                <td>
                                    <!-- Contoh tombol tindakan: bisa dikembangkan untuk edit/hapus -->
                                    <button class="btn btn-sm btn-warning" disabled>Edit</button>
                                    <button class="btn btn-sm btn-danger" disabled>Hapus</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4" class="text-center">Belum ada aktivitas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Toggle tampilkan/ sembunyikan form tambah
document.getElementById('btnTambah').addEventListener('click', function() {
    const form = document.getElementById('formTambah');
    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
});
</script>

</body>
</html>
