<?php
$dsn = 'mysql:dbname=php_book_app;host=localhost;charset=utf8mb4';
$user = 'root';
$password = '';

try {
  $pdo = new PDO($dsn, $user, $password);

  //プレースホルダに置き換えたSQL文を用意する
  $sql_delete = 'DELETE FROM books WHERE id = :id';
  $stmt_delete = $pdo->prepare($sql_delete);

  //実際の値をプレースホルダに割り当てる/SQL文の実行
  $stmt_delete->bindValue(':id',$_GET['id'],PDO::PARAM_INT);
  $stmt_delete->execute();

  //削除した件数の取得・書籍リストへ渡す
  $count = $stmt_delete->rowCount();
  $message = "書籍データを{$count}件削除しました。";

  //書籍一覧へ戻る
  header("Location: read.php?message={$message}");

} catch (PDOException $e) {
  exit($e->getMessage());
}
?>