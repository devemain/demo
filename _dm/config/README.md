# DeveMain Demo Project

![PHP Version](https://img.shields.io/badge/PHP-8.4%2B-blue)
![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red)
![Vue.js Version](https://img.shields.io/badge/Vue.js-3.x-brightgreen)
![AI API](https://img.shields.io/badge/AI--API-Groq-green)
![CI/CD](https://img.shields.io/badge/CI/CD-GitHub_Actions-blue)
![License](https://img.shields.io/badge/License-PROPRIETARY-orange)
![Status](https://img.shields.io/badge/Status-Active-brightgreen)

A professional Laravel project template showcasing modern development practices, Docker setup, and automation tools. Perfect for starting new projects with best practices already configured.

## Live Demo
**[View Live Demo](https://demo-xbqd.onrender.com)**

*Note: This project is hosted on Render's Free plan, which has significant limitations.*

### 🚫 Plan Limitations & Considerations:
- **Limited Resources** - Must use fast, lightweight applications due to constrained CPU/RAM
- **No Permanent Database** - Only a 30-day trial database; all data is deleted afterward
- **Limited Runtime** - 750 active hours per month (shared across all services)
- **Sleep Mode** - Services automatically sleep after 15 minutes of inactivity and may restart anytime
- **Ephemeral Storage** - All data is lost on sleep/restart (temporary file system)
- **Scaling Restrictions** - Only 1 instance, no SSH access, no persistent disks, no caching
- **Network Limitations** - No SMTP ports, cannot receive private network traffic
- **Monthly Quotas** - 100GB bandwidth & 500 build minutes; service pauses if exceeded
- **Potential Suspension** - May be suspended with high outgoing traffic

*Features and functionality are subject to change and expansion.*

## Features

### Development Foundation:
- **Custom Development Tools** - CLI utilities for common tasks
- **Dockerized Environment** - Pre-configured with essential services
- **CI/CD Pipeline** - Automated building and deployment with GitHub Actions
- **Automated Workflows** - Git hooks and deployment scripts
- **Semantic Commits** - Enforced commit message conventions for clean history
- **Automated Tagging** - Configured bot for automatic version tagging
- **Modern PHP Practices** - Clean code architecture and following Laravel conventions
- **Vue.js Integration** - Progressive JavaScript framework for reactive interfaces

### AI-Powered Content:
- **AI Fact Generation** - Powered by Groq AI API for intelligent content
- **Random Tech Facts** - Dynamic, AI-generated facts on homepage
- **Real-time Updates** - Live view tracking and statistics
- **Interactive Experience** - Copy-to-clipboard functionality

### Content Management:
- **Complete Fact Database** - Paginated listing with search
- **Statistics Dashboard** - Detailed analytics and insights
- **Search** - Quick fact discovery

### Vue.js Features:
- **Single File Components** - Clean separation of template, script, and styles
- **Reactive Data Binding** - Automatic UI updates when data changes
- **Component-Based Architecture** - Reusable and maintainable UI components

## Technology Stack

### Core:
- **PHP 8.4+** - Latest PHP features and performance
- **Laravel 12.x** - Progressive PHP framework
- **SQLite** - Lightweight database for development
- **Composer** - PHP dependency management

### Frontend & UI:
- **Vue.js 3.x** - Progressive JavaScript framework for building user interfaces
- **Tailwind CSS** - Utility-first CSS framework
- **Font Awesome** - Professional icon toolkit
- **Vite** - Next-generation frontend tooling
- **npm** - JavaScript package management

### Infrastructure & DevOps:
- **Docker & Docker Compose** - Containerized development
- **Linux** - Production-ready server environment
- **Nginx** - High-performance web server
- **GitHub Actions** - Automated CI/CD pipelines
- **Render.com** - Cloud platform for deployment

### External Services:
- **Groq AI API** - Intelligent fact generation and processing

## Quick Start

```bash
# Clone the repository
git clone https://github.com/devemain/demo.git
cd demo

# Run setup script
./dm.sh

# Start development
./docker.sh start
```
