<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("location:index.php");
    exit;
}

include 'koneksi.php';

$stmt_mhs = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM mahasiswa");
$total_mhs = mysqli_fetch_assoc($stmt_mhs)['total'];

$stmt_dosen = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM dosen");
$total_dosen = mysqli_fetch_assoc($stmt_dosen)['total'];

$stmt_matkul = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM matkul");
$total_matkul = mysqli_fetch_assoc($stmt_matkul)['total'];

$stmt_nilai = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM nilai");
$total_nilai = mysqli_fetch_assoc($stmt_nilai)['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | SIA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .dashboard-container {
            min-height: 100vh;
            background: var(--bg-primary);
        }
        
        .welcome-section {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 2rem 2rem;
            box-shadow: var(--shadow-lg);
        }
        
        .welcome-section h2 {
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .welcome-section p {
            font-size: 1.1rem;
            opacity: 0.95;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card-custom {
            padding: 2rem;
            border-radius: var(--radius-lg);
            color: white;
            position: relative;
            overflow: hidden;
            transition: var(--transition);
            box-shadow: var(--shadow-lg);
        }
        
        .stat-card-custom::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            transition: var(--transition);
        }
        
        .stat-card-custom:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
        }
        
        .stat-card-custom:hover::before {
            top: -60%;
            right: -60%;
        }
        
        .stat-card-custom .icon {
            font-size: 3rem;
            opacity: 0.3;
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
        }
        
        .stat-card-custom h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }
        
        .stat-card-custom p {
            font-size: 1rem;
            opacity: 0.95;
            font-weight: 500;
            position: relative;
            z-index: 1;
        }
        
        .stat-card-1 { background: linear-gradient(135deg, #6366f1, #4f46e5); }
        .stat-card-2 { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .stat-card-3 { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-card-4 { background: linear-gradient(135deg, #f59e0b, #d97706); }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .action-btn {
            padding: 1.5rem;
            border-radius: var(--radius-md);
            background: white;
            border: 2px solid var(--border-color);
            text-decoration: none;
            color: var(--text-primary);
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: var(--shadow-sm);
        }
        
        .action-btn:hover {
            border-color: var(--primary);
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            color: var(--primary);
        }
        
        .action-btn i {
            font-size: 1.5rem;
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
                    <a href="mahasiswa.php" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-user-graduate"></i> Mahasiswa
                    </a>
                    <a href="dosen.php" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-chalkboard-teacher"></i> Dosen
                    </a>
                    <a href="matkul.php" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-book"></i> Mata Kuliah
                    </a>
                    <a href="nilai.php" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-chart-line"></i> Nilai
                    </a>
                    <a href="logout.php" class="btn btn-sm btn-danger">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="welcome-section">
            <div class="container">
                <h2 class="fade-in"><i class="fas fa-hand-wave"></i> Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?>😹</h2>
                <p class="slide-in">Kelola data akademik dengan mudah dan cepat</p>
            </div>
        </div>
        
        <div class="container">
            <div class="stats-grid fade-in">
                <div class="stat-card-custom stat-card-1">
                    <i class="fas fa-user-graduate icon"></i>
                    <h3><?= $total_mhs ?></h3>
                    <p>Total Mahasiswa</p>
                </div>
                
                <div class="stat-card-custom stat-card-2">
                    <i class="fas fa-chalkboard-teacher icon"></i>
                    <h3><?= $total_dosen ?></h3>
                    <p>Total Dosen</p>
                </div>
                
                <div class="stat-card-custom stat-card-3">
                    <i class="fas fa-book icon"></i>
                    <h3><?= $total_matkul ?></h3>
                    <p>Total Mata Kuliah</p>
                </div>
                
                <div class="stat-card-custom stat-card-4">
                    <i class="fas fa-chart-line icon"></i>
                    <h3><?= $total_nilai ?></h3>
                    <p>Total Nilai</p>
                </div>
            </div>
            
            <div class="modern-card slide-in">
                <h4 class="mb-4"><i class="fas fa-bolt"></i> Quick Actions</h4>
                <div class="quick-actions">
                    <a href="mahasiswa.php" class="action-btn">
                        <i class="fas fa-user-plus"></i>
                        <span>Tambah Mahasiswa</span>
                    </a>
                    <a href="dosen.php" class="action-btn">
                        <i class="fas fa-user-tie"></i>
                        <span>Tambah Dosen</span>
                    </a>
                    <a href="matkul.php" class="action-btn">
                        <i class="fas fa-book-medical"></i>
                        <span>Tambah Mata Kuliah</span>
                    </a>
                    <a href="nilai.php" class="action-btn">
                        <i class="fas fa-edit"></i>
                        <span>Input Nilai</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>