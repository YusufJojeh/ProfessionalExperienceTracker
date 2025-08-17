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
        'title' => 'Settings',
        'subtitle' => 'Manage your account settings',
        'language' => 'Language',
        'select_language' => 'Select your preferred language',
        'password' => 'Change Password',
        'current_password' => 'Current Password',
        'new_password' => 'New Password',
        'confirm_password' => 'Confirm New Password',
        'save_changes' => 'Save Changes',
        'notifications' => 'Notification Preferences',
        'coming_soon' => 'Coming soon...',
        'delete_account' => 'Delete Account',
        'delete_confirm' => 'Are you sure you want to delete your account? This action cannot be undone.',
        'delete_btn' => 'Delete My Account',
        'success_msg' => 'Settings updated successfully!',
        'error_msg' => 'Failed to update settings. Please try again.',
        'password_mismatch' => 'New passwords do not match.',
        'password_short' => 'Password must be at least 6 characters.',
        'current_password_wrong' => 'Current password is incorrect.'
    ],
    'ar' => [
        'title' => 'الإعدادات',
        'subtitle' => 'إدارة إعدادات حسابك',
        'language' => 'اللغة',
        'select_language' => 'اختر لغتك المفضلة',
        'password' => 'تغيير كلمة المرور',
        'current_password' => 'كلمة المرور الحالية',
        'new_password' => 'كلمة المرور الجديدة',
        'confirm_password' => 'تأكيد كلمة المرور الجديدة',
        'save_changes' => 'حفظ التغييرات',
        'notifications' => 'تفضيلات الإشعارات',
        'coming_soon' => 'قريباً...',
        'delete_account' => 'حذف الحساب',
        'delete_confirm' => 'هل أنت متأكد أنك تريد حذف حسابك؟ هذا الإجراء لا يمكن التراجع عنه.',
        'delete_btn' => 'حذف حسابي',
        'success_msg' => 'تم تحديث الإعدادات بنجاح!',
        'error_msg' => 'فشل في تحديث الإعدادات. يرجى المحاولة مرة أخرى.',
        'password_mismatch' => 'كلمات المرور الجديدة غير متطابقة.',
        'password_short' => 'يجب أن تكون كلمة المرور 6 أحرف على الأقل.',
        'current_password_wrong' => 'كلمة المرور الحالية غير صحيحة.'
    ]
];
$current_content = $content[$lang];

$user_id = $_SESSION['user_id'];
$success_message = $error_message = '';

// Handle language change
if (isset($_POST['change_language'])) {
    $new_lang = $_POST['language'];
    if (in_array($new_lang, ['en', 'ar'])) {
        $_SESSION['language'] = $new_lang;
        $stmt = $conn->prepare("UPDATE users SET language = ? WHERE id = ?");
        $stmt->bind_param("si", $new_lang, $user_id);
        $stmt->execute();
        $stmt->close();
        $success_message = $current_content['success_msg'];
    }
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    if (strlen($new) < 6) {
        $error_message = $current_content['password_short'];
    } elseif ($new !== $confirm) {
        $error_message = $current_content['password_mismatch'];
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($hashed);
        $stmt->fetch();
        $stmt->close();
        if (!password_verify($current, $hashed)) {
            $error_message = $current_content['current_password_wrong'];
        } else {
            $new_hashed = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $new_hashed, $user_id);
            if ($stmt->execute()) {
                $success_message = $current_content['success_msg'];
            } else {
                $error_message = $current_content['error_msg'];
            }
            $stmt->close();
        }
    }
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
        .settings-hero {
            background: var(--gradient-primary);
            color: white;
            padding: 3rem 0 2rem 0;
            position: relative;
            overflow: hidden;
        }
        .settings-hero .display-5 {
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .settings-card {
            background: var(--white);
            border-radius: 2rem;
            box-shadow: var(--shadow-lg);
            padding: 2.5rem 2rem;
            border: 1px solid var(--gray-200);
            margin-bottom: 2rem;
        }
        .form-label {
            font-weight: 600;
            color: var(--gray-700);
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
        @media (max-width: 768px) {
            .settings-card {
                padding: 1.5rem 1rem;
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
                    <a class="nav-link" href="profile.php">
                        <i class="fas fa-user me-1"></i>
                        <?php echo $lang === 'en' ? 'Profile' : 'الملف الشخصي'; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="settings.php">
                        <i class="fas fa-cog me-1"></i>
                        <?php echo $lang === 'en' ? 'Settings' : 'الإعدادات'; ?>
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
<section class="settings-hero">
    <div class="container">
        <div class="d-flex flex-column align-items-center justify-content-center" style="z-index:2; position:relative;">
            <svg width="100" height="100" viewBox="0 0 100 100" class="mb-3" style="filter: drop-shadow(0 10px 30px rgba(99,102,241,0.2));">
                <defs>
                    <linearGradient id="settingsHeroGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#fff"/>
                        <stop offset="100%" stop-color="#e2e8f0"/>
                    </linearGradient>
                </defs>
                <circle cx="50" cy="50" r="45" fill="none" stroke="url(#settingsHeroGradient)" stroke-width="3"/>
                <circle cx="50" cy="50" r="35" fill="none" stroke="url(#settingsHeroGradient)" stroke-width="2" opacity="0.7"/>
                <circle cx="50" cy="50" r="25" fill="url(#settingsHeroGradient)" opacity="0.3"/>
                <path d="M35 50 L50 65 L65 40" stroke="url(#settingsHeroGradient)" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h1 class="display-5 fw-bold mb-2" data-aos="fade-up"><?php echo $current_content['title']; ?></h1>
            <p class="lead mb-0" data-aos="fade-up" data-aos-delay="100"><?php echo $current_content['subtitle']; ?></p>
        </div>
    </div>
</section>
<div class="container" style="padding:2rem 0;">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?php if ($success_message): ?>
                <div class="alert alert-success" data-aos="fade-up">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="alert alert-danger" data-aos="fade-up">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            <!-- Language Section -->
            <div class="settings-card" data-aos="fade-up" data-aos-delay="100">
                <h3><i class="fas fa-language me-2"></i> <?php echo $current_content['language']; ?></h3>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label"><?php echo $current_content['select_language']; ?></label>
                        <select class="form-control" name="language">
                            <option value="en" <?php if ($lang === 'en') echo 'selected'; ?>>English</option>
                            <option value="ar" <?php if ($lang === 'ar') echo 'selected'; ?>>العربية</option>
                        </select>
                    </div>
                    <button type="submit" name="change_language" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo $current_content['save_changes']; ?>
                    </button>
                </form>
            </div>
            <!-- Password Section -->
            <div class="settings-card" data-aos="fade-up" data-aos-delay="200">
                <h3><i class="fas fa-lock me-2"></i> <?php echo $current_content['password']; ?></h3>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label"><?php echo $current_content['current_password']; ?></label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo $current_content['new_password']; ?></label>
                        <input type="password" class="form-control" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo $current_content['confirm_password']; ?></label>
                        <input type="password" class="form-control" name="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo $current_content['save_changes']; ?>
                    </button>
                </form>
            </div>
            <!-- Notifications Section (Placeholder) -->
            <div class="settings-card" data-aos="fade-up" data-aos-delay="300">
                <h3><i class="fas fa-bell me-2"></i> <?php echo $current_content['notifications']; ?></h3>
                <div class="text-muted"><?php echo $current_content['coming_soon']; ?></div>
            </div>
            <!-- Delete Account Section (Placeholder) -->
            <div class="settings-card" data-aos="fade-up" data-aos-delay="400">
                <h3><i class="fas fa-trash-alt me-2"></i> <?php echo $current_content['delete_account']; ?></h3>
                <div class="mb-3 text-danger"><?php echo $current_content['delete_confirm']; ?></div>
                <button class="btn btn-outline btn-danger" disabled>
                    <i class="fas fa-trash"></i> <?php echo $current_content['delete_btn']; ?>
                </button>
                <div class="text-muted mt-2"><?php echo $current_content['coming_soon']; ?></div>
            </div>
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