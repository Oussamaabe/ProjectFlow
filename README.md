# ProjectFlow - Enterprise Project Management Suite 🚀


[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Symfony Version](https://img.shields.io/badge/Symfony-6.3-blue.svg)](https://symfony.com/)
[![PHP Version](https://img.shields.io/badge/PHP-8.1+-purple.svg)](https://php.net/)
![GitHub last commit](https://img.shields.io/github/last-commit/Oussamaabe/ProjectFlow)

ProjectFlow is a comprehensive web application designed for enterprise-level project management. It provides companies with a complete solution for tracking projects, managing resources, controlling inventory, and handling technical documentation - all within a secure, role-based environment.

## 🌟 Key Features

- **Project Tracking & Management**  
  End-to-end project lifecycle management with timeline visualization
- **Resource Allocation**  
  Human and material resource management with capacity planning
- **Inventory & Procurement Control**  
  Real-time inventory tracking and purchase order management
- **Document Management**  
  Centralized repository for technical and administrative documents
- **Role-Based Access Control**  
  Granular permissions with organizational hierarchy support
- **Advanced Security**  
  Two-factor authentication and data encryption
- **Interactive Dashboard**  
  Real-time statistics and performance metrics visualization

## 🛠️ Tech Stack

**Backend**  
![Symfony](https://img.shields.io/badge/Symfony-000000?style=for-the-badge&logo=symfony&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

**Frontend**  
![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![Recharts](https://img.shields.io/badge/Recharts-FF6384?style=for-the-badge&logo=recharts&logoColor=white)

**Services**  
![SendGrid](https://img.shields.io/badge/SendGrid-00A98F?style=for-the-badge&logo=sendgrid&logoColor=white)
![Composer](https://img.shields.io/badge/Composer-885630?style=for-the-badge&logo=composer&logoColor=white)

**DevOps**  
![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![GitHub Actions](https://img.shields.io/badge/GitHub_Actions-2088FF?style=for-the-badge&logo=github-actions&logoColor=white)

## 🚀 Getting Started

### Prerequisites
- PHP 8.1+
- MySQL 5.7+
- Composer 2.0+
- Node.js 16+

### Installation
```bash
# Clone repository
git clone https://github.com/Oussamaabe/ProjectFlow.git

# Install PHP dependencies
cd ProjectFlow
composer install

# Install JavaScript dependencies
npm install

# Configure environment variables
cp .env.local.example .env.local

# Generate application secret
php bin/console secrets:generate-keys

# Create database
php bin/console doctrine:database:create

# Run migrations
php bin/console doctrine:migrations:migrate

# Load fixtures (sample data)
php bin/console doctrine:fixtures:load

# Start development server
symfony serve
```

## 🌐 Live Demo

Experience ProjectFlow: [https://podstream.netlify.app/]([https://climaviision.netlify.app/](https://climaa-vision.netlify.app/))  


## 🧩 Project Structure

```
ProjectFlow/
├── assets/              # Frontend assets (JS, CSS, images)
├── bin/                 # Console executables
├── config/              # Configuration files
├── migrations/          # Database migration scripts
├── public/              # Web server root directory
├── src/                 # Application source code
│   ├── Controller/      # Application controllers
│   ├── Entity/          # Doctrine entities
│   ├── Form/            # Form types
│   ├── Repository/      # Doctrine repositories
│   ├── Security/        # Authentication components
│   └── Service/         # Business logic services
├── templates/           # Twig templates
├── tests/               # Automated tests
├── var/                 # Generated files (cache, logs)
└── vendor/              # Composer dependencies
```

## 🛡️ Security Features

- **Two-Factor Authentication** (Email/SMS verification)
- Role-Based Access Control (RBAC) with hierarchical permissions
- Password encryption using Argon2 algorithm
- CSRF protection on all forms
- Secure session management
- Input validation and sanitization
- Rate limiting on authentication endpoints

## 📈 Reporting & Analytics

- Real-time project progress tracking
- Resource utilization heatmaps
- Budget vs actual spending reports
- Inventory turnover metrics
- Document version history tracking
- Customizable dashboard widgets

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/Improvement`)
3. Commit your changes (`git commit -m 'Add some feature'`)
4. Push to the branch (`git push origin feature/Improvement`)
5. Open a pull request

Please ensure your code follows PSR-12 coding standards and includes appropriate tests.

## 📜 License

Distributed under the MIT License. See `LICENSE` for more information.

## ✉️ Contact

Project Maintainer: [Oussama Belfakir](https://github.com/Oussamaabe)  
Email: oussama.be005@gmail.com

---

**ProjectFlow - Streamlining Enterprise Project Management**  
*Developed with Symfony and Bootstrap | May 2025 - July 2025*
