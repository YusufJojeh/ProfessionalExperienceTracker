<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin')) {
    header('Location: login.php');
    exit();
}

$lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';

$content = [
    'en' => [
        'title' => 'Admin Dashboard',
        'welcome' => 'Welcome, Administrator',
        'stats' => [
            'total_users' => 'Total Users',
            'total_projects' => 'Total Projects',
            'total_comments' => 'Total Comments',
            'total_categories' => 'Categories'
        ],
        'users' => 'Users',
        'projects' => 'Projects',
        'categories' => 'Categories',
        'recent_users' => 'Recent Users',
        'recent_projects' => 'Recent Projects',
        'view_all' => 'View All',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'confirm_delete' => 'Are you sure you want to delete this item?',
        'item_deleted' => 'Item deleted successfully!',
        'item_updated' => 'Item updated successfully!'
    ],
    'ar' => [
        'title' => 'لوحة تحكم المدير',
        'welcome' => 'مرحباً، المدير',
        'stats' => [
            'total_users' => 'إجمالي المستخدمين',
            'total_projects' => 'إجمالي المشاريع',
            'total_comments' => 'إجمالي التعليقات',
            'total_categories' => 'الفئات'
        ],
        'users' => 'المستخدمين',
        'projects' => 'المشاريع',
        'categories' => 'الفئات',
        'recent_users' => 'المستخدمين الجدد',
        'recent_projects' => 'المشاريع الحديثة',
        'view_all' => 'عرض الكل',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'confirm_delete' => 'هل أنت متأكد من حذف هذا العنصر؟',
        'item_deleted' => 'تم حذف العنصر بنجاح!',
        'item_updated' => 'تم تحديث العنصر بنجاح!'
    ]
];
$current_content = $content[$lang];

// Get statistics
$total_users = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0];
$total_projects = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) FROM projects"))[0];
$total_comments = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) FROM comments"))[0];
$total_categories = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) FROM categories"))[0];

// Get recent users
$recent_users_query = "SELECT * FROM users ORDER BY created_at DESC LIMIT 5";
$recent_users_result = mysqli_query($conn, $recent_users_query);

// Get recent projects
$recent_projects_query = "SELECT p.*, u.full_name, c.name_en, c.name_ar 
                         FROM projects p 
                         JOIN users u ON p.user_id = u.id 
                         LEFT JOIN categories c ON p.category_id = c.id 
                         ORDER BY p.created_at DESC LIMIT 5";
$recent_projects_result = mysqli_query($conn, $recent_projects_query);

// Handle deletions
if (isset($_POST['delete_user'])) {
    $user_id = (int)$_POST['delete_user'];
    if ($user_id != $_SESSION['user_id']) { // Prevent admin from deleting themselves
        mysqli_query($conn, "DELETE FROM users WHERE id = $user_id");
        $success_message = $current_content['item_deleted'];
    }
}

if (isset($_POST['delete_project'])) {
    $project_id = (int)$_POST['delete_project'];
    mysqli_query($conn, "DELETE FROM projects WHERE id = $project_id");
    $success_message = $current_content['item_deleted'];
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
        .admin-header {
            background: var(--gradient-primary);
            color: white;
            padding: 3rem 0 2rem 0;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        .admin-header::before {
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
        .admin-header-content {
            position: relative;
            z-index: 2;
        }
        .admin-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .admin-welcome {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 400;
        }
        .stats-section {
            margin-bottom: 2rem;
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }
        .stats-card {
            background: var(--white);
            border-radius: 1.5rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-200);
            padding: 2rem 1.5rem;
            flex: 1 1 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 180px;
            margin-bottom: 1rem;
        }
        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--primary);
        }
        .stats-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--gray-900);
        }
        .stats-label {
            color: var(--gray-600);
            font-size: 1rem;
            font-weight: 500;
        }
        .admin-section {
            background: var(--white);
            border-radius: 1.5rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-200);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .admin-section h3 {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 1.5rem;
        }
        .table-responsive {
            margin-bottom: 1rem;
        }
        .btn-outline {
            background: transparent;
            color: var(--gray-700);
            border: 2px solid var(--gray-300);
            border-radius: 0.75rem;
            font-weight: 600;
            padding: 0.5rem 1.2rem;
            transition: all 0.3s;
        }
        .btn-outline:hover {
            background: var(--gray-100);
            border-color: var(--gray-400);
            color: var(--gray-800);
            transform: translateY(-2px);
        }
        @media (max-width: 768px) {
            .admin-title {
                font-size: 2rem;
            }
            .admin-header {
                padding: 2rem 0 1rem 0;
            }
            .stats-section {
                flex-direction: column;
                gap: 1rem;
            }
            .admin-section {
                padding: 1rem 0.5rem;
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
                    <a class="nav-link active" href="admin.php">
                        <i class="fas fa-tachometer-alt me-1"></i>
                        <?php echo $lang === 'en' ? 'Admin Dashboard' : 'لوحة تحكم المدير'; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">
                        <i class="fas fa-user me-1"></i>
                        <?php echo $lang === 'en' ? 'User Dashboard' : 'لوحة تحكم المستخدم'; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt me-1"></i>
                        <?php echo $lang === 'en' ? 'Logout' : 'تسجيل الخروج'; ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<section class="admin-header">
    <div class="container">
        <div class="admin-header-content">
            <h1 class="admin-title" data-aos="fade-up">
                <?php echo $current_content['title']; ?>
            </h1>
            <p class="admin-welcome" data-aos="fade-up" data-aos-delay="100">
                <?php echo $current_content['welcome']; ?>
            </p>
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
    <div class="stats-section" data-aos="fade-up" data-aos-delay="200">
        <div class="stats-card">
            <div class="stats-icon"><i class="fas fa-users"></i></div>
            <div class="stats-number"><?php echo $total_users; ?></div>
            <div class="stats-label"><?php echo $current_content['stats']['total_users']; ?></div>
        </div>
        <div class="stats-card">
            <div class="stats-icon"><i class="fas fa-project-diagram"></i></div>
            <div class="stats-number"><?php echo $total_projects; ?></div>
            <div class="stats-label"><?php echo $current_content['stats']['total_projects']; ?></div>
        </div>
        <div class="stats-card">
            <div class="stats-icon"><i class="fas fa-comments"></i></div>
            <div class="stats-number"><?php echo $total_comments; ?></div>
            <div class="stats-label"><?php echo $current_content['stats']['total_comments']; ?></div>
        </div>
        <div class="stats-card">
            <div class="stats-icon"><i class="fas fa-tags"></i></div>
            <div class="stats-number"><?php echo $total_categories; ?></div>
            <div class="stats-label"><?php echo $current_content['stats']['total_categories']; ?></div>
        </div>
    </div>
    <div class="admin-section" data-aos="fade-up" data-aos-delay="300">
        <h3><i class="fas fa-users me-2"></i> <?php echo $current_content['recent_users']; ?></h3>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $lang === 'en' ? 'Name' : 'الاسم'; ?></th>
                        <th><?php echo $lang === 'en' ? 'Email' : 'البريد الإلكتروني'; ?></th>
                        <th><?php echo $lang === 'en' ? 'Role' : 'الدور'; ?></th>
                        <th><?php echo $lang === 'en' ? 'Created' : 'تاريخ التسجيل'; ?></th>
                        <th><?php echo $lang === 'en' ? 'Actions' : 'الإجراءات'; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; while ($user = mysqli_fetch_assoc($recent_users_result)): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                        <td>
                            <form method="POST" style="display:inline-block">
                                <input type="hidden" name="delete_user" value="<?php echo $user['id']; ?>">
                                <button type="submit" class="btn btn-outline btn-sm" onclick="return confirm('<?php echo $current_content['confirm_delete']; ?>');">
                                    <i class="fas fa-trash"></i> <?php echo $current_content['delete']; ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="admin-section" data-aos="fade-up" data-aos-delay="400">
        <h3><i class="fas fa-layer-group me-2"></i> <?php echo $current_content['recent_projects']; ?></h3>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $lang === 'en' ? 'Title' : 'العنوان'; ?></th>
                        <th><?php echo $lang === 'en' ? 'User' : 'المستخدم'; ?></th>
                        <th><?php echo $lang === 'en' ? 'Category' : 'الفئة'; ?></th>
                        <th><?php echo $lang === 'en' ? 'Created' : 'تاريخ الإضافة'; ?></th>
                        <th><?php echo $lang === 'en' ? 'Actions' : 'الإجراءات'; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; while ($project = mysqli_fetch_assoc($recent_projects_result)): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($project['title']); ?></td>
                        <td><?php echo htmlspecialchars($project['full_name']); ?></td>
                        <td><?php echo $lang === 'en' ? $project['name_en'] : $project['name_ar']; ?></td>
                        <td><?php echo date('Y-m-d', strtotime($project['created_at'])); ?></td>
                        <td>
                            <form method="POST" style="display:inline-block">
                                <input type="hidden" name="delete_project" value="<?php echo $project['id']; ?>">
                                <button type="submit" class="btn btn-outline btn-sm" onclick="return confirm('<?php echo $current_content['confirm_delete']; ?>');">
                                    <i class="fas fa-trash"></i> <?php echo $current_content['delete']; ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
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