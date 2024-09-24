<?php
  session_start();

  if(isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header('Location: ./index.php');
    exit;
  }

  
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>会員ページ</title>
</head>
<body>
  <h1>会員ページ</h1>
  <?php
    $email_session = $_SESSION['email'];
  ?>
  <h2>ログイン中のメールアドレス : <?php echo $email_session; ?></h2>
  <h2>登録内容を変更する</h2>
  <form method="POST" action="">
    <div>
      <label for="email">メールアドレス</label>
      <input type="text" name="email">
    </div>
    <div>
      <label for="password">パスワードを入力</label>
      <input type="password" name="password">
    </div>
    <input type="submit" name="change" value="変更する">
  </form>

  <form method="POST" action="">
    <input type="submit" name="logout" value="ログアウト">
  </form>

  <?php
    if(isset($_POST['change'])) {
      if($_SERVER["REQUEST_METHOD"] == "POST") {
        $email_value = $_POST['email'];
        $password_value = $_POST['password'];
  
        if(empty($email_value) || empty($password_value)) {
          echo "メールアドレスとパスワードを入力してください";
          exit;
        }
  
        try {
          $db = new PDO('mysql:host=localhost;dbname=login','root', 'root');
          $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
          $sql = 'UPDATE login_info SET email = "' . $email_value .'",password = "' . $password_value . '" WHERE email ="' . $email_session . '"';
          $stmt = $db->prepare($sql);
          $stmt->execute();
          var_dump($stmt);
          $_SESSION['email'] = $email_value;
  
          echo "登録内容を変更しました";
        } catch (PDOException $e) {
          die("データベース接続失敗 : ". $e->getMessage());
        }
      }
    }
  ?>
</body>
</html>