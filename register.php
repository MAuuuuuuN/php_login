<?php
    if($_SERVER["REQUEST_METHOD"] == "POST") {
      $email_value = $_POST['email'];
      $password_value = $_POST['password'];
      $password_confirm_value = $_POST['password_confirm'];

      if(empty($email_value) || empty($password_value) || empty($password_confirm_value)) {
        echo "メールアドレスとパスワードを入力してください";
        exit;
      }

      if($password_value != $password_confirm_value) {
        echo "パスワードとパスワード(確認用)が一致しません";
        exit;
      }

      try {
        $db = new PDO('mysql:host=localhost;dbname=login', 'root', 'root');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $duplicate_check = 'SELECT * FROM login_info WHERE email = "' . $email_value . '"';
        $check_stmt = $db->prepare($duplicate_check);
        $check_stmt->execute();

        $cnt = $check_stmt->rowCount();

        if($cnt > 0) {
          echo "既に使われているメールアドレスです";
          exit;
        }

        $insert_sql = 'INSERT INTO login_info (email, password) VALUES ("' . $email_value . '", "' . $password_value . '")';
        $insert_stmt = $db->prepare($insert_sql);
        $insert_stmt->execute();

        echo "登録が完了しました<br>5秒後にログインページにリダイレクトします";

        header('Refresh: 5; ./index.php');
        exit;

      } catch (PDOException $e) {
        die("データベース接続失敗 : " . $e->getMessage());
      }
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>登録画面</title>
</head>
<body>
  <h1>登録画面</h1>

  <form method="POST" action="">
    <div>
      <label for='email'>メールアドレス</label>
      <input type="text" name='email'>
    </div>
    <div>
      <label for='password'>パスワードを入力</label>
      <input type="text" name='password'>
    </div>
    <div>
      <label for='password_confirm'>パスワードを入力(確認用)</label>
      <input type="text" name='password_confirm'>
    </div>
    <input type="submit" value="登録する">
  </form>

  <a href="./index.php">ログインページに戻る</a>
</body>
</html>