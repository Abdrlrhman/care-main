[README.md](https://github.com/user-attachments/files/23764515/README.md)
# Clinic Management System

![Logo](path/to/your/logo.png) <!-- ضع رابط اللوجو هنا -->

## Description

Medical clinics often struggle with managing daily operations using traditional internal systems. This project presents an **integrated online medical clinic management system** that allows clinics to manage appointments, financial follow-ups, and team coordination efficiently while providing patients with a simple and easy way to book appointments online.

The system is built using **Laravel 12** with **Spatie Permissions** for secure role management and **Tailwind CSS** for a responsive and user-friendly design.

## Features

- Four main user roles: **Admin, Patient, Doctor, Staff**
- Manage patient data and medical records
- Manage doctors’ schedules and tasks
- Instant appointment booking by patients
- Billing and financial management
- Supports workflow efficiency for clinic staff
- Accessible online from anywhere
- Portable APK version available

## Technologies

- **Backend:** Laravel 12
- **Frontend:** Tailwind CSS
- **Database:** MySQL
- **Development Tools:** XAMPP, VS Code, Composer, Node.js
- **Deployment:** Infinity Free (web), APK24 (mobile APK)

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/Abdrlrhman/care-main
   ```
2. Navigate to the project folder:
   ```bash
   cd clinic-management-system
   ```
3. Install PHP dependencies:
   ```bash
   composer install
   ```
4. Install Node.js dependencies:
   ```bash
   npm install && npm run dev
   ```
5. Copy `.env.example` to `.env` and set your database credentials.
6. Generate application key:
   ```bash
   php artisan key:generate
   ```
7. Run migrations:
   ```bash
   php artisan migrate
   ```
8. Serve the project locally:
   ```bash
   php artisan serve
   ```

## Usage

- Admin can manage users, doctors, patients, appointments, and billing.
- Doctors can view schedules, patient records, and appointments.
- Staff can assist in daily clinic operations.
- Patients can book appointments, view their records, and manage their profiles.

## APK Version

- A portable APK version is created using [APK24](https://apk24.com/) for mobile use.

## Authors

- Abdallah Ahmed Abdallah Ahmed
- Abdalrahman Awad Mohammed
- Mustafa Abdalqader Mohamed Issa

## Supervisor

- T. Taysir Abbas Madani

## Institution

**Al Neelain University**  
Faculty of Engineering – Department of Computer Engineering

**Submission:** November 2025 – Bachelor's degree (Honors) in Computer Engineering
