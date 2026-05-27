<?php
// 현재 페이지 파일명으로 활성 메뉴 표시
$cur = basename($_SERVER['PHP_SELF']);
?>
<nav class="sidebar">
  <div class="nav-section">
    <div class="nav-title">홈</div>
    <a href="main.php" class="<?= $cur==='main.php'?'active':'' ?>">
      <span class="ico">🏠</span> 대시보드
    </a>
  </div>

  <div class="nav-section">
    <div class="nav-title">성적 관리</div>
    <a href="sungjuk_list.php" class="<?= $cur==='sungjuk_list.php'?'active':'' ?>">
      <span class="ico">📋</span> 성적 목록
    </a>
    <a href="sungjuk_insert.php" class="<?= $cur==='sungjuk_insert.php'?'active':'' ?>">
      <span class="ico">➕</span> 성적 등록
    </a>
    <a href="sungjuk_ranking.php" class="<?= $cur==='sungjuk_ranking.php'?'active':'' ?>">
      <span class="ico">🏆</span> 성적 순위
    </a>
  </div>

  <div class="nav-section">
    <div class="nav-title">주소록 관리</div>
    <a href="jusorok_list.php" class="<?= $cur==='jusorok_list.php'?'active':'' ?>">
      <span class="ico">📖</span> 주소록 목록
    </a>
    <a href="jusorok_insert.php" class="<?= $cur==='jusorok_insert.php'?'active':'' ?>">
      <span class="ico">➕</span> 주소 등록
    </a>
  </div>
</nav>
