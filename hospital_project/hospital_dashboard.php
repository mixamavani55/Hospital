<?php
include 'hospital_db.php';
session_start();

if (!isset($_SESSION['h_name'])) {
    header("Location: hospital_login.php");
    exit();
}

$user_name = $_SESSION['h_name'];
$user_email = $_SESSION['h_email'];
$user_role = $_SESSION['h_role'];

// ૧. ઓટો-એક્સપાયર લોજિક
$today = date('Y-m-d');
$conn->query("UPDATE hospital_appointments SET status = 'Expired' WHERE appointment_date < '$today' AND status = 'Pending'");

// ૨. ડેટા ફેચ કરવો
if ($user_role === 'patient') {
    $apt_sql = "SELECT * FROM hospital_appointments WHERE patient_name='$user_name' ORDER BY id DESC";
} else {
    $apt_sql = "SELECT * FROM hospital_appointments ORDER BY id DESC";
}

$apt_result = $conn->query($apt_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Dashboard - Lifeline</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { display: flex; min-height: 100vh; background: #0b111e; color: #fff; }

        .sidebar { width: 260px; background: #161f30; border-right: 2px solid #233044; display: flex; flex-direction: column; justify-content: space-between; padding: 25px 20px; }
        .brand { display: flex; align-items: center; gap: 12px; font-size: 22px; font-weight: 700; color: #00c9a7; margin-bottom: 40px; }
        .nav-menu { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .nav-item a { display: flex; align-items: center; gap: 14px; padding: 12px 16px; color: #9aa0a6; text-decoration: none; border-radius: 12px; font-weight: 500; transition: 0.3s; }
        .nav-item.active a, .nav-item a:hover { background: rgba(0, 201, 167, 0.12); color: #00c9a7; }
        .logout-btn { color: #ff4757 !important; }

        .main-viewport { flex: 1; padding: 30px; overflow-y: auto; }
        .top-navbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .user-welcome h1 { font-size: 26px; font-weight: 700; color: #fff; }
        .user-welcome p { color: #9aa0a6; font-size: 14px; }
        
        .profile-cluster { display: flex; align-items: center; gap: 15px; background: #1f2a3d; padding: 10px 18px; border-radius: 14px; border: 2px solid #233044; }
        .profile-info { text-align: right; }
        .profile-info h4 { font-size: 15px; color: #fff; }
        .profile-info span { font-size: 12px; color: #00c9a7; font-weight: 600; text-transform: uppercase; }

        .data-panel { background: #161f30; border-radius: 20px; border: 2px solid #233044; padding: 25px; margin-top: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.3); }
        .table-section h2 { font-size: 20px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; color: #00c9a7; }
        table { width: 100%; border-collapse: collapse; border-radius: 12px; overflow: hidden; }
        th, td { padding: 15px; text-align: left; font-size: 14px; }
        th { background: #1f2a3d; color: #00c9a7; font-weight: 600; border-bottom: 2px solid #233044; text-transform: uppercase; font-size: 12px; }
        td { background: rgba(31, 42, 61, 0.4); color: #cbd5e1; border-bottom: 1px solid #233044; }

        .badge { padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }
        .badge-accepted { background: rgba(0, 201, 167, 0.15); color: #00c9a7; border: 1px solid rgba(0, 201, 167, 0.3); }
        .badge-pending { background: rgba(245, 159, 0, 0.15); color: #f59f00; border: 1px solid rgba(245, 159, 0, 0.3); }
        .badge-expired { background: rgba(255, 71, 87, 0.15); color: #ff4757; border: 1px solid rgba(255, 71, 87, 0.3); }

        .cancel-btn { color: #ff4757; background: rgba(255, 71, 87, 0.1); padding: 6px 12px; border-radius: 8px; text-decoration: none; font-size: 12px; }
        .cancel-btn:hover { background: #ff4757; color: #fff; }

        @media (max-width: 900px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; }
            .main-viewport { padding: 20px 15px; }
            .data-panel { overflow-x: auto; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div>
            <div class="brand"><i class="fa-solid fa-hospital"></i> Lifeline</div>
            <ul class="nav-menu">
                <li class="nav-item active"><a href="hospital_dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
                <li class="nav-item"><a href="book_appointment.php"><i class="fa-solid fa-calendar-plus"></i> Book Appointment</a></li>
                <?php if ($user_role === 'staff' || $user_role === 'doctor'): ?>
                    <li class="nav-item"><a href="hospital_admin.php"><i class="fa-solid fa-user-md"></i> Doctor Console</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="hospital_logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
            </ul>
        </div>
    </div>

    <div class="main-viewport">
        <div class="top-navbar">
            <div class="user-welcome">
                <h1>Welcome Back, <?php echo htmlspecialchars($user_name); ?>!</h1>
                <p>Digital Healthcare Management Portal</p>
            </div>
            <div class="profile-cluster">
                <div class="profile-info">
                    <h4><?php echo htmlspecialchars($user_name); ?></h4>
                    <span><?php echo htmlspecialchars($user_role); ?></span>
                </div>
                <i class="fa-solid fa-circle-user fa-2x" style="color: #00c9a7;"></i>
            </div>
        </div>

        <div class="data-panel">
            <div class="table-section">
                <h2><i class="fa-solid fa-calendar-check"></i> Appointments & Prescription Records</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Doctor</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Diagnosis (બીમારી)</th>
                            <th>Treatment / Advice</th>
                            <th>Next Visit</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($apt_result && $apt_result->num_rows > 0): ?>
                            <?php while ($apt = $apt_result->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $apt['id']; ?></td>
                                    <td><?php echo htmlspecialchars($apt['doctor_name']); ?></td>
                                    <td><?php echo date('d-M-Y', strtotime($apt['appointment_date'])); ?></td>
                                    <td>
                                        <?php if ($apt['status'] == 'Expired'): ?>
                                            <span class="badge badge-expired">Expired</span>
                                        <?php elseif ($apt['status'] == 'Accepted'): ?>
                                            <span class="badge badge-accepted">Accepted</span>
                                        <?php else: ?>
                                            <span class="badge badge-pending">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <b><?php echo !empty($apt['disease_name']) ? htmlspecialchars($apt['disease_name']) : '-'; ?></b>
                                    </td>
                                    <td>
                                        <?php echo !empty($apt['doctor_response']) ? htmlspecialchars($apt['doctor_response']) : 'Waiting for consultation...'; ?>
                                    </td>
                                    <td>
                                        <span style="color: #00c9a7; font-weight: 600;">
                                            <?php echo !empty($apt['follow_up']) ? htmlspecialchars($apt['follow_up']) : '-'; ?>
                                        </span>
                                    </td>
                                        <td>
                                            <?php if ($apt['status'] === 'Pending'): ?>
                                            <a href="cancel_appointment.php?id=<?php echo $apt['id']; ?>" class="cancel-btn" onclick="return confirm('કેન્સલ કરવા માગો છો?');">Cancel</a>
                                            <?php elseif ($apt['status'] === 'Accepted'): ?>
                                            <a href="download_prescription.php?id=<?php echo $apt['id']; ?>" target="_blank" style="color: #00c9a7; text-decoration: none; font-weight: 600;">
                                            <i class="fa-solid fa-file-pdf"></i> Download PDF
                                            </a>
                                        <?php else: ?>
                                            <span style="color: #6c757d;">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="8" style="text-align: center; color: #9aa0a6;">No appointments found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>