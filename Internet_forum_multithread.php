<!DOCTYPE html>
<html lang = "ja">
     <head>
        <meta charset = "UTF-8">
        <title>multithread</title>
     </head>
     <body>
        <?php
         //DB接続設定//
         $dsn = 'データベース名';
         $user = 'ユーザー名';
         $password = 'パスワード';
         $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
         //変数の設定//
         $threadname = $_POST["threadname"];
         $str = $_POST["str"];
         $name = $_POST["name"];
         $erase = $_POST["erase"];
         $edit = $_POST["edit"];
         $pass = $_POST["pass"];
         $erasepass = $_POST["erasepass"];
         $change = $_POST["change"];
         $editpass = $_POST["editpass"];
         $date = date("Y/m/d H:i:s");
         //スレ一覧を表示//
         $sql ='SHOW TABLES';
	     $result = $pdo -> query($sql);
	     foreach ($result as $row){
		          echo $row[0];
		          echo '<br>';
	     }
	echo "<hr>";
         //スレを作る//
         $sql = "CREATE TABLE IF NOT EXISTS $threadname"
	     ." ("
	     . "id INT AUTO_INCREMENT PRIMARY KEY,"
	     . "name char(32),"
	     . "comment TEXT,"
	     . "date char(32),"
	     . "pass TEXT"
	     .");";
	     $stmt = $pdo->query($sql);
         //文章が入っている場合書き込みをする//
         if(!empty($str && $name) && empty($erase) && empty($edit)){
             if(!empty($pass)){
                     $sql = $pdo -> prepare("INSERT INTO $threadname (name, comment,date, pass) VALUES (:name, :comment, :date, :pass)");
	                 $sql -> bindParam(':name', $name, PDO::PARAM_STR);
	                 $sql -> bindParam(':comment', $str, PDO::PARAM_STR);
	                 $sql -> bindParam(':date', $date, PDO::PARAM_STR);
	                 $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
	                 $sql -> execute();
                     $message = $threadname."に書き込みました！";
             }
             else if (empty($pass)){
                 $message = "<FONT COLOR = RED SIZE = 5>パスワードを入れてください！</FONT>";
             }
         }
         //削除番号が入っている場合文章を削除する//
         if (!empty($erase && $erasepass) && empty($name && $str)){
         $message = "<FONT COLOR = RED SIZE = 5>パスワードが違います!</FONT>" ;
         $sql = "SELECT * FROM $threadname";
	     $stmt = $pdo->query($sql);
	     $results = $stmt->fetchAll();
	     foreach ($results as $row){
		 		 if($row['pass'] == $erasepass && $row['id'] == $erase){
	                    $sql = "delete from $threadname where id=:id";
	                    $stmt = $pdo->prepare($sql);
	                    $stmt->bindParam(':id', $erase, PDO::PARAM_INT);
	                    $stmt->execute();
	                    $message = $erase."番目の書き込みを削除しました！";
                 }
                 continue;
	         }
         }
         //書き込みと削除が両方書き込まれてるときはどっちか選ばせる//
         if (!empty($erase || $change) && !empty($name || $str)){
             echo "<FONT COLOR = RED SIZE = 5>書き込み・削除・編集は同時にできません!</FONT>";
         }
         //編集番号が決まっている場合編集モードにする//
         //編集ボタンを押したときの挙動//
         if (empty($erase && $name && $str)
            && !empty($change && $editpass)){
                $message = "<FONT COLOR = RED SIZE = 5>パスワードが違います!</FONT>" ;
                $sql = "SELECT * FROM $threadname";
	            $stmt = $pdo->query($sql);
	            $results = $stmt->fetchAll();
	            foreach ($results as $row){
                         if($row['pass'] == $editpass && $row['id'] == $change){
                             $editnumber = $_POST["change"];
                             $editname = $row['name'];
                             $editline = $row['comment'];
                             $message = "<FONT COLOR = RED SIZE = 5>編集モードです！</FONT>";
                             }
                        
	                continue ;
	            }
         }
         //編集モードの挙動//
         if (empty($_POST["erase"]) && !empty($_POST["name"] && $_POST["str"] && $_POST["edit"])){
         $sql = "UPDATE $threadname SET name=:name,comment=:comment,date=:date WHERE id=:id";
	     $stmt = $pdo->prepare($sql);
	     $stmt->bindParam(':name', $name, PDO::PARAM_STR);
	     $stmt->bindParam(':comment', $str, PDO::PARAM_STR);
	     $stmt->bindParam(':date', $date, PDO::PARAM_STR);
	     $stmt->bindParam(':id', $edit, PDO::PARAM_INT);
	     $stmt->execute();
	     $message = $_POST["edit"]."番目の書き込みを編集しました!";
         }
	     //システムメッセージの表示//
         if(!empty($message)){
              echo $message."<br><br>";
             } 
    ?>
    <form action = "" method = "post">
             スレ番号<br>
             <input type = "text" name = "threadname" placeholder = "スレ名" value =
             <?php
                 if(!empty($threadname)){
                     echo $threadname;
             }
             ?>>
             <button type = "submit" name = "submit">選択</button><br>
             <?php
             if(!empty($threadname)){
             echo $threadname."を表示しています<br>";
             }
             ?><br>
             投稿<br>
             <input type = "text" name = "name" placeholder = "名前" value =
             <?php
                 if(!empty($change)){
                     echo $editname;
             }
             ?>><br>
             <input type = "text" name = "str" placeholder = "コメント" value =
             <?php
                 if(!empty($change)){
                     echo $editline;
             }
             ?>><br>
             <input type = "text" name = "pass" placeholder = "パスワード">
             <input type = "hidden" name = "edit" placeholder = "隠し値" value = 
                <?php 
                     if(!empty(change)){
                         echo $editnumber;
                     }
                ?>
            >
             <button type = "submit" name = "submit">投稿</button><br><br>
             削除<br>
             <input type = "number" name = "erase" placeholder = "削除対象番号"><br>
             <input type = "text" name = "erasepass" placeholder = "パスワード">
             <button type = "submit" name = "submit">削除</button><br><br>
             編集<br>
             <input type = "number" name = "change" placeholder = "編集対象番号"><br>
             <input type = "text" name = "editpass" placeholder = "パスワード">
             <button type = "submit" name = "submit">編集</button>
     </form>
    <?php
         //ブラウザに書き込み内容を表示//
         echo "書き込み一覧<br><br>";
         $sql = "SELECT * FROM $threadname";
	     $stmt = $pdo->query($sql);
	     $results = $stmt->fetchAll();
	     foreach ($results as $row){
		 echo $row['id'].',';
		 echo $row['name'].',';
		 echo $row['comment'].',';
		 echo $row['date'].'<br>';
	     echo "<hr>";
	     }
     ?>
    </body>
</html>