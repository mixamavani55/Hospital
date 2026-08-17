<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Lifeline Hospital - Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif; }
        
        body { display:flex; justify-content:center; align-items:center; min-height:100vh; background:#0b111e; color: #fff; padding: 20px; }
        .container { position:relative; width: 75%; max-width: 1050px; height: 580px; background:#161f30; border-radius:24px; overflow:hidden; border: 2px solid #233044; box-shadow: 0 25px 60px rgba(0,0,0,0.5); display: flex; }
        .left { width:40%; background:linear-gradient(135deg,#00c9a7,#00897b); display:flex; justify-content:center; align-items:center; flex-direction:column; color:#fff; padding:35px; text-align:center; }
        .left h1 { font-size:42px; margin-bottom:12px; font-weight:700; letter-spacing: 1px; }
        .left p { color: #e0f7f4; font-size: 16px; line-height: 24px; }
        .form-box { width:60%; padding:40px; display: flex; flex-direction: column; justify-content: center; align-items: center; }
        form { width: 100%; max-width: 380px; }
        .form-box h2 { font-size: 34px; margin-bottom:6px; color: #fff; text-align: center; font-weight: 700; }
        .form-box span { display:block; text-align:center; color:#9aa0a6; margin-bottom:24px; font-size:15px; }
        .input { margin:14px 0; }
        .input input, .input select { width:100%; padding:14px 18px; border: 2px solid #233044; background: #1f2a3d; border-radius:12px; font-size:16px; color: #fff; outline:none; transition: 0.3s; }
        .input input:focus, .input select:focus { border-color:#00c9a7; box-shadow:0 0 10px rgba(0,201,167,.4); }
        .input select option { background: #161f30; color: #fff; padding: 12px; }
        button { width:100%; padding:14px; border:none; border-radius:12px; background:linear-gradient(90deg,#00c9a7,#00897b); color:#fff; font-size:17px; cursor:pointer; font-weight:700; transition: 0.3s; margin-top: 8px; }
        button:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,201,167,0.4); }
        .switch { margin-top:20px; text-align:center; color:#9aa0a6; font-size: 15px; }
        .switch a { color:#00c9a7; font-weight:600; cursor:pointer; text-decoration:none; }
        .switch a:hover { text-decoration: underline; }
        .login-form-active #login { display: block; }
        .login-form-active #register { display: none; }
        .register-form-active #login { display: none; }
        .register-form-active #register { display: block; }
        #register { display: none; }

        @media (max-width: 768px) {
            body { overflow-y: auto; padding: 10px; min-height: auto; }
            .container { flex-direction: column; width: 100%; height: auto; }
            .left, .form-box { width: 100%; padding: 25px 15px; }
        }
    </style>
</head>
<body class="login-form-active">

<div class="container">
    <div class="left">
        <h1 id="left-title">Lifeline</h1>
        <p id="left-text">Sign in to access your digital healthcare portal dashboard.</p>
    </div>

    <div class="form-box">
        <!-- LOGIN FORM -->
        <form action="hospital_auth.php" method="POST" id="login" autocomplete="off">
            <h2>Portal Login</h2>
            <span>Enter your secure credentials</span>
            <div class="input">
                <input type="email" name="email" placeholder="Enter Email" required autocomplete="off">
            </div>
            <div class="input">
                <input type="password" name="password" placeholder="Enter Password" required autocomplete="new-password">
            </div>
            <button type="submit" name="login">Login</button>
            <div class="switch">New to Lifeline? <a onclick="showRegister()">Register Here</a></div>
        </form>

        <!-- REGISTER FORM -->
        <form action="hospital_auth.php" method="POST" id="register" autocomplete="off">
            <h2>Create Account</h2>
            <span>Join our smart medical network</span>
            <div class="input">
                <input type="text" name="name" placeholder="Enter Username" required autocomplete="off">
            </div>
            <div class="input">
                <input type="email" name="email" placeholder="Enter Email" required autocomplete="off">
            </div>
            <div class="input">
                <input type="password" name="password" placeholder="Enter Password" required autocomplete="new-password">
            </div>
            <div class="input">
                <select name="role" required>
                    <option value="" disabled selected>Choose Profile Type</option>
                    <option value="patient">Patient</option>
                    <option value="doctor">Doctor</option>
                    <option value="staff">Staff / Receptionist</option>
                </select>
            </div>
            <button type="submit" name="register">Register Account</button>
            <div class="switch">Already registered? <a onclick="showLogin()">Login Instead</a></div>
        </form>
    </div>
</div>

<script>
    const body = document.body;
    const leftTitle = document.getElementById("left-title");
    const leftText = document.getElementById("left-text");

    function showRegister(){
        body.classList.remove("login-form-active");
        body.classList.add("register-form-active");
        leftTitle.innerText = "Care Plus";
        leftText.innerText = "Please select your proper role during registration to get customized dashboard access.";
    }
    function showLogin(){
        body.classList.remove("register-form-active");
        body.classList.add("login-form-active");
        leftTitle.innerText = "Lifeline";
        leftText.innerText = "Sign in to access your digital healthcare portal dashboard.";
    }
</script>
</body>
</html>