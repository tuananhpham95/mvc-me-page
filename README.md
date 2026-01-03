# EduTracker – Learning Progress Tracker

![Skärmbild av min me-sida](/assets/images/edu.png)

[![Build Status](https://scrutinizer-ci.com/g/tuananhpham95/mvc-me-page/badges/build.png?b=main)](https://scrutinizer-ci.com/g/tuananhpham95/mvc-me-page/build-status/main)
[![Code Coverage](https://scrutinizer-ci.com/g/tuananhpham95/mvc-me-page/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/tuananhpham95/mvc-me-page/?branch=main)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/tuananhpham95/mvc-me-page/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/tuananhpham95/mvc-me-page/?branch=main)

---

## About this project

**EduTracker** is a personal learning progress tracker built as part of the **MVC course** at BTH.  
The project is developed using **Symfony**, **Doctrine ORM**, and **Twig**, demonstrating a full-stack web application following the MVC pattern.

With EduTracker, users can:

- Track learning items and goals.
- Save related resources (links, notes).
- Monitor progress with status badges: _Learning_, _Learned_, _To Learn_.
- Edit, delete, or view detailed information for each learning item.

---

## Features

- **CRUD functionality** for learning items (Create, Read, Update, Delete).
- **External resource linking** for each learning item.
- **Status tracking** with badges for easy visualization.
- **Responsive, modern design** with clean UI.
- **Centralized dashboard** showing all learning items in a card layout.

---

## Project Structure

- /proj # Project pages (Home, About, EduTracker)
- /edu # EduTracker templates (index, show, create, edit)
- /src # Controllers, Entities, Forms, Repositories
- /templates # Twig templates
- /public/assets # CSS, JS, Images
- /tests # PHPUnit tests
- /docs # Documentation and generated reports

---

## Getting started

Follow these steps to clone and run the application locally.

### Requirements

- **PHP** 8.1 or later
- **Composer**
- **Node.js & npm**
- **Symfony CLI**

### Installation

1. **Clone the repository**
   ```bash
   git clone git@github.com:tuananhpham95/mvc-me-page.git
   cd mvc-me-page
   ```
2. **Clone the repository**
   ```bash
   composer install
   ```
3. **Install and build frontend assets**
   ```bash
    npm install
    npm run build
   ```
4. **Start the Symfony development server**
   ```bash
   symfony server:start
   ```
5. **Open the project in your browser**
   http://localhost:8000/proj
