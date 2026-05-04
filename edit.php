<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kategori - UTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
    require_once 'config/database.php';
    
    $errors = [];
    $id = $_GET['id'] ?? null;

    // A. Retrieve Data berdasarkan ID
    if (!$id) {
        header("Location: index.php");
        exit();
    }

    $stmt_get = $conn->prepare("SELECT * FROM kategori WHERE id_kategori = ?");
    $stmt_get->bind_param("i", $id);
    $stmt_get->execute();
    $result = $stmt_get->get_result();
    $data = $result->fetch_assoc();

    if (!$data) {
        // Redirect jika ID tidak ditemukan
        header("Location: index.php?error=notfound");
        exit();
    }

    // Ambil data untuk pre-fill form
    $kode = $data['kode_kategori'];
    $nama = $data['nama_kategori'];
    $deskripsi = $data['deskripsi'];
    $status = $data['status'];

    // D. Proses Update Jika Form di-submit
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $kode = strtoupper(trim($_POST['kode_kategori']));
        $nama = trim($_POST['nama_kategori']);
        $deskripsi = trim($_POST['deskripsi']);
        $status = $_POST['status'];

        // C. Validasi (Sama dengan CREATE)
        if (empty($kode) || strlen($kode) < 4 || substr($kode, 0, 4) !== "KAT-") {
            $errors[] = "Kode Kategori tidak valid (Min 4 karakter & diawali KAT-).";
        } else {
            // Cek duplikasi tapi exclude record yang sedang diedit
            $stmt_cek = $conn->prepare("SELECT id_kategori FROM kategori WHERE kode_kategori = ? AND id_kategori != ?");
            $stmt_cek->bind_param("si", $kode, $id);
            $stmt_cek->execute();
            if ($stmt_cek->get_result()->num_rows > 0) {
                $errors[] = "Kode Kategori sudah digunakan oleh data lain.";
            }
        }

        if (empty($nama) || strlen($nama) < 3 || strlen($nama) > 50) {
            $errors[] = "Nama Kategori wajib diisi (3-50 karakter).";
        }

        if (empty($errors)) {
            // D. Proses Update dengan Prepared Statement
            $sql = "UPDATE kategori SET kode_kategori = ?, nama_kategori = ?, deskripsi = ?, status = ? WHERE id_kategori = ?";
            $stmt_upd = $conn->prepare($sql);
            $stmt_upd->bind_param("ssssi", $kode, $nama, $deskripsi, $status, $id);
            
            if ($stmt_upd->execute()) {
                header("Location: index.php?status=updated");
                exit();
            } else {
                $errors[] = "Gagal mengupdate data.";
            }
        }
    }
    ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning">
                        <h4 class="mb-0">Edit Kategori</h4>
                    </div>
                    <div class="card-body">
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Kode Kategori</label>
                                <input type="text" name="kode_kategori" class="form-control" 
                                       value="<?= htmlspecialchars($kode) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Kategori</label>
                                <input type="text" name="nama_kategori" class="form-control" 
                                       value="<?= htmlspecialchars($nama) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($deskripsi) ?></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label d-block">Status</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="aktif" value="Aktif" <?= ($status == 'Aktif') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="aktif">Aktif</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="nonaktif" value="Nonaktif" <?= ($status == 'Nonaktif') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="nonaktif">Nonaktif</label>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning">Update Data</button>
                                <a href="index.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>