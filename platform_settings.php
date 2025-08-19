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
        'title' => 'Platform Settings',
        'general_settings' => 'General Settings',
        'platform_name' => 'Platform Name',
        'platform_name_ar' => 'Platform Name (Arabic)',
        'contact_email' => 'Contact Email',
        'max_file_size' => 'Maximum File Size (MB)',
        'allowed_file_types' => 'Allowed File Types',
        'user_settings' => 'User Settings',
        'allow_registration' => 'Allow User Registration',
        'email_verification' => 'Require Email Verification',
        'max_projects_per_user' => 'Maximum Projects per User',
        'content_settings' => 'Content Settings',
        'auto_approve_projects' => 'Auto-approve Projects',
        'moderate_comments' => 'Moderate Comments',
        'max_comment_length' => 'Maximum Comment Length',
        'save_settings' => 'Save Settings',
        'settings_saved' => 'Settings saved successfully!',
        'settings_error' => 'Failed to save settings. Please try again.',
        'yes' => 'Yes',
        'no' => 'No',
        'security_settings' => 'Security Settings',
        'session_timeout' => 'Session Timeout (minutes)',
        'password_min_length' => 'Minimum Password Length',
        'require_strong_password' => 'Require Strong Password',
        'enable_captcha' => 'Enable CAPTCHA',
        'maintenance_mode' => 'Maintenance Mode',
        'maintenance_message' => 'Maintenance Message'
    ],
    'ar' => [
        'title' => 'إعدادات المنصة',
        'general_settings' => 'الإعدادات العامة',
        'platform_name' => 'اسم المنصة',
        'platform_name_ar' => 'اسم المنصة (العربية)',
        'contact_email' => 'البريد الإلكتروني للتواصل',
        'max_file_size' => 'الحد الأقصى لحجم الملف (ميجابايت)',
        'allowed_file_types' => 'أنواع الملفات المسموحة',
        'user_settings' => 'إعدادات المستخدمين',
        'allow_registration' => 'السماح بتسجيل المستخدمين',
        'email_verification' => 'تطلب التحقق من البريد الإلكتروني',
        'max_projects_per_user' => 'الحد الأقصى للمشاريع لكل مستخدم',
        'content_settings' => 'إعدادات المحتوى',
        'auto_approve_projects' => 'الموافقة التلقائية على المشاريع',
        'moderate_comments' => 'مراقبة التعليقات',
        'max_comment_length' => 'الحد الأقصى لطول التعليق',
        'save_settings' => 'حفظ الإعدادات',
        'settings_saved' => 'تم حفظ الإعدادات بنجاح!',
        'settings_error' => 'فشل في حفظ الإعدادات. حاول مرة أخرى.',
        'yes' => 'نعم',
        'no' => 'لا',
        'security_settings' => 'إعدادات الأمان',
        'session_timeout' => 'مهلة الجلسة (دقائق)',
        'password_min_length' => 'الحد الأدنى لطول كلمة المرور',
        'require_strong_password' => 'تطلب كلمة مرور قوية',
        'enable_captcha' => 'تفعيل CAPTCHA',
        'maintenance_mode' => 'وضع الصيانة',
        'maintenance_message' => 'رسالة الصيانة'
    ]
];
$current_content = $content[$lang];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // In a real application, you would save these to a settings table
    // For now, we'll just show a success message
    $success_message = $current_content['settings_saved'];
}

// Default settings (in a real app, these would come from database)
$settings = [
    'platform_name' => PLATFORM_NAME,
    'platform_name_ar' => PLATFORM_NAME_AR,
    'contact_email' => 'admin@example.com',
    'max_file_size' => 10,
    'allowed_file_types' => 'jpg,jpeg,png,gif,pdf,doc,docx',
    'allow_registration' => true,
    'email_verification' => false,
    'max_projects_per_user' => 50,
    'auto_approve_projects' => true,
    'moderate_comments' => false,
    'max_comment_length' => 500,
    'session_timeout' => 60,
    'password_min_length' => 8,
    'require_strong_password' => true,
    'enable_captcha' => false,
    'maintenance_mode' => false,
    'maintenance_message' => 'We are currently performing maintenance. Please check back later.'
];
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
        .settings-card {
            background: var(--gray-50);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: 1px solid var(--gray-200);
        }
        .form-switch {
            padding-left: 2.5em;
        }
        .form-switch .form-check-input {
            width: 3em;
            height: 1.5em;
            margin-left: -2.5em;
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
    
    <form method="POST">
        <!-- General Settings -->
        <div class="admin-section" data-aos="fade-up" data-aos-delay="100">
            <h3 class="mb-4">
                <i class="fas fa-cog me-2"></i>
                <?php echo $current_content['general_settings']; ?>
            </h3>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?php echo $current_content['platform_name']; ?></label>
                    <input type="text" name="platform_name" class="form-control" value="<?php echo htmlspecialchars($settings['platform_name']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?php echo $current_content['platform_name_ar']; ?></label>
                    <input type="text" name="platform_name_ar" class="form-control" value="<?php echo htmlspecialchars($settings['platform_name_ar']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?php echo $current_content['contact_email']; ?></label>
                    <input type="email" name="contact_email" class="form-control" value="<?php echo htmlspecialchars($settings['contact_email']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?php echo $current_content['max_file_size']; ?></label>
                    <input type="number" name="max_file_size" class="form-control" value="<?php echo $settings['max_file_size']; ?>" min="1" max="100">
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label"><?php echo $current_content['allowed_file_types']; ?></label>
                    <input type="text" name="allowed_file_types" class="form-control" value="<?php echo htmlspecialchars($settings['allowed_file_types']); ?>" placeholder="jpg,jpeg,png,gif,pdf">
                    <small class="text-muted">Comma-separated file extensions</small>
                </div>
            </div>
        </div>
        
        <!-- User Settings -->
        <div class="admin-section" data-aos="fade-up" data-aos-delay="200">
            <h3 class="mb-4">
                <i class="fas fa-users me-2"></i>
                <?php echo $current_content['user_settings']; ?>
            </h3>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="allow_registration" id="allow_registration" <?php echo $settings['allow_registration'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="allow_registration">
                            <?php echo $current_content['allow_registration']; ?>
                        </label>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="email_verification" id="email_verification" <?php echo $settings['email_verification'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="email_verification">
                            <?php echo $current_content['email_verification']; ?>
                        </label>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?php echo $current_content['max_projects_per_user']; ?></label>
                    <input type="number" name="max_projects_per_user" class="form-control" value="<?php echo $settings['max_projects_per_user']; ?>" min="1" max="1000">
                </div>
            </div>
        </div>
        
        <!-- Content Settings -->
        <div class="admin-section" data-aos="fade-up" data-aos-delay="300">
            <h3 class="mb-4">
                <i class="fas fa-edit me-2"></i>
                <?php echo $current_content['content_settings']; ?>
            </h3>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="auto_approve_projects" id="auto_approve_projects" <?php echo $settings['auto_approve_projects'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="auto_approve_projects">
                            <?php echo $current_content['auto_approve_projects']; ?>
                        </label>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="moderate_comments" id="moderate_comments" <?php echo $settings['moderate_comments'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="moderate_comments">
                            <?php echo $current_content['moderate_comments']; ?>
                        </label>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?php echo $current_content['max_comment_length']; ?></label>
                    <input type="number" name="max_comment_length" class="form-control" value="<?php echo $settings['max_comment_length']; ?>" min="50" max="2000">
                </div>
            </div>
        </div>
        
        <!-- Security Settings -->
        <div class="admin-section" data-aos="fade-up" data-aos-delay="400">
            <h3 class="mb-4">
                <i class="fas fa-shield-alt me-2"></i>
                <?php echo $current_content['security_settings']; ?>
            </h3>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?php echo $current_content['session_timeout']; ?></label>
                    <input type="number" name="session_timeout" class="form-control" value="<?php echo $settings['session_timeout']; ?>" min="15" max="1440">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?php echo $current_content['password_min_length']; ?></label>
                    <input type="number" name="password_min_length" class="form-control" value="<?php echo $settings['password_min_length']; ?>" min="6" max="50">
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="require_strong_password" id="require_strong_password" <?php echo $settings['require_strong_password'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="require_strong_password">
                            <?php echo $current_content['require_strong_password']; ?>
                        </label>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="enable_captcha" id="enable_captcha" <?php echo $settings['enable_captcha'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="enable_captcha">
                            <?php echo $current_content['enable_captcha']; ?>
                        </label>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenance_mode" <?php echo $settings['maintenance_mode'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="maintenance_mode">
                            <?php echo $current_content['maintenance_mode']; ?>
                        </label>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label"><?php echo $current_content['maintenance_message']; ?></label>
                    <textarea name="maintenance_message" class="form-control" rows="3"><?php echo htmlspecialchars($settings['maintenance_message']); ?></textarea>
                </div>
            </div>
        </div>
        
        <!-- Save Button -->
        <div class="admin-section" data-aos="fade-up" data-aos-delay="500">
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i>
                    <?php echo $current_content['save_settings']; ?>
                </button>
            </div>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
AOS.init({ duration: 1000, once: true, offset: 100 });
</script>
</body>
</html>
