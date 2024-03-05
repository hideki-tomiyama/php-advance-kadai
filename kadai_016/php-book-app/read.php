<?php
$dsn = 'mysql:dbname=php_book_app;host=localhost;charset=utf8mb4';
$user = 'root';
$password ='';

try {
  //ログイン(D/B)
  $pdo = new PDO($dsn, $user, $password);
  //並び替えボタンが押された時の処理
  if (isset($_GET['order'])) {
    $order = $_GET['order'];
  } else {
    $order = NULL;
  }
  //検索書籍名が入力された時の処理
  if (isset($_GET['keyword'])) {
    $keyword = $_GET['keyword'];
  } else {
    $keyword = NULL;  
  }
  //テーブルからデータ取得するためのSQL文を変数に代入/SQL文実行
  if ($order === 'desc') {
    $sql_select = 'SELECT * FROM books WHERE book_name LIKE :keyword ORDER BY updated_at DESC';
  } else {
    $sql_select = 'SELECT * FROM books WHERE book_name LIKE :keyword ORDER BY updated_at ASC';
  }

  $stmt_select = $pdo->prepare($sql_select);
  //検索機能実装(部分一致)
  $partial_match = "%{$keyword}%";
  //実際の値をプレースホルダにバインドする
  $stmt_select->bindValue(':keyword',$partial_match, PDO::PARAM_STR);
  //SQLの実行
  $stmt_select->execute();
  //SQL文の実行結果を配列で取得
  $books = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
   exit($e->getMessage());
}
?>

<!DOCTYPE HTML>
<html lang="ja">
  <head>
    <title>書籍管理アプリ</title>
    <meta charset= "UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    
    <!-- google Fontsの読み込み -->
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
      <!-- 書籍一覧の作成 -->
      <article class="products">
        <h1>書籍一覧</h1>
        <hr> 
        <?php
          //メッセージパラメータ値を受け取った場合の処理
          if (isset($_GET['message'])) {
            echo "<p class='success'>{$_GET['message']}</p>";
          }
        ?>
        <div class="products-ui">
          <div>
           <!--検索機能  -->
            <a href="read.php?order=desc">
             <img src="images/desc.png" alt="降順に並べ替え" class="sort-img">
            </a>
            <a href="read.php?order=asc">
             <img src="images/asc.png" alt="昇順に並べ替え" class="sort-img">
            </a>
            <form action="read.php" method="get" class="search-form">
              <input type="text" class="search-box" placeholder="書籍名で検索" name="keyword" value="<?= $keyword ?>">
            </form>
          </div>
          <a href="create.php" class="btn">書籍登録</a>  
        </div> 
        <br>
        <!--テーブルの作成  -->
        <table class="products-table">
          <tr>
            <th>書籍コード</th>
            <th>書籍名</th>
            <th>単価（￥）</th>
            <th>在庫数（個）</th>
            <th>ジャンルコード</th>
            <th>編集</th>
            <th>削除</th>
          </tr>
          <?php
           foreach ($books as $book) {
            $table_row = "
               <tr>
               <td>{$book['book_code']}</td>
               <td>{$book['book_name']}</td>
               <td>{$book['price']}</td>
               <td>{$book['stock_quantity']}</td>
               <td>{$book['genre_code']}</td>
               <td><a href='update.php?id={$book['id']}'><img src='images/edit.png' alt='編集' class='edit-icon'></a></td>
               <th><a href='delete.php?id={$book['id']}'><img src='images/delete.png' alt='削除' class='delete-icon'></a></td>
               </tr>
               ";
            echo $table_row;   
           }
          ?>
        </table>
      </article>
    </main>
    
    <footer>
      <p class="copyright">&copy;商品管理アプリ All rights reserved.</p>
    </footer>
  </body>

</html>