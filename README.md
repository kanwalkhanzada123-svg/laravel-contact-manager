# ?? LeadDesk - CRM & Lead Management System

A modern, full-stack Inquiry & Lead Management Dashboard built with **Laravel**, **Tailwind CSS**, and **Chart.js**. It streamlines contact form submissions, customer inquiries, email workflows, and lead qualification for businesses.

---

## ? Features

- ?? **Real-time Analytics & Charts:** Interactive Bar graphs and Doughnut conversion metrics powered by Chart.js.
- ? **Quick Reply Templates:** Single-click canned responses (Quotations, Meeting schedules, Query resolution).
- ?? **Automated Workflows:** Auto-responder acknowledgment emails sent to customers upon form submission.
- ? **Priority & Starred Leads:** Flag high-value inquiries to pin and filter them easily.
- ?? **Internal Admin Notes:** Keep private CRM notes and remarks per lead for team reference.
- ??? **Tab-Based Status Filters:** Instant filtering for *All*, *Pending*, *Replied*, and *Starred* inquiries.
- ?? **Bulk Actions:** Multi-select leads with checkboxes for batch deletion.
- ?? **CSV Export:** One-click data export of all inquiries and internal notes.
- ?? **Real-Time Search:** Search leads by name, email, message body, or private notes.
- ?? **Dark & Light Mode:** Seamless theme switching with persistent local storage state.
- ?? **Authentication & Security:** Protected CRM routes with session-based authentication.

---

## ??? Tech Stack

- **Backend:** Laravel, PHP
- **Database:** MySQL / SQLite (Eloquent ORM, Migrations)
- **Frontend:** Blade Templates, Tailwind CSS, JavaScript
- **Visuals:** Chart.js
- **Mailing:** Laravel Mailables, SMTP / Log Drivers

---

## ?? Installation & Local Setup

\\\ash
# 1. Clone repository
git clone https://github.com/kanwalkhanzada123-svg/laravel-contact-manager.git
cd laravel-contact-manager

# 2. Install dependencies
composer install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Run migrations
php artisan migrate

# 5. Start development server
php artisan serve
\\\

Access in browser:
- **Contact Form:** http://127.0.0.1:8000/contact
- **Admin CRM:** http://127.0.0.1:8000/messages
