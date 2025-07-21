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
    ],
    'ar' => [
        'title' => 'المحفظة',
        'subtitle' => 'عرض مشاريعي المهنية',
        'all' => 'الكل',
        'category' => 'الفئة',
        'no_projects' => 'لا توجد مشاريع للعرض بعد.',
        'view_project' => 'عرض المشروع',
        'filter' => 'تصفية حسب الفئة',
    ]
];
$current_content = $content[$lang];

// Get categories
$categories = $conn->query("SELECT * FROM categories ORDER BY name_en");

// Get filter
$filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Get user (if portfolio is for a specific user, otherwise use session user)
$user_id = isset($_GET['user']) ? (int)$_GET['user'] : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0);
$user_template = 'default';
if ($user_id) {
    $user_stmt = $conn->prepare("SELECT template FROM users WHERE id = ?");
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    if ($row = $user_result->fetch_assoc()) {
        $user_template = $row['template'] ?: 'default';
    }
    $user_stmt->close();
} elseif (isset($_SESSION['user_id'])) {
    $user_template = $_SESSION['template'] ?? 'default';
}

// Increment portfolio views
if ($user_id) {
    $conn->query("UPDATE users SET portfolio_views = portfolio_views + 1 WHERE id = $user_id");
} elseif (isset($_SESSION['user_id'])) {
    $conn->query("UPDATE users SET portfolio_views = portfolio_views + 1 WHERE id = " . (int)$_SESSION['user_id']);
}

// Get projects
if ($filter > 0) {
    $stmt = $conn->prepare("SELECT p.*, c.name_en, c.name_ar FROM projects p JOIN categories c ON p.category_id = c.id WHERE p.status = 'published' AND p.category_id = ? ORDER BY p.created_at DESC");
    $stmt->bind_param("i", $filter);
} else {
    $stmt = $conn->prepare("SELECT p.*, c.name_en, c.name_ar FROM projects p JOIN categories c ON p.category_id = c.id WHERE p.status = 'published' ORDER BY p.created_at DESC");
}
$stmt->execute();
$projects = $stmt->get_result();
$stmt->close();
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
            --primary: #fff;
            --primary-dark: #e2e8f0;
            --secondary: #f59e0b;
            --accent: #10b981;
            --dark: #fff;
            --light: #181818;
            --white: #000;
            --gray-100: #222;
            --gray-200: #333;
            --gray-300: #444;
            --gray-400: #bdbdbd;
            --gray-500: #bdbdbd;
            --gray-600: #bdbdbd;
            --gray-700: #fff;
            --gray-800: #fff;
            --gray-900: #fff;
            --gradient-primary: linear-gradient(135deg, #000 0%, #222 100%);
            --gradient-secondary: linear-gradient(135deg, #f59e0b 0%, #f97316 50%, #ea580c 100%);
            --gradient-accent: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
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
        /* Default template styles */
        <?php if ($user_template === 'modern'): ?>
        .portfolio-header {
            background: var(--gradient-secondary);
            color: #fff;
            border-bottom: 4px solid var(--accent);
            box-shadow: 0 8px 32px rgba(16,185,129,0.08);
        }
        .portfolio-title {
            font-family: 'Cairo', sans-serif;
            font-size: 2.8rem;
            letter-spacing: 1px;
            color: var(--accent);
            background: none;
            -webkit-text-fill-color: unset;
        }
        .portfolio-grid {
            gap: 3rem;
        }
        .project-card {
            border-radius: 2.5rem;
            border: 2px solid var(--accent);
            box-shadow: 0 8px 32px rgba(16,185,129,0.08);
            background: var(--white);
            transition: box-shadow 0.3s, border 0.3s;
        }
        .project-card:hover {
            border-color: var(--primary);
            box-shadow: 0 16px 48px rgba(99,102,241,0.12);
        }
        .project-title {
            color: var(--primary-dark);
            font-size: 1.5rem;
        }
        .btn-view {
            background: var(--gradient-accent);
            color: #fff;
        }
        .btn-view:hover {
            background: var(--gradient-primary);
        }
        <?php else: ?>
        /* Default template (existing styles) */
        .portfolio-header {
            background: var(--gradient-primary);
            color: white;
            padding: 3rem 0 2rem 0;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        .portfolio-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        <?php endif; ?>
        .portfolio-header-content {
            position: relative;
            z-index: 2;
        }
        .portfolio-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .portfolio-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 400;
        }
        .filter-bar {
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
        }
        .filter-bar select {
            min-width: 200px;
            border-radius: 0.75rem;
            border: 2px solid var(--gray-200);
            padding: 0.75rem 1rem;
            font-size: 1rem;
            color: var(--gray-700);
        }
        .portfolio-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
        }
        .project-card {
            background: var(--white);
            border-radius: 1.5rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-200);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
        }
        .project-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.15);
        }
        .project-image {
            width: 100%;
            height: 200px;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            border-radius: 1.5rem 1.5rem 0 0;
            overflow: hidden;
        }
        .project-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .project-content {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .project-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }
        .project-category {
            font-size: 0.95rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        .project-description {
            color: var(--gray-600);
            margin-bottom: 1.2rem;
            line-height: 1.6;
            flex: 1;
        }
        .project-footer {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-top: 1rem;
        }
        .btn-view {
            background: var(--gradient-primary);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s;
        }
        .btn-view:hover {
            background: var(--gradient-secondary);
            color: white;
            transform: translateY(-2px);
        }
        @media (max-width: 768px) {
            .portfolio-title {
                font-size: 2rem;
            }
            .portfolio-header {
                padding: 2rem 0 1rem 0;
            }
            .portfolio-grid {
                gap: 1rem;
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
                        <?php echo $lang === 'en' ? 'Portfolio' : 'المحفظة'; ?>
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
            <h1 class="portfolio-title" data-aos="fade-up">
                <?php echo $current_content['title']; ?>
            </h1>
            <p class="portfolio-subtitle" data-aos="fade-up" data-aos-delay="100">
                <?php echo $current_content['subtitle']; ?>
            </p>
            <div class="d-flex gap-3 flex-wrap mt-3" data-aos="fade-up" data-aos-delay="200">
                <button class="btn btn-outline" id="sharePortfolioBtn">
                    <i class="fas fa-share-alt"></i> <?php echo $lang === 'en' ? 'Share Portfolio' : 'مشاركة المحفظة'; ?>
                </button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#contactModal">
                    <i class="fas fa-envelope"></i> <?php echo $lang === 'en' ? 'Contact' : 'تواصل'; ?>
                </button>
            </div>
        </div>
    </div>
</section>
<!-- Contact Modal -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="contactModalLabel"><?php echo $lang === 'en' ? 'Contact Portfolio Owner' : 'مراسلة صاحب المحفظة'; ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="contactForm">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label"><?php echo $lang === 'en' ? 'Your Email' : 'بريدك الإلكتروني'; ?></label>
            <input type="email" class="form-control" name="email" required>
          </div>
          <div class="mb-3">
            <label class="form-label"><?php echo $lang === 'en' ? 'Message' : 'رسالتك'; ?></label>
            <textarea class="form-control" name="message" rows="4" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline" data-bs-dismiss="modal"><?php echo $lang === 'en' ? 'Close' : 'إغلاق'; ?></button>
          <button type="submit" class="btn btn-primary"><?php echo $lang === 'en' ? 'Send' : 'إرسال'; ?></button>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="container">
    <div class="filter-bar" data-aos="fade-up" data-aos-delay="200">
        <form method="GET" class="d-flex align-items-center gap-2 flex-wrap">
            <label for="category" class="form-label mb-0"><?php echo $current_content['filter']; ?>:</label>
            <select name="category" id="category" class="form-select" onchange="this.form.submit()">
                <option value="0" <?php if ($filter === 0) echo 'selected'; ?>><?php echo $current_content['all']; ?></option>
                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php if ($filter === (int)$cat['id']) echo 'selected'; ?>>
                        <?php echo $lang === 'en' ? $cat['name_en'] : $cat['name_ar']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>
    </div>
    <?php if ($projects->num_rows === 0): ?>
        <div class="text-center text-muted py-5">
            <i class="fas fa-folder-open fa-3x mb-3"></i>
            <div><?php echo $current_content['no_projects']; ?></div>
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
                        <?php echo htmlspecialchars(mb_strimwidth($project['description'], 0, 120, '...')); ?>
                    </div>
                    <div class="project-footer">
                        <a href="project.php?id=<?php echo $project['id']; ?>" class="btn btn-view">
                            <i class="fas fa-eye"></i> <?php echo $current_content['view_project']; ?>
                        </a>
                        <?php if (!empty($project['project_link'])): ?>
                        <a href="<?php echo htmlspecialchars($project['project_link']); ?>" class="btn btn-outline btn-sm" target="_blank" rel="noopener">
                            <i class="fas fa-external-link-alt"></i> Live
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($project['github_link'])): ?>
                        <a href="<?php echo htmlspecialchars($project['github_link']); ?>" class="btn btn-outline btn-sm" target="_blank" rel="noopener">
                            <i class="fab fa-github"></i> GitHub
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
</script>
</body>
</html>