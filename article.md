## 概要
php初学者がログインページを作成して、phpについての知識をインプット/アウトプットを行う。

## 目的
php初心者の私がphpを勉強するにあたって、自分で行った勉強の内容を備忘録として記事を作成していきます。
初学者向けのphp勉強でToDoリストを作成するものが有名ですが、つまづいたところをググるとすぐに答えが出てきてしまうので、比較的記事や情報が少ないログインページを作成します。

今回の記事では、`動くものを作る`というのが目的なため、セキュリティやデータベースの設計などはあまり考えずに実装します。
今回作成したコードをもとに次回以降の記事でコードを改修しつつ、phpについての知識をインプット/アウトプットしていきたいと思います。
また、知識のアウトプットのとして記事を作成するため、ところどころガバガバなところがあるかと思いますが、お手柔らかにお願いいたします。

## 開発環境
- 使用PC : macBookAir M2 2023 15インチ
- macOS : Sonoma 14.6.1
- 実行環境 : MAMP 6.9.0 ARMCPU
  - phpバージョン : 7.4.33
  - mysqlバージョン : 5.7.39

## 要件定義
- トップページ
  - ログインのための「メールアドレス」と「パスワード」入力フォームがあること
  - 「メールアドレス」と「パスワード」を入力して、ログインができるボタンがあること
  - ログインに成功すると、ログイン成功ページに遷移すること
  - ログインに失敗すると、遷移せず失敗した旨のメッセージが表示されること
  - 新規登録のためのページに遷移するボタンがあること
- ログイン成功ページ
   - ログインしている「メールアドレス」を表示すること(「パスワード」は表示しない)
   - 「メールアドレス」と「パスワード」を変更できるフォームがあること
   - ログアウトするボタンがあること
- 新規登録ページ
   - 「メールアドレス」と「パスワード」を入力して登録できるフォームがあること
   - トップページに遷移するボタンがあること

## 開発環境
今回はphp勉強ということで、簡単に導入できるMAMPを使って開発していきます。
(私の環境だと、MAMPの最新バージョンでApacheをうまく動かすことができなかったので、バージョンを下げたものを使用しています。)
ダウンロードしてインストール後、MAMPのフォルダ内のhtdocsフォルダ内にphp_loginというフォルダを作成し、その中にファイルを作成していきます。
動作確認を行う際には、MAMPの右上のStartボタンを押下したあと、`http://localhost/php_login/` にアクセスします。

## データベース設計
今回は、MAMPのMySQLを使用します。
`http://localhost/phpMyAdmin/` からデータベースを表示します。
テーブルには、次のカラムを追加します。
- id(一意)
- email(一意)
- password
- create_date(自動入力)
- update_date(自動入力)

idは、アカウント登録した際に自動的に入力されるようにしています。
また、メールアドレスの重複を防ぐため、一意になるように設定しています。
さらに、create_dateはアカウントを登録した時間、update_dateはアカウントを更新した時間が入るように設定します。

```mysql
CREATE DATABASE login;
CREATE TABLE login_info
(
  id int(11) UNIQUE NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  create_date DATETIME NOT NULL DEFAULT CURRENT_DATETIME,
  update_date DATETIME NOT NULL DEFAULT CURRENT_DATETIME ON UPDATE CURRENT_DATETIME,
  PRIMARY KEY(id)
);
```
まず、「login」というデータベースを作成します。
次に、「login_info」というテーブルとそのカラムを作成します。
テーブルで追加するカラムは上記の通りです。

## デバッグ準備
万が一、構文の不備などでエラーが発生した場合、エラー文がデフォルトのままだと表示されません。
すなわち、完全に手探りな状態で開発することになってしまいます。(エラーログの場所が分からず、1日ほどログなしで書いていました...)
それだと、開発スピードが落ちてしまうため、エラーログを表示できるようにします。

まず、エラーログが表示されるファイルを探します。
今回は、phpinfo.phpという名前でファイルを作り、下記のコードで保存、MAMPのサーバーを起動した状態で、`http://localhost/php_login/phpinfo.php/` にアクセスします。
```php:phpinfo.php
<?php phpinfo(); ?>
```
error_logの欄を探すと、エラーログのファイルパスが出てきます。
これを開いておけば、エラーが発生した際に自動的にエラーが表示されていきます。

![スクリーンショット 2024-10-05 23.32.34.png](https://qiita-image-store.s3.ap-northeast-1.amazonaws.com/0/3903285/8ac9b040-22de-d3a4-5aa2-05d0b42e6f81.png)


## トップページ作成
ここでは、
- ログインするためのメールアドレスとパスワード入力
- 新規登録をするための画面遷移
の2つを実装します。

まずはindex.phpを作成して、htmlの部分を書いていきます。
```php:index.php
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
```

ログインのための機能の部分は\<form>で囲って、情報を送信しやすくします。
\<label>でメールアドレスの文字の部分を囲って、\<input>と関連づけるために、forとnameに共通の変数を設定します。
同様にパスワードの部分も設定していきます。

新規登録するための画面遷移を設定します。
特殊なことをするわけではないので、相対パスで画面遷移を書きます。


次にphpの中身を書いていきます。
```php:index.php
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
        $stmt = $db->prepare($sql);
        $stmt -> execute();
  
        $cnt = $stmt->rowCount();
  
        if ($cnt == 1) {
          session_start();
          $_SESSION['email'] = $email_value;
          header('Location: ./login.php');
          exit;
        } else if ($cnt == 0) {
          echo "メールアドレスもしくはパスワードが間違っています";
        } else if ($cnt > 1) {
          echo "予期せぬエラーが発生しました";
        }
      } catch (PDOException $e) {
        die("データベース接続失敗 : ". $e->getMessage());
      }
    }
?>
```
このphpは、headerを使用しているため、htmlよりも前に記述します。
(headerより前に出力(htmlも含む)が行われていると、エラーが発生してしまうので、必ず最初に記述する)

ボタンが押下された際に、メールアドレスとパスワードが入力されているかを確認し、入っていなければ早期リターンで処理を中止します。
その後、メールアドレスとパスワードを変数に入力し、条件で両方に値が入っていることを確認します。
```php:index.php
$email_value = $_POST['email'];
$password_value = $_POST['password'];

if(empty($email_value) || empty($password_value)) {
  echo "メールアドレスとパスワードを入力してください";
  exit;
}
```

データベースに接続して、SQLのクエリを実行する準備をします。
今回は、MAMPを使うので、アカウントの情報は、'mysql:host=localhost;dbname=login', 'root', 'root'を使用します。
エラーが発生した際に、何が起こったのかを表示できるようにするため、エラーモードを設定します。
実行するクエリを文字列として、設定してクエリの実行します。
返ってきた値のレコード数をカウントします。
```php:index.php
$db = new PDO('mysql:host=localhost;dbname=login', 'root', 'root');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = 'SELECT * FROM login_info WHERE email = "' . $email_value . '" AND password = "' . $password_value . '"';
$stmt = $db->prepare($sql);
$stmt -> execute();
$cnt = $stmt->rowCount();
```

返ってきたレコードが1件だけである場合、セッションを保存し、ログイン完了後のページに遷移します。
もし、レコードの件数が0件である場合、ログインに失敗したメッセージを表示します。
もし、2件以上のレコードが返ってきた場合、登録できるメールアドレスは一意であるとしているので、ユーザー側で対処できないエラーとしてメッセージを表示します。
```php:index.php
if ($cnt == 1) {
  session_start();
  $_SESSION['email'] = $email_value;
  header('Location: ./login.php');
  exit;
} else if ($cnt == 0) {
  echo "メールアドレスもしくはパスワードが間違っています";
} else if ($cnt > 1) {
  echo "予期せぬエラーが発生しました";
}
```

また、try/catch文でデータベース操作で失敗した際のエラーを表示するようにします。


## ログイン後のページ
このページでは、
- ログインしているアカウントのメールアドレスとパスワードを変更する
- ログアウトする
の2種類を実装します。

```php:login.php
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
</body>
</html>
```

ログイン時に保存したセッション情報をもとに現在ログインしているメールアドレスを表示します。
また、ログインページと同じように、変更用のメールアドレスとパスワード入力の<form>を設置します。
さらに、ログアウトするためのボタンを設置します。

次に、phpの中身を書いていきます。
このコードはログアウトボタンとbodyの閉じタグの間に記述します。
```php:login.php
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
```

大体は、ログインページと同じような記述です。
異なる部分としては、一番最初のPOSTの条件分岐とSQLのクエリのUPDATE文です。

前者は、POSTメソッドが2種類存在しているので、そのうちのchangeと付いたボタン(submit)の情報のみを取得しています。
後者は、アカウント情報を更新するためにデータベースを更新する処理をしています。

さらに、htmlよりも前にもphpコードを記述していきます。
```php:login.php
<?php
  session_start();

  if(isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header('Location: ./index.php');
    exit;
  }
?>
```

これは、ログアウトボタンを押下した際に処理をするコードです。
ログインページと同じく、headerを使用しているため、出力よりも前に記述する必要があります。
また、ログアウト後にはセッションの情報は必要ないため、セッション情報を削除します。


## アカウント登録ページ
このページでは、
- アカウント登録を行う
- 登録後、自動的にログインページに遷移する
- ログインページに戻る
の3種類を実装していきます。

```php:register.php
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
```

ログインページと同じような構造ですが、確認用パスワードを入力する欄を設置しています。

次に、htmlコードの前にphpコードを記述していきます。
```php:register.php
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
```

ここでも、同じくメールアドレス・パスワード・確認用パスワードが入力されているかを確認、パスワードと確認用パスワードが同じかどうかを確認しています。
SQLクエリの部分では、メールアドレスをWHERE文で絞り込んでいます。
これで返ってきたレコード数が1件以上あれば、既にメールアドレスが使用されているので、重複仕様を禁止するために登録できないよう早期リターンしています。
返ってきたレコード数が0件であるならば、入力したメールアドレスとパスワードをINSERTして登録します。
さらに、自動的にログインページにリダイレクトする処理をしています。


## まとめ
下記に、今回作成したコードをまとめたものを表示しておきます。

<details><summary>index.php</summary>

```php:index.php
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
        $stmt = $db->prepare($sql);
        $stmt -> execute();
  
        $cnt = $stmt->rowCount();
  
        if ($cnt == 1) {
          session_start();
          $_SESSION['email'] = $email_value;
          header('Location: ./login.php');
          exit;
        } else if ($cnt == 0) {
          echo "メールアドレスもしくはパスワードが間違っています";
        } else if ($cnt > 1) {
          echo "予期せぬエラーが発生しました";
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
```
</details>

<details><summary>login.php</summary>

```php:login.php
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
```
</details>

<details><summary>register.php</summary>

```php:register.php
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
```
</details>

## 今後の課題
- パスワードを保存する際にハッシュを用いること
- 登録からログインを一度に行うこと
- アカウント登録時にユーザーネームも登録すること
- アカウント情報変更時にメールアドレス・パスワードを独立して変更できること
- method周りの理解

ざっと思いついたものだけでもいくつかあります。
特にセキュリティ面で改善すべき課題が多々あります。
次回はこれらの課題を解決していきながら、phpに対する知識をインプット/アウトプットしていきたいと思います。