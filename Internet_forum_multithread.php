<!DOCTYPE html>
<html lang = "en">
     <head>
        <meta charset = "UTF-8">
        <title>multithread</title>
     </head>
     <body>
        <?php
         //Information of database//
         $dsn = 'Database name';
         $user = 'User name';
         $password = 'Password';
         $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
         //Setting of variable//
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
         //Show existing thread//
         $sql ='SHOW TABLES';
	     $result = $pdo -> query($sql);
	     foreach ($result as $row){
		          echo $row[0];
		          echo '<br>';
	     }
	echo "<hr>";
         //Create a new thread//
         $sql = "CREATE TABLE IF NOT EXISTS $threadname"
	     ." ("
	     . "id INT AUTO_INCREMENT PRIMARY KEY,"
	     . "name char(32),"
	     . "comment TEXT,"
	     . "date char(32),"
	     . "pass TEXT"
	     .");";
	     $stmt = $pdo->query($sql);
         //Wirte a comment//
         if(!empty($str && $name) && empty($erase) && empty($edit)){
             if(!empty($pass)){
                     $sql = $pdo -> prepare("INSERT INTO $threadname (name, comment,date, pass) VALUES (:name, :comment, :date, :pass)");
	                 $sql -> bindParam(':name', $name, PDO::PARAM_STR);
	                 $sql -> bindParam(':comment', $str, PDO::PARAM_STR);
	                 $sql -> bindParam(':date', $date, PDO::PARAM_STR);
	                 $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
	                 $sql -> execute();
                     $message = "Wrote your comment to".$threadname;
             }
             else if (empty($pass)){
                 $message = "<FONT COLOR = RED SIZE = 5>Please set a password!</FONT>";
             }
         }
         //Delete your comment//
         if (!empty($erase && $erasepass) && empty($name && $str)){
         $message = "<FONT COLOR = RED SIZE = 5>Password is wrong!</FONT>" ;
         $sql = "SELECT * FROM $threadname";
	     $stmt = $pdo->query($sql);
	     $results = $stmt->fetchAll();
	     foreach ($results as $row){
		 		 if($row['pass'] == $erasepass && $row['id'] == $erase){
	                    $sql = "delete from $threadname where id=:id";
	                    $stmt = $pdo->prepare($sql);
	                    $stmt->bindParam(':id', $erase, PDO::PARAM_INT);
	                    $stmt->execute();
	                    $message = "Comment No.".$erase."was deleted!";
                 }
                 continue;
	         }
         }
         //Choose write, delete or Rewrite//
         if (!empty($erase || $change) && !empty($name || $str)){
             echo "<FONT COLOR = RED SIZE = 5>You can't do more than two actions!</FONT>";
         }
         //Rewrite your comment//
         //Enter the editmode//
         if (empty($erase && $name && $str)
            && !empty($change && $editpass)){
                $message = "<FONT COLOR = RED SIZE = 5>Password is wrong!</FONT>" ;
                $sql = "SELECT * FROM $threadname";
	            $stmt = $pdo->query($sql);
	            $results = $stmt->fetchAll();
	            foreach ($results as $row){
                         if($row['pass'] == $editpass && $row['id'] == $change){
                             $editnumber = $_POST["change"];
                             $editname = $row['name'];
                             $editline = $row['comment'];
                             $message = "<FONT COLOR = RED SIZE = 5>Editmode now！</FONT>";
                             }
                        
	                continue ;
	            }
         }
         //In the editmode//
         if (empty($_POST["erase"]) && !empty($_POST["name"] && $_POST["str"] && $_POST["edit"])){
         $sql = "UPDATE $threadname SET name=:name,comment=:comment,date=:date WHERE id=:id";
	     $stmt = $pdo->prepare($sql);
	     $stmt->bindParam(':name', $name, PDO::PARAM_STR);
	     $stmt->bindParam(':comment', $str, PDO::PARAM_STR);
	     $stmt->bindParam(':date', $date, PDO::PARAM_STR);
	     $stmt->bindParam(':id', $edit, PDO::PARAM_INT);
	     $stmt->execute();
	     $message = "Comment number".$_POST["edit"]."was rewrited!";
         }
	     //Show message//
         if(!empty($message)){
              echo $message."<br><br>";
             } 
    ?>
    <form action = "" method = "post">
             thread name<br>
             <input type = "text" name = "threadname" placeholder = "threadname" value =
             <?php
                 if(!empty($threadname)){
                     echo $threadname;
             }
             ?>>
             <button type = "submit" name = "submit">選択</button><br>
             <?php
             if(!empty($threadname)){
             echo "You are looking".$threadname."<br>";
             }
             ?><br>
             Watch Thread<br>
             <input type = "text" name = "name" placeholder = "name" value =
             <?php
                 if(!empty($change)){
                     echo $editname;
             }
             ?>><br>
             <input type = "text" name = "str" placeholder = "comment" value =
             <?php
                 if(!empty($change)){
                     echo $editline;
             }
             ?>><br>
             <input type = "text" name = "pass" placeholder = "password">
             <input type = "hidden" name = "edit" placeholder = "hiddendate" value = 
                <?php 
                     if(!empty(change)){
                         echo $editnumber;
                     }
                ?>
            >
             <button type = "submit" name = "submit">Post</button><br><br>
             Delete comment<br>
             <input type = "number" name = "erase" placeholder = "comment number"><br>
             <input type = "text" name = "erasepass" placeholder = "password">
             <button type = "submit" name = "submit">Delete</button><br><br>
             Rewrite comment<br>
             <input type = "number" name = "change" placeholder = "comment number"><br>
             <input type = "text" name = "editpass" placeholder = "password">
             <button type = "submit" name = "submit">Edit</button>
     </form>
    <?php
         //Show all comments of thread//
         echo "All comments<br><br>";
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
