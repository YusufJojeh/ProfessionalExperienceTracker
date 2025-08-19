<?php
// Admin Navigation Component
// This file contains the admin navigation that will be included in all admin pages

$admin_nav_content = [
    'en' => [
        'dashboard' => 'Dashboard',
        'users' => 'Manage Users',
        'projects' => 'Manage Projects',
        'categories' => 'Manage Categories',
        'settings' => 'Platform Settings',
        'user_dashboard' => 'User Dashboard',
        'logout' => 'Logout'
    ],
    'ar' => [
        'dashboard' => 'لوحة التحكم',
        'users' => 'إدارة المستخدمين',
        'projects' => 'إدارة المشاريع',
        'categories' => 'إدارة الفئات',
        'settings' => 'إعدادات المنصة',
        'user_dashboard' => 'لوحة تحكم المستخدم',
        'logout' => 'تسجيل الخروج'
    ]
];

$current_admin_nav = $admin_nav_content[$lang];
?>

<!-- Admin Navigation -->
<nav class="navbar navbar-expand-lg admin-navbar">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-rocket me-2"></i>
            <?php echo $lang === 'en' ? PLATFORM_NAME : PLATFORM_NAME_AR; ?>
            
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbarNav" aria-controls="adminNavbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="adminNavbarNav">
            <ul class="navbar-nav me-auto">
                <!-- Main Admin Dashboard -->
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'admin.php' ? 'active' : ''; ?>" href="admin.php">
                        <i class="fas fa-tachometer-alt me-1"></i>
                        <span class="nav-text"><?php echo $current_admin_nav['dashboard']; ?></span>
                    </a>
                </li>
                
                <!-- User Management -->
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'manage_users.php' ? 'active' : ''; ?>" href="manage_users.php">
                        <i class="fas fa-users me-1"></i>
                        <span class="nav-text"><?php echo $current_admin_nav['users']; ?></span>
                    </a>
                </li>
                
                <!-- Project Management -->
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'manage_projects.php' ? 'active' : ''; ?>" href="manage_projects.php">
                        <i class="fas fa-project-diagram me-1"></i>
                        <span class="nav-text"><?php echo $current_admin_nav['projects']; ?></span>
                    </a>
                </li>
                
                <!-- Category Management -->
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'manage_categories.php' ? 'active' : ''; ?>" href="manage_categories.php">
                        <i class="fas fa-tags me-1"></i>
                        <span class="nav-text"><?php echo $current_admin_nav['categories']; ?></span>
                    </a>
                </li>
                
                <!-- Platform Settings -->
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'platform_settings.php' ? 'active' : ''; ?>" href="platform_settings.php">
                        <i class="fas fa-cogs me-1"></i>
                        <span class="nav-text"><?php echo $current_admin_nav['settings']; ?></span>
                    </a>
                </li>
            </ul>
            
            <!-- User Menu -->
            <div class="d-flex align-items-center admin-user-menu">
                <div class="dropdown">
                    <button class="btn dropdown-toggle d-flex align-items-center admin-user-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar me-2">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <span class="user-name d-none d-md-inline"><?php echo $_SESSION['user_name']; ?> (Admin)</span>
                        <i class="fas fa-chevron-down ms-1 d-none d-md-inline"></i>
                    </button>
                    <ul class="dropdown-menu admin-dropdown-menu">
                        <li><a class="dropdown-item" href="dashboard.php">
                            <i class="fas fa-user me-2"></i>
                            <?php echo $current_admin_nav['user_dashboard']; ?>
                        </a></li>
                        <li><a class="dropdown-item" href="profile.php">
                            <i class="fas fa-user-edit me-2"></i>
                            <?php echo $lang === 'en' ? 'Edit Profile' : 'تعديل الملف الشخصي'; ?>
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            <?php echo $current_admin_nav['logout']; ?>
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
/* Admin Navigation Specific Styles */
.admin-navbar {
    background: var(--white);
    box-shadow: var(--shadow-md);
    padding: 0.75rem 0;
    position: sticky;
    top: 0;
    z-index: 1030;
}

.admin-navbar .navbar-brand {
    font-weight: 800;
    font-size: 1.5rem;
    color: var(--primary) !important;
    text-decoration: none;
    display: flex;
    align-items: center;
}

.admin-navbar .navbar-brand:hover {
    color: var(--primary-dark) !important;
}

.admin-navbar .badge {
    font-size: 0.6rem;
    padding: 0.25rem 0.5rem;
    font-weight: 600;
}

.admin-navbar .navbar-toggler {
    border: none;
    padding: 0.5rem;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.admin-navbar .navbar-toggler:focus {
    box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
}

.admin-navbar .navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(37, 99, 235, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
}

.admin-navbar .nav-link {
    color: var(--gray-700) !important;
    font-weight: 500;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    margin: 0 0.25rem;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    white-space: nowrap;
}

.admin-navbar .nav-link:hover {
    color: var(--primary) !important;
    background: var(--gray-100);
    transform: translateY(-1px);
}

.admin-navbar .nav-link.active {
    background: var(--gradient-primary);
    color: white !important;
    box-shadow: var(--shadow-glow);
}

.admin-navbar .nav-link.active:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-glow), var(--shadow-lg);
}

.admin-navbar .nav-link i {
    font-size: 1rem;
    width: 20px;
    text-align: center;
}

/* User Menu Styles */
.admin-user-menu {
    margin-left: auto;
}

.admin-user-btn {
    background: var(--gray-100);
    border: 2px solid var(--gray-200);
    border-radius: 1rem;
    padding: 0.5rem 1rem;
    color: var(--gray-700);
    font-weight: 500;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.admin-user-btn:hover {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.admin-user-btn:focus {
    box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
}

.user-avatar {
    width: 32px;
    height: 32px;
    background: var(--gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.875rem;
}

.user-name {
    font-weight: 500;
    max-width: 150px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Dropdown Menu Styles */
.admin-dropdown-menu {
    border: none;
    box-shadow: var(--shadow-xl);
    border-radius: 1rem;
    padding: 0.5rem;
    min-width: 220px;
    margin-top: 0.5rem;
    background: var(--white);
}

.admin-dropdown-menu .dropdown-item {
    border-radius: 0.5rem;
    padding: 0.75rem 1rem;
    font-weight: 500;
    transition: all 0.3s ease;
    color: var(--gray-700);
    display: flex;
    align-items: center;
}

.admin-dropdown-menu .dropdown-item:hover {
    background: var(--gray-100);
    color: var(--primary);
    transform: translateX(5px);
}

.admin-dropdown-menu .dropdown-item i {
    width: 20px;
    text-align: center;
    margin-right: 0.5rem;
}

.admin-dropdown-menu .dropdown-divider {
    margin: 0.5rem 0;
    border-color: var(--gray-200);
}

/* Responsive Design */
@media (max-width: 991.98px) {
    .admin-navbar {
        padding: 0.5rem 0;
    }
    
    .admin-navbar .navbar-brand {
        font-size: 1.25rem;
    }
    
    .admin-navbar .navbar-collapse {
        background: var(--white);
        border-radius: 1rem;
        box-shadow: var(--shadow-lg);
        margin-top: 1rem;
        padding: 1rem;
    }
    
    .admin-navbar .nav-link {
        padding: 0.75rem 1rem;
        margin: 0.25rem 0;
        border-radius: 0.5rem;
        justify-content: flex-start;
    }
    
    .admin-navbar .nav-link:hover {
        background: var(--gray-100);
        transform: none;
    }
    
    .admin-navbar .nav-link.active {
        background: var(--gradient-primary);
        color: white !important;
    }
    
    .admin-user-menu {
        margin: 1rem 0 0 0;
        width: 100%;
    }
    
    .admin-user-btn {
        width: 100%;
        justify-content: center;
        padding: 0.75rem 1rem;
    }
    
    .user-name {
        max-width: none;
    }
    
    .admin-dropdown-menu {
        position: static !important;
        transform: none !important;
        width: 100%;
        margin-top: 0.5rem;
        box-shadow: none;
        border: 1px solid var(--gray-200);
    }
}

@media (max-width: 575.98px) {
    .admin-navbar .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .admin-navbar .navbar-brand {
        font-size: 1.1rem;
    }
    
    .admin-navbar .badge {
        font-size: 0.5rem;
        padding: 0.2rem 0.4rem;
    }
    
    .admin-navbar .nav-link {
        padding: 0.625rem 0.875rem;
        font-size: 0.9rem;
    }
    
    .admin-navbar .nav-link i {
        font-size: 0.9rem;
        width: 18px;
    }
    
    .admin-user-btn {
        padding: 0.625rem 0.875rem;
        font-size: 0.9rem;
    }
    
    .user-avatar {
        width: 28px;
        height: 28px;
        font-size: 0.75rem;
    }
}

/* RTL Support */
.rtl .admin-navbar .nav-link {
    text-align: right;
}

.rtl .admin-navbar .nav-link i {
    margin-left: 0.5rem;
    margin-right: 0;
}

.rtl .admin-dropdown-menu .dropdown-item {
    text-align: right;
}

.rtl .admin-dropdown-menu .dropdown-item i {
    margin-left: 0.5rem;
    margin-right: 0;
}

.rtl .admin-dropdown-menu .dropdown-item:hover {
    transform: translateX(-5px);
}

/* Animation for mobile menu */
@media (max-width: 991.98px) {
    .admin-navbar .navbar-collapse {
        transition: all 0.3s ease;
    }
    
    .admin-navbar .navbar-collapse.collapsing {
        opacity: 0;
        transform: translateY(-10px);
    }
    
    .admin-navbar .navbar-collapse.show {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Focus styles for accessibility */
.admin-navbar .nav-link:focus,
.admin-user-btn:focus {
    outline: 2px solid var(--primary);
    outline-offset: 2px;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .admin-navbar {
        border-bottom: 2px solid var(--gray-300);
    }
    
    .admin-navbar .nav-link {
        border: 1px solid transparent;
    }
    
    .admin-navbar .nav-link:hover {
        border-color: var(--primary);
    }
    
    .admin-navbar .nav-link.active {
        border-color: var(--primary);
    }
}
</style>
