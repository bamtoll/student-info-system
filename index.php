<?php
require_once 'config.php';
if (!empty($_SESSION['login_user'])) { header("Location: main.php"); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id   = trim($_POST['user_id']   ?? '');
    $pass = trim($_POST['user_pass'] ?? '');
    if ($id === ADMIN_ID && $pass === ADMIN_PASS) {
        $_SESSION['login_user'] = $id;
        $_SESSION['login_time'] = date('Y-m-d H:i:s');
        header("Location: main.php"); exit;
    } else {
        $error = '아이디 또는 패스워드가 올바르지 않습니다.';
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= SITE_NAME ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Malgun Gothic', '맑은 고딕', sans-serif;
    min-height: 100vh;
    background: #dde3ea;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

/* ── 대각선 장식 (좌하단) ── */
.deco-bl-1 {
    position: fixed;
    bottom: -120px; left: -80px;
    width: 520px; height: 180px;
    background: linear-gradient(135deg, #1a3a20 0%, #2e7d32 100%);
    transform: rotate(-20deg);
    opacity: .90;
    z-index: 0;
}
.deco-bl-2 {
    position: fixed;
    bottom: -80px; left: 60px;
    width: 520px; height: 90px;
    background: linear-gradient(135deg, #2e7d32 0%, #43a047 100%);
    transform: rotate(-20deg);
    opacity: .70;
    z-index: 0;
}

/* ── 대각선 장식 (우상단) ── */
.deco-tr-1 {
    position: fixed;
    top: -80px; right: -60px;
    width: 320px; height: 160px;
    background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);
    transform: rotate(-20deg);
    opacity: .85;
    z-index: 0;
}
.deco-tr-2 {
    position: fixed;
    top: 30px; right: -20px;
    width: 320px; height: 60px;
    background: linear-gradient(135deg, #43a047 0%, #66bb6a 100%);
    transform: rotate(-20deg);
    opacity: .60;
    z-index: 0;
}

/* ── 우상단 로고 ── */
.top-logo {
    position: fixed;
    top: 22px; right: 36px;
    text-align: right;
    z-index: 10;
    line-height: 1.3;
}
.top-logo .eng-small {
    font-size: 10px;
    color: rgba(255,255,255,0.7);
    letter-spacing: 2px;
    font-weight: 600;
}
.top-logo .kor-name {
    font-size: 17px;
    font-weight: 700;
    color: #fff;
    letter-spacing: 1px;
}
.top-logo .eng-full {
    font-size: 9px;
    color: rgba(255,255,255,0.55);
    letter-spacing: 1.5px;
    text-transform: uppercase;
}

/* ── 중앙 컴포지션 ── */
.login-wrap {
    position: relative;
    z-index: 5;
    width: 780px;
    height: 440px;
}

/* ── 다크 패널 ── */
.login-panel {
    position: absolute;
    left: 0; top: 0;
    width: 630px;
    height: 440px;
    background: rgba(52, 62, 72, 0.93);
    border-radius: 3px;
    padding: 44px 48px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden;
}

/* LOGIN 워터마크 */
.login-panel::before {
    content: 'LOGIN';
    position: absolute;
    top: 28px; left: 32px;
    font-size: 80px;
    font-weight: 900;
    color: rgba(255,255,255,0.06);
    letter-spacing: 8px;
    pointer-events: none;
    font-style: italic;
}

.school-title {
    color: #fff;
    font-size: 19px;
    font-weight: 700;
    letter-spacing: 1px;
    z-index: 1;
    margin-top: 8px;
}
.school-title span { color: #a5d6a7; }

.forgot-link {
    font-size: 12px;
    color: rgba(255,255,255,0.55);
    text-decoration: none;
    line-height: 1.7;
    z-index: 1;
    align-self: flex-start;
}
.forgot-link:hover { color: #a5d6a7; }

/* ── 흰색 로그인 카드 (우측 겹침) ── */
.login-card {
    position: absolute;
    right: 0; top: 50%;
    transform: translateY(-50%);
    width: 400px;
    background: #fff;
    padding: 52px 44px 44px;
    box-shadow: 0 12px 48px rgba(0,0,0,0.20);
    border-radius: 2px;
}

/* 오류 메시지 */
.login-error {
    background: #fff0f0;
    border: 1px solid #f5c6cb;
    color: #c0392b;
    border-radius: 4px;
    padding: 9px 12px;
    font-size: 12px;
    margin-bottom: 16px;
    text-align: center;
}

/* 입력 필드 */
.field {
    display: flex;
    align-items: center;
    border-bottom: 1.5px solid #dde2e6;
    margin-bottom: 34px;
    padding-bottom: 10px;
    transition: border-color .2s;
}
.field:focus-within { border-bottom-color: #2e7d32; }
.field .ico {
    font-size: 18px;
    color: #c0c0c0;
    width: 40px;
    text-align: center;
    flex-shrink: 0;
}
.field .ico i {
    color: #c0c0c0;
}
.field input {
    flex: 1;
    border: none;
    outline: none;
    font-size: 14px;
    color: #333;
    background: transparent;
    font-family: inherit;
    padding: 2px 0 2px 6px;
}
.field input::placeholder { color: #ccc; }

/* 로그인 버튼 */
.card-bottom {
    display: flex;
    justify-content: flex-end;
    margin-top: 4px;
}
.btn-login {
    background: #1e6b3a;
    color: #fff;
    border: none;
    padding: 15px 38px;
    font-size: 15px;
    font-weight: 700;
    letter-spacing: 3px;
    cursor: pointer;
    border-radius: 3px;
    font-family: inherit;
    transition: background .2s;
}
.btn-login:hover { background: #155724; }

/* ── 하단 저작권 ── */
footer {
    position: fixed;
    bottom: 20px; width: 100%;
    text-align: center;
    font-size: 11.5px;
    color: #999;
    z-index: 5;
    letter-spacing: 0.5px;
}
footer strong { color: #2e7d32; }
</style>
</head>
<body>

<!-- 대각선 장식 -->
<div class="deco-bl-1"></div>
<div class="deco-bl-2"></div>
<div class="deco-tr-1"></div>
<div class="deco-tr-2"></div>

<!-- 우상단 로고 -->
<div class="top-logo">
    <div class="eng-small">Job 1st</div>
    <div class="kor-name">밤톨공업대학교</div>
    <div class="eng-full">BamToll University Of Technical</div>
</div>

<!-- 중앙 로그인 컴포지션 -->
<div class="login-wrap">

    <!-- 다크 패널 -->
    <div class="login-panel">
        <div class="school-title">
            <span>밤톨공업대학교</span> 학생정보서비스
        </div>
        <a href="#" class="forgot-link">
            아이디/패스워드를<br>잊어버리셨나요?
        </a>
    </div>

    <!-- 흰 로그인 카드 -->
    <div class="login-card">

        <?php if ($error): ?>
        <div class="login-error">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="index.php" autocomplete="off">
            <!-- 아이디 -->
            <div class="field">
                <span class="ico"><i class="fa-regular fa-user"></i></span>
                <input type="text" name="user_id"
                       placeholder="아이디"
                       value="<?= htmlspecialchars($_POST['user_id'] ?? '') ?>"
                       required autofocus>
            </div>

            <!-- 패스워드 -->
            <div class="field">
                <span class="ico"><i class="fa-solid fa-key"></i></span>
                <input type="password" name="user_pass"
                       placeholder="패스워드"
                       required>
            </div>

            <!-- 로그인 버튼 -->
            <div class="card-bottom">
                <button type="submit" class="btn-login">
                    LOGIN
                </button>
            </div>
        </form>

    </div><!-- /login-card -->

</div><!-- /login-wrap -->

<!-- 하단 저작권 -->
<footer>
    Copyright &copy; 2024 &nbsp;<strong>BamToll University Of Technical</strong>&nbsp; All Rights Reserved.
</footer>

</body>
</html>
