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
        $sql = 'SELECT password FROM login_info WHERE email = :email';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email_value, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && password_verify($password_value, $result['password'])) {
          session_start();
          $_SESSION['email'] = $email_value;
          header('Location: ./login.php');
          exit;
        } else {
          echo "メールアドレスもしくはパスワードが間違っています";
        }
      } catch (PDOException $e) {
        die("データベース接続失敗 : ". $e->getMessage());
      }
    }
?>

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
      <input type="text" name="email">
    </div>
    <div>
      <label for="password">パスワード : </label>
      <input type="password" name="password">
    </div>
    <input type="submit" value="送信">
  </form>
  
  <a href="./register.php">会員登録はこちら</a>
</body>
</html>