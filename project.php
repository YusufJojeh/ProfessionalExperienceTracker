<?php
session_start();
require_once 'config/database.php';

$lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';

$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($project_id === 0) {
    header('Location: index.php');
    exit();
}

$project_query = "SELECT p.*, u.full_name, u.username, c.name_en, c.name_ar 
                  FROM projects p 
                  JOIN users u ON p.user_id = u.id 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.id = $project_id";
$project_result = mysqli_query($conn, $project_query);
if (mysqli_num_rows($project_result) === 0) {
    header('Location: index.php');
    exit();
}
$project = mysqli_fetch_assoc($project_result);

// Increment project views
$conn->query("UPDATE projects SET views = views + 1 WHERE id = $project_id");

$files_query = "SELECT * FROM project_files WHERE project_id = $project_id ORDER BY uploaded_at DESC";
$files_result = mysqli_query($conn, $files_query);

$comments_query = "SELECT c.*, u.full_name, u.username 
                   FROM comments c 
                   JOIN users u ON c.user_id = u.id 
                   WHERE c.project_id = $project_id 
                   ORDER BY c.created_at DESC";
$comments_result = mysqli_query($conn, $comments_query);

$content = [
    'en' => [
        'title' => 'Project Details',
        'by' => 'by',
        'category' => 'Category',
        'description' => 'Description',
        'files' => 'Files',
        'comments' => 'Comments',
        'add_comment' => 'Add Comment',
        'your_comment' => 'Your Comment',
        'submit' => 'Submit',
        'no_comments' => 'No comments yet.',
        'comment_added' => 'Comment added successfully!',
        'comment_error' => 'Failed to add comment. Please try again.',
        'back_to_portfolio' => 'Back to Portfolio',
    ],
    'ar' => [
        'title' => 'تفاصيل المشروع',
        'by' => 'بواسطة',
        'category' => 'الفئة',
        'description' => 'الوصف',
        'files' => 'الملفات',
        'comments' => 'التعليقات',
        'add_comment' => 'إضافة تعليق',
        'your_comment' => 'تعليقك',
        'submit' => 'إرسال',
        'no_comments' => 'لا توجد تعليقات بعد.',
        'comment_added' => 'تم إضافة التعليق بنجاح!',
        'comment_error' => 'فشل في إضافة التعليق. حاول مرة أخرى.',
        'back_to_portfolio' => 'العودة للمحفظة',
    ]
];
$current_content = $content[$lang];

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    if (!isset($_SESSION['user_id'])) {
        $error_message = $lang === 'en' ? 'You must be logged in to comment.' : 'يجب تسجيل الدخول لإضافة تعليق.';
    } else {
        $comment_text = trim($_POST['comment']);
        if ($comment_text !== '') {
            $user_id = $_SESSION['user_id'];
            $stmt = $conn->prepare("INSERT INTO comments (project_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iis", $project_id, $user_id, $comment_text);
            if ($stmt->execute()) {
                $success_message = $current_content['comment_added'];
            } else {
                $error_message = $current_content['comment_error'];
            }
            $stmt->close();
        } else {
            $error_message = $lang === 'en' ? 'Comment cannot be empty.' : 'لا يمكن أن يكون التعليق فارغاً.';
        }
    }
    // Refresh comments
    $comments_result = mysqli_query($conn, $comments_query);
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $project['title']; ?> - <?php echo $lang === 'en' ? PLATFORM_NAME : PLATFORM_NAME_AR; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #f59e0b;
            --accent: #10b981;
            --dark: #0f172a;
            --light: #f8fafc;
            --white: #ffffff;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --gradient-primary: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
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
        .project-header {
            background: var(--gradient-primary);
            color: white;
            padding: 3rem 0 2rem 0;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        .project-header::before {
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
        .project-header-content {
            position: relative;
            z-index: 2;
        }
        .project-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .project-meta {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 400;
            margin-bottom: 1rem;
        }
        .project-section {
            background: var(--white);
            border-radius: 1.5rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-200);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .project-image {
            width: 100%;
            max-height: 350px;
            object-fit: cover;
            border-radius: 1rem;
            margin-bottom: 1.5rem;
        }
        .project-description {
            color: var(--gray-700);
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }
        .project-files {
            margin-bottom: 1.5rem;
        }
        .file-link {
            display: inline-block;
            margin-right: 1rem;
            margin-bottom: 0.5rem;
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }
        .file-link:hover {
            color: var(--secondary);
            text-decoration: underline;
        }
        .comments-section {
            margin-top: 2rem;
        }
        .comment-card {
            background: var(--gray-100);
            border-radius: 1rem;
            padding: 1.2rem 1.5rem;
            margin-bottom: 1.2rem;
            box-shadow: 0 2px 8px rgba(99,102,241,0.06);
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }
        .comment-avatar {
            width: 48px;
            height: 48px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
        }
        .comment-body {
            flex: 1;
        }
        .comment-author {
            font-weight: 700;
            color: var(--primary);
        }
        .comment-date {
            color: var(--gray-500);
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }
        .comment-text {
            color: var(--gray-800);
            font-size: 1.05rem;
        }
        .add-comment-form {
            background: var(--white);
            border-radius: 1rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-200);
            padding: 1.5rem;
            margin-top: 2rem;
        }
        .form-label {
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
            display: block;
        }
        .form-control {
            width: 100%;
            padding: 1rem;
            border: 2px solid var(--gray-200);
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--white);
            color: var(--gray-800);
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            transform: translateY(-2px);
        }
        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            border: none;
            border-radius: 1rem;
            font-weight: 700;
            font-size: 1rem;
            padding: 0.75rem 2rem;
            transition: all 0.4s ease;
            box-shadow: var(--shadow-lg);
        }
        .btn-primary:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.4);
            color: white;
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
        @media (max-width: 768px) {
            .project-title {
                font-size: 2rem;
            }
            .project-header {
                padding: 2rem 0 1rem 0;
            }
            .project-section {
                padding: 1rem 0.5rem;
            }
            .add-comment-form {
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
                    <a class="nav-link" href="portfolio.php">
                        <i class="fas fa-briefcase me-1"></i>
                        <?php echo $lang === 'en' ? 'Portfolio' : 'المحفظة'; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">
                        <i class="fas fa-tachometer-alt me-1"></i>
                        <?php echo $lang === 'en' ? 'Dashboard' : 'لوحة التحكم'; ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<section class="project-header">
    <div class="container">
        <div class="project-header-content">
            <h1 class="project-title" data-aos="fade-up">
                <?php echo htmlspecialchars($project['title']); ?>
            </h1>
            <div class="project-meta" data-aos="fade-up" data-aos-delay="100">
                <?php echo $current_content['by']; ?> <strong><?php echo htmlspecialchars($project['full_name']); ?></strong> | <?php echo $current_content['category']; ?>: <strong><?php echo $lang === 'en' ? $project['name_en'] : $project['name_ar']; ?></strong>
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
    <div class="project-section" data-aos="fade-up" data-aos-delay="200">
        <?php if (!empty($project['image_path']) && file_exists($project['image_path'])): ?>
            <img src="<?php echo $project['image_path']; ?>" alt="Project Image" class="project-image">
        <?php endif; ?>
        <div class="project-description">
            <h4><i class="fas fa-align-left me-2"></i> <?php echo $current_content['description']; ?></h4>
            <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
        </div>
        <?php if (mysqli_num_rows($files_result) > 0): ?>
        <div class="project-files">
            <h5><i class="fas fa-paperclip me-2"></i> <?php echo $current_content['files']; ?></h5>
            <?php while ($file = mysqli_fetch_assoc($files_result)): ?>
                <a href="<?php echo htmlspecialchars($file['file_path']); ?>" class="file-link" target="_blank">
                    <i class="fas fa-file-alt"></i> <?php echo htmlspecialchars($file['file_name']); ?>
                </a>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>
    </div>
    <div class="project-section comments-section" data-aos="fade-up" data-aos-delay="300">
        <h4><i class="fas fa-comments me-2"></i> <?php echo $current_content['comments']; ?></h4>
        <?php if (mysqli_num_rows($comments_result) === 0): ?>
            <div class="text-muted mb-3"><?php echo $current_content['no_comments']; ?></div>
        <?php else: ?>
            <?php while ($comment = mysqli_fetch_assoc($comments_result)): ?>
            <div class="comment-card">
                <div class="comment-avatar">
                    <?php echo strtoupper(mb_substr($comment['full_name'], 0, 1)); ?>
                </div>
                <div class="comment-body">
                    <div class="comment-author"><?php echo htmlspecialchars($comment['full_name']); ?> <span class="comment-date">&bull; <?php echo date('Y-m-d H:i', strtotime($comment['created_at'])); ?></span></div>
                    <div class="comment-text"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php endif; ?>
        <form method="POST" class="add-comment-form" data-aos="fade-up" data-aos-delay="400">
            <label class="form-label" for="comment"><?php echo $current_content['your_comment']; ?></label>
            <textarea class="form-control mb-3" name="comment" id="comment" rows="3" required></textarea>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> <?php echo $current_content['submit']; ?>
            </button>
        </form>
    </div>
    <div class="text-center mt-4">
        <a href="portfolio.php" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> <?php echo $current_content['back_to_portfolio']; ?>
        </a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
AOS.init({ duration: 1000, once: true, offset: 100 });
</script>
</body>
</html> 