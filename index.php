<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kategori - UTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
    require_once 'config/database.php';
    
    // A. Query Data dengan Prepared Statement
    $query = "SELECT id_kategori, kode_kategori, nama_kategori, deskripsi, status FROM kategori ORDER BY id_kategori DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    ?>
    
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Daftar Kategori Buku</h2>
            <a href="create.php" class="btn btn-primary">Tambah Kategori</a>
        </div>
        
        <?php if(isset($_GET['status'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                Operasi berhasil dilakukan!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">No</th>
                            <th width="100">Kode</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th width="100">Status</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // B. Loop data dan tampilkan
                        if ($result->num_rows > 0) {
                            $no = 1;
                            while ($row = $result->fetch_assoc()) {
                                // Logika Badge Status
                                $statusBadge = ($row['status'] == 'Aktif') 
                                    ? '<span class="badge bg-success">Aktif</span>' 
                                    : '<span class="badge bg-danger">Nonaktif</span>';
                                
                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . htmlspecialchars($row['kode_kategori']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['nama_kategori']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['deskripsi']) . "</td>";
                                echo "<td>" . $statusBadge . "</td>";
                                echo "<td>
                                        <a href='edit.php?id=" . $row['id_kategori'] . "' class='btn btn-sm btn-warning'>Edit</a>
                                        <button onclick='confirmDelete(" . $row['id_kategori'] . ")' class='btn btn-sm btn-danger'>Hapus</button>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>Data tidak ditemukan</td></tr>";
                        }
                        $stmt->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // C. Tombol Aksi dengan Konfirmasi JavaScript
    function confirmDelete(id) {
        if (confirm('Yakin ingin menghapus kategori ini?')) {
            window.location.href = 'delete.php?id=' + id;
        }
    }
    </script>
</body>
</html>