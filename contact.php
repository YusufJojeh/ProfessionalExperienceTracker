<?php
session_start();
require_once 'config/database.php';

// Get current language
$lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';

// Language content
$content = [
    'en' => [
        'title' => 'Contact Us',
        'subtitle' => 'Get in touch with us',
        'contact_info' => 'Contact Information',
        'address' => 'Address',
        'email' => 'Email',
        'phone' => 'Phone',
        'send_message' => 'Send Message',
        'name' => 'Full Name',
        'email' => 'Email Address',
        'subject' => 'Subject',
        'message' => 'Message',
        'submit' => 'Send Message',
        'success_msg' => 'Message sent successfully! We\'ll get back to you soon.',
        'error_msg' => 'Failed to send message. Please try again.',
        'validation_errors' => [
            'name_required' => 'Name is required',
            'email_required' => 'Email is required',
            'email_invalid' => 'Please enter a valid email',
            'subject_required' => 'Subject is required',
            'message_required' => 'Message is required'
        ]
    ],
    'ar' => [
        'title' => 'اتصل بنا',
        'subtitle' => 'تواصل معنا',
        'contact_info' => 'معلومات الاتصال',
        'address' => 'العنوان',
        'email' => 'البريد الإلكتروني',
        'phone' => 'الهاتف',
        'send_message' => 'إرسال رسالة',
        'name' => 'الاسم الكامل',
        'email' => 'البريد الإلكتروني',
        'subject' => 'الموضوع',
        'message' => 'الرسالة',
        'submit' => 'إرسال الرسالة',
        'success_msg' => 'تم إرسال الرسالة بنجاح! سنتواصل معك قريباً.',
        'error_msg' => 'فشل في إرسال الرسالة. يرجى المحاولة مرة أخرى.',
        'validation_errors' => [
            'name_required' => 'الاسم مطلوب',
            'email_required' => 'البريد الإلكتروني مطلوب',
            'email_invalid' => 'يرجى إدخال بريد إلكتروني صحيح',
            'subject_required' => 'الموضوع مطلوب',
            'message_required' => 'الرسالة مطلوبة'
        ]
    ]
];

$current_content = $content[$lang];
$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $subject = sanitize_input($_POST['subject']);
    $message = sanitize_input($_POST['message']);

    // Validation
    if (empty($name)) {
        $errors[] = $current_content['validation_errors']['name_required'];
    }

    if (empty($email)) {
        $errors[] = $current_content['validation_errors']['email_required'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = $current_content['validation_errors']['email_invalid'];
    }

    if (empty($subject)) {
        $errors[] = $current_content['validation_errors']['subject_required'];
    }

    if (empty($message)) {
        $errors[] = $current_content['validation_errors']['message_required'];
    }

    // If no errors, process the contact form
    if (empty($errors)) {
        // In a real application, you would send an email here
        // For now, we'll just show a success message
        $success = true;
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
            --primary-color: #2563eb;
            --secondary-color: #7c3aed;
            --accent-color: #f59e0b;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f8fafc;
            --bg-white: #ffffff;
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            padding: 60px 0;
        }

        .contact-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 1000px;
            margin: 0 auto;
        }

        .contact-header {
            background: var(--gradient-primary);
            color: white;
            padding: 60px 40px;
            text-align: center;
        }

        .contact-header h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .contact-header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .contact-content {
            padding: 60px 40px;
        }

        .contact-info {
            background: var(--bg-light);
            border-radius: 15px;
            padding: 30px;
            height: 100%;
        }

        .contact-info h4 {
            color: var(--text-dark);
            font-weight: 700;
            margin-bottom: 25px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .contact-icon {
            width: 50px;
            height: 50px;
            background: var(--gradient-primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            margin-right: 15px;
        }

        .rtl .contact-icon {
            margin-right: 0;
            margin-left: 15px;
        }

        .contact-details h5 {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 5px;
        }

        .contact-details p {
            color: var(--text-light);
            margin: 0;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        .btn-submit {
            background: var(--gradient-primary);
            border: none;
            color: white;
            padding: 15px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 20px;
        }

        .animated-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .success-animation {
            text-align: center;
            padding: 40px;
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: #d1fae5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #065f46;
            font-size: 3rem;
            animation: bounce 1s ease-in-out;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-20px); }
            60% { transform: translateY(-10px); }
        }

        .map-container {
            height: 300px;
            background: var(--bg-light);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 30px;
        }

        .map-placeholder {
            text-align: center;
            color: var(--text-light);
        }

        .map-placeholder i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }
    </style>
</head>
<body class="<?php echo $lang === 'ar' ? 'rtl' : ''; ?>">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-briefcase me-2"></i>
                Professional Experience Tracker
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-1"></i>
                            <?php echo $lang === 'en' ? 'Home' : 'الرئيسية'; ?>
                        </a>
                    </li>
                    <?php if (is_logged_in()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-1"></i>
                                <?php echo $lang === 'en' ? 'Dashboard' : 'لوحة التحكم'; ?>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>
                                <?php echo $lang === 'en' ? 'Login' : 'تسجيل الدخول'; ?>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="contact-container">
                <div class="contact-header">
                    <svg class="animated-icon" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="50" cy="50" r="45" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="2"/>
                        <circle cx="50" cy="50" r="35" fill="none" stroke="rgba(255,255,255,0.5)" stroke-width="2"/>
                        <circle cx="50" cy="50" r="25" fill="rgba(255,255,255,0.2)"/>
                        <path d="M35 50 L45 60 L65 40" stroke="white" stroke-width="3" fill="none"/>
                    </svg>
                    <h2><?php echo $current_content['title']; ?></h2>
                    <p><?php echo $current_content['subtitle']; ?></p>
                </div>
                
                <div class="contact-content">
                    <?php if ($success): ?>
                        <div class="success-animation">
                            <div class="success-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <h3 class="text-success mb-3"><?php echo $current_content['success_msg']; ?></h3>
                            <a href="index.php" class="btn btn-submit">
                                <i class="fas fa-home me-2"></i>
                                <?php echo $lang === 'en' ? 'Back to Home' : 'العودة للرئيسية'; ?>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <div class="col-lg-4 mb-4">
                                <div class="contact-info">
                                    <h4><?php echo $current_content['contact_info']; ?></h4>
                                    
                                    <div class="contact-item">
                                        <div class="contact-icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div class="contact-details">
                                            <h5><?php echo $current_content['address']; ?></h5>
                                            <p><?php echo $lang === 'en' ? '123 Business Street, Tech City, TC 12345' : '١٢٣ شارع الأعمال، مدينة التقنية، تي سي ١٢٣٤٥'; ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="contact-item">
                                        <div class="contact-icon">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <div class="contact-details">
                                            <h5><?php echo $current_content['email']; ?></h5>
                                            <p>contact@professionaltracker.com</p>
                                        </div>
                                    </div>
                                    
                                    <div class="contact-item">
                                        <div class="contact-icon">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <div class="contact-details">
                                            <h5><?php echo $current_content['phone']; ?></h5>
                                            <p>+1 (555) 123-4567</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-8">
                                <?php if (!empty($errors)): ?>
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <ul class="mb-0">
                                            <?php foreach ($errors as $error): ?>
                                                <li><?php echo $error; ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                                <form method="POST" action="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label"><?php echo $current_content['name']; ?></label>
                                                <input type="text" name="name" class="form-control" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label"><?php echo $current_content['email']; ?></label>
                                                <input type="email" name="email" class="form-control" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label"><?php echo $current_content['subject']; ?></label>
                                        <input type="text" name="subject" class="form-control" value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label"><?php echo $current_content['message']; ?></label>
                                        <textarea name="message" class="form-control" rows="6" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-submit">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        <?php echo $current_content['submit']; ?>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Map Section -->
                        <div class="map-container">
                            <div class="map-placeholder">
                                <i class="fas fa-map"></i>
                                <h5><?php echo $lang === 'en' ? 'Interactive Map Coming Soon' : 'خريطة تفاعلية قريباً'; ?></h5>
                                <p><?php echo $lang === 'en' ? 'We\'re working on adding an interactive map to show our location.' : 'نحن نعمل على إضافة خريطة تفاعلية لعرض موقعنا.'; ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);

        // Form animation
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('.btn-submit');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i><?php echo $lang === 'en' ? 'Sending...' : 'جاري الإرسال...'; ?>';
                submitBtn.disabled = true;
            });
        }
    </script>
</body>
</html> 