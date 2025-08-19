# Professional Experience Tracker - Navigation System

## Overview

The Professional Experience Tracker platform now features a comprehensive navigation system with separate interfaces for regular users and administrators. The navigation has been completely rewritten to provide better organization, enhanced functionality, and improved user experience.

## Navigation Components

### 1. User Navigation (`user_nav.php`)

**Location**: `user_nav.php`

**Features**:
- Dashboard access
- Add Project functionality
- My Portfolio view
- Discover other users
- Profile management
- Settings access
- Admin panel access (for admin users)
- Logout functionality

**Navigation Items**:
- **Dashboard**: Main user dashboard with project overview
- **Add Project**: Create and edit projects
- **My Portfolio**: View personal portfolio
- **Discover**: Browse other users and their work
- **Profile**: Manage personal profile

**User Menu**:
- Profile settings
- Platform settings
- Admin panel (if admin role)
- Logout

### 2. Admin Navigation (`admin_nav.php`)

**Location**: `admin_nav.php`

**Features**:
- Comprehensive admin dashboard
- User management
- Project management
- Content management
- Analytics and reports
- System management
- Platform settings

**Navigation Categories**:

#### Main Admin Dashboard
- **Dashboard**: Overview of platform statistics

#### User Management
- **Manage Users**: View, edit, and delete users
- **User Analytics**: User behavior and statistics
- **Manage Roles**: User role management

#### Project Management
- **Manage Projects**: View, edit, and delete all projects
- **Project Analytics**: Project performance metrics
- **Project Categories**: Category management

#### Content Management
- **Manage Comments**: Moderate user comments
- **Manage Categories**: Create and edit project categories
- **Content Moderation**: Content approval system

#### Analytics & Reports
- **Platform Analytics**: Overall platform statistics
- **User Reports**: Detailed user reports
- **Performance Metrics**: System performance data

#### System Management
- **Platform Settings**: Global platform configuration
- **Backup & Restore**: Database backup functionality
- **System Logs**: System activity logs
- **Security Settings**: Security configuration

## Admin Management Pages

### 1. Manage Projects (`manage_projects.php`)

**Features**:
- View all projects across the platform
- Search and filter projects by title, description, or user
- Filter by project status (completed, ongoing, planned)
- Edit project details
- Delete projects
- View project statistics (views, creation date)

**Functionality**:
- Advanced search capabilities
- Status-based filtering
- Bulk operations
- Project analytics integration

### 2. Manage Categories (`manage_categories.php`)

**Features**:
- Create new project categories
- Edit existing categories
- Delete categories
- View category usage statistics
- Bilingual support (English/Arabic)

**Category Management**:
- Category name (English/Arabic)
- Category description
- Icon selection (FontAwesome)
- Project count per category

### 3. Platform Settings (`platform_settings.php`)

**Features**:
- General platform configuration
- User management settings
- Content moderation settings
- Security settings
- Maintenance mode

**Settings Categories**:

#### General Settings
- Platform name (English/Arabic)
- Contact email
- File upload limits
- Allowed file types

#### User Settings
- Registration control
- Email verification requirements
- Maximum projects per user

#### Content Settings
- Auto-approve projects
- Comment moderation
- Maximum comment length

#### Security Settings
- Session timeout
- Password requirements
- CAPTCHA settings
- Maintenance mode

## Implementation Details

### Navigation Integration

To use the new navigation system in any page:

**For User Pages**:
```php
<?php include 'user_nav.php'; ?>
```

**For Admin Pages**:
```php
<?php include 'admin_nav.php'; ?>
```

### Active Page Detection

The navigation automatically detects the current page and highlights the appropriate menu item:

```php
<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>
```

### Multilingual Support

All navigation elements support both English and Arabic languages:

```php
$lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';
$current_nav = $nav_content[$lang];
```

### Admin Role Detection

Admin features are automatically shown/hidden based on user role:

```php
<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <!-- Admin-only content -->
<?php endif; ?>
```

## Styling and Design

### Color Scheme
- **Primary**: Blue gradient (#2563eb to #60a5fa)
- **Secondary**: Orange gradient (#f97316 to #fdba74)
- **Accent**: Cyan gradient (#06b6d4 to #67e8f9)
- **Neutral**: Gray scale for text and backgrounds

### Responsive Design
- Mobile-first approach
- Collapsible navigation on mobile
- Touch-friendly interface
- Adaptive layouts

### Visual Enhancements
- Smooth animations and transitions
- Hover effects
- Active state indicators
- Dropdown menus with icons

## Security Features

### Admin Access Control
- Role-based access control
- Session validation
- Secure redirects
- Input sanitization

### User Protection
- Prevent self-deletion for admins
- Confirmation dialogs for destructive actions
- CSRF protection
- SQL injection prevention

## Database Integration

### User Roles
The system uses the `role` field in the `users` table:
- `user`: Regular user access
- `admin`: Full administrative access

### Navigation State
Navigation state is managed through:
- Session variables
- Current page detection
- User role validation

## Future Enhancements

### Planned Features
1. **Advanced Analytics Dashboard**
   - Real-time statistics
   - Interactive charts
   - Export functionality

2. **Enhanced Content Moderation**
   - Automated content filtering
   - Report management
   - Moderation queue

3. **System Monitoring**
   - Performance metrics
   - Error logging
   - Health checks

4. **Backup System**
   - Automated backups
   - Restore functionality
   - Backup scheduling

## File Structure

```
ProfessionalExperienceTracker/
├── user_nav.php              # User navigation component
├── admin_nav.php             # Admin navigation component
├── manage_projects.php       # Project management
├── manage_categories.php     # Category management
├── platform_settings.php     # Platform configuration
├── dashboard.php             # User dashboard
├── admin.php                 # Admin dashboard
├── manage_users.php          # User management
└── ... (other existing files)
```

## Usage Instructions

### For Developers

1. **Adding New Admin Pages**:
   - Create the page with admin role check
   - Include `admin_nav.php`
   - Add navigation link in `admin_nav.php`

2. **Adding New User Pages**:
   - Create the page with user authentication
   - Include `user_nav.php`
   - Add navigation link in `user_nav.php`

3. **Modifying Navigation**:
   - Edit the appropriate navigation file
   - Update language arrays
   - Test responsive behavior

### For Administrators

1. **Accessing Admin Panel**:
   - Login with admin credentials
   - Navigate to admin dashboard
   - Use dropdown menus for specific functions

2. **Managing Content**:
   - Use "Manage Projects" for project oversight
   - Use "Manage Categories" for category organization
   - Use "Platform Settings" for configuration

3. **User Management**:
   - Use "Manage Users" for user oversight
   - Monitor user activity through analytics
   - Manage user roles and permissions

## Support and Maintenance

### Regular Maintenance
- Monitor system logs
- Review user reports
- Update platform settings as needed
- Backup database regularly

### Troubleshooting
- Check user permissions
- Verify database connections
- Review error logs
- Test navigation functionality

---

**Note**: This navigation system provides a solid foundation for platform management while maintaining user-friendly interfaces for regular users. The modular design allows for easy expansion and customization as the platform grows.
