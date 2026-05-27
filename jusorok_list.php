<?php
require_once 'config.php';
check_login();

$conn    = db_connect();
$search  = trim($_GET['search'] ?? '');
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset  = ($page - 1) * $perPage;

$where = $search ? "WHERE 이름 LIKE '%" . $conn->real_escape_string($search) . "%'
                    OR  주소 LIKE '%" . $conn->real_escape_string($search) . "%'" : '';

$total  = $conn->query("SELECT COUNT(*) AS cnt FROM jusorok $where")->fetch_assoc()['cnt'];
$pages  = ceil($total / $perPage);
$result = $conn->query("SELECT * FROM jusorok $where ORDER BY 번호 DESC
                        LIMIT $perPage OFFSET $offset");

$msg = $_GET['msg'] ?? '';
$conn->close();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>주소록 목록 - <?= SITE_NAME ?></title>
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
    <div class="page-title">📖 주소록 관리
      <span class="breadcrumb">홈 / 주소록 관리 / 목록</span>
    </div>
    <div class="divider"></div>

    <?php if ($msg === 'deleted'): ?>
    <div class="alert alert-success">✅ 주소록에서 삭제되었습니다.</div>
    <?php elseif ($msg === 'inserted'): ?>
    <div class="alert alert-success">✅ 주소가 등록되었습니다.</div>
    <?php elseif ($msg === 'updated'): ?>
    <div class="alert alert-success">✅ 주소가 수정되었습니다.</div>
    <?php endif; ?>

    <div class="card">
      <div class="toolbar">
        <form class="search-box" method="get" action="">
          <input type="text" name="search"
                 placeholder="이름 또는 주소 검색..."
                 value="<?= h($search) ?>">
          <button type="submit" class="btn btn-secondary btn-sm">🔍 검색</button>
          <?php if($search): ?>
          <a href="jusorok_list.php" class="btn btn-secondary btn-sm">✕ 초기화</a>
          <?php endif; ?>
        </form>
        <a href="jusorok_insert.php" class="btn btn-success">➕ 주소 등록</a>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th width="70">번호</th>
              <th width="150">이름</th>
              <th>주소</th>
              <th width="140">관리</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($total === 0): ?>
            <tr><td colspan="4" style="color:#aaa;padding:24px;">
              <?= $search ? '검색 결과가 없습니다.' : '등록된 주소록이 없습니다.' ?>
            </td></tr>
            <?php else:
              while ($r = $result->fetch_assoc()):
            ?>
            <tr>
              <td><?= $r['번호'] ?></td>
              <td style="font-weight:600;">
                📌 <?= h($r['이름']) ?>
              </td>
              <td style="text-align:left;">
                🏠 <?= h($r['주소']) ?>
              </td>
              <td>
                <a href="jusorok_edit.php?id=<?= $r['번호'] ?>"
                   class="btn btn-warning btn-sm">✏️ 수정</a>
                <button onclick="confirmDelete(<?= $r['번호'] ?>, '<?= h($r['이름']) ?>')"
                        class="btn btn-danger btn-sm">🗑️ 삭제</button>
              </td>
            </tr>
            <?php endwhile; endif; ?>
          </tbody>
        </table>
      </div>

      <?php if($total > 0): ?>
      <div style="margin-top:12px;font-size:12px;color:#888;">
        총 <?= $total ?>명 | 페이지 <?= $page ?>/<?= max(1,$pages) ?>
      </div>
      <?php endif; ?>

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
    <h3>주소록 삭제</h3>
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
        '<strong>' + name + '</strong> 님의 주소를<br>정말 삭제하시겠습니까?';
    document.getElementById('deleteLink').href = 'jusorok_delete.php?id=' + id;
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
