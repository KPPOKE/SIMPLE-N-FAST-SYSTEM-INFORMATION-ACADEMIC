<?php
session_start();
include 'koneksi.php'; 

if (!isset($_SESSION['username'])) {
    header('location:index.php');
    exit;
}

if (isset($_POST['tambah'])) {
    $nidn = mysqli_real_escape_string($koneksi, $_POST['nidn']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);

    $stmt = mysqli_prepare($koneksi, "INSERT INTO dosen (nidn, nama) VALUES (?, ?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $nidn, $nama);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            $_SESSION['success_message'] = "Data dosen berhasil ditambahkan.";
            header('location:dosen.php');
            exit;
        } else {
            $_SESSION['error_message'] = "Gagal menambahkan data dosen: " . mysqli_error($koneksi);
        }
    } else {
        $_SESSION['error_message'] = "Gagal menyiapkan statement: " . mysqli_error($koneksi);
    }
    header('location:dosen.php'); // Redirect even on error to show message
    exit;
}

if (isset($_GET['hapus'])) {
    $nidn = mysqli_real_escape_string($koneksi, $_GET['hapus']);

    $check_stmt = mysqli_prepare($koneksi, "SELECT COUNT(*) FROM nilai WHERE nidn_dosen = ?");
    if ($check_stmt) {
        mysqli_stmt_bind_param($check_stmt, "s", $nidn);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_bind_result($check_stmt, $nilai_count);
        mysqli_stmt_fetch($check_stmt);
        mysqli_stmt_close($check_stmt);

        if ($nilai_count > 0) {
            $_SESSION['error_message'] = "Tidak dapat menghapus dosen karena masih memiliki data nilai terkait.";
        } else {
            $stmt = mysqli_prepare($koneksi, "DELETE FROM dosen WHERE nidn=?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $nidn);
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    $_SESSION['success_message'] = "Data dosen berhasil dihapus.";
                } else {
                    $_SESSION['error_message'] = "Gagal menghapus data dosen: " . mysqli_error($koneksi);
                }
            } else {
                $_SESSION['error_message'] = "Gagal menyiapkan statement: " . mysqli_error($koneksi);
            }
        }
    } else {
        $_SESSION['error_message'] = "Gagal menyiapkan statement pengecekan relasi: " . mysqli_error($koneksi);
    }
    header('location:dosen.php');
    exit;
}

if (isset($_POST['update'])) {
    $nidn = mysqli_real_escape_string($koneksi, $_POST['nidn']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);

    $stmt = mysqli_prepare($koneksi, "UPDATE dosen SET nama=? WHERE nidn=?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $nama, $nidn);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            $_SESSION['success_message'] = "Data dosen berhasil diperbarui.";
            header('location:dosen.php');
            exit;
        } else {
            $_SESSION['error_message'] = "Gagal memperbarui data dosen: " . mysqli_error($koneksi);
        }
    } else {
        $_SESSION['error_message'] = "Gagal menyiapkan statement: " . mysqli_error($koneksi);
    }
    header('location:dosen.php'); // Redirect even on error to show message
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Dosen | SIA</title>
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
            <h3 class="fade-in"><i class="fas fa-chalkboard-teacher"></i> Data Dosen</h3>
        </div>
    </div>
    
    <div class="container mb-5">
        <div class="modern-card fade-in">
            <div class="action-bar">
                <div class="search-box" style="flex: 1; max-width: 400px;">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" class="modern-input" placeholder="Cari dosen...">
                </div>
                <button class="btn-modern btn-success-modern" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fas fa-plus"></i> Tambah Dosen
                </button>
            </div>

            <div class="table-wrapper">
                <table class="modern-table" id="dosenTable">
                    <thead>
                        <tr>
                            <th><i class="fas fa-id-badge"></i> NIDN</th>
                            <th><i class="fas fa-user"></i> Nama Dosen</th>
                            <th style="text-align: center;"><i class="fas fa-cog"></i> Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($koneksi, "SELECT * FROM dosen ORDER BY nama ASC");
                        $count = 0;
                        if ($query) {
                            while ($row = mysqli_fetch_assoc($query)) {
                                $count++;
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['nidn'], ENT_QUOTES, 'UTF-8') . "</td>";
                                echo "<td>" . htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8') . "</td>";
                                echo "<td style='text-align: center;'>";
                                echo "<div style='display: flex; gap: 0.5rem; justify-content: center; align-items: center;'>";
                                echo "<button class='btn-modern btn-warning-modern btn-sm btn-edit-dosen' ";
                                echo "data-nidn='" . htmlspecialchars($row['nidn'], ENT_QUOTES, 'UTF-8') . "' ";
                                echo "data-nama='" . htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8') . "' ";
                                echo "title='Edit' style='padding: 0.5rem 0.75rem;'>";
                                echo "<i class='fas fa-edit'></i>";
                                echo "</button>";
                                echo "<a href='?hapus=" . urlencode($row['nidn']) . "' onclick=\"return confirm('Yakin hapus data?')\" class='btn-modern btn-danger-modern btn-sm' title='Hapus' style='padding: 0.5rem 0.75rem;'>";
                                echo "<i class='fas fa-trash'></i>";
                                echo "</a>";
                                echo "</div>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        }
                        
                        if ($count == 0) {
                            echo "<tr><td colspan='3'>";
                            echo "<div class='empty-state'>";
                            echo "<i class='fas fa-inbox'></i>";
                            echo "<h4>Belum ada data</h4>";
                            echo "<p>Silakan tambah data dosen terlebih dahulu</p>";
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
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Dosen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-id-badge"></i> NIDN</label>
                            <input type="text" name="nidn" class="modern-input" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-user"></i> Nama Dosen</label>
                            <input type="text" name="nama" class="modern-input" required>
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
    
    <div class="modal fade modal-modern" id="modalEditDosen">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Data Dosen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="nidn" id="edit_nidn">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-user"></i> Nama Dosen</label>
                            <input type="text" name="nama" id="edit_nama" class="modern-input" required>
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
            const tableRows = document.querySelectorAll('#dosenTable tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });
        
        document.querySelectorAll('.btn-edit-dosen').forEach(button => {
            button.addEventListener('click', function() {
                const nidn = this.getAttribute('data-nidn');
                const nama = this.getAttribute('data-nama');
                
                document.getElementById('edit_nidn').value = nidn;
                document.getElementById('edit_nama').value = nama;
                
                const modal = new bootstrap.Modal(document.getElementById('modalEditDosen'));
                modal.show();
            });
        });
    </script>
</body>
</html>