<?php
require_once 'config.php';
check_login();

$conn = db_connect();

// 검색어 처리
$search  = trim($_GET['search'] ?? '');
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset  = ($page - 1) * $perPage;

$where = $search ? "WHERE 이름 LIKE '%" . $conn->real_escape_string($search) . "%'" : '';

$total = $conn->query("SELECT COUNT(*) AS cnt FROM sungjuk $where")->fetch_assoc()['cnt'];
$pages = ceil($total / $perPage);

$result = $conn->query("SELECT *, (국어+영어+수학) AS 총점,
    ROUND((국어+영어+수학)/3,1) AS 평균
    FROM sungjuk $where ORDER BY 번호 DESC
    LIMIT $perPage OFFSET $offset");

// 삭제 성공/실패 메시지
$msg  = $_GET['msg']  ?? '';
$conn->close();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>성적 목록 - <?= SITE_NAME ?></title>
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
    <div class="page-title">📝 성적 관리
      <span class="breadcrumb">홈 / 성적 관리 / 목록</span>
    </div>
    <div class="divider"></div>

    <?php if ($msg === 'deleted'): ?>
    <div class="alert alert-success">✅ 성적이 삭제되었습니다.</div>
    <?php endif; ?>

    <div class="card">
      <!-- 툴바 -->
      <div class="toolbar">
        <form class="search-box" method="get" action="">
          <input type="text" name="search"
                 placeholder="이름 검색..."
                 value="<?= h($search) ?>">
          <button type="submit" class="btn btn-secondary btn-sm">🔍 검색</button>
          <?php if($search): ?>
          <a href="sungjuk_list.php" class="btn btn-secondary btn-sm">✕ 초기화</a>
          <?php endif; ?>
        </form>
        <div style="display:flex;gap:8px;">
          <a href="sungjuk_insert.php"  class="btn btn-success">➕ 성적 등록</a>
          <a href="sungjuk_ranking.php" class="btn btn-info">🏆 순위 보기</a>
        </div>
      </div>

      <!-- 테이블 -->
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th width="60">번호</th>
              <th>이름</th>
              <th width="80">국어</th>
              <th width="80">영어</th>
              <th width="80">수학</th>
              <th width="80">총점</th>
              <th width="80">평균</th>
              <th width="140">관리</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($total === 0): ?>
            <tr><td colspan="8" style="color:#aaa;padding:24px;">
              <?= $search ? '검색 결과가 없습니다.' : '등록된 성적이 없습니다.' ?>
            </td></tr>
            <?php else:
              while ($r = $result->fetch_assoc()):
                $cls = $r['총점'] >= 240 ? 'score-high' : ($r['총점'] >= 180 ? 'score-mid' : 'score-low');
            ?>
            <tr>
              <td><?= $r['번호'] ?></td>
              <td style="text-align:left;font-weight:600;"><?= h($r['이름']) ?></td>
              <td class="<?= $r['국어']>=80?'score-high':($r['국어']>=60?'score-mid':'score-low') ?>"><?= $r['국어'] ?></td>
              <td class="<?= $r['영어']>=80?'score-high':($r['영어']>=60?'score-mid':'score-low') ?>"><?= $r['영어'] ?></td>
              <td class="<?= $r['수학']>=80?'score-high':($r['수학']>=60?'score-mid':'score-low') ?>"><?= $r['수학'] ?></td>
              <td class="<?= $cls ?>"><?= $r['총점'] ?></td>
              <td><?= $r['평균'] ?></td>
              <td>
                <a href="sungjuk_edit.php?id=<?= $r['번호'] ?>"
                   class="btn btn-warning btn-sm">✏️ 수정</a>
                <button onclick="confirmDelete(<?= $r['번호'] ?>, '<?= h($r['이름']) ?>')"
                        class="btn btn-danger btn-sm">🗑️ 삭제</button>
              </td>
            </tr>
            <?php endwhile; endif; ?>
          </tbody>
        </table>
      </div>

      <!-- 통계 요약 -->
      <?php if($total > 0): ?>
      <div style="margin-top:12px;font-size:12px;color:#888;">
        총 <?= $total ?>명 | 페이지 <?= $page ?>/<?= max(1,$pages) ?>
      </div>
      <?php endif; ?>

      <!-- 페이지네이션 -->
      <?php if ($pages > 1): ?>
      <div class="pagination">
        <?php if ($page > 1): ?>
          <a href="?search=<?= urlencode($search) ?>&page=<?= $page-1 ?>">◀</a>
        <?php endif; ?>
        <?php for ($i = max(1,$page-3); $i <= min($pages,$page+3); $i++): ?>
          <?php if ($i === $page): ?>
            <span class="active"><?= $i ?></span>
          <?php else: ?>
            <a href="?search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
          <?php endif; ?>
        <?php endfor; ?>
        <?php if ($page < $pages): ?>
          <a href="?search=<?= urlencode($search) ?>&page=<?= $page+1 ?>">▶</a>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>

  </main>
</div>

<!-- 삭제 확인 모달 -->
<div class="modal-overlay" id="deleteModal">
  <div class="modal-box">
    <div class="icon">⚠️</div>
    <h3>성적 삭제</h3>
    <p id="deleteMsg">삭제하면 복구할 수 없습니다.<br>정말 삭제하시겠습니까?</p>
    <div class="modal-btns">
      <a id="deleteLink" href="#" class="btn btn-danger">삭제</a>
      <button onclick="closeModal()" class="btn btn-secondary">취소</button>
    </div>
  </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('deleteMsg').innerHTML =
        '<strong>' + name + '</strong> 학생의 성적을<br>정말 삭제하시겠습니까?';
    document.getElementById('deleteLink').href = 'sungjuk_delete.php?id=' + id;
    document.getElementById('deleteModal').classList.add('show');
}
function closeModal() {
    document.getElementById('deleteModal').classList.remove('show');
}
document.getElementById('deleteModal').addEventListener('click', function(e){
    if (e.target === this) closeModal();
});
</script>
</body>
</html>
