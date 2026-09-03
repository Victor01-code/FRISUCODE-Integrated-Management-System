# FRISUCODE Management System & Global Platform 🌍

![Status](https://img.shields.io/badge/Status-Active-success)
![Version](https://img.shields.io/badge/Version-1.0.0-blue)
![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green)

A comprehensive, multilingual digital transformation platform designed for **FRISUCODE**, a registered Tanzanian Non-Governmental Organization (NGO Reg #67812) based in Arusha. 

This platform serves as both a public-facing gateway for international donors/partners and a secure, role-based internal management system for staff to track beneficiaries, projects, and financials.

---

## 🎯 Core Features

### 1. Public NGO Website
A high-performance, SEO-optimized, and beautifully designed gateway tailored for international sponsors and institutional partners.
*   **🌍 Full Multilingual Support**: Seamlessly switch between English, Swahili, French, German, and Spanish.
*   **💳 Integrated Donation Hub**: Accept one-time and monthly recurring donations (Credit Card, Mobile Money).
*   **🤝 Dedicated Partners Portal**: Structured partnership tiers and clear UN SDG alignment to attract corporate CSR and foundations.
*   **📊 Transparent Reporting**: Dedicated transparency page with live impact metrics, downloadable annual reports, and a financial allocation breakdown.
*   **📱 Mobile-First Design**: Optimized for everything from large desktop displays to budget smartphones used in the field.

### 2. Internal Management System (Smart Office)
A secure portal for staff, project managers, and finance officers.
*   **👥 Beneficiary Tracking**: Centralized student registry, tracking education progress, school fees, and mentorship.
*   **📈 Project Lifecycle Management**: Track community infrastructure projects, health initiatives, and micro-loans from start to finish.
*   **💰 Financial Treasury**: Audit-ready financial records tracking income and expenditures.
*   **🔐 Role-Based Security**: Multi-level permissions (Super Admin, Project Manager, Finance, Staff) protected by BCRYPT password hashing.
*   **📧 Automated Email System**: Instant, branded email notifications for new donations, account credentials, and security alerts.

---

## 🛠️ Technology Stack

*   **Frontend**: HTML5, Vanilla CSS3 (Custom Design System, Glassmorphism), JavaScript (Vanilla)
*   **Backend**: PHP 8+ (Custom MVC-inspired architecture)
*   **Database**: MySQL / MariaDB (PDO)
*   **Icons**: FontAwesome 6
*   **Fonts**: Google Fonts (Inter, Outfit)

---

## 🚀 Quick Start Guide

### Prerequisites
*   Web Server (Apache/Nginx)
*   PHP 8.0 or higher
*   MySQL 5.7+ or MariaDB

### Installation

1.  **Clone the repository**
    ```bash
    git clone https://github.com/yourusername/frisucode_ms.git
    cd frisucode_ms
    ```

2.  **Environment Setup**
    *   Move the project to your web server's root directory (e.g., `htdocs` for XAMPP or `/var/www/html`).
    *   Ensure the `uploads` directory is writable by the web server.

3.  **Database Configuration**
    *   Create a new MySQL database named `frisucode_db`.
    *   Import the provided SQL schema (located in `database/schema.sql` if applicable).
    *   Update the database credentials in `system/config/db.php`:
      ```php
      define('DB_HOST', 'localhost');
      define('DB_USER', 'root');
      define('DB_PASS', '');
      define('DB_NAME', 'frisucode_db');
      ```

4.  **Access the System**
    *   Public Site: `http://localhost/frisucode_ms/`
    *   Staff Portal: `http://localhost/frisucode_ms/system/auth/login.php`

---

## 📂 Directory Structure

```text
frisucode_ms/
├── app/                  # Core application logic & controllers
├── assets/               # CSS, JS, Images, and static assets
│   ├── css/              # Custom stylesheets (style.css)
│   └── images/           # Brand assets, logos, placeholder images
├── database/             # SQL schemas and migration scripts
├── lang/                 # Translation dictionaries (EN, SW, FR, DE, ES)
├── partials/             # Reusable UI components (header, footer)
├── system/               # Internal management (auth, config, admin dashboard)
│   ├── auth/             # Login, password reset, session logic
│   └── config/           # Database connections and global settings
├── uploads/              # User-uploaded files (beneficiary photos, reports)
└── [Public Pages]        # index.php, about.php, programs.php, partners.php, etc.
```

---

## 🤝 Contribution Guidelines

This project was custom-built for FRISUCODE. If you are a volunteer developer looking to contribute:
1.  Fork the repository.
2.  Create a feature branch (`git checkout -b feature/AmazingFeature`).
3.  Commit your changes (`git commit -m 'Add some AmazingFeature'`).
4.  Push to the branch (`git push origin feature/AmazingFeature`).
5.  Open a Pull Request.

---

## 📜 License

This project is licensed under the MIT License. See the `LICENSE` file for details.

---

*Empowering Communities Through Education — [www.frisucode.com](https://www.frisucode.com)*
