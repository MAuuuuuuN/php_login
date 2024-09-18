<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <h1>ログインページ</h1>
  
  <form action="index.php">
    <p>メールアドレス</p>
    <input type="text" name="email">
    <p>パスワード</p>
    <input type="text" name="pass">
    <input type="submit" value="送信">
  </form>
  
  <a href="">会員登録はこちら</a>

  <?php
    // if($_POST['email'] || $_POST['pass']) {
    //   try {
    //     $db = new PDO('mysql:host=localhost;dbname=login','root', 'root');
    //     $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //   } catch (PDOException $e) {
    //     die("データベース接続失敗 : ". $e->getMessage());
    //   }
  
    //   if($_SERVER['REQUEST_METHOD'] === 'POST') {
    //     $email = $_POST['email'];
    //     $password = $_POST['password'];
    //   }
  
    //   $query = "SELECT * FROM login_info WHERE email = :email";
    //   $stmt = $db->prepare($query);
    //   $stmt->bindParam(':email', $email);
    //   $stmt->execute();
    //   $user = $stmt->fetch(PDO::FETCH_ASSOC);
  
    //   if($user) {
    //     echo "成功";
    //   } else {
    //     echo "失敗";
    //   }
      
    //   $db = null;
    // }

    try {
      $db = new PDO('mysql:hostc=localhost;dbname=login', 'root', 'root');
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $sql = 'SELECT * FROM login_info WHERE email = "test@test.com1" AND password = "test1"';
      $stmt = $db->query($sql);
      var_dump($stmt);

      $cnt = $stmt->rowCount();

      echo $cnt;


      if ($cnt == 1) {
        echo "成功";
      } else if ($cnt == 0) {
        echo "見つかりません";
      } else {
        echo "致命的なエラー";
      }

    } catch (PDOException $e) {
      die("データベース接続失敗 : ". $e->getMessage());
    }
  ?>
  
</body>
</html>