<?php
require_once 'config.php';
check_login();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header("Location: jusorok_list.php"); exit; }

$conn  = db_connect();
$check = $conn->query("SELECT 번호 FROM jusorok WHERE 번호=$id");
if ($check->num_rows > 0) {
    $conn->query("DELETE FROM jusorok WHERE 번호=$id");
}
$conn->close();

header("Location: jusorok_list.php?msg=deleted");
exit;
?>
