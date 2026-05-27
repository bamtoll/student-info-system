<?php
require_once 'config.php';
check_login();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header("Location: sungjuk_list.php");
    exit;
}

$conn = db_connect();

// 존재 여부 확인 후 삭제
$check = $conn->query("SELECT 번호 FROM sungjuk WHERE 번호=$id");
if ($check->num_rows > 0) {
    $conn->query("DELETE FROM sungjuk WHERE 번호=$id");
    update_ranking($conn);   // 순위 테이블 자동 갱신
}

$conn->close();
header("Location: sungjuk_list.php?msg=deleted");
exit;
?>
