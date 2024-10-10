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
  <?php
    if (isset($_SESSION['message'])) {
      echo "<p>" . htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8') . "</p>";
      unset($_SESSION['message']);
    }
  ?>
  <h1>会員ページ</h1>
  <?php
    $email_session = $_SESSION['email'];
  ?>
  <h2>ログイン中のメールアドレス : <?php echo htmlspecialchars($email_session, ENT_QUOTES, 'UTF-8'); ?></h2>
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
        $email_value = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password_value = $_POST['password'];
  
        if(empty($email_value) || empty($password_value)) {
          echo "メールアドレスとパスワードを入力してください";
          exit;
        }

        if (!filter_var($email_value, FILTER_VALIDATE_EMAIL)) {
          echo "不正な形式のメールアドレスです。";
          exit;
        }
  
        try {
          $db = new PDO('mysql:host=localhost;dbname=login','root', 'root');
          $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $sql = 'UPDATE login_info SET email = :email, password = :password WHERE email = :old_email';
          $stmt = $db->prepare($sql);
          $hashed_password = password_hash($password_value, PASSWORD_DEFAULT);
          $stmt->bindParam(':email', $email_value);
          $stmt->bindParam(':password', $hashed_password);
          $stmt->bindParam(':old_email', $email_session);
          $stmt->execute();
          $_SESSION['email'] = $email_value;
  
          $_SESSION['message'] = '登録内容を変更しました';
          header('Location: ' . $_SERVER['PHP_SELF']);
          exit;
        } catch (PDOException $e) {
          die("データベース接続失敗 : ". $e->getMessage());
        }
      }
    }
    
  ?>
</body>
</html>