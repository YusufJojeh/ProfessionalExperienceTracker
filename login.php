<?php
session_start();
require_once 'config/database.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Get current language
$lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';

// Language content
$content = [
    'en' => [
        'title' => 'Welcome Back',
        'subtitle' => 'Sign in to your professional portfolio and continue your journey',
        'form_title' => 'Sign In',
        'email_label' => 'Email Address',
        'password_label' => 'Password',
        'remember_me' => 'Remember me',
        'forgot_password' => 'Forgot password?',
        'login_btn' => 'Sign In',
        'register_link' => 'Don\'t have an account?',
        'register_btn' => 'Create Account',
        'features' => [
            'Secure Access' => 'Enterprise-grade security for your data',
            'Real-time Sync' => 'Your portfolio updates instantly',
            'Global Reach' => 'Connect with opportunities worldwide',
            '24/7 Support' => 'Professional support whenever you need'
        ]
    ],
    'ar' => [
        'title' => 'مرحباً بعودتك',
        'subtitle' => 'سجل دخولك إلى محفظتك المهنية واستمر في رحلتك',
        'form_title' => 'تسجيل الدخول',
        'email_label' => 'عنوان البريد الإلكتروني',
        'password_label' => 'كلمة المرور',
        'remember_me' => 'تذكرني',
        'forgot_password' => 'نسيت كلمة المرور؟',
        'login_btn' => 'تسجيل الدخول',
        'register_link' => 'ليس لديك حساب؟',
        'register_btn' => 'إنشاء حساب',
        'features' => [
            'وصول آمن' => 'أمان على مستوى المؤسسات لبياناتك',
            'مزامنة مباشرة' => 'محفظتك تتحدث فوراً',
            'وصول عالمي' => 'تواصل مع الفرص في جميع أنحاء العالم',
            'دعم 24/7' => 'دعم مهني متى احتجت'
        ]
    ]
];

$current_content = $content[$lang];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    $errors = [];
    
    // Validation
    if (empty($email)) {
        $errors[] = $lang === 'en' ? 'Email is required' : 'البريد الإلكتروني مطلوب';
    }
    
    if (empty($password)) {
        $errors[] = $lang === 'en' ? 'Password is required' : 'كلمة المرور مطلوبة';
    }
    
    // Authenticate user if no errors
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                
                header('Location: dashboard.php');
                exit();
            } else {
                $errors[] = $lang === 'en' ? 'Invalid email or password' : 'البريد الإلكتروني أو كلمة المرور غير صحيحة';
            }
        } else {
            $errors[] = $lang === 'en' ? 'Invalid email or password' : 'البريد الإلكتروني أو كلمة المرور غير صحيحة';
        }
        $stmt->close();
    }
}
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
            --gradient-dark: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --shadow-2xl: 0 25px 50px -12px rgb(0 0 0 / 0.25);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: '<?php echo $lang === 'ar' ? 'Cairo' : 'Inter'; ?>', sans-serif;
            line-height: 1.6;
            color: var(--gray-800);
            background: var(--light);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .rtl {
            direction: rtl;
            text-align: right;
        }

        /* Main Container */
        .main-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: var(--gradient-primary);
            position: relative;
            overflow: hidden;
        }

        .main-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(139, 92, 246, 0.4) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(168, 85, 247, 0.4) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(99, 102, 241, 0.3) 0%, transparent 50%);
            animation: backgroundFloat 20s ease-in-out infinite;
        }

        @keyframes backgroundFloat {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(-20px, -20px) rotate(1deg); }
            50% { transform: translate(20px, -10px) rotate(-1deg); }
            75% { transform: translate(-10px, 20px) rotate(0.5deg); }
        }

        /* Form Container */
        .form-container {
            background: var(--white);
            border-radius: 2rem;
            box-shadow: var(--shadow-2xl);
            overflow: hidden;
            position: relative;
            z-index: 2;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .form-header {
            background: var(--gradient-primary);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .form-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .form-header-content {
            position: relative;
            z-index: 2;
        }

        .form-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .form-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 400;
        }

        /* Form Body */
        .form-body {
            padding: 3rem 2rem;
        }

        .form-group {
            margin-bottom: 2rem;
            position: relative;
        }

        .form-label {
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.75rem;
            display: block;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 1rem 1.5rem;
            border: 2px solid var(--gray-200);
            border-radius: 1rem;
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

        .form-control::placeholder {
            color: var(--gray-400);
        }

        /* Checkbox */
        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .form-check-input {
            width: 1.2rem;
            height: 1.2rem;
            margin-right: 0.75rem;
            accent-color: var(--primary);
        }

        .form-check-label {
            font-weight: 500;
            color: var(--gray-700);
            cursor: pointer;
        }

        /* Forgot Password Link */
        .forgot-password {
            text-align: right;
            margin-bottom: 1.5rem;
        }

        .forgot-password a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .forgot-password a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* Button */
        .btn-primary-custom {
            width: 100%;
            background: var(--gradient-secondary);
            border: none;
            padding: 1rem 2rem;
            border-radius: 1rem;
            font-weight: 700;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: white;
            transition: all 0.4s ease;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .btn-primary-custom::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.6s ease;
        }

        .btn-primary-custom:hover::before {
            left: 100%;
        }

        .btn-primary-custom:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 20px 40px rgba(245, 158, 11, 0.4);
        }

        /* Register Link */
        .register-link {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--gray-200);
        }

        .register-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .register-link a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* Social Login Section */
        .social-login-section {
            background: var(--gray-100);
            padding: 2rem;
            text-align: center;
        }

        .divider {
            position: relative;
            text-align: center;
            margin: 2rem 0;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--gray-300);
        }

        .divider span {
            background: var(--gray-100);
            padding: 0 1rem;
            color: var(--gray-600);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .social-buttons {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 1rem;
            border: 2px solid var(--gray-200);
            border-radius: 1rem;
            background: var(--white);
            color: var(--gray-700);
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .social-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.5s ease;
        }

        .social-btn:hover::before {
            left: 100%;
        }

        .social-btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .social-btn i {
            font-size: 1.2rem;
        }

        /* Google Button */
        .google-btn {
            border-color: #ea4335;
            color: #ea4335;
        }

        .google-btn:hover {
            background: #ea4335;
            color: white;
            border-color: #ea4335;
        }

        /* Facebook Button */
        .facebook-btn {
            border-color: #1877f2;
            color: #1877f2;
        }

        .facebook-btn:hover {
            background: #1877f2;
            color: white;
            border-color: #1877f2;
        }

        /* GitHub Button */
        .github-btn {
            border-color: #333;
            color: #333;
        }

        .github-btn:hover {
            background: #333;
            color: white;
            border-color: #333;
        }

        /* LinkedIn Button */
        .linkedin-btn {
            border-color: #0077b5;
            color: #0077b5;
        }

        .linkedin-btn:hover {
            background: #0077b5;
            color: white;
            border-color: #0077b5;
        }

        /* Responsive Social Buttons */
        @media (max-width: 480px) {
            .social-buttons {
                grid-template-columns: 1fr;
            }
        }

        /* Error Messages */
        .alert {
            border-radius: 1rem;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        /* Navigation */
        .navbar {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: transparent;
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: white !important;
            text-decoration: none;
        }

        .navbar-brand:hover {
            color: var(--gray-200) !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-container {
                margin: 1rem;
                border-radius: 1.5rem;
            }
            
            .form-header {
                padding: 2rem 1.5rem;
            }
            
            .form-title {
                font-size: 2rem;
            }
            
            .form-body {
                padding: 2rem 1.5rem;
            }
            
            .social-login-section {
                padding: 2rem 1.5rem;
            }
        }

        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .slide-in {
            animation: slideIn 0.6s ease-in-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>
</head>
<body class="<?php echo $lang === 'ar' ? 'rtl' : ''; ?>">
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-rocket me-2"></i>
                <?php echo $lang === 'en' ? PLATFORM_NAME : PLATFORM_NAME_AR; ?>
            </a>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="main-container">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-8 col-xl-6">
                    <div class="form-container fade-in">
                        <!-- Form Header -->
                        <div class="form-header">
                            <div class="form-header-content">
                                <h1 class="form-title" data-aos="fade-up">
                                    <?php echo $current_content['form_title']; ?>
                                </h1>
                                <p class="form-subtitle" data-aos="fade-up" data-aos-delay="100">
                                    <?php echo $current_content['subtitle']; ?>
                                </p>
                            </div>
                        </div>

                        <!-- Form Body -->
                        <div class="form-body">
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger fade-in">
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="" data-aos="fade-up" data-aos-delay="200">
                                <div class="form-group">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-2"></i>
                                        <?php echo $current_content['email_label']; ?>
                                    </label>
                                    <input type="email" 
                                           id="email" 
                                           name="email" 
                                           class="form-control" 
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                           placeholder="<?php echo $lang === 'en' ? 'Enter your email address' : 'أدخل عنوان بريدك الإلكتروني'; ?>"
                                           required>
                                </div>

                                <div class="form-group">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock me-2"></i>
                                        <?php echo $current_content['password_label']; ?>
                                    </label>
                                    <input type="password" 
                                           id="password" 
                                           name="password" 
                                           class="form-control" 
                                           placeholder="<?php echo $lang === 'en' ? 'Enter your password' : 'أدخل كلمة المرور'; ?>"
                                           required>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input type="checkbox" id="remember" name="remember" class="form-check-input">
                                        <label for="remember" class="form-check-label">
                                            <?php echo $current_content['remember_me']; ?>
                                        </label>
                                    </div>
                                    <div class="forgot-password">
                                        <a href="#"><?php echo $current_content['forgot_password']; ?></a>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary-custom">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    <?php echo $current_content['login_btn']; ?>
                                </button>
                            </form>

                            <div class="register-link">
                                <p class="mb-0">
                                    <?php echo $current_content['register_link']; ?>
                                    <a href="register.php"><?php echo $current_content['register_btn']; ?></a>
                                </p>
                            </div>
                        </div>

                        <!-- Social Login Section -->
                        <div class="social-login-section">
                            <div class="divider">
                                <span><?php echo $lang === 'en' ? 'Or continue with' : 'أو استمر بـ'; ?></span>
                            </div>
                            
                            <div class="social-buttons">
                                <button class="social-btn google-btn" onclick="socialLogin('google')" data-aos="fade-up" data-aos-delay="100">
                                    <i class="fab fa-google"></i>
                                    <span><?php echo $lang === 'en' ? 'Google' : 'جوجل'; ?></span>
                                </button>
                                
                                <button class="social-btn facebook-btn" onclick="socialLogin('facebook')" data-aos="fade-up" data-aos-delay="200">
                                    <i class="fab fa-facebook-f"></i>
                                    <span><?php echo $lang === 'en' ? 'Facebook' : 'فيسبوك'; ?></span>
                                </button>
                                
                                <button class="social-btn github-btn" onclick="socialLogin('github')" data-aos="fade-up" data-aos-delay="300">
                                    <i class="fab fa-github"></i>
                                    <span><?php echo $lang === 'en' ? 'GitHub' : 'جيت هب'; ?></span>
                                </button>
                                
                                <button class="social-btn linkedin-btn" onclick="socialLogin('linkedin')" data-aos="fade-up" data-aos-delay="400">
                                    <i class="fab fa-linkedin-in"></i>
                                    <span><?php echo $lang === 'en' ? 'LinkedIn' : 'لينكد إن'; ?></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });

        // Form enhancement
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });

        // Show welcome message
        setTimeout(() => {
            showToast('<?php echo $lang === 'en' ? 'Welcome back to Profolio Elite!' : 'مرحباً بعودتك إلى إيليت المحفظة المهنية!'; ?>', 'success');
        }, 1000);

        // Social login function
        function socialLogin(provider) {
            // Show loading state
            const button = event.target.closest('.social-btn');
            const originalContent = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span><?php echo $lang === 'en' ? 'Connecting...' : 'جاري الاتصال...'; ?></span>';
            button.disabled = true;
            
            // Simulate social login process
            setTimeout(() => {
                showToast(`<?php echo $lang === 'en' ? 'Connecting to' : 'جاري الاتصال بـ'; ?> ${provider}...`, 'info');
                
                // Reset button after 2 seconds
                setTimeout(() => {
                    button.innerHTML = originalContent;
                    button.disabled = false;
                    showToast(`<?php echo $lang === 'en' ? 'Social login feature coming soon!' : 'ميزة تسجيل الدخول الاجتماعي قريباً!'; ?>`, 'success');
                }, 2000);
            }, 1000);
        }

        // Toast notification function
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'success' ? 'success' : type === 'info' ? 'info' : 'danger'} position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            toast.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'info' ? 'info-circle' : 'exclamation-circle'} me-2"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 5000);
        }
    </script>
</body>
</html> 