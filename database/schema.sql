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
INSERT INTO users (username, email, password, full_name, bio) VALUES
('ahmed_dev', 'ahmed@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ahmed Hassan', 'Full-stack developer with 5+ years of experience'),
('sara_designer', 'sara@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sara Ali', 'Creative UI/UX designer passionate about user experience'),
('mohammed_marketing', 'mohammed@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mohammed Omar', 'Digital marketing specialist and SEO expert');

-- Insert sample projects
INSERT INTO projects (user_id, category_id, title, description, client, start_date, end_date, status, technologies) VALUES
(2, 1, 'E-commerce Platform', 'Developed a full-featured e-commerce platform with payment integration and admin dashboard', 'TechCorp', '2023-01-15', '2023-06-30', 'completed', 'PHP, MySQL, JavaScript, Bootstrap'),
(2, 2, 'Mobile Banking App', 'Created a secure mobile banking application with biometric authentication', 'BankPlus', '2023-07-01', '2023-12-15', 'completed', 'React Native, Node.js, MongoDB'),
(3, 4, 'Brand Identity Design', 'Designed complete brand identity including logo, color palette, and marketing materials', 'StartupXYZ', '2023-03-01', '2023-04-30', 'completed', 'Adobe Creative Suite, Figma'),
(4, 5, 'SEO Campaign', 'Managed comprehensive SEO campaign resulting in 300% increase in organic traffic', 'E-commerce Store', '2023-05-01', '2023-08-31', 'completed', 'Google Analytics, SEMrush, Content Strategy');

-- Insert sample comments
INSERT INTO comments (project_id, user_id, comment, rating) VALUES
(1, 3, 'Excellent work! The platform is very user-friendly and performs great.', 5),
(1, 4, 'Great communication throughout the project. Highly recommended!', 5),
(2, 2, 'The app exceeded our expectations. Very professional work.', 5),
(3, 2, 'Beautiful design that perfectly represents our brand vision.', 5);

ALTER TABLE users
  ADD COLUMN location VARCHAR(255) DEFAULT NULL,
  ADD COLUMN website VARCHAR(255) DEFAULT NULL,
  ADD COLUMN github VARCHAR(255) DEFAULT NULL,
  ADD COLUMN linkedin VARCHAR(255) DEFAULT NULL,
  ADD COLUMN twitter VARCHAR(255) DEFAULT NULL,
  ADD COLUMN instagram VARCHAR(255) DEFAULT NULL; 