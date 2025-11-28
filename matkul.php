<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("location:index.php");
    exit;
}

if (isset($_POST['tambah'])) {
    $kode = $_POST['kode_matkul'];
    $nama = $_POST['nama_matkul'];
    $sks = $_POST['sks'];
    
    $stmt = mysqli_prepare($koneksi, "INSERT INTO matkul (kode_matkul, nama_matkul, sks) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssi", $kode, $nama, $sks);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("location:matkul.php");
    exit;
}

if (isset($_POST['update'])) {
    $kode = $_POST['kode_matkul'];
    $nama = $_POST['nama_matkul'];
    $sks = $_POST['sks'];
    
    $stmt = mysqli_prepare($koneksi, "UPDATE matkul SET nama_matkul=?, sks=? WHERE kode_matkul=?");
    mysqli_stmt_bind_param($stmt, "sis", $nama, $sks, $kode);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("location:matkul.php");
    exit;
}

if (isset($_GET['hapus'])) {
    $kode = $_GET['hapus'];
    
    mysqli_begin_transaction($koneksi);
    
    try {
        $stmt_nilai = mysqli_prepare($koneksi, "DELETE FROM nilai WHERE kode_matkul=?");
        mysqli_stmt_bind_param($stmt_nilai, "s", $kode);
        mysqli_stmt_execute($stmt_nilai);
        mysqli_stmt_close($stmt_nilai);
        
        $stmt_matkul = mysqli_prepare($koneksi, "DELETE FROM matkul WHERE kode_matkul=?");
        mysqli_stmt_bind_param($stmt_matkul, "s", $kode);
        mysqli_stmt_execute($stmt_matkul);
        mysqli_stmt_close($stmt_matkul);
        
        mysqli_commit($koneksi);
        header("location:matkul.php");
        exit;
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        header("location:matkul.php?error=delete_failed");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mata Kuliah | SIA</title>
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
        
        .sks-badge {
            display: inline-block;
            padding: 0.375rem 0.875rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.875rem;
            background: linear-gradient(135deg, var(--info), #2563eb);
            color: white;
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
            <h3 class="fade-in"><i class="fas fa-book"></i> Data Mata Kuliah</h3>
        </div>
    </div>
    
    <div class="container mb-5">
        <div class="modern-card fade-in">
            <div class="action-bar">
                <div class="search-box" style="flex: 1; max-width: 400px;">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" class="modern-input" placeholder="Cari mata kuliah...">
                </div>
                <button class="btn-modern btn-success-modern" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fas fa-plus"></i> Tambah Mata Kuliah
                </button>
            </div>

            <div class="table-wrapper">
                <table class="modern-table" id="matkulTable">
                    <thead>
                        <tr>
                            <th><i class="fas fa-code"></i> Kode</th>
                            <th><i class="fas fa-book-open"></i> Nama Mata Kuliah</th>
                            <th style="text-align: center;"><i class="fas fa-star"></i> SKS</th>
                            <th style="text-align: center;"><i class="fas fa-cog"></i> Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $data = mysqli_query($koneksi, "SELECT * FROM matkul ORDER BY nama_matkul ASC");
                        $count = 0;
                        while ($row = mysqli_fetch_assoc($data)) {
                            $count++;
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['kode_matkul']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_matkul']) . "</td>";
                            echo "<td style='text-align: center;'><span class='sks-badge'>" . htmlspecialchars($row['sks']) . " SKS</span></td>";
                            echo "<td style='text-align: center;'>";
                            echo "<div style='display: flex; gap: 0.5rem; justify-content: center; align-items: center;'>";
                            echo "<button class='btn-modern btn-warning-modern btn-sm btn-edit-matkul' ";
                            echo "data-kode='" . htmlspecialchars($row['kode_matkul']) . "' ";
                            echo "data-nama='" . htmlspecialchars($row['nama_matkul']) . "' ";
                            echo "data-sks='" . htmlspecialchars($row['sks']) . "' ";
                            echo "title='Edit' style='padding: 0.5rem 0.75rem;'>";
                            echo "<i class='fas fa-edit'></i>";
                            echo "</button>";
                            echo "<a href='?hapus=" . urlencode($row['kode_matkul']) . "' onclick=\"return confirm('Yakin hapus data?')\" class='btn-modern btn-danger-modern btn-sm' title='Hapus' style='padding: 0.5rem 0.75rem;'>";
                            echo "<i class='fas fa-trash'></i>";
                            echo "</a>";
                            echo "</div>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        
                        if ($count == 0) {
                            echo "<tr><td colspan='4'>";
                            echo "<div class='empty-state'>";
                            echo "<i class='fas fa-inbox'></i>";
                            echo "<h4>Belum ada data</h4>";
                            echo "<p>Silakan tambah data mata kuliah terlebih dahulu</p>";
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
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Mata Kuliah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-code"></i> Kode Mata Kuliah</label>
                            <input type="text" name="kode_matkul" class="modern-input" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-book-open"></i> Nama Mata Kuliah</label>
                            <input type="text" name="nama_matkul" class="modern-input" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-star"></i> SKS</label>
                            <input type="number" name="sks" class="modern-input" required min="1" max="6">
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
    
    <div class="modal fade modal-modern" id="modalEditMatkul">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Data Mata Kuliah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="kode_matkul" id="edit_kode">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-book-open"></i> Nama Mata Kuliah</label>
                            <input type="text" name="nama_matkul" id="edit_nama" class="modern-input" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-star"></i> SKS</label>
                            <input type="number" name="sks" id="edit_sks" class="modern-input" required min="1" max="6">
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
            const tableRows = document.querySelectorAll('#matkulTable tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });
        
        document.querySelectorAll('.btn-edit-matkul').forEach(button => {
            button.addEventListener('click', function() {
                const kode = this.getAttribute('data-kode');
                const nama = this.getAttribute('data-nama');
                const sks = this.getAttribute('data-sks');
                
                document.getElementById('edit_kode').value = kode;
                document.getElementById('edit_nama').value = nama;
                document.getElementById('edit_sks').value = sks;
                
                const modal = new bootstrap.Modal(document.getElementById('modalEditMatkul'));
                modal.show();
            });
        });
    </script>
</body>
</html>