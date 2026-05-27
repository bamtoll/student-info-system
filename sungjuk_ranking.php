<?php
require_once 'config.php';
check_login();

$conn = db_connect();

// 순위 테이블 최신화 후 조회
update_ranking($conn);

$result = $conn->query(
    "SELECT * FROM sungjuk_ranking ORDER BY 순위 ASC, 이름 ASC"
);

// 전체 통계
$stats = $conn->query("SELECT
    COUNT(*) AS 인원,
    MAX(총점) AS 최고총점,
    MIN(총점) AS 최저총점,
    ROUND(AVG(총점),1) AS 평균총점,
    ROUND(AVG(평균),1) AS 평균평균
FROM sungjuk_ranking")->fetch_assoc();

$conn->close();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>성적 순위 - <?= SITE_NAME ?></title>
<link rel="stylesheet" href="assets/style.css">
<style>
.podium { display:flex; align-items:flex-end; justify-content:center; gap:16px; margin:28px 0; }
.podium-item { text-align:center; }
.podium-box {
    border-radius: 8px 8px 0 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 12px 20px;
    color: #fff;
    font-weight: 700;
    min-width: 110px;
}
.p1 { background:linear-gradient(135deg,#f7c948,#e6a817); height:140px; }
.p2 { background:linear-gradient(135deg,#b0c0d0,#8a9dac); height:110px; }
.p3 { background:linear-gradient(135deg,#d4915e,#b87544); height:90px; }
.podium-name { font-size:15px; margin-bottom:4px; }
.podium-score { font-size:22px; }
.podium-sub { font-size:11px; opacity:.85; margin-top:2px; }
.podium-rank { font-size:28px; margin-bottom:6px; }
</style>
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
    <div class="page-title">🏆 성적 순위
      <span class="breadcrumb">홈 / 성적 관리 / 순위</span>
    </div>
    <div class="divider"></div>

    <!-- 전체 통계 -->
    <div class="stat-grid" style="grid-template-columns:repeat(5,1fr);">
      <div class="stat-card">
        <span class="stat-icon">👥</span>
        <div class="stat-info">
          <div class="num"><?= $stats['인원'] ?></div>
          <div class="label">전체 인원</div>
        </div>
      </div>
      <div class="stat-card green">
        <span class="stat-icon">⬆️</span>
        <div class="stat-info">
          <div class="num"><?= $stats['최고총점'] ?></div>
          <div class="label">최고 총점</div>
        </div>
      </div>
      <div class="stat-card" style="border-top-color:#e74c3c;">
        <span class="stat-icon">⬇️</span>
        <div class="stat-info">
          <div class="num"><?= $stats['최저총점'] ?></div>
          <div class="label">최저 총점</div>
        </div>
      </div>
      <div class="stat-card orange">
        <span class="stat-icon">📊</span>
        <div class="stat-info">
          <div class="num"><?= $stats['평균총점'] ?></div>
          <div class="label">평균 총점</div>
        </div>
      </div>
      <div class="stat-card purple">
        <span class="stat-icon">📈</span>
        <div class="stat-info">
          <div class="num"><?= $stats['평균평균'] ?></div>
          <div class="label">전체 평균</div>
        </div>
      </div>
    </div>

    <?php
    // 시상대용 TOP 3 추출
    $conn2 = db_connect();
    $top3 = $conn2->query("SELECT * FROM sungjuk_ranking ORDER BY 순위 ASC LIMIT 3")->fetch_all(MYSQLI_ASSOC);
    $conn2->close();
    if (count($top3) >= 2):
    ?>
    <!-- 시상대 (TOP 3) -->
    <div class="card">
      <div class="card-title">🥇 시상대 TOP 3</div>
      <div class="podium">
        <?php
        // 순서: 2위 - 1위 - 3위 (시상대 형태)
        $order = [1,0,2]; // 배열 인덱스
        $pcls  = ['p2','p1','p3'];
        $medals= ['🥈','🥇','🥉'];
        foreach ($order as $oi => $idx):
            if (!isset($top3[$idx])) continue;
            $t = $top3[$idx];
        ?>
        <div class="podium-item">
          <div style="margin-bottom:8px;font-size:13px;font-weight:700;color:#555;">
            <?= h($t['이름']) ?></div>
          <div class="podium-box <?= $pcls[$oi] ?>">
            <div class="podium-rank"><?= $medals[$oi] ?></div>
            <div class="podium-score"><?= $t['총점'] ?>점</div>
            <div class="podium-sub">평균 <?= $t['평균'] ?>점</div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- 전체 순위 테이블 -->
    <div class="card">
      <div class="card-title">📊 전체 순위표</div>
      <div class="toolbar" style="justify-content:flex-end;">
        <button onclick="window.print()" class="btn btn-info btn-sm">🖨️ 인쇄</button>
        <a href="sungjuk_list.php" class="btn btn-secondary btn-sm">← 성적 목록</a>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th width="70">순위</th>
              <th>이름</th>
              <th width="80">국어</th>
              <th width="80">영어</th>
              <th width="80">수학</th>
              <th width="90">총점</th>
              <th width="90">평균</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // result 포인터 초기화를 위해 재조회
            $conn3 = db_connect();
            $res3  = $conn3->query("SELECT * FROM sungjuk_ranking ORDER BY 순위 ASC, 이름 ASC");
            if ($res3->num_rows === 0):
            ?>
            <tr><td colspan="7" style="color:#aaa;padding:24px;">성적 데이터가 없습니다.</td></tr>
            <?php else:
              while ($r = $res3->fetch_assoc()):
                $rankCls = $r['순위']==1?'rank-1':($r['순위']==2?'rank-2':($r['순위']==3?'rank-3':'rank-n'));
                $totalCls = $r['총점']>=240?'score-high':($r['총점']>=180?'score-mid':'score-low');
            ?>
            <tr>
              <td>
                <span class="rank-badge <?= $rankCls ?>"><?= $r['순위'] ?></span>
              </td>
              <td style="text-align:left;font-weight:<?= $r['순위']<=3?700:400 ?>;">
                <?= $r['순위']<=3?'⭐ ':'' ?><?= h($r['이름']) ?>
              </td>
              <td class="<?= $r['국어']>=80?'score-high':($r['국어']>=60?'score-mid':'score-low') ?>"><?= $r['국어'] ?></td>
              <td class="<?= $r['영어']>=80?'score-high':($r['영어']>=60?'score-mid':'score-low') ?>"><?= $r['영어'] ?></td>
              <td class="<?= $r['수학']>=80?'score-high':($r['수학']>=60?'score-mid':'score-low') ?>"><?= $r['수학'] ?></td>
              <td class="<?= $totalCls ?>" style="font-weight:700;"><?= $r['총점'] ?></td>
              <td><?= $r['평균'] ?></td>
            </tr>
            <?php endwhile; endif; $conn3->close(); ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>
</body>
</html>
