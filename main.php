<?php
require_once 'config.php';
check_login();

$conn = db_connect();

// 통계 조회
$cnt_sungjuk  = $conn->query("SELECT COUNT(*) AS cnt FROM sungjuk")->fetch_assoc()['cnt'];
$cnt_jusorok  = $conn->query("SELECT COUNT(*) AS cnt FROM jusorok")->fetch_assoc()['cnt'];

$row_avg = $conn->query("SELECT
    AVG(국어+영어+수학) AS 총평균,
    MAX(국어+영어+수학) AS 최고총점
FROM sungjuk")->fetch_assoc();

$top = $conn->query("SELECT 이름, (국어+영어+수학) AS 총점
    FROM sungjuk ORDER BY 총점 DESC LIMIT 1")->fetch_assoc();

$conn->close();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>대시보드 - <?= SITE_NAME ?></title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<!-- 상단 헤더 -->
<header class="top-header">
  <div class="logo">🎓 <span>밤톨공업대학교</span> 학생정보서비스</div>
  <div class="user-info">
    <span>👤 <?= h($_SESSION['login_user']) ?> 님</span>
    <span>🕐 <?= $_SESSION['login_time'] ?></span>
    <a href="logout.php">로그아웃</a>
  </div>
</header>

<div class="layout">

  <!-- 사이드바 -->
  <?php include 'includes/sidebar.php'; ?>

  <!-- 메인 콘텐츠 -->
  <main class="content">
    <div class="page-title">📊 대시보드
      <span class="breadcrumb">홈 / 대시보드</span>
    </div>
    <div class="divider"></div>

    <!-- 통계 카드 -->
    <div class="stat-grid">
      <div class="stat-card">
        <span class="stat-icon">📝</span>
        <div class="stat-info">
          <div class="num"><?= $cnt_sungjuk ?></div>
          <div class="label">성적 등록 학생 수</div>
        </div>
      </div>
      <div class="stat-card green">
        <span class="stat-icon">📋</span>
        <div class="stat-info">
          <div class="num"><?= $cnt_jusorok ?></div>
          <div class="label">주소록 등록 수</div>
        </div>
      </div>
      <div class="stat-card orange">
        <span class="stat-icon">📈</span>
        <div class="stat-info">
          <div class="num"><?= $row_avg['총평균'] ? round($row_avg['총평균'],1) : 0 ?></div>
          <div class="label">전체 평균 총점</div>
        </div>
      </div>
      <div class="stat-card purple">
        <span class="stat-icon">🏆</span>
        <div class="stat-info">
          <div class="num"><?= $top ? h($top['이름']) : '-' ?></div>
          <div class="label">수석 (<?= $top ? $top['총점'] : 0 ?>점)</div>
        </div>
      </div>
    </div>

    <!-- 바로가기 카드 -->
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
      <div class="card">
        <div class="card-title">📝 성적 관리</div>
        <p style="font-size:13px;color:#666;margin-bottom:16px;">학생 성적(국어·영어·수학)을 등록, 수정, 삭제하고 순위를 확인합니다.</p>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
          <a href="sungjuk_list.php"    class="btn btn-primary">📋 성적 목록</a>
          <a href="sungjuk_insert.php"  class="btn btn-success">➕ 성적 등록</a>
          <a href="sungjuk_ranking.php" class="btn btn-info">🏆 순위 보기</a>
        </div>
      </div>
      <div class="card">
        <div class="card-title">📋 주소록 관리</div>
        <p style="font-size:13px;color:#666;margin-bottom:16px;">학생 주소록을 등록, 수정, 삭제합니다.</p>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
          <a href="jusorok_list.php"   class="btn btn-primary">📋 주소록 목록</a>
          <a href="jusorok_insert.php" class="btn btn-success">➕ 주소 등록</a>
        </div>
      </div>
    </div>

    <!-- 최근 성적 미리보기 -->
    <div class="card" style="margin-top:0;">
      <div class="card-title">🕐 최근 등록 성적 (상위 5건)</div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>번호</th><th>이름</th><th>국어</th><th>영어</th>
              <th>수학</th><th>총점</th><th>평균</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $conn2 = db_connect();
            $res = $conn2->query("SELECT *, (국어+영어+수학) AS 총점,
                                  ROUND((국어+영어+수학)/3,1) AS 평균
                                  FROM sungjuk ORDER BY 번호 DESC LIMIT 5");
            if ($res->num_rows === 0):
            ?>
            <tr><td colspan="7" style="color:#aaa;padding:20px;">등록된 성적이 없습니다.</td></tr>
            <?php else:
              while ($r = $res->fetch_assoc()):
                $cls = $r['총점'] >= 240 ? 'score-high' : ($r['총점'] >= 180 ? 'score-mid' : 'score-low');
            ?>
            <tr>
              <td><?= $r['번호'] ?></td>
              <td><?= h($r['이름']) ?></td>
              <td><?= $r['국어'] ?></td>
              <td><?= $r['영어'] ?></td>
              <td><?= $r['수학'] ?></td>
              <td class="<?= $cls ?>"><?= $r['총점'] ?></td>
              <td><?= $r['평균'] ?></td>
            </tr>
            <?php endwhile; endif; $conn2->close(); ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>
</body>
</html>
