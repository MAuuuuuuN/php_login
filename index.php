<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ログインページ</title>
</head>
<body>
  <h1>ログインページ</h1>
  
  <form method="POST" action="">
    <div>
      <label for="email">メールアドレス : </label>
      <input type="text" name="email" id="email">
    </div>
    <div>
      <label for="password">パスワード : </label>
      <input type="password" name="password" id="password">
    </div>
    <input type="submit" value="送信">
  </form>
  
  <a href="./register.php">会員登録はこちら</a>

  <?php
    if($_SERVER["REQUEST_METHOD"] == "POST") {
      $email_value = $_POST['email'];
      $password_value = $_POST['password'];

      if(empty($email_value) || empty($password_value)) {
        echo "メールアドレスとパスワードを入力してください";
        exit;
      }

      try {
        $db = new PDO('mysql:host=localhost;dbname=login', 'root', 'root');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = 'SELECT * FROM login_info WHERE email = "' . $email_value . '" AND password = "' . $password_value . '"';
        echo $sql;
        $stmt = $db->prepare($sql);
        $stmt -> execute();
  
        $cnt = $stmt->rowCount();
  
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
    }

  ?>
  
</body>
</html>