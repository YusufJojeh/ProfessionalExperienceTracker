<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get current language
$lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';

// Language content
$content = [
    'en' => [
        'title' => 'Add New Project',
        'project_info' => 'Project Information',
        'project_title' => 'Project Title',
        'project_title_placeholder' => 'Enter your project title',
        'project_description' => 'Project Description',
        'project_description_placeholder' => 'Describe your project in detail...',
        'project_category' => 'Category',
        'select_category' => 'Select a category',
        'project_status' => 'Status',
        'status_draft' => 'Draft',
        'status_published' => 'Published',
        'project_technologies' => 'Technologies Used',
        'technologies_placeholder' => 'e.g., HTML, CSS, JavaScript, React',
        'project_link' => 'Project Link (Optional)',
        'project_link_placeholder' => 'https://your-project.com',
        'github_link' => 'GitHub Link (Optional)',
        'github_link_placeholder' => 'https://github.com/username/project',
        'project_image' => 'Project Image',
        'choose_file' => 'Choose File',
        'no_file_chosen' => 'No file chosen',
        'add_project' => 'Add Project',
        'cancel' => 'Cancel',
        'project_added' => 'Project added successfully!',
        'project_error' => 'Failed to add project. Please try again.',
        'required_fields' => 'Please fill in all required fields.',
        'file_size_error' => 'File size must be less than 5MB.',
        'file_type_error' => 'Only JPG, PNG, and GIF files are allowed.',
        'back_to_dashboard' => 'Back to Dashboard',
        'preview_project' => 'Preview Project',
        'save_draft' => 'Save as Draft',
        'publish_project' => 'Publish Project'
    ],
    'ar' => [
        'title' => 'إضافة مشروع جديد',
        'project_info' => 'معلومات المشروع',
        'project_title' => 'عنوان المشروع',
        'project_title_placeholder' => 'أدخل عنوان مشروعك',
        'project_description' => 'وصف المشروع',
        'project_description_placeholder' => 'صف مشروعك بالتفصيل...',
        'project_category' => 'الفئة',
        'select_category' => 'اختر فئة',
        'project_status' => 'الحالة',
        'status_draft' => 'مسودة',
        'status_published' => 'منشور',
        'project_technologies' => 'التقنيات المستخدمة',
        'technologies_placeholder' => 'مثال: HTML, CSS, JavaScript, React',
        'project_link' => 'رابط المشروع (اختياري)',
        'project_link_placeholder' => 'https://your-project.com',
        'github_link' => 'رابط GitHub (اختياري)',
        'github_link_placeholder' => 'https://github.com/username/project',
        'project_image' => 'صورة المشروع',
        'choose_file' => 'اختر ملف',
        'no_file_chosen' => 'لم يتم اختيار ملف',
        'add_project' => 'إضافة المشروع',
        'cancel' => 'إلغاء',
        'project_added' => 'تم إضافة المشروع بنجاح!',
        'project_error' => 'فشل في إضافة المشروع. يرجى المحاولة مرة أخرى.',
        'required_fields' => 'يرجى ملء جميع الحقول المطلوبة.',
        'file_size_error' => 'يجب أن يكون حجم الملف أقل من 5 ميجابايت.',
        'file_type_error' => 'يُسمح فقط بملفات JPG و PNG و GIF.',
        'back_to_dashboard' => 'العودة إلى لوحة التحكم',
        'preview_project' => 'معاينة المشروع',
        'save_draft' => 'حفظ كمسودة',
        'publish_project' => 'نشر المشروع'
    ]
];

$current_content = $content[$lang];

// Check if editing a project
$edit_mode = isset($_GET['edit']) && $_GET['edit'] == '1';
$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$project = null;

if ($edit_mode && $project_id > 0) {
    // Get project data for editing
    $stmt = $conn->prepare("SELECT * FROM projects WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $project_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $project = $result->fetch_assoc();
        // Update content for edit mode
        $current_content['title'] = $lang === 'en' ? 'Edit Project' : 'تعديل المشروع';
        $current_content['add_project'] = $lang === 'en' ? 'Update Project' : 'تحديث المشروع';
        $current_content['project_added'] = $lang === 'en' ? 'Project updated successfully!' : 'تم تحديث المشروع بنجاح!';
        $current_content['project_error'] = $lang === 'en' ? 'Failed to update project. Please try again.' : 'فشل في تحديث المشروع. يرجى المحاولة مرة أخرى.';
        $current_content['save_draft'] = $lang === 'en' ? 'Update Draft' : 'تحديث المسودة';
        $current_content['publish_project'] = $lang === 'en' ? 'Update & Publish' : 'تحديث ونشر';
    } else {
        // Project not found or doesn't belong to user
        header('Location: dashboard.php');
        exit();
    }
    $stmt->close();
}

// Get categories
$stmt = $conn->prepare("SELECT * FROM categories ORDER BY name_en");
$stmt->execute();
$categories = $stmt->get_result();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category_id = (int)$_POST['category_id'];
    $status = $_POST['status'];
    $technologies = trim($_POST['technologies']);
    $project_link = trim($_POST['project_link']);
    $github_link = trim($_POST['github_link']);
    $user_id = $_SESSION['user_id'];
    
    // Validation
    $errors = [];
    if (empty($title)) {
        $errors[] = $lang === 'en' ? 'Project title is required.' : 'عنوان المشروع مطلوب.';
    }
    if (empty($description)) {
        $errors[] = $lang === 'en' ? 'Project description is required.' : 'وصف المشروع مطلوب.';
    }
    if ($category_id <= 0) {
        $errors[] = $lang === 'en' ? 'Please select a category.' : 'يرجى اختيار فئة.';
    }
    
    // Handle file upload
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowed_types)) {
            $errors[] = $current_content['file_type_error'];
        }
        
        if ($file['size'] > $max_size) {
            $errors[] = $current_content['file_size_error'];
        }
        
        if (empty($errors)) {
            $upload_dir = 'uploads/projects/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $file_extension;
            $image_path = $upload_dir . $filename;
            
            if (!move_uploaded_file($file['tmp_name'], $image_path)) {
                $errors[] = $lang === 'en' ? 'Failed to upload image.' : 'فشل في رفع الصورة.';
            }
        }
    }
    
    // Insert or update project if no errors
    if (empty($errors)) {
        if ($edit_mode && $project_id > 0) {
            // Update existing project
            if ($image_path) {
                // New image uploaded
                $stmt = $conn->prepare("UPDATE projects SET title = ?, description = ?, category_id = ?, status = ?, technologies = ?, project_link = ?, github_link = ?, image_path = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
                $stmt->bind_param("ssisssssii", $title, $description, $category_id, $status, $technologies, $project_link, $github_link, $image_path, $project_id, $user_id);
            } else {
                // Keep existing image
                $stmt = $conn->prepare("UPDATE projects SET title = ?, description = ?, category_id = ?, status = ?, technologies = ?, project_link = ?, github_link = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
                $stmt->bind_param("ssisssssi", $title, $description, $category_id, $status, $technologies, $project_link, $github_link, $project_id, $user_id);
            }
        } else {
            // Insert new project
            $stmt = $conn->prepare("INSERT INTO projects (user_id, title, description, category_id, status, technologies, project_link, github_link, image_path, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("ississsss", $user_id, $title, $description, $category_id, $status, $technologies, $project_link, $github_link, $image_path);
        }
        
        if ($stmt->execute()) {
            $success_message = $current_content['project_added'];
            if ($edit_mode) {
                // Redirect to dashboard after successful update
                header('Location: dashboard.php?success=project_updated');
                exit();
            } else {
                // Clear form data only for new projects
            $_POST = array();
            }
        } else {
            $error_message = $current_content['project_error'];
        }
        $stmt->close();
    } else {
        $error_message = implode('<br>', $errors);
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
            --gradient-secondary: linear-gradient(135deg, #f59e0b 0%, #f97316 50%, #ea580c 100%);
            --gradient-accent: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            --gradient-dark: linear-gradient(135deg, #000 0%, #222 50%, #444 100%);
            
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
            overflow-x: hidden;
        }

        .rtl {
            direction: rtl;
            text-align: right;
        }

        /* Navigation */
        .navbar {
            background: var(--white);
            box-shadow: var(--shadow-md);
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

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--gradient-primary);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 80%;
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
            background: var(--gradient-primary);
            color: white;
            padding: 3rem 0;
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
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 400;
        }

        /* Main Content */
        .main-content {
            padding: 2rem 0;
            min-height: calc(100vh - 200px);
        }

        /* Form Styles */
        .form-container {
            background: var(--white);
            border-radius: 1.5rem;
            padding: 3rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-200);
            position: relative;
            overflow: hidden;
        }

        .form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .form-section {
            margin-bottom: 2.5rem;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 1.5rem;
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title::before {
            content: '';
            width: 4px;
            height: 24px;
            background: var(--gradient-primary);
            border-radius: 2px;
        }

        .form-group {
            margin-bottom: 1.5rem;
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

        .form-control::placeholder {
            color: var(--gray-400);
        }

        .form-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }

        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }

        /* File Upload */
        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-upload-input {
            position: absolute;
            left: -9999px;
        }

        .file-upload-label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 1.5rem;
            border: 2px dashed var(--gray-300);
            border-radius: 0.75rem;
            background: var(--gray-50);
            color: var(--gray-600);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-upload-label:hover {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.05);
            color: var(--primary);
        }

        .file-upload-label i {
            font-size: 1.5rem;
        }

        .file-name {
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: var(--gray-500);
        }

        /* Buttons */
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

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.6s ease;
        }

        .btn:hover::before {
            left: 100%;
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

        .btn-secondary {
            background: var(--gradient-secondary);
            color: white;
        }

        .btn-secondary:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 20px 40px rgba(245, 158, 11, 0.4);
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

        /* Alerts */
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

        /* Preview Section */
        .preview-section {
            background: var(--gray-100);
            border-radius: 1.5rem;
            padding: 2rem;
            margin-top: 2rem;
            border: 1px solid var(--gray-200);
        }

        .preview-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 1rem;
        }

        .preview-content {
            background: var(--white);
            border-radius: 1rem;
            padding: 1.5rem;
            border: 1px solid var(--gray-200);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }
            
            .form-container {
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

        /* Loading Animation */
        .loading {
            display: none;
            align-items: center;
            gap: 0.5rem;
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="<?php echo $lang === 'ar' ? 'rtl' : ''; ?>">
    <!-- Navigation -->
    <?php include 'user_nav.php'; ?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="page-header-content">
                <h1 class="page-title" data-aos="fade-up">
                    <?php echo $current_content['title']; ?>
                </h1>
                <p class="page-subtitle" data-aos="fade-up" data-aos-delay="100">
                    <?php if ($edit_mode): ?>
                        <?php echo $lang === 'en' ? 'Update your project details and showcase your work' : 'حدث تفاصيل مشروعك وعرض عملك'; ?>
                    <?php else: ?>
                    <?php echo $lang === 'en' ? 'Create a new project to showcase your work' : 'أنشئ مشروعاً جديداً لعرض عملك'; ?>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Alerts -->
                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success" data-aos="fade-up">
                            <i class="fas fa-check-circle"></i>
                            <?php echo $success_message; ?>
                            <a href="portfolio.php?user=<?php echo $_SESSION['user_id']; ?>" class="btn btn-primary ms-3 mt-2">
                                <i class="fas fa-briefcase"></i> <?php echo $lang === 'en' ? 'View My Portfolio' : 'عرض محفظتي'; ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger" data-aos="fade-up">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Form Container -->
                    <div class="form-container" data-aos="fade-up" data-aos-delay="200">
                        <form method="POST" enctype="multipart/form-data" id="projectForm">
                            <?php if ($edit_mode && $project_id > 0): ?>
                                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                            <?php endif; ?>
                            <!-- Project Information Section -->
                            <div class="form-section">
                                <h2 class="section-title">
                                    <i class="fas fa-info-circle"></i>
                                    <?php echo $current_content['project_info']; ?>
                                </h2>
                                
                                <div class="form-group">
                                    <label class="form-label"><?php echo $current_content['project_title']; ?> *</label>
                                    <input type="text" class="form-control" name="title" 
                                           value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ($project ? htmlspecialchars($project['title']) : ''); ?>"
                                           placeholder="<?php echo $current_content['project_title_placeholder']; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label"><?php echo $current_content['project_description']; ?> *</label>
                                    <textarea class="form-control form-textarea" name="description" 
                                              placeholder="<?php echo $current_content['project_description_placeholder']; ?>" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ($project ? htmlspecialchars($project['description']) : ''); ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $current_content['project_category']; ?> *</label>
                                            <select class="form-control form-select" name="category_id" required>
                                                <option value=""><?php echo $current_content['select_category']; ?></option>
                                                <?php 
                                                $categories->data_seek(0); // Reset pointer to beginning
                                                while ($category = $categories->fetch_assoc()): 
                                                    $selected = (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) || 
                                                               ($project && $project['category_id'] == $category['id']);
                                                ?>
                                                    <option value="<?php echo $category['id']; ?>" 
                                                            <?php echo $selected ? 'selected' : ''; ?>>
                                                        <?php echo $lang === 'en' ? $category['name_en'] : $category['name_ar']; ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $current_content['project_status']; ?></label>
                                            <select class="form-control form-select" name="status">
                                                <?php 
                                                $status_selected = isset($_POST['status']) ? $_POST['status'] : ($project ? $project['status'] : 'planned');
                                                ?>
                                                <option value="planned" <?php echo ($status_selected === 'planned') ? 'selected' : ''; ?>>
                                                    <?php echo $lang === 'en' ? 'Planned' : 'مخطط'; ?>
                                                </option>
                                                <option value="ongoing" <?php echo ($status_selected === 'ongoing') ? 'selected' : ''; ?>>
                                                    <?php echo $lang === 'en' ? 'Ongoing' : 'قيد التنفيذ'; ?>
                                                </option>
                                                <option value="completed" <?php echo ($status_selected === 'completed') ? 'selected' : ''; ?>>
                                                    <?php echo $lang === 'en' ? 'Completed' : 'مكتمل'; ?>
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Project Details Section -->
                            <div class="form-section">
                                <h2 class="section-title">
                                    <i class="fas fa-cogs"></i>
                                    <?php echo $lang === 'en' ? 'Project Details' : 'تفاصيل المشروع'; ?>
                                </h2>
                                
                                <div class="form-group">
                                    <label class="form-label"><?php echo $current_content['project_technologies']; ?></label>
                                    <input type="text" class="form-control" name="technologies" 
                                           value="<?php echo isset($_POST['technologies']) ? htmlspecialchars($_POST['technologies']) : ($project ? htmlspecialchars($project['technologies']) : ''); ?>"
                                           placeholder="<?php echo $current_content['technologies_placeholder']; ?>">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $current_content['project_link']; ?></label>
                                            <input type="url" class="form-control" name="project_link" 
                                                   value="<?php echo isset($_POST['project_link']) ? htmlspecialchars($_POST['project_link']) : ($project ? htmlspecialchars($project['project_link']) : ''); ?>"
                                                   placeholder="<?php echo $current_content['project_link_placeholder']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label"><?php echo $current_content['github_link']; ?></label>
                                            <input type="url" class="form-control" name="github_link" 
                                                   value="<?php echo isset($_POST['github_link']) ? htmlspecialchars($_POST['github_link']) : ($project ? htmlspecialchars($project['github_link']) : ''); ?>"
                                                   placeholder="<?php echo $current_content['github_link_placeholder']; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label"><?php echo $current_content['project_image']; ?></label>
                                    <?php if ($edit_mode && $project && !empty($project['image_path']) && file_exists($project['image_path'])): ?>
                                        <div class="current-image mb-3">
                                            <label class="form-label"><?php echo $lang === 'en' ? 'Current Image:' : 'الصورة الحالية:'; ?></label>
                                            <div class="current-image-container">
                                                <img src="<?php echo htmlspecialchars($project['image_path']); ?>" alt="Current Project Image" style="max-width: 200px; max-height: 150px; border-radius: 0.5rem; border: 2px solid var(--gray-200);">
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="file-upload">
                                        <input type="file" class="file-upload-input" name="image" id="imageInput" accept="image/*">
                                        <label for="imageInput" class="file-upload-label">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <span><?php echo $edit_mode ? ($lang === 'en' ? 'Choose New File' : 'اختر ملف جديد') : $current_content['choose_file']; ?></span>
                                        </label>
                                        <div class="file-name" id="fileName"><?php echo $current_content['no_file_chosen']; ?></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview Section -->
                            <div class="preview-section" id="previewSection" style="display: none;">
                                <h3 class="preview-title">
                                    <i class="fas fa-eye me-2"></i>
                                    <?php echo $current_content['preview_project']; ?>
                                </h3>
                                <div class="preview-content" id="previewContent">
                                    <!-- Preview content will be generated here -->
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="btn-group">
                                <a href="dashboard.php" class="btn btn-outline">
                                    <i class="fas fa-arrow-left"></i>
                                    <?php echo $current_content['back_to_dashboard']; ?>
                                </a>
                                <button type="button" class="btn btn-outline" onclick="previewProject()">
                                    <i class="fas fa-eye"></i>
                                    <?php echo $current_content['preview_project']; ?>
                                </button>
                                <button type="submit" name="action" value="draft" class="btn btn-secondary">
                                    <i class="fas fa-save"></i>
                                    <?php echo $current_content['save_draft']; ?>
                                </button>
                                <button type="submit" name="action" value="publish" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                    <?php echo $current_content['publish_project']; ?>
                                </button>
                            </div>
                        </form>
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
        AOS.init({ duration: 1000, once: true, offset: 100 });

        // File upload handling
        document.getElementById('imageInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const fileName = document.getElementById('fileName');
            
            if (file) {
                fileName.textContent = file.name;
                fileName.style.color = 'var(--primary)';
            } else {
                fileName.textContent = '<?php echo $current_content['no_file_chosen']; ?>';
                fileName.style.color = 'var(--gray-500)';
            }
        });

        // Preview project function
        function previewProject() {
            const form = document.getElementById('projectForm');
            const formData = new FormData(form);
            const previewSection = document.getElementById('previewSection');
            const previewContent = document.getElementById('previewContent');
            
            // Get form values
            const title = formData.get('title') || 'Project Title';
            const description = formData.get('description') || 'Project description will appear here...';
            const technologies = formData.get('technologies') || 'Technologies used';
            
            // Generate preview HTML
            const previewHTML = `
                <div class="project-card">
                    <div class="project-image" style="height: 200px; background: var(--gradient-primary); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem; border-radius: 1rem 1rem 0 0;">
                        <i class="fas fa-code"></i>
                    </div>
                    <div class="project-content" style="padding: 1.5rem;">
                        <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--gray-900); margin-bottom: 1rem;">${title}</h3>
                        <p style="color: var(--gray-600); margin-bottom: 1.5rem; line-height: 1.6;">${description}</p>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1.5rem;">
                            ${technologies.split(',').map(tech => `<span style="padding: 0.25rem 0.75rem; background: var(--gray-100); color: var(--gray-700); border-radius: 1rem; font-size: 0.8rem;">${tech.trim()}</span>`).join('')}
                        </div>
                        <div style="display: flex; gap: 1rem;">
                            <button style="padding: 0.75rem 1.5rem; background: var(--gradient-primary); color: white; border: none; border-radius: 0.75rem; font-weight: 600;">Live Demo</button>
                            <button style="padding: 0.75rem 1.5rem; background: transparent; color: var(--primary); border: 2px solid var(--primary); border-radius: 0.75rem; font-weight: 600;">Source Code</button>
                        </div>
                    </div>
                </div>
            `;
            
            previewContent.innerHTML = previewHTML;
            previewSection.style.display = 'block';
            
            // Scroll to preview
            previewSection.scrollIntoView({ behavior: 'smooth' });
        }

        // Form validation
        document.getElementById('projectForm').addEventListener('submit', function(e) {
            const title = this.querySelector('input[name="title"]').value.trim();
            const description = this.querySelector('textarea[name="description"]').value.trim();
            const category = this.querySelector('select[name="category_id"]').value;
            
            if (!title || !description || !category) {
                e.preventDefault();
                showToast('<?php echo $current_content['required_fields']; ?>', 'error');
            }
        });

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
            
            setTimeout(() => {
                toast.remove();
            }, 4000);
        }

        // Auto-save draft functionality
        let autoSaveTimer;
        const form = document.getElementById('projectForm');
        
        form.addEventListener('input', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                // Here you could implement auto-save functionality
                console.log('Auto-saving draft...');
            }, 3000);
        });
    </script>
</body>
</html> 