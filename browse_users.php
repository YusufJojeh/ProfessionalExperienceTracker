<?php
session_start();
require_once 'config/database.php';

$lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';

$content = [
    'en' => [
        'title' => 'Discover Creators',
        'subtitle' => 'Explore amazing portfolios from talented professionals',
        'search_users' => 'Search users...',
        'filter_by_category' => 'Filter by Category',
        'all_categories' => 'All Categories',
        'view_portfolio' => 'View Portfolio',
        'projects_count' => 'projects',
        'portfolio_views' => 'portfolio views',
        'no_users_found' => 'No users found matching your criteria.',
        'featured_creators' => 'Featured Creators',
        'recent_activity' => 'Recent Activity',
        'sort_by' => 'Sort by',
        'newest' => 'Newest',
        'most_projects' => 'Most Projects',
        'most_views' => 'Most Views',
        'alphabetical' => 'Alphabetical',
        'category' => 'Category',
        'specialization' => 'Specialization',
        'experience' => 'Experience',
        'location' => 'Location',
        'contact' => 'Contact',
        'follow' => 'Follow',
        'unfollow' => 'Unfollow',
        'loading' => 'Loading...',
        'error_loading' => 'Error loading users. Please try again.'
    ],
    'ar' => [
        'title' => 'اكتشف المبدعين',
        'subtitle' => 'استكشف محافظ رائعة من محترفين موهوبين',
        'search_users' => 'البحث عن المستخدمين...',
        'filter_by_category' => 'تصفية حسب الفئة',
        'all_categories' => 'جميع الفئات',
        'view_portfolio' => 'عرض المحفظة',
        'projects_count' => 'مشاريع',
        'portfolio_views' => 'مشاهدات المحفظة',
        'no_users_found' => 'لم يتم العثور على مستخدمين يطابقون معاييرك.',
        'featured_creators' => 'المبدعون المميزون',
        'recent_activity' => 'النشاط الأخير',
        'sort_by' => 'ترتيب حسب',
        'newest' => 'الأحدث',
        'most_projects' => 'الأكثر مشاريع',
        'most_views' => 'الأكثر مشاهدة',
        'alphabetical' => 'أبجدي',
        'category' => 'الفئة',
        'specialization' => 'التخصص',
        'experience' => 'الخبرة',
        'location' => 'الموقع',
        'contact' => 'تواصل',
        'follow' => 'متابعة',
        'unfollow' => 'إلغاء المتابعة',
        'loading' => 'جاري التحميل...',
        'error_loading' => 'خطأ في تحميل المستخدمين. حاول مرة أخرى.'
    ]
];

$current_content = $content[$lang];

// Get filter and search parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Get categories for filter
$categories = $conn->query("SELECT * FROM categories ORDER BY name_en");

// Build query for users with their project counts and portfolio views
$where_conditions = ["u.id != ?", "u.role = 'user'"]; // Exclude current user and admin users
$params = [isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0];
$param_types = "i";

if (!empty($search)) {
    $where_conditions[] = "(u.full_name LIKE ? OR u.username LIKE ? OR u.bio LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types .= "sss";
}

if ($category_filter > 0) {
    $where_conditions[] = "EXISTS (SELECT 1 FROM projects p WHERE p.user_id = u.id AND p.category_id = ?)";
    $params[] = $category_filter;
    $param_types .= "i";
}

$where_clause = implode(" AND ", $where_conditions);

// Build order clause
$order_clause = match($sort) {
    'most_projects' => 'project_count DESC',
    'most_views' => 'u.portfolio_views DESC',
    'alphabetical' => 'u.full_name ASC',
    default => 'u.created_at DESC'
};

$query = "SELECT u.*, 
          COUNT(p.id) as project_count,
          MAX(p.created_at) as last_project_date
          FROM users u 
          LEFT JOIN projects p ON u.id = p.user_id 
          WHERE $where_clause 
          GROUP BY u.id 
          ORDER BY $order_clause";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$users = $stmt->get_result();
$stmt->close();

// Get featured users (users with most projects or views)
$featured_query = "SELECT u.*, COUNT(p.id) as project_count 
                   FROM users u 
                   LEFT JOIN projects p ON u.id = p.user_id 
                   WHERE u.id != ? AND u.role = 'user'
                   GROUP BY u.id 
                   HAVING project_count > 0 
                   ORDER BY u.portfolio_views DESC, project_count DESC 
                   LIMIT 6";

$featured_stmt = $conn->prepare($featured_query);
$current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$featured_stmt->bind_param("i", $current_user_id);
$featured_stmt->execute();
$featured_users = $featured_stmt->get_result();
$featured_stmt->close();
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $current_content['title']; ?> - <?php echo $lang === 'en' ? PLATFORM_NAME : PLATFORM_NAME_AR; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- AOS Animation -->
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

        .rtl {
            direction: rtl;
            text-align: right;
        }

        /* Navigation */
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

        .nav-link.active {
            color: var(--primary) !important;
            background: var(--gray-100);
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

        /* Page Header */
        .page-header {
            background: var(--gradient-hero);
            color: white;
            padding: 4rem 0 3rem 0;
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(139, 92, 246, 0.4) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(168, 85, 247, 0.4) 0%, transparent 50%);
            animation: headerFloat 20s ease-in-out infinite;
        }

        @keyframes headerFloat {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(-20px, -20px) rotate(1deg); }
            50% { transform: translate(20px, -10px) rotate(-1deg); }
            75% { transform: translate(-10px, 20px) rotate(0.5deg); }
        }

        .page-header-content {
            position: relative;
            z-index: 2;
        }

        .page-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-subtitle {
            font-size: 1.25rem;
            opacity: 0.9;
            font-weight: 400;
            margin-bottom: 2rem;
        }

        /* Search and Filter Section */
        .search-filter-section {
            background: var(--white);
            border-radius: 1.5rem;
            padding: 2rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-200);
            margin-bottom: 3rem;
        }

        .search-box {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .search-box input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid var(--gray-200);
            border-radius: 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--white);
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
        }

        .filter-controls {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-select {
            padding: 0.75rem 1rem;
            border: 2px solid var(--gray-200);
            border-radius: 0.75rem;
            background: var(--white);
            color: var(--gray-700);
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* User Cards */
        .user-card {
            background: var(--white);
            border-radius: 1.5rem;
            padding: 2rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .user-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-2xl);
        }

        .user-avatar-large {
            width: 80px;
            height: 80px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-lg);
        }

        .user-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        .user-username {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .user-bio {
            color: var(--gray-600);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .user-stats {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--gray-600);
            font-size: 0.9rem;
        }

        .stat-item i {
            color: var(--primary);
        }

        .user-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-glow);
            color: white;
        }

        .btn-outline {
            background: transparent;
            color: var(--gray-700);
            border: 2px solid var(--gray-300);
            border-radius: 0.75rem;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(37, 99, 235, 0.05);
        }

        /* Featured Section */
        .featured-section {
            margin-bottom: 3rem;
        }

        .section-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 2rem;
            text-align: center;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--white);
            border-radius: 1.5rem;
            box-shadow: var(--shadow-lg);
        }

        .empty-state-icon {
            font-size: 4rem;
            color: var(--gray-400);
            margin-bottom: 1.5rem;
        }

        .empty-state-text {
            color: var(--gray-600);
            font-size: 1.1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }
            
            .search-filter-section {
                padding: 1.5rem;
            }
            
            .filter-controls {
                flex-direction: column;
                align-items: stretch;
            }
            
            .user-card {
                padding: 1.5rem;
            }
            
            .user-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body class="<?php echo $lang === 'ar' ? 'rtl' : ''; ?>">
    <!-- Navigation -->
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
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
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
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link active" href="browse_users.php">
                            <i class="fas fa-users me-1"></i>
                            <?php echo $lang === 'en' ? 'Discover' : 'اكتشف'; ?>
                        </a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center">
                    <?php if (isset($_SESSION['user_id'])): ?>
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
                    <?php else: ?>
                    <a href="login.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-1"></i>
                        <?php echo $lang === 'en' ? 'Login' : 'تسجيل الدخول'; ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="page-header-content">
                <h1 class="page-title" data-aos="fade-up">
                    <?php echo $current_content['title']; ?>
                </h1>
                <p class="page-subtitle" data-aos="fade-up" data-aos-delay="100">
                    <?php echo $current_content['subtitle']; ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container">
        <!-- Search and Filter Section -->
        <div class="search-filter-section" data-aos="fade-up" data-aos-delay="200">
            <form method="GET" id="searchForm">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="<?php echo $current_content['search_users']; ?>">
                </div>
                
                <div class="filter-controls">
                    <select name="category" class="filter-select">
                        <option value="0"><?php echo $current_content['all_categories']; ?></option>
                        <?php 
                        $categories->data_seek(0);
                        while ($category = $categories->fetch_assoc()): 
                        ?>
                        <option value="<?php echo $category['id']; ?>" 
                                <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>>
                            <?php echo $lang === 'en' ? $category['name_en'] : $category['name_ar']; ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                    
                    <select name="sort" class="filter-select">
                        <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>
                            <?php echo $current_content['newest']; ?>
                        </option>
                        <option value="most_projects" <?php echo $sort === 'most_projects' ? 'selected' : ''; ?>>
                            <?php echo $current_content['most_projects']; ?>
                        </option>
                        <option value="most_views" <?php echo $sort === 'most_views' ? 'selected' : ''; ?>>
                            <?php echo $current_content['most_views']; ?>
                        </option>
                        <option value="alphabetical" <?php echo $sort === 'alphabetical' ? 'selected' : ''; ?>>
                            <?php echo $current_content['alphabetical']; ?>
                        </option>
                    </select>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i>
                        <?php echo $lang === 'en' ? 'Apply Filters' : 'تطبيق الفلاتر'; ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- Featured Creators Section -->
        <?php if ($featured_users->num_rows > 0): ?>
        <div class="featured-section" data-aos="fade-up" data-aos-delay="300">
            <h2 class="section-title">
                <i class="fas fa-star me-2"></i>
                <?php echo $current_content['featured_creators']; ?>
            </h2>
            <div class="row g-4">
                <?php while ($user = $featured_users->fetch_assoc()): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="user-card">
                        <div class="user-avatar-large">
                            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                        </div>
                        <h3 class="user-name"><?php echo htmlspecialchars($user['full_name']); ?></h3>
                        <div class="user-username">@<?php echo htmlspecialchars($user['username']); ?></div>
                        
                        <?php if (!empty($user['bio'])): ?>
                        <p class="user-bio"><?php echo htmlspecialchars(mb_strimwidth($user['bio'], 0, 100, '...')); ?></p>
                        <?php endif; ?>
                        
                        <div class="user-stats">
                            <div class="stat-item">
                                <i class="fas fa-layer-group"></i>
                                <span><?php echo $user['project_count']; ?> <?php echo $current_content['projects_count']; ?></span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-eye"></i>
                                <span><?php echo $user['portfolio_views']; ?> <?php echo $current_content['portfolio_views']; ?></span>
                            </div>
                        </div>
                        
                        <div class="user-actions">
                            <a href="portfolio.php?user=<?php echo $user['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-briefcase"></i>
                                <?php echo $current_content['view_portfolio']; ?>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- All Users Section -->
        <div class="row g-4" data-aos="fade-up" data-aos-delay="400">
            <?php if ($users->num_rows > 0): ?>
                <?php while ($user = $users->fetch_assoc()): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="user-card">
                        <div class="user-avatar-large">
                            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                        </div>
                        <h3 class="user-name"><?php echo htmlspecialchars($user['full_name']); ?></h3>
                        <div class="user-username">@<?php echo htmlspecialchars($user['username']); ?></div>
                        
                        <?php if (!empty($user['bio'])): ?>
                        <p class="user-bio"><?php echo htmlspecialchars(mb_strimwidth($user['bio'], 0, 100, '...')); ?></p>
                        <?php endif; ?>
                        
                        <div class="user-stats">
                            <div class="stat-item">
                                <i class="fas fa-layer-group"></i>
                                <span><?php echo $user['project_count']; ?> <?php echo $current_content['projects_count']; ?></span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-eye"></i>
                                <span><?php echo $user['portfolio_views']; ?> <?php echo $current_content['portfolio_views']; ?></span>
                            </div>
                            <?php if ($user['last_project_date']): ?>
                            <div class="stat-item">
                                <i class="fas fa-clock"></i>
                                <span><?php echo date('M Y', strtotime($user['last_project_date'])); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="user-actions">
                            <a href="portfolio.php?user=<?php echo $user['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-briefcase"></i>
                                <?php echo $current_content['view_portfolio']; ?>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="empty-state-text">
                            <?php echo $current_content['no_users_found']; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({ duration: 1000, once: true, offset: 100 });

        // Auto-submit form on filter change
        document.querySelectorAll('.filter-select').forEach(select => {
            select.addEventListener('change', function() {
                document.getElementById('searchForm').submit();
            });
        });

        // Search with debounce
        let searchTimer;
        const searchInput = document.querySelector('input[name="search"]');
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                document.getElementById('searchForm').submit();
            }, 500);
        });
    </script>
</body>
</html>
