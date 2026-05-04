<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kategori - UTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
    require_once 'config/database.php';
    
    $errors = [];
    $kode = '';
    $nama = '';
    $deskripsi = '';
    $status = 'Aktif';
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // C. Sanitasi input
        $kode = strtoupper(trim($_POST['kode_kategori'] ?? ''));
        $nama = trim($_POST['nama_kategori'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $status = $_POST['status'] ?? 'Aktif';

        // B. Validasi Kode Kategori
        if (empty($kode)) {
            $errors[] = "Kode Kategori wajib diisi.";
        } elseif (strlen($kode) < 4 || strlen($kode) > 10) {
            $errors[] = "Kode Kategori harus berjumlah 4-10 karakter.";
        } elseif (substr($kode, 0, 4) !== "KAT-") {
            $errors[] = "Kode Kategori harus diawali dengan 'KAT-'.";
        } else {
            // Cek duplikasi kode ke database
            $stmt_cek = $conn->prepare("SELECT id_kategori FROM kategori WHERE kode_kategori = ?");
            $stmt_cek->bind_param("s", $kode);
            $stmt_cek->execute();
            if ($stmt_cek->get_result()->num_rows > 0) {
                $errors[] = "Kode Kategori sudah terdaftar di sistem.";
            }
            $stmt_cek->close();
        }

        // Validasi Nama Kategori
        if (empty($nama)) {
            $errors[] = "Nama Kategori wajib diisi.";
        } elseif (strlen($nama) < 3 || strlen($nama) > 50) {
            $errors[] = "Nama Kategori minimal 3 karakter dan maksimal 50 karakter.";
        }

        // Validasi Deskripsi
        if (!empty($deskripsi) && strlen($deskripsi) > 200) {
            $errors[] = "Deskripsi maksimal 200 karakter.";
        }

        // Validasi Status
        if (!in_array($status, ['Aktif', 'Nonaktif'])) {
            $errors[] = "Status tidak valid.";
        }

        // Jika tidak ada error, insert data
        if (empty($errors)) {
            $sql = "INSERT INTO kategori (kode_kategori, nama_kategori, deskripsi, status) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            // Sanitasi output sebelum simpan (Opsional, tapi diminta di spec C)
            $safe_nama = htmlspecialchars($nama);
            $safe_deskripsi = htmlspecialchars($deskripsi);
            
            $stmt->bind_param("ssss", $kode, $safe_nama, $safe_deskripsi, $status);
            
            if ($stmt->execute()) {
                header("Location: index.php?status=success");
                exit();
            } else {
                $errors[] = "Terjadi kesalahan sistem saat menyimpan data.";
            }
            $stmt->close();
        }
    }
    ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Tambah Kategori Baru</h4>
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
                                       placeholder="Contoh: KAT-001" value="<?= htmlspecialchars($kode) ?>" required>
                                <small class="text-muted">4-10 karakter, harus diawali KAT-</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Kategori</label>
                                <input type="text" name="nama_kategori" class="form-control" 
                                       value="<?= htmlspecialchars($nama) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi (Opsional)</label>
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
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="index.php" class="btn btn-secondary">Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>