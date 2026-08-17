# 🏥 Hospital Management System (PHP & MySQL)

A web-based Hospital Management System developed using **PHP**, **MySQL**, **HTML5**, **CSS3**, and **JavaScript**. This system manages patient appointments, doctor consultations, digital prescription generation, and automated status tracking.

---

## ✨ Features

### 👤 Patient Panel
- **Book Appointments:** Easily schedule appointments with doctors and specializations.
- **Appointment Dashboard:** View live status of booked appointments (Pending, Accepted, Expired).
- **Digital Prescription PDF:** Download/Print official digital prescriptions with doctor's advice, diagnosis, and follow-up dates.
- **Cancel Request:** Cancel pending appointments directly from the dashboard.

### 👨‍⚕️ Doctor / Admin Panel
- **Consultation Management:** Review pending patient appointments.
- **Medical Advice & Diagnosis:** Add disease names, clinical response/prescriptions, and select follow-up durations (e.g., 1 Month, 3 Months).
- **Auto-Update Status:** Automatically marks appointments as `Accepted` upon response submission.

### ⚙️ System Features
- **Auto-Expiry System:** Automatically marks unhandled appointments as `Expired` once the appointment date passes.
- **Dynamic SQL Querying:** Handles case-insensitive user profile matching.
- **Print-Ready Prescription:** Clean, CSS-styled printable prescription template (`download_prescription.php`).

---

## 🛠️ Tech Stack

- **Frontend:** HTML5, CSS3, JavaScript, FontAwesome Icons
- **Backend:** PHP (Procedural)
- **Database:** MySQL / MariaDB
- **Server Environment:** XAMPP / WAMP / Apache

---

## 🗄️ Database Structure

The main table used for handling appointments is `hospital_appointments`:

```sql
CREATE TABLE hospital_appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_name VARCHAR(255) NOT NULL,
    doctor_name VARCHAR(255) NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time VARCHAR(50) DEFAULT '10:00 AM',
    status VARCHAR(50) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    doctor_response TEXT DEFAULT NULL,
    disease_name VARCHAR(255) DEFAULT NULL,
    follow_up VARCHAR(100) DEFAULT NULL
);
