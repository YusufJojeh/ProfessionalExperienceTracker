<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect('login.php');
}

// Get current language
$lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';

// Language content
$content = [
    'en' => [
        'title' => 'Profile Settings',
        'subtitle' => 'Manage your profile information',
        'personal_info' => 'Personal Information',
        'full_name' => 'Full Name',
        'username' => 'Username',
        'email' => 'Email Address',
        'bio' => 'Bio',
        'profile_image' => 'Profile Image',
        'language' => 'Language',
        'save_changes' => 'Save Changes',
        'cancel' => 'Cancel',
        'success_msg' => 'Profile updated successfully!',
        'error_msg' => 'Failed to update profile. Please try again.',
        'validation_errors' => [
            'name_required' => 'Full name is required',
            'username_required' => 'Username is required',
            'username_taken' => 'Username already exists',
            'email_required' => 'Email is required',
            'email_invalid' => 'Please enter a valid email',
            'email_taken' => 'Email already exists'
        ],
        'location' => 'Location',
        'website' => 'Website',
        'social_links' => 'Social Links',
        'back_to_dashboard' => 'Back to Dashboard',
        'bio_placeholder' => 'Tell us about yourself...',
        'profile_updated' => 'Profile updated successfully!',
        'profile_error' => 'Failed to update profile. Please try again.'
    ],
    'ar' => [
        'title' => 'إعدادات الملف الشخصي',
        'subtitle' => 'أدر معلومات ملفك الشخصي',
        'personal_info' => 'المعلومات الشخصية',
        'full_name' => 'الاسم الكامل',
        'username' => 'اسم المستخدم',
        'email' => 'البريد الإلكتروني',
        'bio' => 'نبذة مختصرة',
        'profile_image' => 'صورة الملف الشخصي',
        'language' => 'اللغة',
        'save_changes' => 'حفظ التغييرات',
        'cancel' => 'إلغاء',
        'success_msg' => 'تم تحديث الملف الشخصي بنجاح!',
        'error_msg' => 'فشل في تحديث الملف الشخصي. يرجى المحاولة مرة أخرى.',
        'validation_errors' => [
            'name_required' => 'الاسم الكامل مطلوب',
            'username_required' => 'اسم المستخدم مطلوب',
            'username_taken' => 'اسم المستخدم موجود بالفعل',
            'email_required' => 'البريد الإلكتروني مطلوب',
            'email_invalid' => 'يرجى إدخال بريد إلكتروني صحيح',
            'email_taken' => 'البريد الإلكتروني موجود بالفعل'
        ],
        'location' => 'الموقع',
        'website' => 'الموقع الإلكتروني',
        'social_links' => 'روابط التواصل الاجتماعي',
        'back_to_dashboard' => 'العودة لللوحة التحكم',
        'bio_placeholder' => 'أخبرنا عن نفسك...',
        'profile_updated' => 'تم تحديث الملف الشخصي بنجاح!',
        'profile_error' => 'فشل في تحديث الملف الشخصي. يرجى المحاولة مرة أخرى.'
    ]
];

$current_content = $content[$lang];
$errors = [];
$success = false;

// Get user data
$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $full_name = trim($_POST['full_name']);
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $bio = trim($_POST['bio']);
        $location = trim($_POST['location']);
        $website = trim($_POST['website']);
        $github = trim($_POST['github']);
        $linkedin = trim($_POST['linkedin']);
        $twitter = trim($_POST['twitter']);
        $instagram = trim($_POST['instagram']);
        $template = isset($_POST['template']) ? $_POST['template'] : 'default';
        
        // Validation
        $errors = [];
        if (empty($full_name)) {
            $errors[] = $lang === 'en' ? 'Full name is required.' : 'الاسم الكامل مطلوب.';
        }
        if (empty($username)) {
            $errors[] = $lang === 'en' ? 'Username is required.' : 'اسم المستخدم مطلوب.';
        }
        if (empty($email)) {
            $errors[] = $lang === 'en' ? 'Email is required.' : 'البريد الإلكتروني مطلوب.';
        }
        
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
        $stmt->bind_param("ssi", $username, $email, $user_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = $lang === 'en' ? 'Username or email already exists.' : 'اسم المستخدم أو البريد الإلكتروني موجود بالفعل.';
        }
        $stmt->close();
        
        if (empty($errors)) {
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, bio = ?, location = ?, website = ?, github = ?, linkedin = ?, twitter = ?, instagram = ?, template = ? WHERE id = ?");
            $stmt->bind_param("sssssssssssi", $full_name, $username, $email, $bio, $location, $website, $github, $linkedin, $twitter, $instagram, $template, $user_id);
            
            if ($stmt->execute()) {
                $success_message = $current_content['profile_updated'];
                // Update session data
                $_SESSION['user_name'] = $full_name;
                // Refresh user data
                $user['full_name'] = $full_name;
                $user['username'] = $username;
                $user['email'] = $email;
                $user['bio'] = $bio;
                $user['location'] = $location;
                $user['website'] = $website;
                $user['github'] = $github;
                $user['linkedin'] = $linkedin;
                $user['twitter'] = $twitter;
                $user['instagram'] = $instagram;
                $user['template'] = $template;
            } else {
                $error_message = $current_content['profile_error'];
            }
            $stmt->close();
        } else {
            $error_message = implode('<br>', $errors);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $current_content['title']; ?> - Professional Experience Tracker</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #fff;
            --secondary-color: #7c3aed;
            --accent-color: #f59e0b;
            --text-dark: #fff;
            --text-light: #bdbdbd;
            --bg-light: #181818;
            --bg-white: #000;
            --gradient-primary: linear-gradient(135deg, #000 0%, #222 100%);
        }

        body {
            font-family: '<?php echo $lang === 'ar' ? 'Cairo' : 'Poppins'; ?>', sans-serif;
            background: var(--bg-light);
        }

        .rtl {
            direction: rtl;
            text-align: right;
        }

        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
        }

        .main-content {
            padding: 30px 0;
        }

        .profile-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .profile-header h2 {
            color: var(--text-dark);
            font-weight: 700;
            margin-bottom: 10px;
        }

        .profile-header p {
            color: var(--text-light);
            font-size: 1.1rem;
        }

        .profile-image-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .current-profile-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid var(--primary-color);
            margin: 0 auto 20px;
            background: var(--bg-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--primary-color);
            overflow: hidden;
        }

        .current-profile-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        .btn-primary-custom {
            background: var(--gradient-primary);
            border: none;
            color: white;
            padding: 15px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .btn-secondary-custom {
            background: var(--text-light);
            border: none;
            color: white;
            padding: 15px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .btn-secondary-custom:hover {
            background: var(--text-dark);
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 20px;
        }

        .file-upload-area {
            border: 2px dashed #e5e7eb;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            margin-top: 10px;
        }

        .file-upload-area:hover {
            border-color: var(--primary-color);
            background: var(--bg-light);
        }

        .file-upload-area input[type="file"] {
            display: none;
        }

        .file-upload-icon {
            font-size: 2rem;
            color: var(--text-light);
            margin-bottom: 10px;
        }
        @keyframes avatarPulse {
            0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
            50% { transform: scale(1.05); box-shadow: 0 0 0 20px rgba(245, 158, 11, 0.1); }
        }
        .profile-name {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-900);
            text-align: center;
            margin-bottom: 0.5rem;
        }
        .profile-username {
            color: var(--primary);
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .profile-form .form-group {
            margin-bottom: 1.5rem;
        }
        .profile-form .form-label {
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
            display: block;
        }
        .profile-form .form-control {
            width: 100%;
            padding: 1rem;
            border: 2px solid var(--gray-200);
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--white);
            color: var(--gray-800);
        }
        .profile-form .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            transform: translateY(-2px);
        }
        .btn-group {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 1rem;
            font-weight: 700;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.4s ease;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        .btn-primary {
            background: var(--gradient-primary);
            color: white;
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
            .profile-card {
                padding: 2rem 1.5rem;
            }
            .btn-group {
                flex-direction: column;
            }
            .btn {
                width: 100%;
                justify-content: center;
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
                            <?php echo $lang === 'en' ? 'Portfolio' : 'المحفظة'; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="profile.php">
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
    <!-- Hero Section -->
    <section class="profile-hero" style="background: var(--gradient-primary); color: white; padding: 3rem 0 2rem 0; position: relative; overflow: hidden;">
        <div class="container">
            <div class="d-flex flex-column align-items-center justify-content-center" style="z-index:2; position:relative;">
                <svg width="100" height="100" viewBox="0 0 100 100" class="mb-3" style="filter: drop-shadow(0 10px 30px rgba(99,102,241,0.2));">
                    <defs>
                        <linearGradient id="profileHeroGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#fff"/>
                            <stop offset="100%" stop-color="#e2e8f0"/>
                        </linearGradient>
                    </defs>
                    <circle cx="50" cy="50" r="45" fill="none" stroke="url(#profileHeroGradient)" stroke-width="3"/>
                    <circle cx="50" cy="50" r="35" fill="none" stroke="url(#profileHeroGradient)" stroke-width="2" opacity="0.7"/>
                    <circle cx="50" cy="50" r="25" fill="url(#profileHeroGradient)" opacity="0.3"/>
                    <path d="M35 50 L50 65 L65 40" stroke="url(#profileHeroGradient)" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <h1 class="display-5 fw-bold mb-2" data-aos="fade-up"><?php echo $current_content['title']; ?></h1>
                <p class="lead mb-0" data-aos="fade-up" data-aos-delay="100"><?php echo $current_content['subtitle']; ?></p>
            </div>
        </div>
    </section>
    <!-- Main Content -->
    <div class="main-content" style="padding: 2rem 0;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
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
                    <div class="profile-card" data-aos="fade-up" data-aos-delay="200" style="background: var(--white); border-radius: 2rem; box-shadow: var(--shadow-lg); padding: 2.5rem 2rem; border: 1px solid var(--gray-200);">
                        <div class="profile-avatar mb-3 text-center">
                            <i class="fas fa-user-circle fa-4x" style="color: var(--primary);"></i>
                        </div>
                        <div class="profile-name text-center mb-1" style="font-size: 2rem; font-weight: 700; color: var(--gray-900);">
                            <?php echo htmlspecialchars($user['full_name']); ?>
                        </div>
                        <div class="profile-username text-center mb-4" style="color: var(--primary);">
                            @<?php echo htmlspecialchars($user['username']); ?>
                        </div>
                        <form method="POST" class="profile-form">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $current_content['full_name']; ?> *</label>
                                        <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $current_content['username']; ?> *</label>
                                        <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $current_content['email']; ?> *</label>
                                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $current_content['location']; ?></label>
                                        <input type="text" class="form-control" name="location" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $current_content['bio']; ?></label>
                                        <textarea class="form-control" name="bio" placeholder="<?php echo $current_content['bio_placeholder']; ?>"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $current_content['website']; ?></label>
                                        <input type="url" class="form-control" name="website" value="<?php echo htmlspecialchars($user['website'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $current_content['social_links']; ?></label>
                                        <div class="row g-2">
                                            <div class="col-6 col-md-3">
                                                <input type="text" class="form-control mb-2" name="github" placeholder="GitHub" value="<?php echo htmlspecialchars($user['github'] ?? ''); ?>">
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <input type="text" class="form-control mb-2" name="linkedin" placeholder="LinkedIn" value="<?php echo htmlspecialchars($user['linkedin'] ?? ''); ?>">
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <input type="text" class="form-control mb-2" name="twitter" placeholder="Twitter" value="<?php echo htmlspecialchars($user['twitter'] ?? ''); ?>">
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <input type="text" class="form-control mb-2" name="instagram" placeholder="Instagram" value="<?php echo htmlspecialchars($user['instagram'] ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $lang === 'en' ? 'Portfolio Template' : 'قالب المحفظة'; ?></label>
                                        <select class="form-control" name="template">
                                            <option value="default" <?php if (($user['template'] ?? 'default') === 'default') echo 'selected'; ?>><?php echo $lang === 'en' ? 'Default' : 'الافتراضي'; ?></option>
                                            <option value="modern" <?php if (($user['template'] ?? 'default') === 'modern') echo 'selected'; ?>><?php echo $lang === 'en' ? 'Modern' : 'عصري'; ?></option>
                                            <option value="classic" <?php if (($user['template'] ?? 'default') === 'classic') echo 'selected'; ?>><?php echo $lang === 'en' ? 'Classic' : 'كلاسيكي'; ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="btn-group mt-4">
                                <a href="dashboard.php" class="btn btn-outline">
                                    <i class="fas fa-arrow-left"></i>
                                    <?php echo $current_content['back_to_dashboard']; ?>
                                </a>
                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    <?php echo $current_content['save_changes']; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
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
        // Show toast on success or error
        <?php if (isset($success_message)): ?>
        showToast('<?php echo $success_message; ?>', 'success');
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
        showToast('<?php echo $error_message; ?>', 'danger');
        <?php endif; ?>
    </script>
</body>
</html> 