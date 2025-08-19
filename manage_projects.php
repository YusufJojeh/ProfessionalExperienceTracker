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
        'title' => 'Manage Projects',
        'search' => 'Search projects...',
        'title_col' => 'Title',
        'user' => 'User',
        'category' => 'Category',
        'status' => 'Status',
        'created' => 'Created',
        'views' => 'Views',
        'actions' => 'Actions',

        'delete' => 'Delete',
        'view' => 'View',
        'confirm_delete' => 'Are you sure you want to delete this project?',
        'project_deleted' => 'Project deleted successfully!',
        'project_delete_error' => 'Failed to delete project. Please try again.',
        'no_projects' => 'No projects found.',
        'filter_all' => 'All Projects',
        'filter_completed' => 'Completed',
        'filter_ongoing' => 'Ongoing',
        'filter_planned' => 'Planned'
    ],
    'ar' => [
        'title' => 'إدارة المشاريع',
        'search' => 'ابحث في المشاريع...',
        'title_col' => 'العنوان',
        'user' => 'المستخدم',
        'category' => 'الفئة',
        'status' => 'الحالة',
        'created' => 'تاريخ الإضافة',
        'views' => 'المشاهدات',
        'actions' => 'الإجراءات',

        'delete' => 'حذف',
        'view' => 'عرض',
        'confirm_delete' => 'هل أنت متأكد من حذف هذا المشروع؟',
        'project_deleted' => 'تم حذف المشروع بنجاح!',
        'project_delete_error' => 'فشل في حذف المشروع. حاول مرة أخرى.',
        'no_projects' => 'لا توجد مشاريع.',
        'filter_all' => 'جميع المشاريع',
        'filter_completed' => 'مكتمل',
        'filter_ongoing' => 'قيد التنفيذ',
        'filter_planned' => 'مخطط'
    ]
];
$current_content = $content[$lang];

// Handle project deletion
if (isset($_POST['delete_project'])) {
    $project_id = (int)$_POST['delete_project'];
    if (mysqli_query($conn, "DELETE FROM projects WHERE id = $project_id")) {
        $success_message = $current_content['project_deleted'];
    } else {
        $error_message = $current_content['project_delete_error'];
    }
}

// Search/filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$where_conditions = [];
if ($search !== '') {
    $search_esc = mysqli_real_escape_string($conn, $search);
    $where_conditions[] = "(p.title LIKE '%$search_esc%' OR p.description LIKE '%$search_esc%' OR u.full_name LIKE '%$search_esc%')";
}

if ($status_filter !== '' && $status_filter !== 'all') {
    $status_esc = mysqli_real_escape_string($conn, $status_filter);
    $where_conditions[] = "p.status = '$status_esc'";
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

$projects_query = "SELECT p.*, u.full_name, c.name_en, c.name_ar 
                  FROM projects p 
                  JOIN users u ON p.user_id = u.id 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  $where_clause 
                  ORDER BY p.created_at DESC";
$projects_result = mysqli_query($conn, $projects_query);
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
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #3b82f6;
            --secondary: #f97316;
            --secondary-dark: #ea580c;
            --secondary-light: #fb923c;
            --accent: #06b6d4;
            --accent-dark: #0891b2;
            --accent-light: #22d3ee;
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
            --gradient-primary: linear-gradient(135deg, #2563eb 0%, #3b82f6 50%, #60a5fa 100%);
            --gradient-secondary: linear-gradient(135deg, #f97316 0%, #fb923c 50%, #fdba74 100%);
            --gradient-accent: linear-gradient(135deg, #06b6d4 0%, #22d3ee 50%, #67e8f9 100%);
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            --shadow-glow: 0 0 20px rgba(37, 99, 235, 0.3);
        }
        body {
            font-family: '<?php echo $lang === 'ar' ? 'Cairo' : 'Inter'; ?>', sans-serif;
            background: var(--light);
            color: var(--gray-800);
            overflow-x: hidden;
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
        .filter-section {
            background: var(--gray-50);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
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
    
    <div class="filter-section" data-aos="fade-up" data-aos-delay="100">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label"><?php echo $current_content['search']; ?></label>
                <input type="text" name="search" class="form-control" placeholder="<?php echo $current_content['search']; ?>" value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label"><?php echo $current_content['status']; ?></label>
                <select name="status" class="form-select">
                    <option value="all"><?php echo $current_content['filter_all']; ?></option>
                    <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>><?php echo $current_content['filter_completed']; ?></option>
                    <option value="ongoing" <?php echo $status_filter === 'ongoing' ? 'selected' : ''; ?>><?php echo $current_content['filter_ongoing']; ?></option>
                    <option value="planned" <?php echo $status_filter === 'planned' ? 'selected' : ''; ?>><?php echo $current_content['filter_planned']; ?></option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
    
    <div class="admin-section" data-aos="fade-up" data-aos-delay="200">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $current_content['title_col']; ?></th>
                        <th><?php echo $current_content['user']; ?></th>
                        <th><?php echo $current_content['category']; ?></th>
                        <th><?php echo $current_content['status']; ?></th>
                        <th><?php echo $current_content['views']; ?></th>
                        <th><?php echo $current_content['created']; ?></th>
                        <th><?php echo $current_content['actions']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($projects_result) === 0): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4"><?php echo $current_content['no_projects']; ?></td></tr>
                    <?php else: $i = 1; while ($project = mysqli_fetch_assoc($projects_result)): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td>
                            <div class="fw-bold"><?php echo htmlspecialchars($project['title']); ?></div>
                            <small class="text-muted"><?php echo substr(htmlspecialchars($project['description']), 0, 100); ?>...</small>
                        </td>
                        <td><?php echo htmlspecialchars($project['full_name']); ?></td>
                        <td><?php echo $lang === 'en' ? $project['name_en'] : $project['name_ar']; ?></td>
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
                        <td><?php echo $project['views']; ?></td>
                        <td><?php echo date('Y-m-d', strtotime($project['created_at'])); ?></td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="project.php?id=<?php echo $project['id']; ?>" class="btn btn-outline btn-sm" title="<?php echo $current_content['view']; ?>">
                                    <i class="fas fa-eye"></i> <?php echo $lang === 'en' ? 'View' : 'عرض'; ?>
                                </a>
                                <form method="POST" style="display:inline-block">
                                    <input type="hidden" name="delete_project" value="<?php echo $project['id']; ?>">
                                    <button type="submit" class="btn btn-outline btn-sm" onclick="return confirm('<?php echo $current_content['confirm_delete']; ?>');" title="<?php echo $current_content['delete']; ?>">
                                        <i class="fas fa-trash"></i> <?php echo $current_content['delete']; ?>
                                    </button>
                                </form>
                            </div>
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
