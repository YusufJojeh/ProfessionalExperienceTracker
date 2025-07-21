# Professional Experience Tracker
# منصة توثيق وتقديم الخبرات المهنية

A comprehensive full-stack web platform for professionals to showcase their work, manage projects, and build stunning portfolios. Built with PHP, MySQL, and modern front-end technologies.

## 🌟 Features

### Core Features
- **User Authentication & Registration** - Secure login/registration system
- **Project Management** - Add, edit, and organize professional projects
- **Portfolio Creation** - Beautiful, responsive portfolio pages
- **File Uploads** - Support for images, documents, and media files
- **Comments & Ratings** - Interactive feedback system for projects
- **Multi-language Support** - Arabic and English interface
- **Admin Dashboard** - Complete administrative control panel

### Technical Features
- **Responsive Design** - Mobile-first, modern UI/UX
- **Animated Landing Page** - Stunning marketing page with SVG animations
- **Real-time Notifications** - Toast notifications and alerts
- **Security** - Input validation, prepared statements, XSS protection
- **Performance** - Optimized database queries and caching

## 🚀 Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP/WAMP/MAMP (for local development)

### Setup Instructions

1. **Clone or Download the Project**
   ```bash
   # If using git
   git clone [repository-url]
   cd professional-experience-tracker
   ```

2. **Database Setup**
   - Create a new MySQL database named `professional_experience_tracker`
   - Import the database schema from `database/schema.sql`
   - Update database credentials in `config/database.php` if needed

3. **Web Server Configuration**
   - Place the project in your web server's document root
   - Ensure the `uploads/` directory is writable
   - Configure your web server to serve from the project root

4. **Access the Application**
   - Open your browser and navigate to `http://localhost/professional-experience-tracker`
   - The application should be ready to use!

### Default Login Credentials

**Admin Account:**
- Username: `admin`
- Password: `admin123`
- Email: `admin@example.com`

**Sample User Accounts:**
- Username: `ahmed_dev` / Password: `user123`
- Username: `sara_designer` / Password: `user123`
- Username: `mohammed_marketing` / Password: `user123`

## 📁 Project Structure

```
professional-experience-tracker/
├── config/
│   └── database.php          # Database configuration
├── database/
│   └── schema.sql            # Database schema and sample data
├── uploads/
│   ├── projects/             # Project file uploads
│   └── profiles/             # Profile image uploads
├── index.php                 # Landing page
├── register.php              # User registration
├── login.php                 # User login
├── dashboard.php             # User dashboard
├── add_project.php           # Add new project
├── project.php               # View individual project
├── portfolio.php             # Public portfolio page
├── profile.php               # User profile management
├── admin.php                 # Admin dashboard
├── contact.php               # Contact form
├── logout.php                # Logout functionality
├── multilanguage.php         # Language switcher
└── README.md                 # This file
```

## 🎨 Pages Overview

### Public Pages
- **index.php** - Stunning animated landing page with marketing content
- **portfolio.php** - Public user portfolios with project showcases
- **project.php** - Individual project details with comments and files
- **contact.php** - Contact form with animated feedback

### User Pages
- **register.php** - User registration with validation
- **login.php** - Secure login with remember me functionality
- **dashboard.php** - User dashboard with project management
- **add_project.php** - Project creation with file uploads
- **profile.php** - Profile management and settings

### Admin Pages
- **admin.php** - Administrative dashboard with user and project management

## 🔧 Configuration

### Database Configuration
Edit `config/database.php` to match your database settings:
```php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'professional_experience_tracker';
```

### File Upload Settings
The application supports file uploads for:
- Profile images (JPG, PNG, GIF, max 5MB)
- Project files (Images, PDFs, documents, videos, max 10MB each)

### Language Settings
The platform supports:
- **English** (en) - Default language
- **Arabic** (ar) - RTL support with Cairo font

## 🛡️ Security Features

- **Input Sanitization** - All user inputs are sanitized
- **SQL Injection Protection** - Prepared statements and escaping
- **XSS Protection** - Output encoding and validation
- **Session Security** - Secure session management
- **File Upload Security** - Type and size validation

## 🎯 Key Features in Detail

### 1. Stunning Landing Page
- Animated SVG icons and elements
- Responsive design with Bootstrap 5
- Marketing-focused content
- Call-to-action buttons
- Testimonial carousel
- Animated counters and statistics

### 2. Project Management
- Create detailed project descriptions
- Upload multiple files per project
- Categorize projects by type
- Set project status (completed, ongoing, planned)
- Add client information and budget
- Track project duration

### 3. Portfolio Showcase
- Public portfolio pages for each user
- Professional project displays
- File downloads
- Rating and comment system
- Responsive grid layout

### 4. User Experience
- Intuitive navigation
- Modern UI/UX design
- Mobile-responsive interface
- Fast loading times
- Smooth animations

### 5. Admin Features
- User management
- Project oversight
- Category management
- Statistics dashboard
- Content moderation

## 🌐 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## 📱 Mobile Responsiveness

The platform is fully responsive and optimized for:
- Desktop computers
- Tablets
- Mobile phones
- All screen sizes

## 🔄 Multi-language Support

### Switching Languages
- Language switcher in the top-left corner
- Automatic language detection
- RTL support for Arabic
- Font optimization for each language

### Supported Languages
- **English** - Professional and clean interface
- **Arabic** - Full RTL support with appropriate fonts

## 🚀 Performance Optimization

- Optimized database queries
- Efficient file handling
- Compressed CSS and JavaScript
- CDN resources for faster loading
- Responsive images

## 🛠️ Customization

### Styling
- CSS variables for easy theming
- Bootstrap 5 framework
- Custom animations and effects
- Modular CSS structure

### Functionality
- Modular PHP code
- Easy to extend and modify
- Well-documented functions
- Clean code structure

## 📞 Support

For support and questions:
- Email: contact@professionaltracker.com
- Documentation: See inline code comments
- Issues: Create an issue in the repository

## 📄 License

This project is open-source and available under the MIT License.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit pull requests or create issues for bugs and feature requests.

## 🔮 Future Enhancements

- Email notifications
- Social media integration
- Advanced search and filtering
- Export portfolio to PDF
- API endpoints
- Mobile app integration
- Advanced analytics
- Payment integration

---

**Built with ❤️ for professionals worldwide**

*Professional Experience Tracker - Showcase Your Work, Build Your Future* # ProfessionalExperienceTracker
