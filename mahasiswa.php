<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['username'])) {
    header("location: index.php");
    exit;
}

if (isset($_POST['tambah'])) {
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $prodi = $_POST['prodi'];
    $angkatan = $_POST['angkatan'];
    
    $stmt = mysqli_prepare($koneksi, "INSERT INTO mahasiswa (nim, nama, prodi, angkatan) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $nim, $nama, $prodi, $angkatan);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    header("location:mahasiswa.php");
    exit;
}

if (isset($_POST['update'])) {
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $prodi = $_POST['prodi'];
    $angkatan = $_POST['angkatan'];
    
    $stmt = mysqli_prepare($koneksi, "UPDATE mahasiswa SET nama=?, prodi=?, angkatan=? WHERE nim=?");
    mysqli_stmt_bind_param($stmt, "ssss", $nama, $prodi, $angkatan, $nim);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    header("location:mahasiswa.php");
    exit;
}

if (isset($_GET['hapus'])) {
    $nim = $_GET['hapus'];
    
    mysqli_begin_transaction($koneksi);
    
    try {
        $stmt_nilai = mysqli_prepare($koneksi, "DELETE FROM nilai WHERE nim=?");
        mysqli_stmt_bind_param($stmt_nilai, "s", $nim);
        mysqli_stmt_execute($stmt_nilai);
        mysqli_stmt_close($stmt_nilai);
        
        $stmt_mhs = mysqli_prepare($koneksi, "DELETE FROM mahasiswa WHERE nim=?");
        mysqli_stmt_bind_param($stmt_mhs, "s", $nim);
        mysqli_stmt_execute($stmt_mhs);
        mysqli_stmt_close($stmt_mhs);
        
        mysqli_commit($koneksi);
        header("location:mahasiswa.php");
        exit;
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        header("location:mahasiswa.php?error=delete_failed");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa | SIA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .page-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 2rem 2rem;
            box-shadow: var(--shadow-lg);
        }
        
        .page-header h3 {
            font-weight: 700;
            font-size: 2rem;
            margin: 0;
        }
        
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .table-wrapper {
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <nav class="modern-navbar">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <a class="navbar-brand" href="dashboard.php">
                    <i class="fas fa-graduation-cap"></i> SIA
                </a>
                <div class="d-flex gap-2">
                    <a href="dashboard.php" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a href="logout.php" class="btn btn-sm btn-danger">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="page-header">
        <div class="container">
            <h3 class="fade-in"><i class="fas fa-user-graduate"></i> Data Mahasiswa</h3>
        </div>
    </div>
    
    <div class="container mb-5">
        <div class="modern-card fade-in">
            <div class="action-bar">
                <div class="search-box" style="flex: 1; max-width: 400px;">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" class="modern-input" placeholder="Cari mahasiswa...">
                </div>
                <button class="btn-modern btn-success-modern" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fas fa-plus"></i> Tambah Mahasiswa
                </button>
            </div>

            <div class="table-wrapper">
                <table class="modern-table" id="mahasiswaTable">
                    <thead>
                        <tr>
                            <th><i class="fas fa-id-card"></i> NIM</th>
                            <th><i class="fas fa-user"></i> Nama</th>
                            <th><i class="fas fa-building"></i> Prodi</th>
                            <th><i class="fas fa-calendar"></i> Angkatan</th>
                            <th style="text-align: center;"><i class="fas fa-cog"></i> Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $data = mysqli_query($koneksi, "SELECT * FROM mahasiswa ORDER BY angkatan DESC");
                        $count = 0;
                        while ($row = mysqli_fetch_assoc($data)) {
                            $count++;
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['nim']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['prodi']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['angkatan']) . "</td>";
                            echo "<td style='text-align: center;'>";
                            echo "<div style='display: flex; gap: 0.5rem; justify-content: center; align-items: center;'>";
                            echo "<button class='btn-modern btn-warning-modern btn-sm btn-edit-mhs' ";
                            echo "data-nim='" . htmlspecialchars($row['nim']) . "' ";
                            echo "data-nama='" . htmlspecialchars($row['nama']) . "' ";
                            echo "data-prodi='" . htmlspecialchars($row['prodi']) . "' ";
                            echo "data-angkatan='" . htmlspecialchars($row['angkatan']) . "' ";
                            echo "title='Edit' style='padding: 0.5rem 0.75rem;'>";
                            echo "<i class='fas fa-edit'></i>";
                            echo "</button>";
                            echo "<a href='?hapus=" . urlencode($row['nim']) . "' onclick=\"return confirm('Yakin hapus data?')\" class='btn-modern btn-danger-modern btn-sm' title='Hapus' style='padding: 0.5rem 0.75rem;'>";
                            echo "<i class='fas fa-trash'></i>";
                            echo "</a>";
                            echo "</div>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        
                        if ($count == 0) {
                            echo "<tr><td colspan='5'>";
                            echo "<div class='empty-state'>";
                            echo "<i class='fas fa-inbox'></i>";
                            echo "<h4>Belum ada data</h4>";
                            echo "<p>Silakan tambah data mahasiswa terlebih dahulu</p>";
                            echo "</div>";
                            echo "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade modal-modern" id="modalTambah">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-id-card"></i> NIM</label>
                            <input type="text" name="nim" class="modern-input" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-user"></i> Nama</label>
                            <input type="text" name="nama" class="modern-input" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-building"></i> Prodi</label>
                            <input type="text" name="prodi" class="modern-input" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-calendar"></i> Angkatan</label>
                            <input type="number" name="angkatan" class="modern-input" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="tambah" class="btn-modern btn-success-modern">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="modal fade modal-modern" id="modalEditMahasiswa">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Data Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="nim" id="edit_nim">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-user"></i> Nama</label>
                            <input type="text" name="nama" id="edit_nama" class="modern-input" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-building"></i> Prodi</label>
                            <input type="text" name="prodi" id="edit_prodi" class="modern-input" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-calendar"></i> Angkatan</label>
                            <input type="number" name="angkatan" id="edit_angkatan" class="modern-input" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="update" class="btn-modern btn-primary-modern">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#mahasiswaTable tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });
        
        document.querySelectorAll('.btn-edit-mhs').forEach(button => {
            button.addEventListener('click', function() {
                const nim = this.getAttribute('data-nim');
                const nama = this.getAttribute('data-nama');
                const prodi = this.getAttribute('data-prodi');
                const angkatan = this.getAttribute('data-angkatan');
                
                document.getElementById('edit_nim').value = nim;
                document.getElementById('edit_nama').value = nama;
                document.getElementById('edit_prodi').value = prodi;
                document.getElementById('edit_angkatan').value = angkatan;
                
                const modal = new bootstrap.Modal(document.getElementById('modalEditMahasiswa'));
                modal.show();
            });
        });
    </script>
</body>
</html>