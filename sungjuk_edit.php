<?php
require_once 'config.php';
check_login();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header("Location: sungjuk_list.php");
    exit;
}

$conn = db_connect();
$errors = [];

// 기존 데이터 조회
$row = $conn->query("SELECT * FROM sungjuk WHERE 번호=$id")->fetch_assoc();
if (!$row) {
    $conn->close();
    header("Location: sungjuk_list.php");
    exit;
}

// POST 처리 (수정 저장)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $이름 = trim($_POST['이름'] ?? '');
    $국어 = $_POST['국어'] ?? '';
    $영어 = $_POST['영어'] ?? '';
    $수학 = $_POST['수학'] ?? '';

    if ($이름 === '') $errors[] = '이름을 입력하세요.';
    if (!is_numeric($국어)||$국어<0||$국어>100) $errors[] = '국어 점수는 0~100 사이여야 합니다.';
    if (!is_numeric($영어)||$영어<0||$영어>100) $errors[] = '영어 점수는 0~100 사이여야 합니다.';
    if (!is_numeric($수학)||$수학<0||$수학>100) $errors[] = '수학 점수는 0~100 사이여야 합니다.';

    if (empty($errors)) {
        $stmt = $conn->prepare(
            "UPDATE sungjuk SET 이름=?, 국어=?, 영어=?, 수학=? WHERE 번호=?"
        );
        $stmt->bind_param('siiii', $이름, $국어, $영어, $수학, $id);
        if ($stmt->execute()) {
            update_ranking($conn);
            $stmt->close();
            $conn->close();
            header("Location: sungjuk_list.php?msg=updated");
            exit;
        } else {
            $errors[] = 'DB 오류: ' . $stmt->error;
        }
        $stmt->close();
    }
    // 오류 시 입력값 유지
    $row = array_merge($row, [
        '이름'=>$이름, '국어'=>$국어, '영어'=>$영어, '수학'=>$수학
    ]);
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>성적 수정 - <?= SITE_NAME ?></title>
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
    <div class="page-title">✏️ 성적 수정
      <span class="breadcrumb">홈 / 성적 관리 / 수정 (번호: <?= $id ?>)</span>
    </div>
    <div class="divider"></div>

    <div class="card" style="max-width:520px;">
      <div class="card-title">✏️ 성적 수정</div>

      <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        ⚠️ <?= implode('<br>⚠️ ', array_map('h', $errors)) ?>
      </div>
      <?php endif; ?>

      <form method="post" action="sungjuk_edit.php?id=<?= $id ?>">
        <div class="form-group">
          <label>이름 <span class="req">*</span></label>
          <input type="text" name="이름" class="form-control"
                 value="<?= h($row['이름']) ?>"
                 placeholder="학생 이름" maxlength="50" required>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>국어 <span class="req">*</span></label>
            <input type="number" name="국어" class="form-control"
                   value="<?= h($row['국어']) ?>" min="0" max="100" required>
          </div>
          <div class="form-group">
            <label>영어 <span class="req">*</span></label>
            <input type="number" name="영어" class="form-control"
                   value="<?= h($row['영어']) ?>" min="0" max="100" required>
          </div>
          <div class="form-group">
            <label>수학 <span class="req">*</span></label>
            <input type="number" name="수학" class="form-control"
                   value="<?= h($row['수학']) ?>" min="0" max="100" required>
          </div>
        </div>

        <div id="scorePreview" style="background:#f0f6ff;border-radius:6px;
             padding:10px 14px;font-size:13px;margin-bottom:20px;">
          📊 총점: <strong id="pTotal"><?= $row['국어']+$row['영어']+$row['수학'] ?></strong>점 &nbsp;|&nbsp;
          평균: <strong id="pAvg"><?= round(($row['국어']+$row['영어']+$row['수학'])/3,1) ?></strong>점
        </div>

        <div style="display:flex;gap:10px;">
          <button type="submit" class="btn btn-warning">💾 수정 저장</button>
          <a href="sungjuk_list.php" class="btn btn-secondary">취소</a>
        </div>
      </form>
    </div>
  </main>
</div>

<script>
const fields = ['국어','영어','수학'];
function calcScore() {
    let total = 0;
    fields.forEach(f => {
        total += parseInt(document.querySelector('[name="'+f+'"]').value) || 0;
    });
    document.getElementById('pTotal').textContent = total;
    document.getElementById('pAvg').textContent   = (total/3).toFixed(1);
}
fields.forEach(f => {
    document.querySelector('[name="'+f+'"]').addEventListener('input', calcScore);
});
</script>
</body>
</html>
