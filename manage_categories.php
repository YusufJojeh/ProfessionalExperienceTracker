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
        'title' => 'Manage Categories',
        'add_category' => 'Add Category',
        'name_en' => 'Name (English)',
        'name_ar' => 'Name (Arabic)',
        'description' => 'Description',
        'icon' => 'Icon',
        'actions' => 'Actions',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'confirm_delete' => 'Are you sure you want to delete this category?',
        'category_added' => 'Category added successfully!',
        'category_updated' => 'Category updated successfully!',
        'category_deleted' => 'Category deleted successfully!',
        'category_error' => 'An error occurred. Please try again.',
        'no_categories' => 'No categories found.',
        'projects_count' => 'Projects'
    ],
    'ar' => [
        'title' => 'إدارة الفئات',
        'add_category' => 'إضافة فئة',
        'name_en' => 'الاسم (الإنجليزية)',
        'name_ar' => 'الاسم (العربية)',
        'description' => 'الوصف',
        'icon' => 'الأيقونة',
        'actions' => 'الإجراءات',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'save' => 'حفظ',
        'cancel' => 'إلغاء',
        'confirm_delete' => 'هل أنت متأكد من حذف هذه الفئة؟',
        'category_added' => 'تم إضافة الفئة بنجاح!',
        'category_updated' => 'تم تحديث الفئة بنجاح!',
        'category_deleted' => 'تم حذف الفئة بنجاح!',
        'category_error' => 'حدث خطأ. حاول مرة أخرى.',
        'no_categories' => 'لا توجد فئات.',
        'projects_count' => 'المشاريع'
    ]
];
$current_content = $content[$lang];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        $name_en = mysqli_real_escape_string($conn, $_POST['name_en']);
        $name_ar = mysqli_real_escape_string($conn, $_POST['name_ar']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $icon = mysqli_real_escape_string($conn, $_POST['icon']);
        
        $query = "INSERT INTO categories (name_en, name_ar, description, icon) VALUES ('$name_en', '$name_ar', '$description', '$icon')";
        if (mysqli_query($conn, $query)) {
            $success_message = $current_content['category_added'];
        } else {
            $error_message = $current_content['category_error'];
        }
    } elseif (isset($_POST['update_category'])) {
        $category_id = (int)$_POST['category_id'];
        $name_en = mysqli_real_escape_string($conn, $_POST['name_en']);
        $name_ar = mysqli_real_escape_string($conn, $_POST['name_ar']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $icon = mysqli_real_escape_string($conn, $_POST['icon']);
        
        $query = "UPDATE categories SET name_en = '$name_en', name_ar = '$name_ar', description = '$description', icon = '$icon' WHERE id = $category_id";
        if (mysqli_query($conn, $query)) {
            $success_message = $current_content['category_updated'];
        } else {
            $error_message = $current_content['category_error'];
        }
    } elseif (isset($_POST['delete_category'])) {
        $category_id = (int)$_POST['delete_category'];
        if (mysqli_query($conn, "DELETE FROM categories WHERE id = $category_id")) {
            $success_message = $current_content['category_deleted'];
        } else {
            $error_message = $current_content['category_error'];
        }
    }
}

// Get categories with project count
$categories_query = "SELECT c.*, COUNT(p.id) as project_count 
                    FROM categories c 
                    LEFT JOIN projects p ON c.id = p.category_id 
                    GROUP BY c.id 
                    ORDER BY c.name_en";
$categories_result = mysqli_query($conn, $categories_query);
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
        .btn-outline {
            background: transparent;
            color: var(--gray-700);
            border: 2px solid var(--gray-300);
            border-radius: 0.75rem;
            font-weight: 600;
            padding: 0.5rem 1.2rem;
            transition: all 0.3s;
        }
        .btn-outline:hover {
            background: var(--gray-100);
            border-color: var(--gray-400);
            color: var(--gray-800);
            transform: translateY(-2px);
        }
        .category-card {
            background: var(--gray-50);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
        }
        .category-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        .category-icon {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        .modal-content {
            border-radius: 1rem;
            border: none;
            box-shadow: var(--shadow-xl);
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
    
    <div class="admin-section" data-aos="fade-up" data-aos-delay="100">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><?php echo $current_content['title']; ?></h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fas fa-plus me-2"></i><?php echo $current_content['add_category']; ?>
            </button>
        </div>
        
        <div class="row">
            <?php if (mysqli_num_rows($categories_result) === 0): ?>
                <div class="col-12 text-center text-muted py-5">
                    <i class="fas fa-tags fa-3x mb-3"></i>
                    <div><?php echo $current_content['no_categories']; ?></div>
                </div>
            <?php else: while ($category = mysqli_fetch_assoc($categories_result)): ?>
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="category-card">
                    <div class="category-icon">
                        <i class="<?php echo htmlspecialchars($category['icon']); ?>"></i>
                    </div>
                    <h5 class="mb-2"><?php echo htmlspecialchars($category['name_en']); ?></h5>
                    <p class="text-muted mb-2"><?php echo htmlspecialchars($category['name_ar']); ?></p>
                    <p class="small text-muted mb-3"><?php echo htmlspecialchars($category['description']); ?></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-primary"><?php echo $category['project_count']; ?> <?php echo $current_content['projects_count']; ?></span>
                        <div class="btn-group">
                            <button class="btn btn-outline btn-sm" onclick="editCategory(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['name_en']); ?>', '<?php echo htmlspecialchars($category['name_ar']); ?>', '<?php echo htmlspecialchars($category['description']); ?>', '<?php echo htmlspecialchars($category['icon']); ?>')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" style="display:inline-block">
                                <input type="hidden" name="delete_category" value="<?php echo $category['id']; ?>">
                                <button type="submit" class="btn btn-outline btn-sm" onclick="return confirm('<?php echo $current_content['confirm_delete']; ?>');">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; endif; ?>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $current_content['add_category']; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label"><?php echo $current_content['name_en']; ?></label>
                        <input type="text" name="name_en" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo $current_content['name_ar']; ?></label>
                        <input type="text" name="name_ar" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo $current_content['description']; ?></label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo $current_content['icon']; ?></label>
                        <input type="text" name="icon" class="form-control" placeholder="fas fa-tag" required>
                        <small class="text-muted">Use FontAwesome icon classes (e.g., fas fa-tag)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $current_content['cancel']; ?></button>
                    <button type="submit" name="add_category" class="btn btn-primary"><?php echo $current_content['save']; ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $current_content['edit']; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="category_id" id="edit_category_id">
                    <div class="mb-3">
                        <label class="form-label"><?php echo $current_content['name_en']; ?></label>
                        <input type="text" name="name_en" id="edit_name_en" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo $current_content['name_ar']; ?></label>
                        <input type="text" name="name_ar" id="edit_name_ar" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo $current_content['description']; ?></label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo $current_content['icon']; ?></label>
                        <input type="text" name="icon" id="edit_icon" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $current_content['cancel']; ?></button>
                    <button type="submit" name="update_category" class="btn btn-primary"><?php echo $current_content['save']; ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
AOS.init({ duration: 1000, once: true, offset: 100 });

function editCategory(id, nameEn, nameAr, description, icon) {
    document.getElementById('edit_category_id').value = id;
    document.getElementById('edit_name_en').value = nameEn;
    document.getElementById('edit_name_ar').value = nameAr;
    document.getElementById('edit_description').value = description;
    document.getElementById('edit_icon').value = icon;
    
    new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
}
</script>
</body>
</html>
