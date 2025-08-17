<?php
session_start();
require_once 'config/database.php';

$lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';

$content = [
    'en' => [
        'title' => 'Portfolio',
        'subtitle' => 'Showcasing My Professional Projects',
        'all' => 'All',
        'category' => 'Category',
        'no_projects' => 'No projects to display yet.',
        'view_project' => 'View Project',
        'filter' => 'Filter by Category',
        'share_portfolio' => 'Share Portfolio',
        'contact' => 'Contact',
        'live_demo' => 'Live Demo',
        'github' => 'GitHub',
        'technologies' => 'Technologies',
        'client' => 'Client',
        'status' => 'Status',
        'completed' => 'Completed',
        'ongoing' => 'Ongoing',
        'planned' => 'Planned',
        'portfolio_views' => 'Portfolio Views',
        'total_projects' => 'Total Projects',
        'years_experience' => 'Years Experience',
        'happy_clients' => 'Happy Clients',
        'add_sample_projects' => 'Add Sample Projects',
        'sample_projects_added' => 'Sample projects added successfully!',
        'contact_portfolio_owner' => 'Contact Portfolio Owner',
        'your_email' => 'Your Email',
        'message' => 'Message',
        'send' => 'Send',
        'close' => 'Close',
        'message_sent' => 'Message sent successfully!',
        'error_sending' => 'Error sending message. Please try again.',
        'portfolio_shared' => 'Portfolio link copied to clipboard!',
        'loading' => 'Loading...',
        'search_projects' => 'Search projects...',
        'sort_by' => 'Sort by',
        'newest' => 'Newest',
        'oldest' => 'Oldest',
        'name_az' => 'Name A-Z',
        'name_za' => 'Name Z-A'
    ],
    'ar' => [
        'title' => 'المحفظة',
        'subtitle' => 'عرض مشاريعي المهنية',
        'all' => 'الكل',
        'category' => 'الفئة',
        'no_projects' => 'لا توجد مشاريع للعرض بعد.',
        'view_project' => 'عرض المشروع',
        'filter' => 'تصفية حسب الفئة',
        'share_portfolio' => 'مشاركة المحفظة',
        'contact' => 'تواصل',
        'live_demo' => 'عرض مباشر',
        'github' => 'جيثب',
        'technologies' => 'التقنيات',
        'client' => 'العميل',
        'status' => 'الحالة',
        'completed' => 'مكتمل',
        'ongoing' => 'قيد التنفيذ',
        'planned' => 'مخطط',
        'portfolio_views' => 'مشاهدات المحفظة',
        'total_projects' => 'إجمالي المشاريع',
        'years_experience' => 'سنوات الخبرة',
        'happy_clients' => 'عملاء سعداء',
        'add_sample_projects' => 'إضافة مشاريع تجريبية',
        'sample_projects_added' => 'تم إضافة المشاريع التجريبية بنجاح!',
        'contact_portfolio_owner' => 'مراسلة صاحب المحفظة',
        'your_email' => 'بريدك الإلكتروني',
        'message' => 'رسالتك',
        'send' => 'إرسال',
        'close' => 'إغلاق',
        'message_sent' => 'تم إرسال الرسالة بنجاح!',
        'error_sending' => 'خطأ في إرسال الرسالة. حاول مرة أخرى.',
        'portfolio_shared' => 'تم نسخ رابط المحفظة إلى الحافظة!',
        'loading' => 'جاري التحميل...',
        'search_projects' => 'البحث في المشاريع...',
        'sort_by' => 'ترتيب حسب',
        'newest' => 'الأحدث',
        'oldest' => 'الأقدم',
        'name_az' => 'الاسم أ-ي',
        'name_za' => 'الاسم ي-أ'
    ]
];
$current_content = $content[$lang];

// Get categories
$categories = $conn->query("SELECT * FROM categories ORDER BY name_en");

// Get filter and search parameters
$filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Get user (if portfolio is for a specific user, otherwise use session user)
$user_id = isset($_GET['user']) ? (int)$_GET['user'] : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0);
$user_template = 'default';
$user_info = null;

if ($user_id) {
    $user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    if ($row = $user_result->fetch_assoc()) {
        $user_info = $row;
        $user_template = $row['template'] ?: 'default';
    }
    $user_stmt->close();
} elseif (isset($_SESSION['user_id'])) {
    $user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $user_stmt->bind_param("i", $_SESSION['user_id']);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    if ($row = $user_result->fetch_assoc()) {
        $user_info = $row;
        $user_template = $row['template'] ?: 'default';
    }
    $user_stmt->close();
}

// If no user found, redirect to login
if (!$user_info) {
    header('Location: login.php');
    exit();
}

// Increment portfolio views
if ($user_id) {
    $conn->query("UPDATE users SET portfolio_views = portfolio_views + 1 WHERE id = $user_id");
} elseif (isset($_SESSION['user_id'])) {
    $conn->query("UPDATE users SET portfolio_views = portfolio_views + 1 WHERE id = " . (int)$_SESSION['user_id']);
}

// Handle sample projects addition
if (isset($_POST['add_sample_projects']) && isset($_SESSION['user_id'])) {
    $sample_projects = [
        [
            'title' => 'E-Commerce Platform',
            'description' => 'A modern e-commerce platform built with React and Node.js, featuring real-time inventory management, secure payment processing, and responsive design.',
            'category_id' => 1,
            'client' => 'TechCorp Inc.',
            'technologies' => 'React, Node.js, MongoDB, Stripe',
            'status' => 'completed',
            'project_link' => 'https://demo-ecommerce.com',
            'github_link' => 'https://github.com/demo/ecommerce',
            'start_date' => '2023-01-15',
            'end_date' => '2023-06-30',
            'budget' => 15000.00
        ],
        [
            'title' => 'Mobile Banking App',
            'description' => 'Cross-platform mobile banking application with biometric authentication, real-time transactions, and advanced security features.',
            'category_id' => 2,
            'client' => 'BankSecure',
            'technologies' => 'React Native, Firebase, Biometric API',
            'status' => 'completed',
            'project_link' => 'https://demo-banking.com',
            'github_link' => 'https://github.com/demo/banking',
            'start_date' => '2023-07-01',
            'end_date' => '2023-12-15',
            'budget' => 25000.00
        ],
        [
            'title' => 'Brand Identity Design',
            'description' => 'Complete brand identity package including logo design, color palette, typography, and marketing materials for a startup company.',
            'category_id' => 3,
            'client' => 'StartupXYZ',
            'technologies' => 'Adobe Illustrator, Photoshop, InDesign',
            'status' => 'completed',
            'project_link' => '',
            'github_link' => '',
            'start_date' => '2023-03-01',
            'end_date' => '2023-04-30',
            'budget' => 5000.00
        ]
    ];
    
    $stmt = $conn->prepare("INSERT INTO projects (user_id, category_id, title, description, client, technologies, status, project_link, github_link, start_date, end_date, budget) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($sample_projects as $project) {
        $stmt->bind_param("iisssssssssd", 
            $_SESSION['user_id'],
            $project['category_id'],
            $project['title'],
            $project['description'],
            $project['client'],
            $project['technologies'],
            $project['status'],
            $project['project_link'],
            $project['github_link'],
            $project['start_date'],
            $project['end_date'],
            $project['budget']
        );
        $stmt->execute();
    }
    $stmt->close();
    
    $success_message = $current_content['sample_projects_added'];
}

// Build query for projects
$where_conditions = ["p.user_id = ?"];
$params = [$user_id];
$param_types = "i";

if ($filter > 0) {
    $where_conditions[] = "p.category_id = ?";
    $params[] = $filter;
    $param_types .= "i";
}

if (!empty($search)) {
    $where_conditions[] = "(p.title LIKE ? OR p.description LIKE ? OR p.client LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types .= "sss";
}

$where_clause = implode(" AND ", $where_conditions);

// Build order clause
$order_clause = match($sort) {
    'oldest' => 'p.created_at ASC',
    'name_az' => 'p.title ASC',
    'name_za' => 'p.title DESC',
    default => 'p.created_at DESC'
};

$query = "SELECT p.*, c.name_en, c.name_ar FROM projects p 
          JOIN categories c ON p.category_id = c.id 
          WHERE $where_clause 
          ORDER BY $order_clause";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$projects = $stmt->get_result();
$stmt->close();

// Get project statistics for the current user
$stats_query = "SELECT 
    COUNT(*) as total_projects,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_projects,
    SUM(CASE WHEN status = 'ongoing' THEN 1 ELSE 0 END) as ongoing_projects,
    SUM(CASE WHEN status = 'planned' THEN 1 ELSE 0 END) as planned_projects
FROM projects WHERE user_id = ?";

$stats_stmt = $conn->prepare($stats_query);
$stats_stmt->bind_param("i", $user_id);
$stats_stmt->execute();
$stats_result = $stats_stmt->get_result();
$stats = $stats_result->fetch_assoc();
$stats_stmt->close();

$total_projects = $stats['total_projects'];
$completed_projects = $stats['completed_projects'];
$ongoing_projects = $stats['ongoing_projects'];
$planned_projects = $stats['planned_projects'];
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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

        /* Enhanced Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(37, 99, 235, 0.1);
            padding: 1rem 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
        }

        .navbar-brand::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--gradient-secondary);
            transition: width 0.3s ease;
        }

        .navbar-brand:hover::after {
            width: 100%;
        }

        .nav-link {
            font-weight: 500;
            color: var(--gray-700) !important;
            margin: 0 0.5rem;
            padding: 0.75rem 1.25rem !important;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--gradient-primary);
            opacity: 0.1;
            transition: left 0.3s ease;
        }

        .nav-link:hover::before {
            left: 0;
        }

        .nav-link:hover {
            color: var(--primary) !important;
            transform: translateY(-2px);
        }

        .nav-link.active {
            background: var(--gradient-primary);
            color: white !important;
            box-shadow: var(--shadow-glow);
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
            transition: all 0.3s ease;
        }

        .user-avatar:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow-glow);
        }

        /* Enhanced Portfolio Header */
        .portfolio-header {
            background: var(--gradient-hero);
            color: white;
            padding: 6rem 0 4rem 0;
            margin-top: 80px;
            position: relative;
            overflow: hidden;
        }

        .portfolio-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
                        radial-gradient(circle at 40% 40%, rgba(120, 219, 255, 0.3) 0%, transparent 50%);
            animation: backgroundFloat 20s ease-in-out infinite;
        }

        @keyframes backgroundFloat {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-20px) rotate(1deg); }
            66% { transform: translateY(10px) rotate(-1deg); }
        }

        .portfolio-header-content {
            position: relative;
            z-index: 2;
        }

        .portfolio-title {
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: headerFloat 6s ease-in-out infinite;
        }

        @keyframes headerFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .portfolio-subtitle {
            font-size: 1.25rem;
            opacity: 0.95;
            font-weight: 400;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .portfolio-location {
            font-size: 1rem;
            opacity: 0.9;
            font-weight: 400;
            margin-bottom: 2rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .user-profile-image {
            margin-bottom: 1rem;
        }

        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 700;
            color: white;
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            margin: 0 auto;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .social-link {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .social-link:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        /* Statistics Section */
        .stats-section {
            background: var(--white);
            padding: 3rem 0;
            margin-bottom: 3rem;
            box-shadow: var(--shadow-lg);
        }

        .stat-card {
            text-align: center;
            padding: 2rem 1rem;
            border-radius: 1.5rem;
            background: var(--gradient-glass);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1rem;
            color: var(--gray-600);
            font-weight: 500;
        }

        /* Enhanced Filter Bar */
        .filter-section {
            background: var(--white);
            padding: 2rem 0;
            margin-bottom: 3rem;
            box-shadow: var(--shadow-md);
            border-radius: 1rem;
        }

        .filter-bar {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-label {
            font-weight: 600;
            color: var(--gray-700);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-control {
            min-width: 200px;
            border-radius: 0.75rem;
            border: 2px solid var(--gray-200);
            padding: 0.75rem 1rem;
            font-size: 1rem;
            color: var(--gray-700);
            background: var(--white);
            transition: all 0.3s ease;
        }

        .filter-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Enhanced Portfolio Grid */
        .portfolio-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 2.5rem;
            margin-bottom: 4rem;
        }

        .project-card {
            background: var(--white);
            border-radius: 1.5rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-200);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.4s ease;
            position: relative;
        }

        .project-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .project-card:hover::before {
            transform: scaleX(1);
        }

        .project-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: var(--shadow-2xl);
        }

        .project-image {
            width: 100%;
            height: 220px;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3.5rem;
            position: relative;
            overflow: hidden;
        }

        .project-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        .project-card:hover .project-image::before {
            transform: translateX(100%);
        }

        .project-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .project-content {
            padding: 2rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .project-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }

        .project-category {
            font-size: 0.9rem;
            color: var(--primary);
            margin-bottom: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .project-description {
            color: var(--gray-600);
            margin-bottom: 1.5rem;
            line-height: 1.6;
            flex: 1;
        }

        .project-meta {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .project-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: var(--gray-500);
        }

        .project-meta-item i {
            color: var(--primary);
        }

        .project-status {
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-completed {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
        }

        .status-ongoing {
            background: rgba(59, 130, 246, 0.1);
            color: #2563eb;
        }

        .status-planned {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
        }

        .project-footer {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-top: auto;
            flex-wrap: wrap;
        }

        .btn-view {
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

        .btn-view:hover {
            background: var(--gradient-secondary);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-glow-secondary);
        }

        .btn-outline {
            border: 2px solid var(--gray-200);
            background: transparent;
            color: var(--gray-700);
            border-radius: 0.75rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
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

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--white);
            border-radius: 1.5rem;
            box-shadow: var(--shadow-lg);
            margin: 2rem 0;
        }

        .empty-state-icon {
            font-size: 4rem;
            color: var(--gray-400);
            margin-bottom: 1.5rem;
            animation: iconFloat 3s ease-in-out infinite;
        }

        @keyframes iconFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .empty-state-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 1rem;
        }

        .empty-state-text {
            color: var(--gray-500);
            margin-bottom: 2rem;
        }

        .btn-add-sample {
            background: var(--gradient-secondary);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-weight: 600;
            padding: 1rem 2rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-add-sample:hover {
            background: var(--gradient-primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-glow);
        }

        /* Success Message */
        .success-message {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.2);
            color: #16a34a;
            padding: 1rem;
            border-radius: 0.75rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .portfolio-title {
                font-size: 2.5rem;
            }
            
            .portfolio-header {
                padding: 4rem 0 2rem 0;
            }
            
            .portfolio-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-control {
                min-width: auto;
            }
            
            .project-footer {
                flex-direction: column;
                align-items: stretch;
            }
            
            .btn-view, .btn-outline {
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .portfolio-title {
                font-size: 2rem;
            }
            
            .project-content {
                padding: 1.5rem;
            }
            
            .stat-number {
                font-size: 2rem;
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
                    <a class="nav-link active" href="portfolio.php">
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
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<section class="portfolio-header">
    <div class="container">
        <div class="portfolio-header-content">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="portfolio-title" data-aos="fade-up">
                        <?php echo $user_info['full_name']; ?>
                    </h1>
                    <p class="portfolio-subtitle" data-aos="fade-up" data-aos-delay="100">
                        <?php echo $user_info['bio'] ?: $current_content['subtitle']; ?>
                    </p>
                    <?php if ($user_info['location']): ?>
                    <p class="portfolio-location" data-aos="fade-up" data-aos-delay="150">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <?php echo htmlspecialchars($user_info['location']); ?>
                    </p>
                    <?php endif; ?>
                    <div class="d-flex gap-3 flex-wrap mt-3" data-aos="fade-up" data-aos-delay="200">
                        <?php if (isset($_GET['user']) && $_GET['user'] != $_SESSION['user_id']): ?>
                            <a href="browse_users.php" class="btn btn-outline-light">
                                <i class="fas fa-arrow-left"></i> <?php echo $lang === 'en' ? 'Back to Discover' : 'العودة للاكتشاف'; ?>
                            </a>
                        <?php endif; ?>
                        <button class="btn btn-outline-light" id="sharePortfolioBtn">
                            <i class="fas fa-share-alt"></i> <?php echo $current_content['share_portfolio']; ?>
                        </button>
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#contactModal">
                            <i class="fas fa-envelope"></i> <?php echo $current_content['contact']; ?>
                        </button>
                    </div>
                </div>
                <div class="col-lg-4 text-center" data-aos="fade-up" data-aos-delay="300">
                    <div class="user-profile-image">
                        <?php if ($user_info['profile_image']): ?>
                            <img src="<?php echo htmlspecialchars($user_info['profile_image']); ?>" alt="Profile Image" class="profile-img">
                        <?php else: ?>
                            <div class="profile-avatar">
                                <?php echo strtoupper(substr($user_info['full_name'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="social-links mt-3">
                        <?php if ($user_info['website']): ?>
                            <a href="<?php echo htmlspecialchars($user_info['website']); ?>" target="_blank" class="social-link">
                                <i class="fas fa-globe"></i>
                            </a>
                        <?php endif; ?>
                        <?php if ($user_info['github']): ?>
                            <a href="<?php echo htmlspecialchars($user_info['github']); ?>" target="_blank" class="social-link">
                                <i class="fab fa-github"></i>
                            </a>
                        <?php endif; ?>
                        <?php if ($user_info['linkedin']): ?>
                            <a href="<?php echo htmlspecialchars($user_info['linkedin']); ?>" target="_blank" class="social-link">
                                <i class="fab fa-linkedin"></i>
                            </a>
                        <?php endif; ?>
                        <?php if ($user_info['twitter']): ?>
                            <a href="<?php echo htmlspecialchars($user_info['twitter']); ?>" target="_blank" class="social-link">
                                <i class="fab fa-twitter"></i>
                            </a>
                        <?php endif; ?>
                        <?php if ($user_info['instagram']): ?>
                            <a href="<?php echo htmlspecialchars($user_info['instagram']); ?>" target="_blank" class="social-link">
                                <i class="fab fa-instagram"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="stats-section" data-aos="fade-up" data-aos-delay="300">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $user_info ? $user_info['portfolio_views'] : 0; ?></div>
                    <div class="stat-label"><?php echo $current_content['portfolio_views']; ?></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_projects; ?></div>
                    <div class="stat-label"><?php echo $current_content['total_projects']; ?></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $completed_projects; ?></div>
                    <div class="stat-label"><?php echo $current_content['completed']; ?></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $ongoing_projects + $planned_projects; ?></div>
                    <div class="stat-label"><?php echo $lang === 'en' ? 'In Progress' : 'قيد التنفيذ'; ?></div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Contact Modal -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="contactModalLabel"><?php echo $current_content['contact_portfolio_owner']; ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="contactForm">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label"><?php echo $current_content['your_email']; ?></label>
            <input type="email" class="form-control" name="email" required>
          </div>
          <div class="mb-3">
            <label class="form-label"><?php echo $current_content['message']; ?></label>
            <textarea class="form-control" name="message" rows="4" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline" data-bs-dismiss="modal"><?php echo $current_content['close']; ?></button>
          <button type="submit" class="btn btn-primary"><?php echo $current_content['send']; ?></button>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="container">
    <?php if (isset($success_message)): ?>
        <div class="success-message" data-aos="fade-up">
            <i class="fas fa-check-circle"></i>
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <!-- Enhanced Filter Section -->
    <section class="filter-section" data-aos="fade-up" data-aos-delay="200">
        <div class="container">
            <form method="GET" class="filter-bar">
                <div class="filter-group">
                    <label for="category" class="filter-label"><?php echo $current_content['filter']; ?></label>
                    <select name="category" id="category" class="filter-control" onchange="this.form.submit()">
                        <option value="0" <?php if ($filter === 0) echo 'selected'; ?>><?php echo $current_content['all']; ?></option>
                        <?php while ($cat = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php if ($filter === (int)$cat['id']) echo 'selected'; ?>>
                                <?php echo $lang === 'en' ? $cat['name_en'] : $cat['name_ar']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="search" class="filter-label"><?php echo $current_content['search_projects']; ?></label>
                    <input type="text" name="search" id="search" class="filter-control" 
                           value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="<?php echo $current_content['search_projects']; ?>" 
                           onkeyup="this.form.submit()">
                </div>
                
                <div class="filter-group">
                    <label for="sort" class="filter-label"><?php echo $current_content['sort_by']; ?></label>
                    <select name="sort" id="sort" class="filter-control" onchange="this.form.submit()">
                        <option value="newest" <?php if ($sort === 'newest') echo 'selected'; ?>><?php echo $current_content['newest']; ?></option>
                        <option value="oldest" <?php if ($sort === 'oldest') echo 'selected'; ?>><?php echo $current_content['oldest']; ?></option>
                        <option value="name_az" <?php if ($sort === 'name_az') echo 'selected'; ?>><?php echo $current_content['name_az']; ?></option>
                        <option value="name_za" <?php if ($sort === 'name_za') echo 'selected'; ?>><?php echo $current_content['name_za']; ?></option>
                    </select>
                </div>
            </form>
        </div>
    </section>

    <!-- Projects Content -->
    <?php if ($projects->num_rows === 0): ?>
        <div class="empty-state" data-aos="fade-up" data-aos-delay="300">
            <div class="empty-state-icon">
                <i class="fas fa-folder-open"></i>
            </div>
            <h3 class="empty-state-title"><?php echo $current_content['no_projects']; ?></h3>
            <p class="empty-state-text">
                <?php echo $lang === 'en' ? 'Start building your portfolio by adding your first project.' : 'ابدأ في بناء محفظتك بإضافة مشروعك الأول.'; ?>
            </p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="add_sample_projects" class="btn-add-sample">
                        <i class="fas fa-plus"></i> <?php echo $current_content['add_sample_projects']; ?>
                    </button>
                </form>
                <a href="add_project.php" class="btn-add-sample ms-2">
                    <i class="fas fa-plus"></i> <?php echo $lang === 'en' ? 'Add Your Project' : 'أضف مشروعك'; ?>
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="portfolio-grid" data-aos="fade-up" data-aos-delay="300">
            <?php while ($project = $projects->fetch_assoc()): ?>
            <div class="project-card">
                <div class="project-image">
                    <?php if (!empty($project['image_path']) && file_exists($project['image_path'])): ?>
                        <img src="<?php echo $project['image_path']; ?>" alt="Project Image">
                    <?php else: ?>
                        <i class="fas fa-code"></i>
                    <?php endif; ?>
                </div>
                <div class="project-content">
                    <div class="project-title"><?php echo htmlspecialchars($project['title']); ?></div>
                    <div class="project-category">
                        <i class="fas fa-tag"></i> <?php echo $lang === 'en' ? $project['name_en'] : $project['name_ar']; ?>
                    </div>
                    <div class="project-description">
                        <?php echo htmlspecialchars(mb_strimwidth($project['description'], 0, 150, '...')); ?>
                    </div>
                    
                    <div class="project-meta">
                        <?php if (!empty($project['client'])): ?>
                        <div class="project-meta-item">
                            <i class="fas fa-user"></i>
                            <span><?php echo htmlspecialchars($project['client']); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($project['technologies'])): ?>
                        <div class="project-meta-item">
                            <i class="fas fa-code"></i>
                            <span><?php echo htmlspecialchars(mb_strimwidth($project['technologies'], 0, 30, '...')); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($project['start_date'] && $project['end_date']): ?>
                        <div class="project-meta-item">
                            <i class="fas fa-calendar"></i>
                            <span><?php echo date('M Y', strtotime($project['start_date'])); ?> - <?php echo date('M Y', strtotime($project['end_date'])); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($project['budget']): ?>
                        <div class="project-meta-item">
                            <i class="fas fa-dollar-sign"></i>
                            <span>$<?php echo number_format($project['budget'], 0); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="project-status status-<?php echo $project['status']; ?>">
                            <?php echo $current_content[$project['status']]; ?>
                        </div>
                    </div>
                    
                    <div class="project-footer">
                        <a href="project.php?id=<?php echo $project['id']; ?>" class="btn-view">
                            <i class="fas fa-eye"></i> <?php echo $current_content['view_project']; ?>
                        </a>
                        <?php if (!empty($project['project_link'])): ?>
                        <a href="<?php echo htmlspecialchars($project['project_link']); ?>" class="btn-outline" target="_blank" rel="noopener">
                            <i class="fas fa-external-link-alt"></i> <?php echo $current_content['live_demo']; ?>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($project['github_link'])): ?>
                        <a href="<?php echo htmlspecialchars($project['github_link']); ?>" class="btn-outline" target="_blank" rel="noopener">
                            <i class="fab fa-github"></i> <?php echo $current_content['github']; ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
AOS.init({ duration: 1000, once: true, offset: 100 });
// Toast notification function
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(toast);
    setTimeout(() => { toast.remove(); }, 4000);
}

// Share Portfolio functionality
document.getElementById('sharePortfolioBtn').addEventListener('click', async () => {
    const url = window.location.href;
    try {
        await navigator.clipboard.writeText(url);
        showToast(<?php echo json_encode($current_content['portfolio_shared']); ?>, 'success');
    } catch (err) {
        showToast(<?php echo json_encode($current_content['error_sending']); ?>, 'danger');
    }
});

// Contact Form functionality
document.getElementById('contactForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const email = formData.get('email');
    const message = formData.get('message');

    if (!email || !message) {
        showToast(<?php echo json_encode($current_content['error_sending']); ?>, 'danger');
        return;
    }

    const loadingToast = showToast(<?php echo json_encode($current_content['loading']); ?>, 'info');

    try {
        const response = await fetch('send_message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `email=${encodeURIComponent(email)}&message=${encodeURIComponent(message)}`,
        });

        const data = await response.json();

        if (data.success) {
            showToast(<?php echo json_encode($current_content['message_sent']); ?>, 'success');
            form.reset();
        } else {
            showToast(data.message || <?php echo json_encode($current_content['error_sending']); ?>, 'danger');
        }
    } catch (error) {
        showToast(<?php echo json_encode($current_content['error_sending']); ?>, 'danger');
    } finally {
        loadingToast.remove();
    }
});
</script>
</body>
</html>