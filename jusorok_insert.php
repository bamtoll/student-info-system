<?php
require_once 'config.php';
check_login();

$errors = [];
$data   = ['이름'=>'','주소'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['이름'] = trim($_POST['이름'] ?? '');
    $data['주소'] = trim($_POST['주소'] ?? '');

    if ($data['이름'] === '') $errors[] = '이름을 입력하세요.';
    if ($data['주소'] === '') $errors[] = '주소를 입력하세요.';

    if (empty($errors)) {
        $conn = db_connect();
        $stmt = $conn->prepare("INSERT INTO jusorok (이름, 주소) VALUES (?, ?)");
        $stmt->bind_param('ss', $data['이름'], $data['주소']);
        if ($stmt->execute()) {
            $stmt->close(); $conn->close();
            header("Location: jusorok_list.php?msg=inserted");
            exit;
        } else {
            $errors[] = 'DB 오류: ' . $stmt->error;
        }
        $stmt->close(); $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>주소 등록 - <?= SITE_NAME ?></title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header class="top-header">
  <div class="logo">🎓 <span>밤톨공업대학교</span> 학생정보서비스</div>
  <div class="user-info">
    <span>👤 <?= h($_SESSION['login_user']) ?> 님</span>
    <a href="logout.php">로그아웃</a>
  </div>
</header>

<div class="layout">
  <?php include 'includes/sidebar.php'; ?>

  <main class="content">
    <div class="page-title">➕ 주소 등록
      <span class="breadcrumb">홈 / 주소록 관리 / 등록</span>
    </div>
    <div class="divider"></div>

    <div class="card" style="max-width:520px;">
      <div class="card-title">📌 주소록 입력</div>

      <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        ⚠️ <?= implode('<br>⚠️ ', array_map('h', $errors)) ?>
      </div>
      <?php endif; ?>

      <form method="post" action="jusorok_insert.php">
        <div class="form-group">
          <label>이름 <span class="req">*</span></label>
          <input type="text" name="이름" class="form-control"
                 value="<?= h($data['이름']) ?>"
                 placeholder="학생 이름 입력" maxlength="50" required autofocus>
        </div>
        <div class="form-group">
          <label>주소 <span class="req">*</span></label>
          <input type="text" name="주소" class="form-control"
                 value="<?= h($data['주소']) ?>"
                 placeholder="예) 대구시 북구 칠성동 123" maxlength="200" required>
        </div>
        <div style="display:flex;gap:10px;">
          <button type="submit" class="btn btn-success">💾 등록</button>
          <a href="jusorok_list.php" class="btn btn-secondary">취소</a>
        </div>
      </form>
    </div>
  </main>
</div>
</body>
</html>
