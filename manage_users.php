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
        'title' => 'Manage Users',
        'search' => 'Search users...',
        'name' => 'Name',
        'email' => 'Email',
        'role' => 'Role',
        'created' => 'Created',
        'actions' => 'Actions',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'confirm_delete' => 'Are you sure you want to delete this user?',
        'user_deleted' => 'User deleted successfully!',
        'user_delete_error' => 'Failed to delete user. Please try again.',
        'no_users' => 'No users found.',
    ],
    'ar' => [
        'title' => 'إدارة المستخدمين',
        'search' => 'ابحث عن المستخدمين...',
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'role' => 'الدور',
        'created' => 'تاريخ التسجيل',
        'actions' => 'الإجراءات',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'confirm_delete' => 'هل أنت متأكد من حذف هذا المستخدم؟',
        'user_deleted' => 'تم حذف المستخدم بنجاح!',
        'user_delete_error' => 'فشل في حذف المستخدم. حاول مرة أخرى.',
        'no_users' => 'لا يوجد مستخدمون.',
    ]
];
$current_content = $content[$lang];

// Handle user deletion
if (isset($_POST['delete_user'])) {
    $user_id = (int)$_POST['delete_user'];
    if ($user_id != $_SESSION['user_id']) { // Prevent admin from deleting themselves
        if (mysqli_query($conn, "DELETE FROM users WHERE id = $user_id")) {
            $success_message = $current_content['user_deleted'];
        } else {
            $error_message = $current_content['user_delete_error'];
        }
    }
}

// Search/filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
if ($search !== '') {
    $search_esc = mysqli_real_escape_string($conn, $search);
    $where = "WHERE full_name LIKE '%$search_esc%' OR email LIKE '%$search_esc%' OR username LIKE '%$search_esc%'";
}
$users_query = "SELECT * FROM users $where ORDER BY created_at DESC";
$users_result = mysqli_query($conn, $users_query);
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
        .admin-section {
            background: var(--white);
            border-radius: 1.5rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-200);
            padding: 2rem;
            margin-bottom: 2rem;
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
            .admin-section {
                padding: 1rem 0.5rem;
            }
        }
    </style>
</head>
<body class="<?php echo $lang === 'ar' ? 'rtl' : ''; ?>">
<?php include 'admin_nav.php'; ?>
<section class="admin-header">
    <div class="container">
        <div class="admin-header-content">
            <h1 class="admin-title" data-aos="fade-up">
                <?php echo $current_content['title']; ?>
            </h1>
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
    <div class="admin-section" data-aos="fade-up" data-aos-delay="200">
        <form method="GET" class="mb-4 d-flex gap-2 flex-wrap align-items-center">
            <input type="text" name="search" class="form-control" style="max-width:300px" placeholder="<?php echo $current_content['search']; ?>" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-outline">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $current_content['name']; ?></th>
                        <th><?php echo $current_content['email']; ?></th>
                        <th><?php echo $current_content['role']; ?></th>
                        <th><?php echo $current_content['created']; ?></th>
                        <th><?php echo $current_content['actions']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($users_result) === 0): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4"><?php echo $current_content['no_users']; ?></td></tr>
                    <?php else: $i = 1; while ($user = mysqli_fetch_assoc($users_result)): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                        <td>
                            <a href="profile.php?user=<?php echo $user['id']; ?>" class="btn btn-outline btn-sm">
                                <i class="fas fa-eye"></i> <?php echo $lang === 'en' ? 'View' : 'عرض'; ?>
                            </a>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <form method="POST" style="display:inline-block">
                                <input type="hidden" name="delete_user" value="<?php echo $user['id']; ?>">
                                <button type="submit" class="btn btn-outline btn-sm" onclick="return confirm('<?php echo $current_content['confirm_delete']; ?>');">
                                    <i class="fas fa-trash"></i> <?php echo $current_content['delete']; ?>
                                </button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
AOS.init({ duration: 1000, once: true, offset: 100 });
</script>
</body>
</html>