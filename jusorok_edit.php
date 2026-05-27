<?php
require_once 'config.php';
check_login();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header("Location: jusorok_list.php"); exit; }

$conn   = db_connect();
$errors = [];
$row    = $conn->query("SELECT * FROM jusorok WHERE 번호=$id")->fetch_assoc();
if (!$row) { $conn->close(); header("Location: jusorok_list.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $이름 = trim($_POST['이름'] ?? '');
    $주소 = trim($_POST['주소'] ?? '');

    if ($이름 === '') $errors[] = '이름을 입력하세요.';
    if ($주소 === '') $errors[] = '주소를 입력하세요.';

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE jusorok SET 이름=?, 주소=? WHERE 번호=?");
        $stmt->bind_param('ssi', $이름, $주소, $id);
        if ($stmt->execute()) {
            $stmt->close(); $conn->close();
            header("Location: jusorok_list.php?msg=updated");
            exit;
        } else {
            $errors[] = 'DB 오류: ' . $stmt->error;
        }
        $stmt->close();
    }
    $row = array_merge($row, ['이름'=>$이름, '주소'=>$주소]);
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>주소 수정 - <?= SITE_NAME ?></title>
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
    <div class="page-title">✏️ 주소 수정
      <span class="breadcrumb">홈 / 주소록 관리 / 수정 (번호: <?= $id ?>)</span>
    </div>
    <div class="divider"></div>

    <div class="card" style="max-width:520px;">
      <div class="card-title">✏️ 주소록 수정</div>

      <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        ⚠️ <?= implode('<br>⚠️ ', array_map('h', $errors)) ?>
      </div>
      <?php endif; ?>

      <form method="post" action="jusorok_edit.php?id=<?= $id ?>">
        <div class="form-group">
          <label>이름 <span class="req">*</span></label>
          <input type="text" name="이름" class="form-control"
                 value="<?= h($row['이름']) ?>"
                 placeholder="학생 이름" maxlength="50" required>
        </div>
        <div class="form-group">
          <label>주소 <span class="req">*</span></label>
          <input type="text" name="주소" class="form-control"
                 value="<?= h($row['주소']) ?>"
                 placeholder="예) 대구시 북구 칠성동 123" maxlength="200" required>
        </div>
        <div style="display:flex;gap:10px;">
          <button type="submit" class="btn btn-warning">💾 수정 저장</button>
          <a href="jusorok_list.php" class="btn btn-secondary">취소</a>
        </div>
      </form>
    </div>
  </main>
</div>
</body>
</html>
