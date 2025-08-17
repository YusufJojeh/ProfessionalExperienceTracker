<?php
session_start();
require_once 'config/database.php';

// Get current language
$lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';

// Language content
$content = [
    'en' => [
        'title' => 'Profolio Elite',
        'subtitle' => 'Where Professional Excellence Meets Digital Innovation',
        'hero_title' => 'Transform Your Professional Journey Into Digital Excellence',
        'hero_subtitle' => 'Create stunning portfolios, showcase your expertise, and connect with opportunities worldwide. The ultimate platform for professionals who demand excellence.',
        'features' => [
            'Smart Portfolio Builder' => 'Create a portfolio with stunning templates',
            'Real-time Analytics' => 'Track your portfolio performance and visitor insights',
            'Global Networking' => 'Connect with professionals and opportunities worldwide',
            'Advanced Security' => 'Enterprise-grade security for your professional data',
            'Mobile-First Design' => 'Perfect experience across all devices and platforms',
            'Instant Publishing' => 'Go live with your portfolio in seconds'
        ],
        'cta_primary' => 'Start Your Journey',
        'cta_secondary' => 'View Showcase',
        'stats' => [
            'users' => 'Active Professionals',
            'projects' => 'Portfolios Created',
            'countries' => 'Global Reach',
            'satisfaction' => 'Client Satisfaction'
        ],
        'testimonials' => [
            [
                'name' => 'Alex Chen',
                'role' => 'Senior Developer',
                'text' => 'This platform revolutionized how I showcase my work. The analytics helped me understand what clients love most!'
            ],
            [
                'name' => 'Sarah Johnson',
                'role' => 'UX Designer',
                'text' => 'The portfolio builder is incredibly intuitive. I created my professional showcase in under 30 minutes!'
            ],
            [
                'name' => 'Marcus Rodriguez',
                'role' => 'Digital Marketer',
                'text' => 'The networking features opened doors I never knew existed. This is the future of professional branding.'
            ]
        ]
    ],
    'ar' => [
        'title' => 'إيليت المحفظة المهنية',
        'subtitle' => 'حيث يلتقي التميز المهني بالابتكار الرقمي',
        'hero_title' => 'حوّل رحلتك المهنية إلى تميز رقمي',
        'hero_subtitle' => 'أنشئ محافظ مذهلة، وعرّف بخبراتك، وتواصل مع الفرص حول العالم. المنصة المثالية للمهنيين الباحثين عن التميز.',
        'features' => [
            'منشئ المحفظة الذكي' => 'أنشئ محفظة باستخدام قوالب مذهلة',
            'التحليلات المباشرة' => 'تتبع أداء محفظتك ورؤى الزوار',
            'الشبكات العالمية' => 'تواصل مع المهنيين والفرص في جميع أنحاء العالم',
            'الأمان المتقدم' => 'أمان على مستوى المؤسسات لبياناتك المهنية',
            'تصميم محمول أولاً' => 'تجربة مثالية عبر جميع الأجهزة والمنصات',
            'النشر الفوري' => 'انطلق بمحفظتك في ثوانٍ'
        ],
        'cta_primary' => 'ابدأ رحلتك',
        'cta_secondary' => 'عرض المعرض',
        'stats' => [
            'users' => 'مهني نشط',
            'projects' => 'محفظة تم إنشاؤها',
            'countries' => 'الوصول العالمي',
            'satisfaction' => 'رضا العملاء'
        ],
        'testimonials' => [
            [
                'name' => 'أليكس تشين',
                'role' => 'مطور كبير',
                'text' => 'هذه المنصة ثورت طريقة عرض عملي. التحليلات ساعدتني في فهم ما يحبه العملاء أكثر!'
            ],
            [
                'name' => 'سارة جونسون',
                'role' => 'مصممة تجربة المستخدم',
                'text' => 'منشئ المحفظة بديهي للغاية. أنشأت معرضي المهني في أقل من 30 دقيقة!'
            ],
            [
                'name' => 'ماركوس رودريغيز',
                'role' => 'مسوق رقمي',
                'text' => 'ميزات الشبكات فتحت أبواباً لم أعرف أنها موجودة. هذا هو مستقبل العلامة التجارية المهنية.'
            ]
        ]
    ]
];

$current_content = $content[$lang];
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $current_content['title']; ?></title>
    
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
            overflow-x: hidden;
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
            width: 100%;
            height: 2px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover::after {
            transform: scaleX(1);
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
            transition: left 0.3s ease;
            z-index: -1;
            opacity: 0.1;
        }

        .nav-link:hover::before {
            left: 0;
        }

        .nav-link:hover {
            color: var(--primary) !important;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Revolutionary Hero Section */
        .hero-section {
            min-height: 100vh;
            background: var(--gradient-hero);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(37, 99, 235, 0.4) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(6, 182, 212, 0.4) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(249, 115, 22, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 60% 60%, rgba(59, 130, 246, 0.3) 0%, transparent 50%);
            animation: heroFloat 20s ease-in-out infinite;
        }

        @keyframes heroFloat {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(-30px, -30px) rotate(2deg); }
            50% { transform: translate(30px, -20px) rotate(-2deg); }
            75% { transform: translate(-20px, 30px) rotate(1deg); }
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            max-width: 1000px;
            padding: 0 2rem;
        }

        .hero-title {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 900;
            margin-bottom: 1.5rem;
            line-height: 1.1;
            background: linear-gradient(135deg, #ffffff 0%, #e0f2fe 50%, #f0f9ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .hero-subtitle {
            font-size: clamp(1.1rem, 2.5vw, 1.35rem);
            margin-bottom: 3rem;
            opacity: 0.95;
            font-weight: 400;
            line-height: 1.7;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Enhanced Animated Icons */
        .hero-icon {
            width: 140px;
            height: 140px;
            margin: 0 auto 2rem;
            animation: iconFloat 8s ease-in-out infinite;
            filter: drop-shadow(0 25px 50px rgba(0, 0, 0, 0.4));
        }

        @keyframes iconFloat {
            0%, 100% { transform: translateY(0px) rotate(0deg) scale(1); }
            25% { transform: translateY(-25px) rotate(8deg) scale(1.08); }
            50% { transform: translateY(-35px) rotate(0deg) scale(1.12); }
            75% { transform: translateY(-25px) rotate(-8deg) scale(1.08); }
        }

        /* Revolutionary Buttons */
        .btn-primary-custom {
            background: var(--gradient-secondary);
            border: none;
            padding: 1.25rem 3rem;
            border-radius: 3rem;
            font-weight: 700;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: white;
            transition: all 0.4s ease;
            box-shadow: var(--shadow-xl), var(--shadow-glow-secondary);
            position: relative;
            overflow: hidden;
            margin: 0.5rem;
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
            transform: translateY(-8px) scale(1.05);
            box-shadow: 0 30px 60px rgba(249, 115, 22, 0.5), var(--shadow-glow-secondary);
        }

        .btn-secondary-custom {
            background: var(--gradient-glass);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 1.25rem 3rem;
            border-radius: 3rem;
            font-weight: 700;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            margin: 0.5rem;
            backdrop-filter: blur(10px);
        }

        .btn-secondary-custom::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            transition: left 0.4s ease;
            z-index: -1;
        }

        .btn-secondary-custom:hover::before {
            left: 0;
        }

        .btn-secondary-custom:hover {
            border-color: rgba(255, 255, 255, 0.6);
            transform: translateY(-8px) scale(1.05);
            box-shadow: 0 25px 50px rgba(255, 255, 255, 0.2);
        }

        /* Enhanced Features Section */
        .features-section {
            padding: 10rem 0;
            background: var(--white);
            position: relative;
        }

        .features-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: linear-gradient(to bottom, var(--light), transparent);
        }

        .section-title {
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            font-weight: 800;
            text-align: center;
            margin-bottom: 1.5rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--gradient-primary);
            border-radius: 2px;
        }

        .section-subtitle {
            font-size: 1.3rem;
            text-align: center;
            color: var(--gray-600);
            margin-bottom: 5rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.7;
        }

        .feature-card {
            background: var(--white);
            padding: 3.5rem 2.5rem;
            border-radius: 2rem;
            box-shadow: var(--shadow-lg);
            transition: all 0.5s ease;
            height: 100%;
            border: 1px solid var(--gray-200);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transition: transform 0.5s ease;
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--gradient-primary);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .feature-card:hover::after {
            opacity: 0.02;
        }

        .feature-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: var(--shadow-2xl), var(--shadow-glow);
            border-color: var(--primary);
        }

        .feature-icon {
            width: 90px;
            height: 90px;
            background: var(--gradient-primary);
            border-radius: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2.5rem;
            font-size: 2.2rem;
            color: white;
            transition: all 0.5s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-icon::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        .feature-card:hover .feature-icon::before {
            left: 100%;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.15) rotate(8deg);
            box-shadow: 0 20px 40px rgba(37, 99, 235, 0.4);
        }

        .feature-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 1.25rem;
            text-align: center;
        }

        .feature-description {
            color: var(--gray-600);
            text-align: center;
            line-height: 1.7;
            font-size: 1.05rem;
        }

        /* Enhanced Stats Section */
        .stats-section {
            padding: 8rem 0;
            background: var(--gradient-dark);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .stats-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(37, 99, 235, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(6, 182, 212, 0.1) 0%, transparent 50%);
        }

        .stat-item {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
            z-index: 2;
        }

        .stat-number {
            font-size: clamp(3rem, 6vw, 5rem);
            font-weight: 900;
            margin-bottom: 0.75rem;
            display: block;
            background: var(--gradient-secondary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .stat-label {
            font-size: 1.2rem;
            opacity: 0.95;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        /* Enhanced Testimonials */
        .testimonials-section {
            padding: 10rem 0;
            background: var(--gray-50);
            position: relative;
        }

        .testimonial-card {
            background: var(--white);
            padding: 3.5rem 2.5rem;
            border-radius: 2rem;
            box-shadow: var(--shadow-lg);
            margin: 1rem 0;
            position: relative;
            border-left: 5px solid var(--primary);
            transition: all 0.4s ease;
        }

        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: 1rem;
            left: 2rem;
            font-size: 4rem;
            color: var(--primary);
            opacity: 0.1;
            font-family: serif;
        }

        .testimonial-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-2xl);
        }

        .testimonial-text {
            font-style: italic;
            margin-bottom: 2.5rem;
            font-size: 1.15rem;
            line-height: 1.7;
            color: var(--gray-700);
            position: relative;
            z-index: 2;
        }

        .testimonial-author {
            font-weight: 700;
            color: var(--primary);
            font-size: 1.2rem;
        }

        .testimonial-role {
            color: var(--gray-500);
            font-size: 1rem;
            margin-top: 0.25rem;
        }

        /* Enhanced Footer */
        .footer {
            background: var(--gray-900);
            color: white;
            padding: 5rem 0 2rem;
            position: relative;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--gradient-primary);
        }

        .footer h5 {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
        }

        .social-links a {
            display: inline-block;
            width: 50px;
            height: 50px;
            background: var(--gradient-primary);
            border-radius: 50%;
            text-align: center;
            line-height: 50px;
            color: white;
            margin: 0 0.75rem;
            transition: all 0.4s ease;
            font-size: 1.2rem;
        }

        .social-links a:hover {
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 15px 30px rgba(37, 99, 235, 0.4);
        }

        /* Enhanced Language Switcher */
        .language-switcher {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
        }

        .lang-btn {
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid var(--primary);
            padding: 0.875rem 1.75rem;
            border-radius: 2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-md);
            color: var(--primary);
        }

        .lang-btn:hover {
            background: var(--primary);
            color: white;
            transform: scale(1.05);
            box-shadow: var(--shadow-lg), var(--shadow-glow);
        }

        /* Enhanced Toast Notifications */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .custom-toast {
            background: var(--white);
            border-radius: 1rem;
            box-shadow: var(--shadow-xl);
            border-left: 4px solid var(--primary);
            animation: slideIn 0.4s ease;
            margin-bottom: 1rem;
            backdrop-filter: blur(10px);
        }

        @keyframes slideIn {
            from { transform: translateX(100%) scale(0.9); opacity: 0; }
            to { transform: translateX(0) scale(1); opacity: 1; }
        }

        /* Responsive Enhancements */
        @media (max-width: 768px) {
            .hero-content {
                padding: 0 1rem;
            }
            
            .btn-primary-custom,
            .btn-secondary-custom {
                padding: 1rem 2.5rem;
                font-size: 1rem;
            }
            
            .feature-card {
                padding: 2.5rem 2rem;
            }
            
            .testimonial-card {
                padding: 2.5rem 2rem;
            }
        }

        /* Additional Animations */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        /* Glass Morphism Effects */
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="<?php echo $lang === 'ar' ? 'rtl' : ''; ?>">
    <!-- Language Switcher -->
    <div class="language-switcher">
        <button class="lang-btn" onclick="switchLanguage('<?php echo $lang === 'en' ? 'ar' : 'en'; ?>')">
            <?php echo $lang === 'en' ? 'العربية' : 'English'; ?>
        </button>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

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
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features"><?php echo $lang === 'en' ? 'Features' : 'المميزات'; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials"><?php echo $lang === 'en' ? 'Testimonials' : 'الشهادات'; ?></a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php"><?php echo $lang === 'en' ? 'Dashboard' : 'لوحة التحكم'; ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="browse_users.php"><?php echo $lang === 'en' ? 'Discover' : 'اكتشف'; ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php"><?php echo $lang === 'en' ? 'Logout' : 'تسجيل الخروج'; ?></a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php"><?php echo $lang === 'en' ? 'Login' : 'تسجيل الدخول'; ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php"><?php echo $lang === 'en' ? 'Register' : 'التسجيل'; ?></a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-bg"></div>
        <div class="hero-content">
            <svg class="hero-icon" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="iconGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:#ffffff;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#e2e8f0;stop-opacity:1" />
                    </linearGradient>
                </defs>
                <circle cx="50" cy="50" r="45" fill="none" stroke="url(#iconGradient)" stroke-width="3"/>
                <circle cx="50" cy="50" r="35" fill="none" stroke="url(#iconGradient)" stroke-width="2" opacity="0.7"/>
                <circle cx="50" cy="50" r="25" fill="url(#iconGradient)" opacity="0.3"/>
                <path d="M35 50 L45 60 L65 40" stroke="url(#iconGradient)" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            
            <h1 class="hero-title" data-aos="fade-up"><?php echo $current_content['hero_title']; ?></h1>
            <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="200"><?php echo $current_content['hero_subtitle']; ?></p>
            
            <div class="d-flex flex-wrap justify-content-center gap-3" data-aos="fade-up" data-aos-delay="400">
                <a href="register.php" class="btn btn-primary-custom">
                    <i class="fas fa-rocket me-2"></i>
                    <?php echo $current_content['cta_primary']; ?>
                </a>
                <a href="#features" class="btn btn-secondary-custom">
                    <i class="fas fa-play me-2"></i>
                    <?php echo $current_content['cta_secondary']; ?>
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">
                <?php echo $lang === 'en' ? 'Why Choose Profolio Elite?' : 'لماذا تختار إيليت المحفظة المهنية؟'; ?>
            </h2>
            <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">
                <?php echo $lang === 'en' ? 'Everything you need to showcase your professional excellence' : 'كل ما تحتاجه لعرض تميزك المهني'; ?>
            </p>
            
            <div class="row g-4">
                <?php 
                $icons = ['fas fa-magic', 'fas fa-chart-line', 'fas fa-globe', 'fas fa-shield-alt', 'fas fa-mobile-alt', 'fas fa-bolt'];
                $i = 0;
                foreach ($current_content['features'] as $title => $description): 
                ?>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $i * 100; ?>">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="<?php echo $icons[$i]; ?>"></i>
                        </div>
                        <h4 class="feature-title"><?php echo $title; ?></h4>
                        <p class="feature-description"><?php echo $description; ?></p>
                    </div>
                </div>
                <?php $i++; endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <?php 
                $stats = [12500, 34000, 150, 98];
                $i = 0;
                foreach ($current_content['stats'] as $key => $label): 
                ?>
                <div class="col-lg-3 col-md-6" data-aos="zoom-in" data-aos-delay="<?php echo $i * 100; ?>">
                    <div class="stat-item">
                        <span class="stat-number" data-count="<?php echo $stats[$i]; ?>">0</span>
                        <div class="stat-label"><?php echo $label; ?></div>
                    </div>
                </div>
                <?php $i++; endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials-section">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">
                <?php echo $lang === 'en' ? 'What Our Users Say' : 'ماذا يقول مستخدمونا'; ?>
            </h2>
            
            <div class="row">
                <?php foreach ($current_content['testimonials'] as $index => $testimonial): ?>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                    <div class="testimonial-card">
                        <div class="testimonial-text">"<?php echo $testimonial['text']; ?>"</div>
                        <div class="testimonial-author"><?php echo $testimonial['name']; ?></div>
                        <div class="testimonial-role"><?php echo $testimonial['role']; ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h5><?php echo $lang === 'en' ? PLATFORM_NAME : PLATFORM_NAME_AR; ?></h5>
                    <p class="text-muted">
                        <?php echo $lang === 'en' ? 'Empowering professionals to showcase their excellence and connect with opportunities worldwide.' : 'تمكين المهنيين لعرض تميزهم والتواصل مع الفرص في جميع أنحاء العالم.'; ?>
                    </p>
                </div>
                <div class="col-lg-6 text-end">
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="text-muted mb-0">
                        &copy; <?php echo date('Y'); ?> <?php echo $lang === 'en' ? PLATFORM_NAME : PLATFORM_NAME_AR; ?>. 
                        <?php echo $lang === 'en' ? 'All rights reserved.' : 'جميع الحقوق محفوظة.'; ?>
                    </p>
                </div>
            </div>
        </div>
    </footer>

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

        // Animated counters
        function animateCounters() {
            const counters = document.querySelectorAll('.stat-number');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-count'));
                const increment = target / 100;
                let current = 0;
                
                const updateCounter = () => {
                    if (current < target) {
                        current += increment;
                        counter.textContent = Math.ceil(current);
                        setTimeout(updateCounter, 20);
                    } else {
                        counter.textContent = target;
                    }
                };
                
                updateCounter();
            });
        }

        // Trigger counters when stats section is visible
        const statsSection = document.querySelector('.stats-section');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.unobserve(entry.target);
                }
            });
        });
        observer.observe(statsSection);

        // Language switcher
        function switchLanguage(lang) {
            fetch('multilanguage.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'language=' + lang
            }).then(() => {
                window.location.reload();
            });
        }

        // Show welcome toast
        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = 'custom-toast p-3';
            toast.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-${type === 'success' ? 'check-circle text-success' : 'info-circle text-info'} me-2"></i>
                    <span>${message}</span>
                </div>
            `;
            toastContainer.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 5000);
        }

        // Show welcome message
        setTimeout(() => {
            showToast('<?php echo $lang === 'en' ? 'Welcome to Profolio Elite!' : 'مرحباً بك في إيليت المحفظة المهنية!'; ?>');
        }, 1000);

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html> 