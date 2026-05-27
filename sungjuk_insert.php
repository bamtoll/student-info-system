<?php
require_once 'config.php';
check_login();

$errors = [];
$data   = ['이름'=>'','국어'=>'','영어'=>'','수학'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['이름'] = trim($_POST['이름'] ?? '');
    $data['국어'] = $_POST['국어'] ?? '';
    $data['영어'] = $_POST['영어'] ?? '';
    $data['수학'] = $_POST['수학'] ?? '';

    // 유효성 검사
    if ($data['이름'] === '')           $errors[] = '이름을 입력하세요.';
    if ($data['국어'] === '' || !is_numeric($data['국어']) || $data['국어']<0 || $data['국어']>100)
        $errors[] = '국어 점수는 0~100 사이 숫자여야 합니다.';
    if ($data['영어'] === '' || !is_numeric($data['영어']) || $data['영어']<0 || $data['영어']>100)
        $errors[] = '영어 점수는 0~100 사이 숫자여야 합니다.';
    if ($data['수학'] === '' || !is_numeric($data['수학']) || $data['수학']<0 || $data['수학']>100)
        $errors[] = '수학 점수는 0~100 사이 숫자여야 합니다.';

    if (empty($errors)) {
        $conn = db_connect();
        $stmt = $conn->prepare(
            "INSERT INTO sungjuk (이름, 국어, 영어, 수학) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param('siii',
            $data['이름'],
            $data['국어'],
            $data['영어'],
            $data['수학']
        );
        if ($stmt->execute()) {
            update_ranking($conn);   // 순위 테이블 자동 갱신
            $stmt->close();
            $conn->close();
            header("Location: sungjuk_list.php?msg=inserted");
            exit;
        } else {
            $errors[] = 'DB 오류: ' . $stmt->error;
        }
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>성적 등록 - <?= SITE_NAME ?></title>
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
    <div class="page-title">➕ 성적 등록
      <span class="breadcrumb">홈 / 성적 관리 / 등록</span>
    </div>
    <div class="divider"></div>

    <div class="card" style="max-width:520px;">
      <div class="card-title">📝 학생 성적 입력</div>

      <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        ⚠️ <?= implode('<br>⚠️ ', array_map('h', $errors)) ?>
      </div>
      <?php endif; ?>

      <form method="post" action="sungjuk_insert.php">
        <div class="form-group">
          <label>이름 <span class="req">*</span></label>
          <input type="text" name="이름" class="form-control"
                 value="<?= h($data['이름']) ?>"
                 placeholder="학생 이름 입력" maxlength="50" required>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>국어 <span class="req">*</span></label>
            <input type="number" name="국어" class="form-control"
                   value="<?= h($data['국어']) ?>"
                   min="0" max="100" placeholder="0~100" required>
          </div>
          <div class="form-group">
            <label>영어 <span class="req">*</span></label>
            <input type="number" name="영어" class="form-control"
                   value="<?= h($data['영어']) ?>"
                   min="0" max="100" placeholder="0~100" required>
          </div>
          <div class="form-group">
            <label>수학 <span class="req">*</span></label>
            <input type="number" name="수학" class="form-control"
                   value="<?= h($data['수학']) ?>"
                   min="0" max="100" placeholder="0~100" required>
          </div>
        </div>

        <!-- 실시간 합계 미리보기 -->
        <div id="scorePreview" style="background:#f0f6ff;border-radius:6px;
             padding:10px 14px;font-size:13px;margin-bottom:20px;display:none;">
          📊 총점: <strong id="pTotal">0</strong>점 &nbsp;|&nbsp;
          평균: <strong id="pAvg">0</strong>점
        </div>

        <div style="display:flex;gap:10px;">
          <button type="submit" class="btn btn-success">💾 등록</button>
          <a href="sungjuk_list.php" class="btn btn-secondary">취소</a>
        </div>
      </form>
    </div>
  </main>
</div>

<script>
// 점수 실시간 계산
const fields = ['국어','영어','수학'];
function calcScore() {
    let total = 0, valid = true;
    fields.forEach(f => {
        const v = parseInt(document.querySelector('[name="'+f+'"]').value) || 0;
        if (isNaN(v)) valid = false;
        total += v;
    });
    if (valid) {
        document.getElementById('pTotal').textContent = total;
        document.getElementById('pAvg').textContent   = (total/3).toFixed(1);
        document.getElementById('scorePreview').style.display = 'block';
    }
}
fields.forEach(f => {
    document.querySelector('[name="'+f+'"]').addEventListener('input', calcScore);
});
</script>
</body>
</html>
