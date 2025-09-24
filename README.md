# Learner Environment Platform

A comprehensive PHP-based learning platform built with Laravel that provides an interactive environment for students to solve coding challenges, receive AI-powered feedback, and for teachers to track student progress through advanced clustering and analytics.

## Features

### For Students
- Interactive coding challenges with real-time feedback
- Personalized learning paths based on performance
- AI-powered recommendations for improvement
- Progress tracking and achievement system

### For Teachers
- Student performance analytics dashboard
- AI-driven clustering of students by learning patterns
- Challenge creation and management tools
- Detailed insights into student learning behaviors

### For Administrators
- User management and role-based access control
- Security scanning and vulnerability detection
- System performance monitoring
- Configuration management

## Technical Architecture

### MVC Structure
The application follows the Model-View-Controller (MVC) architecture pattern with PSR-4 compliant namespaces:

- **Models**: Database entities and relationships
- **Views**: Blade templates for UI rendering
- **Controllers**: Request handling and business logic coordination
- **Services**: Business logic implementation and separation of concerns

### Key Components

#### Service Layer
- **ChallengeService**: Manages challenge-related operations
- **UserService**: Handles user-related business logic
- **CacheService**: Optimizes performance through strategic caching
- **SecurityScanner**: Detects code vulnerabilities and security issues
- **PDOWrapper**: Provides secure database operations

#### Security Features
- Enhanced CSRF protection with security headers
- SQL injection prevention through prepared statements
- Automated security scanning for vulnerability detection
- Role-based access control

#### Performance Optimization
- Strategic caching with group-based invalidation
- Database query optimization
- Asset bundling and compression

#### AI Integration
- Python-based clustering service for student grouping
- Recommendation engine for personalized learning
- Automated feedback generation

## Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL 8.0 or higher
- Node.js and NPM
- Python 3.8 or higher (for AI services)

### Setup Instructions

1. Clone the repository:
   ```bash
   git clone https://github.com/your-organization/learner-env.git
   cd learner-env
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install JavaScript dependencies:
   ```bash
   npm install
   ```

4. Copy the environment file and configure your settings:
   ```bash
   cp .env.example .env
   ```

5. Generate application key:
   ```bash
   php artisan key:generate
   ```

6. Run database migrations and seed initial data:
   ```bash
   php artisan migrate --seed
   ```

7. Build frontend assets:
   ```bash
   npm run dev
   ```

8. Start the development server:
   ```bash
   php artisan serve
   ```

9. Set up the Python service (for AI features):
   ```bash
   cd python_service
   pip install -r requirements.txt
   python app.py
   ```

### Docker Setup

Alternatively, you can use Docker for a containerized setup:

```bash
docker-compose up -d
```

This will start the following services:
- Web server (Nginx)
- PHP-FPM
- MySQL database
- Python service for AI features

## Usage

### Default User Accounts

After seeding the database, the following accounts are available:

- **Admin**:
  - Email: admin@learner.com
  - Password: password

- **Teacher**:
  - Email: teacher@learner.com
  - Password: password

- **Student**:
  - Email: student@learner.com
  - Password: password

### Key URLs

- **Main Application**: http://localhost:8000
- **Admin Dashboard**: http://localhost:8000/admin/dashboard
- **Teacher Dashboard**: http://localhost:8000/teacher/dashboard
- **Student Dashboard**: http://localhost:8000/student/dashboard

## Development

### Directory Structure

```
learner-env/
├─ app/
│  ├─ Http/
│  │  ├─ Controllers/
│  │  │  ├─ Auth
│  │  │  ├─ StudentController.php
│  │  │  ├─ TeacherController.php
│  │  │  ├─ AdminController.php
│  │  │  ├─ ChallengeController.php
│  │  │  ├─ CompetencyController.php
│  │  │  ├─ UserController.php
│  │  │  └─ HeatmapController.php
│  │  └─ Middleware/RoleMiddleware.php
│  ├─ Models/
│  │  ├─ User.php
│  │  ├─ LearnerProfile.php
│  │  ├─ Competency.php
│  │  ├─ Challenge.php
│  │  ├─ Attempt.php
│  │  └─ HeatmapLine.php
├─ database/
│  ├─ migrations/
│  └─ seeders/
├─ resources/views/
│  ├─ layouts/app.blade.php
│  ├─ student/
│  ├─ teacher/
│  ├─ admin/
│  └─ challenges/
├─ routes/web.php
├─ python_service/
│  ├─ app.py
│  ├─ evaluator.py
│  ├─ profile.py
│  ├─ cluster.py
│  ├─ expert_rules.py
│  └─ requirements.txt
└─ docker-compose.yml
```

### Adding New Features

1. Create necessary migrations for database changes
2. Implement models with appropriate relationships
3. Create or update service classes for business logic
4. Implement controller methods for request handling
5. Create or update views for UI components
6. Add routes to make the feature accessible
7. Write tests to ensure functionality

## Security

The application includes several security features:

- **PDOWrapper**: Prevents SQL injection through parameterized queries
- **EnhancedCsrfProtection**: Provides CSRF protection with additional security headers
- **SecurityScanner**: Detects common vulnerabilities in the codebase
- **Role-based Access Control**: Restricts access based on user roles

To run a security scan:

1. Log in as an administrator
2. Navigate to Security Dashboard
3. Click "Run Security Scan"

## Performance Optimization

The application uses several strategies for performance optimization:

- **CacheService**: Provides a unified interface for caching with group-based invalidation
- **Database Indexing**: Key fields are indexed for faster queries
- **Asset Bundling**: Frontend assets are bundled and minified

## Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature-name`
3. Commit your changes: `git commit -m 'Add some feature'`
4. Push to the branch: `git push origin feature-name`
5. Submit a pull request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgments

- Laravel Framework
- Bootstrap for UI components
- Chart.js for data visualization
- scikit-learn for machine learning algorithms
