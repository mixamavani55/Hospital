<?php
include 'hospital_db.php';
session_start();

// સીક્યોરિટી ચેક: ફક્ત સ્ટાફ કે ડોક્ટર જ આ પેજ જોઈ શકે
if (!isset($_SESSION['h_name']) || ($_SESSION['h_role'] !== 'doctor' && $_SESSION['h_role'] !== 'staff')) {
    header("Location: hospital_dashboard.php");
    exit();
}

$msg = "";

// ૧. ઓટો-એક્સપાયર લોજિક: જે તારીખ વીતી ગઈ હોય તેને આપોઆપ Expired કરી દેશે
$today = date('Y-m-d');
$conn->query("UPDATE hospital_appointments SET status = 'Expired' WHERE appointment_date < '$today' AND status = 'Pending'");

// ૨. ડોક્ટર તપાસ કરીને રિસ્પોન્સ, બીમારી અને ફોલો-અપ ટાઈમ આપે
if (isset($_POST['submit_response'])) {
    $apt_id = intval($_POST['apt_id']);
    $disease_name = $conn->real_escape_string($_POST['disease_name']);
    $doctor_response = $conn->real_escape_string($_POST['doctor_response']);
    $follow_up = $conn->real_escape_string($_POST['follow_up']);

    $update_sql = "UPDATE hospital_appointments 
                   SET status = 'Accepted', 
                       disease_name = '$disease_name', 
                       doctor_response = '$doctor_response', 
                       follow_up = '$follow_up' 
                   WHERE id = $apt_id";

    if ($conn->query($update_sql)) {
        // પેશન્ટની માહિતી ફેચ કરો (ઈમેઈલ મેળવવા)
        $patient_query = $conn->query("SELECT * FROM hospital_appointments WHERE id = $apt_id");
        if ($patient_query && $patient_query->num_rows > 0) {
            $patient = $patient_query->fetch_assoc();
            
            // જો પેશન્ટનો ઈમેઈલ ટેબલમાં હાજર હોય તો ઈમેઈલ મોકલો
            if (!empty($patient['patient_email'])) {
                $to = $patient['patient_email'];
                $subject = "Medical Prescription Update - Lifeline Healthcare";
                $message = "Hello " . $patient['patient_name'] . ",\n\n"
                         . "Your appointment consultation response is now available.\n\n"
                         . "Diagnosis: " . $disease_name . "\n"
                         . "Doctor's Advice: " . $doctor_response . "\n"
                         . "Next Visit: " . $follow_up . "\n\n"
                         . "You can login to your dashboard and download your digital prescription.\n\n"
                         . "Regards,\nLifeline Healthcare Team";
                $headers = "From: no-reply@lifelinehospital.com";

                @mail($to, $subject, $message, $headers);
            }
        }

        $msg = "<div style='color: #00c9a7; margin-bottom: 15px;'>પેશન્ટની તપાસ પૂર્ણ થઈ ગઈ છે અને રિસ્પોન્સ સાથે ઈમેઈલ પણ મોકલાઈ ગયો છે!</div>";
    }
}

// એપોઇન્ટમેન્ટ્સ ફેચ કરવી
$appointments = $conn->query("SELECT * FROM hospital_appointments ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Panel - Lifeline</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif; }
        body { background:#0b111e; color:#fff; padding:30px; }
        .container { max-width: 1100px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        h1 { color: #00c9a7; }
        .back-btn { background: #1f2a3d; color: #fff; padding: 10px 18px; text-decoration: none; border-radius: 10px; border: 1px solid #233044; }
        .card { background: #161f30; border: 2px solid #233044; border-radius: 16px; padding: 20px; margin-bottom: 20px; }
        .badge { padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; }
        .badge-pending { background: rgba(245, 159, 0, 0.2); color: #f59f00; }
        .badge-accepted { background: rgba(0, 201, 167, 0.2); color: #00c9a7; }
        .badge-expired { background: rgba(255, 71, 87, 0.2); color: #ff4757; }
        input[type="text"], textarea, select { width: 100%; padding: 10px; background: #1f2a3d; border: 1px solid #233044; color: #fff; border-radius: 8px; margin: 6px 0 14px 0; outline: none; }
        .btn-submit { background: #00c9a7; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1><i class="fa-solid fa-user-md"></i> Doctor Consultation Panel</h1>
        <a href="hospital_dashboard.php" class="back-btn">← Dashboard</a>
    </div>

    <?php echo $msg; ?>

    <?php if ($appointments && $appointments->num_rows > 0): ?>
        <?php while ($apt = $appointments->fetch_assoc()): ?>
            <div class="card">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <h3>Patient: <?php echo htmlspecialchars($apt['patient_name']); ?></h3>
                    <span class="badge badge-<?php echo strtolower($apt['status']); ?>"><?php echo $apt['status']; ?></span>
                </div>
                <p style="color: #9aa0a6; margin-top: 5px;">Date: <?php echo $apt['appointment_date']; ?> | Time: <?php echo $apt['appointment_time']; ?></p>
                <p style="margin-top: 10px;"><b>Problem Description:</b> <?php echo htmlspecialchars($apt['problem_description']); ?></p>

                <?php if ($apt['status'] === 'Pending'): ?>
                    <form method="POST" style="margin-top: 15px; border-top: 1px solid #233044; padding-top: 15px;">
                        <input type="hidden" name="apt_id" value="<?php echo $apt['id']; ?>">
                        
                        <label style="font-size: 14px; color: #00c9a7;">૧. કેવી બીમારી છે? (Disease / Diagnosis):</label>
                        <input type="text" name="disease_name" placeholder="e.g. Viral Fever, Migraine, High Blood Pressure" required>

                        <label style="font-size: 14px; color: #00c9a7;">૨. શું કરવું પડશે? (Treatment / Doctor Advice):</label>
                        <textarea name="doctor_response" rows="3" placeholder="દવાઓ અથવા કાળજી રાખવાની બાબતો લખો..." required></textarea>

                        <label style="font-size: 14px; color: #00c9a7;">૩. ક્યારે ફરી બતાવવા આવવું? (Next Follow-up Visit):</label>
                        <select name="follow_up" required>
                            <option value="No Follow-up Needed">ફરી આવવાની જરૂર નથી (No Follow-up)</option>
                            <option value="1 Week (૧ અઠવાડિયા પછી)">૧ અઠવાડિયા પછી (1 Week)</option>
                            <option value="2 Weeks (૨ અઠવાડિયા પછી)">૨ અઠવાડિયા પછી (2 Weeks)</option>
                            <option value="1 Month (૧ મહિના પછી)">૧ મહિના પછી (1 Month)</option>
                            <option value="3 Months (૩ મહિના પછી)">૩ મહિના પછી (3 Months)</option>
                        </select>

                        <button type="submit" name="submit_response" class="btn-submit">Accept & Send Consultation Report</button>
                    </form>
                <?php elseif ($apt['status'] === 'Accepted'): ?>
                    <div style="margin-top: 15px; background: #1f2a3d; padding: 15px; border-radius: 10px; border-left: 4px solid #00c9a7;">
                        <p style="margin-bottom: 5px;"><b style="color: #00c9a7;">Disease:</b> <?php echo htmlspecialchars($apt['disease_name']); ?></p>
                        <p style="margin-bottom: 5px;"><b style="color: #00c9a7;">Advice / Treatment:</b> <?php echo htmlspecialchars($apt['doctor_response']); ?></p>
                        <p><b style="color: #00c9a7;">Next Visit:</b> <?php echo htmlspecialchars($apt['follow_up']); ?></p>
                    </div>
                <?php elseif ($apt['status'] === 'Expired'): ?>
                    <p style="color: #ff4757; font-size: 13px; margin-top: 10px;">દર્દી સમયસર ઉપસ્થિત ન રહેતા એપોઇન્ટમેન્ટ Expired થઈ ગઈ છે.</p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No appointments found.</p>
    <?php endif; ?>
</div>

</body>
</html>