<?php
include 'hospital_db.php';
session_start();

if (!isset($_SESSION['h_name']) || !isset($_GET['id'])) {
    header("Location: hospital_dashboard.php");
    exit();
}

$apt_id = intval($_GET['id']);
$user_name = $_SESSION['h_name'];

// પેશન્ટની એપોઇન્ટમેન્ટ ડેટા મેળવવો
$result = $conn->query("SELECT * FROM hospital_appointments WHERE id = $apt_id AND (patient_name = '$user_name' OR '{$_SESSION['h_role']}' IN ('doctor', 'staff'))");

if (!$result || $result->num_rows == 0) {
    echo "Prescription not found!";
    exit();
}

$apt = $result->fetch_assoc();

if ($apt['status'] !== 'Accepted') {
    echo "Prescription is only available for accepted appointments.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prescription_#<?php echo $apt['id']; ?></title>
    <style>
        body { font-family: 'Arial', sans-serif; padding: 40px; color: #333; background: #fff; }
        .prescription-card { border: 2px solid #00c9a7; padding: 30px; border-radius: 12px; max-width: 800px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #00c9a7; padding-bottom: 15px; margin-bottom: 20px; }
        .hospital-name { font-size: 28px; font-weight: bold; color: #00c9a7; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px; background: #f8f9fa; padding: 15px; border-radius: 8px; }
        .section-title { font-size: 16px; font-weight: bold; color: #00c9a7; margin-top: 15px; margin-bottom: 5px; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
        .content-box { font-size: 15px; line-height: 1.6; margin-bottom: 15px; white-space: pre-line; }
        .footer { text-align: center; margin-top: 40px; font-size: 12px; color: #777; border-top: 1px solid #eee; padding-top: 15px; }
        .print-btn { display: block; width: 100%; max-width: 200px; margin: 20px auto 0 auto; padding: 12px; background: #00c9a7; color: #fff; text-align: center; font-weight: bold; text-decoration: none; border-radius: 8px; border: none; cursor: pointer; }
        @media print {
            .print-btn { display: none; }
            body { padding: 0; }
            .prescription-card { border: none; }
        }
    </style>
</head>
<body>

<div class="prescription-card">
    <div class="header">
        <div>
            <div class="hospital-name">Lifeline Healthcare</div>
            <div style="font-size: 13px; color: #555;">Digital Medical Prescription</div>
        </div>
        <div style="text-align: right; font-size: 13px;">
            <b>Prescription ID:</b> #<?php echo $apt['id']; ?><br>
            <b>Date:</b> <?php echo date('d-M-Y', strtotime($apt['appointment_date'])); ?>
        </div>
    </div>

    <div class="info-grid">
        <div><b>Patient Name:</b> <?php echo htmlspecialchars($apt['patient_name']); ?></div>
        <div><b>Attending Doctor:</b> <?php echo htmlspecialchars($apt['doctor_name']); ?></div>
        <div><b>Appointment Time:</b> <?php echo htmlspecialchars($apt['appointment_time']); ?></div>
        <div><b>Status:</b> Completed & Accepted</div>
    </div>

    <div class="section-title">DIAGNOSIS / DISEASE</div>
    <div class="content-box"><b><?php echo htmlspecialchars($apt['disease_name']); ?></b></div>

    <div class="section-title">DOCTOR'S ADVICE & PRESCRIPTION</div>
    <div class="content-box"><?php echo htmlspecialchars($apt['doctor_response']); ?></div>

    <div class="section-title">NEXT FOLLOW-UP VISIT</div>
    <div class="content-box" style="color: #00c9a7; font-weight: bold;"><?php echo htmlspecialchars($apt['follow_up']); ?></div>

    <div class="footer">
        <p>This is a computer-generated prescription from Lifeline Healthcare System.</p>
    </div>
</div>

<button onclick="window.print()" class="print-btn">🖨️ Download / Print PDF</button>

</body>
</html>