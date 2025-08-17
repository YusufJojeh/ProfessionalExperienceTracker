<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';

$content = [
    'en' => [
        'title' => 'Dashboard',
        'welcome' => 'Welcome back',
        'my_projects' => 'My Projects',
        'add_project' => 'Add Project',
        'view_portfolio' => 'View Portfolio',
        'edit_profile' => 'Edit Profile',
        'no_projects' => 'You have not added any projects yet.',
        'actions' => 'Actions',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'status' => 'Status',
        'published' => 'Published',
        'draft' => 'Draft',
        'created' => 'Created',
        'view' => 'View',
        'logout' => 'Logout',
        'project_deleted' => 'Project deleted successfully!',
        'project_delete_error' => 'Failed to delete project. Please try again.'
    ],
    'ar' => [
        'title' => 'لوحة التحكم',
        'welcome' => 'مرحباً بعودتك',
        'my_projects' => 'مشاريعي',
        'add_project' => 'إضافة مشروع',
        'view_portfolio' => 'عرض المحفظة',
        'edit_profile' => 'تعديل الملف الشخصي',
        'no_projects' => 'لم تقم بإضافة أي مشاريع بعد.',
        'actions' => 'الإجراءات',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'status' => 'الحالة',
        'published' => 'منشور',
        'draft' => 'مسودة',
        'created' => 'تاريخ الإضافة',
        'view' => 'عرض',
        'logout' => 'تسجيل الخروج',
        'project_deleted' => 'تم حذف المشروع بنجاح!',
        'project_delete_error' => 'فشل في حذف المشروع. حاول مرة أخرى.'
    ]
];
$current_content = $content[$lang];

// Handle success messages
$success_message = null;
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'project_updated':
            $success_message = $lang === 'en' ? 'Project updated successfully!' : 'تم تحديث المشروع بنجاح!';
            break;
    }
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle project deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $project_id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM projects WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $project_id, $user_id);
    if ($stmt->execute()) {
        $success_message = $current_content['project_deleted'];
    } else {
        $error_message = $current_content['project_delete_error'];
    }
    $stmt->close();
}

// Fetch user's projects
$stmt = $conn->prepare("SELECT * FROM projects WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$projects = $stmt->get_result();
$stmt->close();

// Analytics: get portfolio views and total project views
$portfolio_views = (int)($user['portfolio_views'] ?? 0);
$project_views = 0;
$project_views_query = $conn->query("SELECT SUM(views) as total FROM projects WHERE user_id = $user_id");
if ($row = $project_views_query->fetch_assoc()) {
    $project_views = (int)($row['total'] ?? 0);
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $current_content['title']; ?> - <?php echo $lang === 'en' ? PLATFORM_NAME : PLATFORM_NAME_AR; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            /* New Color Palette - Modern Ocean & Sunset Theme */
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #3b82f6;
            --secondary: #f97316;
            --secondary-dark: #ea580c;
            --secondary-light: #fb923c;
            --accent: #06b6d4;
            --accent-dark: #0891b2;
            --accent-light: #22d3ee;
            
            /* Neutral Colors */
            --dark: #0f172a;
            --dark-light: #1e293b;
            --light: #f8fafc;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            
            /* New Gradients */
            --gradient-primary: linear-gradient(135deg, #2563eb 0%, #3b82f6 50%, #60a5fa 100%);
            --gradient-secondary: linear-gradient(135deg, #f97316 0%, #fb923c 50%, #fdba74 100%);
            --gradient-accent: linear-gradient(135deg, #06b6d4 0%, #22d3ee 50%, #67e8f9 100%);
            --gradient-dark: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            --gradient-hero: linear-gradient(135deg, #1e40af 0%, #3b82f6 25%, #06b6d4 50%, #0891b2 75%, #0c4a6e 100%);
            --gradient-glass: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            
            /* Enhanced Shadows */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            --shadow-glow: 0 0 20px rgba(37, 99, 235, 0.3);
            --shadow-glow-secondary: 0 0 20px rgba(249, 115, 22, 0.3);
        }
        body {
            font-family: '<?php echo $lang === 'ar' ? 'Cairo' : 'Inter'; ?>', sans-serif;
            background: var(--light);
            color: var(--gray-800);
            overflow-x: hidden;
            line-height: 1.6;
        }
        .navbar {
            background: var(--white);
            box-shadow: var(--shadow-lg);
            border-bottom: 1px solid var(--gray-200);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .nav-link {
            font-weight: 500;
            color: var(--gray-700) !important;
            margin: 0 0.5rem;
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            position: relative;
        }
        .nav-link:hover {
            color: var(--primary) !important;
            background: var(--gray-100);
            transform: translateY(-2px);
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .dashboard-header {
            background: var(--gradient-primary);
            color: white;
            padding: 3rem 0 2rem 0;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        .dashboard-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 80%, rgba(139, 92, 246, 0.4) 0%, transparent 50%), radial-gradient(circle at 80% 20%, rgba(168, 85, 247, 0.4) 0%, transparent 50%);
            animation: headerFloat 20s ease-in-out infinite;
        }
        @keyframes headerFloat {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(-20px, -20px) rotate(1deg); }
            50% { transform: translate(20px, -10px) rotate(-1deg); }
            75% { transform: translate(-10px, 20px) rotate(0.5deg); }
        }
        .dashboard-header-content {
            position: relative;
            z-index: 2;
        }
        .dashboard-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .dashboard-welcome {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 400;
        }
        .dashboard-actions {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .dashboard-actions .btn {
            padding: 1rem 2rem;
            border-radius: 1rem;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: var(--shadow-lg);
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            border: none;
        }
        .btn-primary:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.4);
            color: white;
        }
        .btn-outline {
            background: transparent;
            color: var(--gray-700);
            border: 2px solid var(--gray-300);
        }
        .btn-outline:hover {
            background: var(--gray-100);
            border-color: var(--gray-400);
            color: var(--gray-800);
            transform: translateY(-2px);
        }
        .alert {
            border-radius: 1rem;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .alert-success {
            background: var(--gradient-accent);
            color: white;
        }
        .alert-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }
        .projects-section {
            background: var(--white);
            border-radius: 1.5rem;
            padding: 2rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-200);
            margin-bottom: 2rem;
        }
        .projects-table th, .projects-table td {
            vertical-align: middle;
        }
        .badge-published {
            background: var(--accent);
            color: white;
            font-weight: 600;
            border-radius: 0.5rem;
            padding: 0.5em 1em;
        }
        .badge-draft {
            background: var(--gray-400);
            color: white;
            font-weight: 600;
            border-radius: 0.5rem;
            padding: 0.5em 1em;
        }
        .badge-success {
            background: var(--accent);
            color: white;
            font-weight: 600;
            border-radius: 0.5rem;
            padding: 0.5em 1em;
        }
        .badge-warning {
            background: var(--secondary);
            color: white;
            font-weight: 600;
            border-radius: 0.5rem;
            padding: 0.5em 1em;
        }
        .badge-info {
            background: var(--primary);
            color: white;
            font-weight: 600;
            border-radius: 0.5rem;
            padding: 0.5em 1em;
        }
        .badge-secondary {
            background: var(--gray-500);
            color: white;
            font-weight: 600;
            border-radius: 0.5rem;
            padding: 0.5em 1em;
        }
        @media (max-width: 768px) {
            .dashboard-title {
                font-size: 2rem;
            }
            .projects-section {
                padding: 1rem 0.5rem;
            }
            .dashboard-actions {
                flex-direction: column;
            }
            .dashboard-actions .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body class="<?php echo $lang === 'ar' ? 'rtl' : ''; ?>">
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-rocket me-2"></i>
            <?php echo $lang === 'en' ? PLATFORM_NAME : PLATFORM_NAME_AR; ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="dashboard.php">
                        <i class="fas fa-tachometer-alt me-1"></i>
                        <?php echo $lang === 'en' ? 'Dashboard' : 'لوحة التحكم'; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_project.php">
                        <i class="fas fa-plus me-1"></i>
                        <?php echo $lang === 'en' ? 'Add Project' : 'إضافة مشروع'; ?>
                    </a>
                </li>
                                     <li class="nav-item">
                         <a class="nav-link" href="portfolio.php">
                             <i class="fas fa-briefcase me-1"></i>
                             <?php echo $lang === 'en' ? 'My Portfolio' : 'محفظتي'; ?>
                         </a>
                     </li>
                     <li class="nav-item">
                         <a class="nav-link" href="browse_users.php">
                             <i class="fas fa-users me-1"></i>
                             <?php echo $lang === 'en' ? 'Discover' : 'اكتشف'; ?>
                         </a>
                     </li>
                     <li class="nav-item">
                         <a class="nav-link" href="profile.php">
                             <i class="fas fa-user me-1"></i>
                             <?php echo $lang === 'en' ? 'Profile' : 'الملف الشخصي'; ?>
                         </a>
                     </li>
            </ul>
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                        <div class="user-avatar me-2">
                            <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                        </div>
                        <span class="d-none d-md-inline"><?php echo $_SESSION['user_name']; ?></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="profile.php">
                            <i class="fas fa-user me-2"></i>
                            <?php echo $lang === 'en' ? 'Profile' : 'الملف الشخصي'; ?>
                        </a></li>
                        <li><a class="dropdown-item" href="settings.php">
                            <i class="fas fa-cog me-2"></i>
                            <?php echo $lang === 'en' ? 'Settings' : 'الإعدادات'; ?>
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            <?php echo $lang === 'en' ? 'Logout' : 'تسجيل الخروج'; ?>
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
<section class="dashboard-header">
    <div class="container">
        <div class="dashboard-header-content">
            <h1 class="dashboard-title" data-aos="fade-up">
                <?php echo $current_content['title']; ?>
            </h1>
            <p class="dashboard-welcome" data-aos="fade-up" data-aos-delay="100">
                <?php echo $current_content['welcome']; ?>, <?php echo htmlspecialchars($user['full_name']); ?>!
            </p>
            <div class="row mt-4 mb-2" data-aos="fade-up" data-aos-delay="150">
                <div class="col-6 col-md-3 mb-2">
                    <div class="stats-card text-center p-3" style="background:var(--white);border-radius:1rem;box-shadow:var(--shadow-lg);">
                        <div class="stats-icon mb-1 text-dark"><i class="fas fa-eye"></i></div>
                        <div class="stats-number text-dark"><?php echo $portfolio_views; ?></div>
                        <div class="stats-label text-dark"><?php echo $lang === 'en' ? 'Portfolio Views' : 'مشاهدات المحفظة'; ?></div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <div class="stats-card text-center p-3" style="background:var(--white);border-radius:1rem;box-shadow:var(--shadow-lg);">
                        <div class="stats-icon mb-1 text-dark"><i class="fas fa-layer-group"></i></div>
                        <div class="stats-number text-dark"><?php echo $project_views; ?></div>
                        <div class="stats-label text-dark"><?php echo $lang === 'en' ? 'Project Views' : 'مشاهدات المشاريع'; ?></div>
                    </div>
                </div>
            </div>
            <div class="dashboard-actions" data-aos="fade-up" data-aos-delay="200">
                <a href="add_project.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> <?php echo $current_content['add_project']; ?>
                </a>
                <a href="portfolio.php" class="btn btn-outline">
                    <i class="fas fa-briefcase"></i> <?php echo $current_content['view_portfolio']; ?>
                </a>
                <a href="profile.php" class="btn btn-outline">
                    <i class="fas fa-user"></i> <?php echo $current_content['edit_profile']; ?>
                </a>
            </div>
        </div>
    </div>
</section>
<div class="container">
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success" data-aos="fade-up">
            <i class="fas fa-check-circle"></i>
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger" data-aos="fade-up">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    <div class="projects-section" data-aos="fade-up" data-aos-delay="200">
        <h2 class="mb-4"><i class="fas fa-layer-group me-2"></i> <?php echo $current_content['my_projects']; ?></h2>
        <?php if ($projects->num_rows === 0): ?>
            <div class="text-center text-muted py-5">
                <i class="fas fa-folder-open fa-3x mb-3"></i>
                <div><?php echo $current_content['no_projects']; ?></div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table projects-table align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo $lang === 'en' ? 'Title' : 'العنوان'; ?></th>
                            <th><?php echo $current_content['status']; ?></th>
                            <th><?php echo $current_content['created']; ?></th>
                            <th><?php echo $current_content['actions']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; while ($project = $projects->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($project['title']); ?></td>
                            <td>
                                <?php 
                                $status_class = '';
                                $status_text = '';
                                switch ($project['status']) {
                                    case 'completed':
                                        $status_class = 'badge-success';
                                        $status_text = $lang === 'en' ? 'Completed' : 'مكتمل';
                                        break;
                                    case 'ongoing':
                                        $status_class = 'badge-warning';
                                        $status_text = $lang === 'en' ? 'Ongoing' : 'قيد التنفيذ';
                                        break;
                                    case 'planned':
                                        $status_class = 'badge-info';
                                        $status_text = $lang === 'en' ? 'Planned' : 'مخطط';
                                        break;
                                    default:
                                        $status_class = 'badge-secondary';
                                        $status_text = $lang === 'en' ? 'Unknown' : 'غير معروف';
                                }
                                ?>
                                <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                            </td>
                            <td><?php echo date('Y-m-d', strtotime($project['created_at'])); ?></td>
                            <td>
                                <a href="project.php?id=<?php echo $project['id']; ?>" class="btn btn-outline btn-sm">
                                    <i class="fas fa-eye"></i> <?php echo $current_content['view']; ?>
                                </a>
                                                                 <a href="add_project.php?edit=1&id=<?php echo $project['id']; ?>" class="btn btn-outline btn-sm">
                                    <i class="fas fa-edit"></i> <?php echo $current_content['edit']; ?>
                                </a>
                                <a href="dashboard.php?delete=<?php echo $project['id']; ?>" class="btn btn-outline btn-sm" onclick="return confirm('<?php echo $lang === 'en' ? 'Are you sure?' : 'هل أنت متأكد؟'; ?>');">
                                    <i class="fas fa-trash"></i> <?php echo $current_content['delete']; ?>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    // Initialize AOS
    AOS.init({ duration: 1000, once: true, offset: 100 });

    // Animated counters for stats
    function animateCounters() {
        const counters = document.querySelectorAll('.stats-number');
        counters.forEach(counter => {
            const target = parseInt(counter.textContent);
            const increment = Math.max(1, Math.floor(target / 100));
            let current = 0;
            const updateCounter = () => {
                if (current < target) {
                    current += increment;
                    counter.textContent = Math.min(current, target);
                    setTimeout(updateCounter, 20);
                } else {
                    counter.textContent = target;
                }
            };
            updateCounter();
        });
    }
    // Trigger counters when stats section is visible
    const statsSection = document.querySelector('.stats-section');
    if (statsSection) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.unobserve(entry.target);
                }
            });
        });
        observer.observe(statsSection);
    }
</script>
</body>
</html> 