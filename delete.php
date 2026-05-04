<?php
require_once 'config/database.php';

// A. Validasi ID dari GET
$id = $_GET['id'] ?? null;

if ($id) {
    // Cek apakah ID ada di database untuk memastikan validitas
    $stmt_check = $conn->prepare("SELECT id_kategori FROM kategori WHERE id_kategori = ?");
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        // B. Proses Delete dengan Prepared Statement
        $stmt_del = $conn->prepare("DELETE FROM kategori WHERE id_kategori = ?");
        $stmt_del->bind_param("i", $id);
        
        if ($stmt_del->execute()) {
            // Cek affected_rows untuk memastikan ada baris yang terhapus
            if ($stmt_del->affected_rows > 0) {
                // C. Redirect dengan pesan sukses
                header("Location: index.php?status=deleted");
                exit();
            }
        }
        $stmt_del->close();
    } else {
        // Redirect jika ID tidak ditemukan di DB
        header("Location: index.php?status=notfound");
        exit();
    }
    $stmt_check->close();
} else {
    // Redirect jika ID tidak ada di parameter URL
    header("Location: index.php");
    exit();
}
?>