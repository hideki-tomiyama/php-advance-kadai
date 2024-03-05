<?php
$dsn = 'mysql:dbname=php_book_app;host=localhost;charset=utf8mb4';
$user = 'root';
$password = '';

//更新ボタンを押したときの処理
if (isset($_POST['submit'])) {
  try {
    $pdo = new PDO($dsn, $user, $password);

    //プレースホルダに置き換えたSQL文の用意
    $sql_update = '
      UPDATE books
      SET book_code = :book_code,
      book_name = :book_name,
      price = :price,
      stock_quantity = :stock_quantity,
      genre_code = :genre_code
      WHERE id = :id
    ';
    $stmt_update = $pdo->prepare($sql_update);

    //実際の値をプレースホルダに割り当てる
    $stmt_update->bindValue(':book_code', $_POST['book_code'], PDO::PARAM_INT);
    $stmt_update->bindValue(':book_name', $_POST['book_name'], PDO::PARAM_STR);
    $stmt_update->bindValue(':price', $_POST['price'], PDO::PARAM_INT);
    $stmt_update->bindValue(':stock_quantity', $_POST['stock_quantity'], PDO::PARAM_INT);
    $stmt_update->bindValue(':genre_code', $_POST['genre_code'], PDO::PARAM_INT);
    $stmt_update->bindValue(':id', $_GET['id'], PDO::PARAM_INT);

    //SQL文の実行
    $stmt_update->execute();

    //更新した件数の取得・商品一覧ページへメッセージを渡す
    $count = $stmt_update->rowCount();
    $message = "書籍データを{$count}件編集しました。";

    //書籍一覧へ戻る
    header("Location: read.php?message={$message}");
  }catch (PDOException $e) {
    exit($e->getMessage());
  }
}
//idパラメータが存在した時の処理
if (isset($_GET['id'])) {
  try {
    $pdo = new PDO($dsn, $user, $password);

    //idパラメータの値をプレースホルダに置き換えたSQL文を用意
    $sql_select_book = 'SELECT * FROM books WHERE id = :id';
    $stmt_select_book = $pdo->prepare($sql_select_book);

    //実際の値をプレースホルダに割り当てる/SQL文実行
    $stmt_select_book->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt_select_book->execute();

    //SQL文の配列取得
    $book = $stmt_select_book->fetch(PDO::FETCH_ASSOC);

    //idパラメータが存在しない時の処理
    if ($book === FALSE) {
      exit('idパラメータの値が不正です');
    }

   //ジャンルコード読み込み処理/SQL文の実行
   $sql_select_genre_code = 'SELECT genre_code FROM genres';
   $stmt_select_genre_code = $pdo->query($sql_select_genre_code);

   //SQL文の配列の取得
   $genre_codes =$stmt_select_genre_code->fetchAll(PDO::FETCH_COLUMN);

  }catch (PDOException $e) {
    exit($e->getMessage());
  }
} else {
  //idパラメータが存在しない時の処理
  exit ('idパラメータが存在しません。');
}
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>書籍編集</title>
   <link rel="stylesheet" href="css/style.css">
 
   <!-- Google Fontsの読み込み -->
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
  </head>
 
 <body>
    <header>
      <nav>
       <a href="index.php">書籍管理アプリ</a>
      </nav>
    </header>
    <main>
      <article class="registration">
        <h1>書籍編集</h1>
        <hr>
        <div class="back">
          <a href="read.php" class="btn">&lt; 戻る</a>
        </div>
        <form action="update.php?id=<?= $_GET['id'] ?>" method="post" class="registration-form">
          <div>
            <label for="book_code">商品コード</label>
            <input type="number" name="book_code" value="<?= $book['book_code'] ?>" min="0" max="100000000" required>
 
            <label for="book_name">書籍名</label>
            <input type="text" name="book_name" value="<?= $book['book_name'] ?>" maxlength="50" required>
 
            <label for="price">単価(円)</label>
            <input type="number" name="price" value="<?= $book['price'] ?>" min="0" max="100000000" required>
 
            <label for="stock_quantity">在庫数(冊)</label>
            <input type="number" name="stock_quantity" value="<?= $book['stock_quantity'] ?>" min="0" max="100000000" required>
 
            <label for="genre_code">ジャンルコード</label>
            <select name="genre_code" required>
              <option disabled selected value>選択してください</option>
              <?php
              // 配列の中身を順番に取り出し、セレクトボックスの選択肢として出力する
               foreach ($genre_codes as $genre_code) {
                  // もし変数$genre_codeが書籍のジャンルコードの値と一致していれば、selected属性をつけて初期値にする
                  if ($genre_code === $book['genre_code']) {
                   echo "<option value='{$genre_code}' selected>{$genre_code}</option>";
                  } else {
                   echo "<option value='{$genre_code}'>{$genre_code}</option>";
                  }
                }
              ?>
            </select>
          </div>
          <button type="submit" class="submit-btn" name="submit" value="update">更新</button>
        </form>
      </article>
    </main>
    <footer>
      <p class="copyright">&copy; 商品管理アプリ All rights reserved.</p>
    </footer>
  </body>
</html>