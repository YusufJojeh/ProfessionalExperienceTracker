<?php
// User Navigation Component
// This file contains the user navigation that will be included in all user pages

$user_nav_content = [
    'en' => [
        'dashboard' => 'Dashboard',
        'add_project' => 'Add Project',
        'my_portfolio' => 'My Portfolio',
        'discover' => 'Discover',
        'profile' => 'Profile',
        'settings' => 'Settings',
        'logout' => 'Logout',
        'admin_panel' => 'Admin Panel'
    ],
    'ar' => [
        'dashboard' => 'لوحة التحكم',
        'add_project' => 'إضافة مشروع',
        'my_portfolio' => 'محفظتي',
        'discover' => 'اكتشف',
        'profile' => 'الملف الشخصي',
        'settings' => 'الإعدادات',
        'logout' => 'تسجيل الخروج',
        'admin_panel' => 'لوحة المدير'
    ]
];

$current_user_nav = $user_nav_content[$lang];
?>

<!-- User Navigation -->
<nav class="navbar navbar-expand-lg user-navbar">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-rocket me-2"></i>
            <?php echo $lang === 'en' ? PLATFORM_NAME : PLATFORM_NAME_AR; ?>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#userNavbarNav" aria-controls="userNavbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="userNavbarNav">
            <ul class="navbar-nav me-auto">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                        <i class="fas fa-tachometer-alt me-1"></i>
                        <span class="nav-text"><?php echo $current_user_nav['dashboard']; ?></span>
                    </a>
                </li>
                
                <!-- Add Project -->
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'add_project.php' ? 'active' : ''; ?>" href="add_project.php">
                        <i class="fas fa-plus me-1"></i>
                        <span class="nav-text"><?php echo $current_user_nav['add_project']; ?></span>
                    </a>
                </li>
                
                <!-- My Portfolio -->
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'portfolio.php' ? 'active' : ''; ?>" href="portfolio.php">
                        <i class="fas fa-briefcase me-1"></i>
                        <span class="nav-text"><?php echo $current_user_nav['my_portfolio']; ?></span>
                    </a>
                </li>
                
                <!-- Discover -->
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'browse_users.php' ? 'active' : ''; ?>" href="browse_users.php">
                        <i class="fas fa-users me-1"></i>
                        <span class="nav-text"><?php echo $current_user_nav['discover']; ?></span>
                    </a>
                </li>
                
                <!-- Profile -->
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active' : ''; ?>" href="profile.php">
                        <i class="fas fa-user me-1"></i>
                        <span class="nav-text"><?php echo $current_user_nav['profile']; ?></span>
                    </a>
                </li>
            </ul>
            
            <!-- User Menu -->
            <div class="d-flex align-items-center user-menu">
                <div class="dropdown">
                    <button class="btn dropdown-toggle d-flex align-items-center user-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar me-2">
                            <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                        </div>
                        <span class="user-name d-none d-md-inline"><?php echo $_SESSION['user_name']; ?></span>
                        <i class="fas fa-chevron-down ms-1 d-none d-md-inline"></i>
                    </button>
                    <ul class="dropdown-menu user-dropdown-menu">
                        <li><a class="dropdown-item" href="profile.php">
                            <i class="fas fa-user me-2"></i>
                            <?php echo $current_user_nav['profile']; ?>
                        </a></li>
                        <li><a class="dropdown-item" href="settings.php">
                            <i class="fas fa-cog me-2"></i>
                            <?php echo $current_user_nav['settings']; ?>
                        </a></li>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="admin.php">
                            <i class="fas fa-user-shield me-2"></i>
                            <?php echo $current_user_nav['admin_panel']; ?>
                        </a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            <?php echo $current_user_nav['logout']; ?>
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
/* User Navigation Specific Styles */
.user-navbar {
    background: var(--white);
    box-shadow: var(--shadow-md);
    padding: 0.75rem 0;
    position: sticky;
    top: 0;
    z-index: 1030;
}

.user-navbar .navbar-brand {
    font-weight: 800;
    font-size: 1.5rem;
    color: var(--primary) !important;
    text-decoration: none;
    display: flex;
    align-items: center;
}

.user-navbar .navbar-brand:hover {
    color: var(--primary-dark) !important;
}

.user-navbar .navbar-toggler {
    border: none;
    padding: 0.5rem;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.user-navbar .navbar-toggler:focus {
    box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
}

.user-navbar .navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(37, 99, 235, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
}

.user-navbar .nav-link {
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

.user-navbar .nav-link:hover {
    color: var(--primary) !important;
    background: var(--gray-100);
    transform: translateY(-1px);
}

.user-navbar .nav-link.active {
    background: var(--gradient-primary);
    color: white !important;
    box-shadow: var(--shadow-glow);
}

.user-navbar .nav-link.active:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-glow), var(--shadow-lg);
}

.user-navbar .nav-link i {
    font-size: 1rem;
    width: 20px;
    text-align: center;
}

/* User Menu Styles */
.user-menu {
    margin-left: auto;
}

.user-btn {
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

.user-btn:hover {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.user-btn:focus {
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
    font-weight: 600;
}

.user-name {
    font-weight: 500;
    max-width: 150px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Dropdown Menu Styles */
.user-dropdown-menu {
    border: none;
    box-shadow: var(--shadow-xl);
    border-radius: 1rem;
    padding: 0.5rem;
    min-width: 220px;
    margin-top: 0.5rem;
    background: var(--white);
}

.user-dropdown-menu .dropdown-item {
    border-radius: 0.5rem;
    padding: 0.75rem 1rem;
    font-weight: 500;
    transition: all 0.3s ease;
    color: var(--gray-700);
    display: flex;
    align-items: center;
}

.user-dropdown-menu .dropdown-item:hover {
    background: var(--gray-100);
    color: var(--primary);
    transform: translateX(5px);
}

.user-dropdown-menu .dropdown-item i {
    width: 20px;
    text-align: center;
    margin-right: 0.5rem;
}

.user-dropdown-menu .dropdown-divider {
    margin: 0.5rem 0;
    border-color: var(--gray-200);
}

/* Responsive Design */
@media (max-width: 991.98px) {
    .user-navbar {
        padding: 0.5rem 0;
    }
    
    .user-navbar .navbar-brand {
        font-size: 1.25rem;
    }
    
    .user-navbar .navbar-collapse {
        background: var(--white);
        border-radius: 1rem;
        box-shadow: var(--shadow-lg);
        margin-top: 1rem;
        padding: 1rem;
    }
    
    .user-navbar .nav-link {
        padding: 0.75rem 1rem;
        margin: 0.25rem 0;
        border-radius: 0.5rem;
        justify-content: flex-start;
    }
    
    .user-navbar .nav-link:hover {
        background: var(--gray-100);
        transform: none;
    }
    
    .user-navbar .nav-link.active {
        background: var(--gradient-primary);
        color: white !important;
    }
    
    .user-menu {
        margin: 1rem 0 0 0;
        width: 100%;
    }
    
    .user-btn {
        width: 100%;
        justify-content: center;
        padding: 0.75rem 1rem;
    }
    
    .user-name {
        max-width: none;
    }
    
    .user-dropdown-menu {
        position: static !important;
        transform: none !important;
        width: 100%;
        margin-top: 0.5rem;
        box-shadow: none;
        border: 1px solid var(--gray-200);
    }
}

@media (max-width: 575.98px) {
    .user-navbar .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .user-navbar .navbar-brand {
        font-size: 1.1rem;
    }
    
    .user-navbar .nav-link {
        padding: 0.625rem 0.875rem;
        font-size: 0.9rem;
    }
    
    .user-navbar .nav-link i {
        font-size: 0.9rem;
        width: 18px;
    }
    
    .user-btn {
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
.rtl .user-navbar .nav-link {
    text-align: right;
}

.rtl .user-navbar .nav-link i {
    margin-left: 0.5rem;
    margin-right: 0;
}

.rtl .user-dropdown-menu .dropdown-item {
    text-align: right;
}

.rtl .user-dropdown-menu .dropdown-item i {
    margin-left: 0.5rem;
    margin-right: 0;
}

.rtl .user-dropdown-menu .dropdown-item:hover {
    transform: translateX(-5px);
}

/* Animation for mobile menu */
@media (max-width: 991.98px) {
    .user-navbar .navbar-collapse {
        transition: all 0.3s ease;
    }
    
    .user-navbar .navbar-collapse.collapsing {
        opacity: 0;
        transform: translateY(-10px);
    }
    
    .user-navbar .navbar-collapse.show {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Focus styles for accessibility */
.user-navbar .nav-link:focus,
.user-btn:focus {
    outline: 2px solid var(--primary);
    outline-offset: 2px;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .user-navbar {
        border-bottom: 2px solid var(--gray-300);
    }
    
    .user-navbar .nav-link {
        border: 1px solid transparent;
    }
    
    .user-navbar .nav-link:hover {
        border-color: var(--primary);
    }
    
    .user-navbar .nav-link.active {
        border-color: var(--primary);
    }
}
</style>
