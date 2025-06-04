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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    if ($_GET['action'] === 'create') {
        $activity = trim($_POST['activity'] ?? '');
        $status = isset($_POST['status']) && $_POST['status'] == 1 ? 1 : 0;

        if (!empty($activity)) {
            $stmt = $pdo->prepare("INSERT INTO todo (activity, status, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$activity, $status]);
        }
        header("Location: index.php");
        exit;
    } elseif ($_GET['action'] === 'update') {
        // Proses update data
        $id = (int)($_POST['id'] ?? 0);
        $activity = trim($_POST['activity'] ?? '');
        $status = isset($_POST['status']) && $_POST['status'] == 1 ? 1 : 0;

        if ($id > 0 && !empty($activity)) {
            $stmt = $pdo->prepare("UPDATE todo SET activity = ?, status = ? WHERE id = ?");
            $stmt->execute([$activity, $status, $id]);
        }
        header("Location: index.php");
        exit;
    }
}

// Proses hapus
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM todo WHERE id = ?");
        $stmt->execute([$id]);
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
                <!-- Tombol Tambah -->
                <button id="btnTambah" class="btn btn-primary">Tambah</button>
            </div>

            <!-- Form Tambah, hidden awalnya -->
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
                        <button type="button" id="btnBatalTambah" class="btn btn-secondary">Batal</button>
                    </div>
                </form>
            </div>

            <hr>

            <table class="table table-striped" id="todoTable">
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
                            <tr data-id="<?= $todo['id'] ?>">
                                <td class="activity"><?= htmlspecialchars($todo['activity']) ?></td>
                                <td class="status" data-status="<?= $todo['status'] ?>">
                                    <?php if ($todo['status']) : ?>
                                        <span class="badge bg-success">Selesai</span>
                                    <?php else : ?>
                                        <span class="badge bg-danger">Belum Selesai</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $todo['created_at'] ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning btnEdit">Edit</button>
                                    <a href="index.php?action=delete&id=<?= $todo['id'] ?>" 
                                       class="btn btn-sm btn-danger btnDelete"
                                       onclick="return confirm('Yakin ingin menghapus aktivitas ini?');">
                                       Hapus
                                    </a>
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

            <!-- Form Edit Modal (simple) -->
            <div id="editModal" style="display:none; position:fixed; top:20%; left:50%; transform:translateX(-50%); background:#fff; padding:20px; border:1px solid #ccc; box-shadow:0 2px 10px rgba(0,0,0,0.2); z-index:1000; width: 400px;">
                <h5>Edit Aktivitas</h5>
                <form method="POST" action="index.php?action=update" id="formEdit">
                    <input type="hidden" name="id" id="editId">
                    <div class="mb-3">
                        <label for="editActivity" class="form-label">Aktivitas</label>
                        <input type="text" class="form-control" id="editActivity" name="activity" required>
                    </div>
                    <div class="mb-3">
                        <label for="editStatus" class="form-label">Status</label>
                        <select name="status" id="editStatus" class="form-select" required>
                            <option value="0">Belum Selesai</option>
                            <option value="1">Selesai</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="button" id="btnBatalEdit" class="btn btn-secondary">Batal</button>
                </form>
            </div>

            <!-- Overlay modal -->
            <div id="overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.4); z-index:900;"></div>
        </div>
    </div>
</div>

<script>
// Toggle form tambah
document.getElementById('btnTambah').addEventListener('click', function() {
    const form = document.getElementById('formTambah');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
});
document.getElementById('btnBatalTambah').addEventListener('click', function() {
    document.getElementById('formTambah').style.display = 'none';
});

// Edit button click
document.querySelectorAll('.btnEdit').forEach(btn => {
    btn.addEventListener('click', function() {
        const tr = this.closest('tr');
        const id = tr.getAttribute('data-id');
        const activity = tr.querySelector('.activity').textContent.trim();
        const status = tr.querySelector('.status').getAttribute('data-status');

        // Isi form edit
        document.getElementById('editId').value = id;
        document.getElementById('editActivity').value = activity;
        document.getElementById('editStatus').value = status;

        // Tampilkan modal edit dan overlay
        document.getElementById('editModal').style.display = 'block';
        document.getElementById('overlay').style.display = 'block';
    });
});

// Batal edit
document.getElementById('btnBatalEdit').addEventListener('click', function() {
    document.getElementById('editModal').style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
});

// Klik di overlay tutup modal edit
document.getElementById('overlay').addEventListener('click', function() {
    this.style.display = 'none';
    document.getElementById('editModal').style.display = 'none';
});
</script>

</body>
</html>



