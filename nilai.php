<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("location:index.php");
    exit;
}

if (isset($_POST['tambah'])) {
    $nim = $_POST['nim'];
    $kode_matkul = $_POST['kode_matkul'];
    $nilai = $_POST['nilai'];
    
    $stmt = mysqli_prepare($koneksi, "INSERT INTO nilai (nim, kode_matkul, nilai) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssd", $nim, $kode_matkul, $nilai);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("location:nilai.php");
    exit;
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nim = $_POST['nim'];
    $kode_matkul = $_POST['kode_matkul'];
    $nilai = $_POST['nilai'];
    
    $stmt = mysqli_prepare($koneksi, "UPDATE nilai SET nim=?, kode_matkul=?, nilai=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssdi", $nim, $kode_matkul, $nilai, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("location:nilai.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Nilai | SIA</title>
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
        
        .nilai-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1rem;
            min-width: 60px;
            text-align: center;
        }
        
        .nilai-a { background: linear-gradient(135deg, #10b981, #059669); color: white; }
        .nilai-b { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; }
        .nilai-c { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
        .nilai-d { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
        .nilai-e { background: linear-gradient(135deg, #6b7280, #4b5563); color: white; }
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
            <h3 class="fade-in"><i class="fas fa-chart-line"></i> Data Nilai Mahasiswa</h3>
        </div>
    </div>
    
    <div class="container mb-5">
        <div class="modern-card fade-in">
            <div class="action-bar">
                <div class="search-box" style="flex: 1; max-width: 400px;">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" class="modern-input" placeholder="Cari nilai...">
                </div>
                <button class="btn-modern btn-success-modern" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fas fa-plus"></i> Tambah Nilai
                </button>
            </div>

            <div class="table-wrapper">
                <table class="modern-table" id="nilaiTable">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> No</th>
                            <th><i class="fas fa-id-card"></i> NIM</th>
                            <th><i class="fas fa-user"></i> Nama Mahasiswa</th>
                            <th><i class="fas fa-book"></i> Mata Kuliah</th>
                            <th style="text-align: center;"><i class="fas fa-star"></i> Nilai</th>
                            <th style="text-align: center;"><i class="fas fa-award"></i> Grade</th>
                            <th style="text-align: center;"><i class="fas fa-cog"></i> Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT n.*, m.nama as nama_mahasiswa, mk.nama_matkul 
                                  FROM nilai n 
                                  JOIN mahasiswa m ON n.nim = m.nim 
                                  JOIN matkul mk ON n.kode_matkul = mk.kode_matkul 
                                  ORDER BY n.id DESC";
                        $data = mysqli_query($koneksi, $query);
                        $no = 1;
                        $count = 0;
                        
                        while ($row = mysqli_fetch_assoc($data)) {
                            $count++;
                            $nilai = $row['nilai'];
                            
                            if ($nilai >= 80) {
                                $grade = 'A';
                                $badge_class = 'nilai-a';
                            } elseif ($nilai >= 70) {
                                $grade = 'B';
                                $badge_class = 'nilai-b';
                            } elseif ($nilai >= 60) {
                                $grade = 'C';
                                $badge_class = 'nilai-c';
                            } elseif ($nilai >= 50) {
                                $grade = 'D';
                                $badge_class = 'nilai-d';
                            } else {
                                $grade = 'E';
                                $badge_class = 'nilai-e';
                            }
                            
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['nim']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_mahasiswa']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_matkul']) . "</td>";
                            echo "<td style='text-align: center;'><strong>" . htmlspecialchars($nilai) . "</strong></td>";
                            echo "<td style='text-align: center;'><span class='nilai-badge " . $badge_class . "'>" . $grade . "</span></td>";
                            echo "<td style='text-align: center;'>";
                            echo "<div style='display: flex; gap: 0.5rem; justify-content: center; align-items: center;'>";
                            echo "<button class='btn-modern btn-warning-modern btn-sm btn-edit-nilai' ";
                            echo "data-id='" . $row['id'] . "' ";
                            echo "data-nim='" . htmlspecialchars($row['nim']) . "' ";
                            echo "data-kode-matkul='" . htmlspecialchars($row['kode_matkul']) . "' ";
                            echo "data-nilai='" . htmlspecialchars($row['nilai']) . "' ";
                            echo "title='Edit' style='padding: 0.5rem 0.75rem;'>";
                            echo "<i class='fas fa-edit'></i>";
                            echo "</button>";
                            echo "<a href='hapus_nilai.php?id=" . $row['id'] . "' onclick=\"return confirm('Yakin hapus data nilai?')\" class='btn-modern btn-danger-modern btn-sm' title='Hapus' style='padding: 0.5rem 0.75rem;'>";
                            echo "<i class='fas fa-trash'></i>";
                            echo "</a>";
                            echo "</div>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        
                        if ($count == 0) {
                            echo "<tr><td colspan='7'>";
                            echo "<div class='empty-state'>";
                            echo "<i class='fas fa-inbox'></i>";
                            echo "<h4>Belum ada data</h4>";
                            echo "<p>Silakan tambah data nilai terlebih dahulu</p>";
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
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Nilai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-user-graduate"></i> Mahasiswa</label>
                            <select name="nim" class="modern-input" required>
                                <option value="">Pilih Mahasiswa</option>
                                <?php
                                $mhs = mysqli_query($koneksi, "SELECT * FROM mahasiswa ORDER BY nama ASC");
                                while ($m = mysqli_fetch_assoc($mhs)) {
                                    echo "<option value='" . htmlspecialchars($m['nim']) . "'>" . htmlspecialchars($m['nim']) . " - " . htmlspecialchars($m['nama']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-book"></i> Mata Kuliah</label>
                            <select name="kode_matkul" class="modern-input" required>
                                <option value="">Pilih Mata Kuliah</option>
                                <?php
                                $mk = mysqli_query($koneksi, "SELECT * FROM matkul ORDER BY nama_matkul ASC");
                                while ($matkul = mysqli_fetch_assoc($mk)) {
                                    echo "<option value='" . htmlspecialchars($matkul['kode_matkul']) . "'>" . htmlspecialchars($matkul['nama_matkul']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-star"></i> Nilai (0-100)</label>
                            <input type="number" name="nilai" class="modern-input" required min="0" max="100" step="0.01" placeholder="Masukkan nilai">
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
    
    <div class="modal fade modal-modern" id="modalEditNilai">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Data Nilai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-user-graduate"></i> Mahasiswa</label>
                            <select name="nim" id="edit_nim" class="modern-input" required>
                                <option value="">Pilih Mahasiswa</option>
                                <?php
                                $mhs = mysqli_query($koneksi, "SELECT * FROM mahasiswa ORDER BY nama ASC");
                                while ($m = mysqli_fetch_assoc($mhs)) {
                                    echo "<option value='" . htmlspecialchars($m['nim']) . "'>" . htmlspecialchars($m['nim']) . " - " . htmlspecialchars($m['nama']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-book"></i> Mata Kuliah</label>
                            <select name="kode_matkul" id="edit_kode_matkul" class="modern-input" required>
                                <option value="">Pilih Mata Kuliah</option>
                                <?php
                                $mk = mysqli_query($koneksi, "SELECT * FROM matkul ORDER BY nama_matkul ASC");
                                while ($matkul = mysqli_fetch_assoc($mk)) {
                                    echo "<option value='" . htmlspecialchars($matkul['kode_matkul']) . "'>" . htmlspecialchars($matkul['nama_matkul']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-star"></i> Nilai (0-100)</label>
                            <input type="number" name="nilai" id="edit_nilai" class="modern-input" required min="0" max="100" step="0.01">
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
            const tableRows = document.querySelectorAll('#nilaiTable tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });
        
        document.querySelectorAll('.btn-edit-nilai').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nim = this.getAttribute('data-nim');
                const kodeMatkul = this.getAttribute('data-kode-matkul');
                const nilai = this.getAttribute('data-nilai');
                
                document.getElementById('edit_id').value = id;
                document.getElementById('edit_nim').value = nim;
                document.getElementById('edit_kode_matkul').value = kodeMatkul;
                document.getElementById('edit_nilai').value = nilai;
                
                const modal = new bootstrap.Modal(document.getElementById('modalEditNilai'));
                modal.show();
            });
        });
    </script>
</body>
</html>
