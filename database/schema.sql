-- Professional Experience Tracker Database Schema
-- منصة توثيق وتقديم الخبرات المهنية

-- Create database
CREATE DATABASE IF NOT EXISTS professional_experience_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE professional_experience_tracker;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    bio TEXT,
    profile_image VARCHAR(255),
    role ENUM('user', 'admin') DEFAULT 'user',
    language ENUM('en', 'ar') DEFAULT 'en',
    template VARCHAR(50) DEFAULT 'default', -- Added for portfolio template selection
    portfolio_views INT DEFAULT 0, -- Analytics: portfolio views
    location VARCHAR(255) DEFAULT NULL,
    website VARCHAR(255) DEFAULT NULL,
    github VARCHAR(255) DEFAULT NULL,
    linkedin VARCHAR(255) DEFAULT NULL,
    twitter VARCHAR(255) DEFAULT NULL,
    instagram VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name_en VARCHAR(100) NOT NULL,
    name_ar VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Projects table
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    client VARCHAR(100),
    start_date DATE,
    end_date DATE,
    status ENUM('completed', 'ongoing', 'planned') DEFAULT 'completed',
    technologies TEXT,
    budget DECIMAL(10,2),
    project_link VARCHAR(500) DEFAULT NULL, -- Live demo link
    github_link VARCHAR(500) DEFAULT NULL, -- GitHub repository link
    image_path VARCHAR(500) DEFAULT NULL, -- Project image path
    views INT DEFAULT 0, -- Analytics: project views
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Project files table
CREATE TABLE project_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(50),
    file_size INT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

-- Comments table
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Portfolio shares table
CREATE TABLE portfolio_shares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    share_token VARCHAR(255) UNIQUE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default categories
INSERT INTO categories (name_en, name_ar, description, icon) VALUES
('Web Development', 'تطوير الويب', 'Web applications and websites', 'fas fa-globe'),
('Mobile Development', 'تطوير التطبيقات', 'Mobile applications', 'fas fa-mobile-alt'),
('Graphic Design', 'التصميم الجرافيكي', 'Visual design and branding', 'fas fa-palette'),
('UI/UX Design', 'تصميم واجهات المستخدم', 'User interface and experience design', 'fas fa-paint-brush'),
('Digital Marketing', 'التسويق الرقمي', 'Online marketing and SEO', 'fas fa-bullhorn'),
('Content Writing', 'كتابة المحتوى', 'Articles, blogs, and copywriting', 'fas fa-pen'),
('Video Production', 'إنتاج الفيديو', 'Video editing and production', 'fas fa-video'),
('Photography', 'التصوير الفوتوغرافي', 'Professional photography', 'fas fa-camera'),
('Consulting', 'الاستشارات', 'Business and technical consulting', 'fas fa-chart-line'),
('Other', 'أخرى', 'Other professional services', 'fas fa-briefcase');

-- Insert admin user (password: admin123)
INSERT INTO users (username, email, password, full_name, role) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin');

-- Insert sample users (password: user123)
INSERT INTO users (username, email, password, full_name, bio, location, website, github, linkedin) VALUES
('ahmed_dev', 'ahmed@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ahmed Hassan', 'Full-stack developer with 5+ years of experience in web and mobile development. Passionate about creating innovative solutions and clean code.', 'Cairo, Egypt', 'https://ahmed.dev', 'https://github.com/ahmeddev', 'https://linkedin.com/in/ahmedhassan'),
('sara_designer', 'sara@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sara Ali', 'Creative UI/UX designer passionate about user experience and creating beautiful, functional interfaces that users love.', 'Dubai, UAE', 'https://sara.design', 'https://github.com/saradesigner', 'https://linkedin.com/in/saraali'),
('mohammed_marketing', 'mohammed@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mohammed Omar', 'Digital marketing specialist and SEO expert with proven track record of increasing organic traffic and conversions.', 'Riyadh, Saudi Arabia', 'https://mohammed.marketing', 'https://github.com/mohammedmarketing', 'https://linkedin.com/in/mohammedomar');

-- Insert sample projects with enhanced data
INSERT INTO projects (user_id, category_id, title, description, client, start_date, end_date, status, technologies, project_link, github_link, budget) VALUES
(2, 1, 'E-commerce Platform', 'Developed a full-featured e-commerce platform with payment integration, inventory management, and comprehensive admin dashboard. Features include user authentication, product catalog, shopping cart, order management, and analytics dashboard.', 'TechCorp Inc.', '2023-01-15', '2023-06-30', 'completed', 'PHP, MySQL, JavaScript, Bootstrap, Stripe API, PayPal', 'https://demo-ecommerce.techcorp.com', 'https://github.com/ahmeddev/ecommerce-platform', 15000.00),
(2, 2, 'Mobile Banking App', 'Created a secure mobile banking application with biometric authentication, real-time transactions, push notifications, and advanced security features. The app supports multiple accounts, fund transfers, bill payments, and investment tracking.', 'BankPlus Financial', '2023-07-01', '2023-12-15', 'completed', 'React Native, Node.js, MongoDB, Firebase, Biometric API, Socket.io', 'https://bankplus-app.com', 'https://github.com/ahmeddev/banking-app', 25000.00),
(3, 4, 'Brand Identity Design', 'Designed complete brand identity package including logo design, color palette, typography guidelines, business cards, letterheads, and comprehensive brand guidelines document. Created a modern, professional look that reflects the company values.', 'StartupXYZ', '2023-03-01', '2023-04-30', 'completed', 'Adobe Illustrator, Photoshop, InDesign, Figma, Brand Guidelines', '', '', 5000.00),
(4, 5, 'SEO Campaign', 'Managed comprehensive SEO campaign for an e-commerce store, including keyword research, on-page optimization, content creation, link building, and technical SEO improvements. Achieved 300% increase in organic traffic and 150% increase in conversions.', 'E-commerce Store', '2023-05-01', '2023-08-31', 'completed', 'Google Analytics, SEMrush, Ahrefs, Content Strategy, Technical SEO', 'https://ecommerce-store.com', '', 8000.00),
(2, 1, 'Task Management System', 'Built a collaborative task management system with real-time updates, team collaboration features, file sharing, and progress tracking. Includes project templates, time tracking, and comprehensive reporting.', 'ProjectFlow Solutions', '2023-09-01', '2023-11-30', 'completed', 'Vue.js, Laravel, PostgreSQL, Redis, WebSockets', 'https://taskflow.projectflow.com', 'https://github.com/ahmeddev/task-management', 12000.00),
(3, 3, 'Marketing Campaign Design', 'Created visual assets for a comprehensive marketing campaign including social media graphics, email templates, banner ads, and print materials. Maintained consistent brand identity across all touchpoints.', 'Growth Marketing Co.', '2023-06-01', '2023-07-15', 'completed', 'Adobe Creative Suite, Canva, Social Media Templates', '', '', 3500.00);

-- Insert sample comments
INSERT INTO comments (project_id, user_id, comment, rating) VALUES
(1, 3, 'Excellent work! The platform is very user-friendly and performs great. The payment integration works seamlessly.', 5),
(1, 4, 'Great communication throughout the project. The admin dashboard is intuitive and the analytics are very helpful. Highly recommended!', 5),
(2, 2, 'The app exceeded our expectations. Very professional work with excellent security features. The biometric authentication works perfectly.', 5),
(3, 2, 'Beautiful design that perfectly represents our brand vision. The brand guidelines document is comprehensive and easy to follow.', 5),
(4, 2, 'Outstanding results! The SEO campaign delivered beyond our expectations. Our organic traffic and conversions have increased significantly.', 5),
(5, 3, 'The task management system is intuitive and feature-rich. The real-time collaboration features are game-changing for our team.', 5); 