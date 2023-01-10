<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>

<body>
    <h1>簡易掲示板</h1>
    <?php
    
        $initial_num = "投稿番号を選択";
        
        
     //データベース接続
        $dsn ='データベース名';
        $user = 'ユーザー名';
        $password = 'パスワード';
        $db = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        
        //テーブルの作成
        $sql = "CREATE TABLE IF NOT EXISTS keijiban"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "date char(32),"
    . "pass char(32)"
    .");";
    $stmt = $db->query($sql);
    
    
    //日付表示の指定
    $date = date("Y年m月d日 H時i分s秒");
    
    
    //編集番号受信したときの処理
    if(!empty($_POST["post_edi"]) && !empty($_POST["post_edi_pass"])){
        $post_edi = $_POST["post_edi"];
        $post_edi_pass = $_POST["post_edi_pass"];
        $edi_stmt = $db -> prepare("SELECT * FROM keijiban WHERE id=$post_edi");
        $edi_stmt -> execute();
        $edi_stmt = $edi_stmt->fetch(PDO::FETCH_ASSOC);
        $edi_pass = $edi_stmt["pass"];
        $edi_num = $edi_stmt["id"];
        
        if($edi_num != $post_edi){
            echo "投稿番号が存在しません"."<br>";
        }
        
        if($edi_pass == $post_edi_pass){
            $edi_no = $edi_stmt["id"];
            $edi_name = $edi_stmt["name"];
            $edi_com = $edi_stmt["comment"];
        }elseif($edi_pass != $post_edi_pass){
            echo "パスワードが違います"."<br>";
        }
    }
        
        
        
        
    
    
    
    ?>
    
    
    <form action="" method="post">
    お名前：<input type="text" name="post_name" value=<?php
    
        if(!empty($edi_no)){
            echo $edi_name;
        }
    
    ?>><br>
    
    コメント：<input type="text" name="post_com" value=<?php
    
        if(!empty($edi_no)){
            echo $edi_com;
        }
    
    ?>><br>
    
    パスワード：<input type="text" name="post_pass" value=<?php
        if(!empty($edi_no)){
                echo $edi_pass;
        }
    ?>><br>
    
    <input type="submit" name="submit" value="投稿"><br><br>
    
    <!--編集機能用-->
    <input type="hidden" name="post_edino" value=<?php
        if(!empty($edi_no)){
                echo $edi_no;
        }
    ?>><br>
    
    削除対象番号：<input type="number" name="post_del" placeholder=<?=$initial_num?>><br>
    パスワード：<input type="text" name="del_pass" placeholder="設定したパスワードを入力してください"><br>
    <input type="submit" name="del_botan" value="削除"><br><br>
    
    編集対象番号：<input type="number" name="post_edi" placeholder=<?=$initial_num?>><br>
    パスワード：<input type="text" name="post_edi_pass" placeholder="設定したパスワードを入力してください"><br>
    <input type="submit" name="edi_botan" value="編集"><br><br>
    
    </form>
    
    <?php
       
    //投稿＆編集機能
    if(!empty($_POST["post_name"]) && !empty($_POST["post_com"]) && !empty($_POST["post_pass"])){
        $post_name = $_POST["post_name"];
        $post_com = $_POST["post_com"];
        $post_pass = $_POST["post_pass"];
        
        //編集の上書き保存
        if(!empty($_POST["post_edino"])){
            $post_edino = $_POST["post_edino"];
            $postedino_stmt = $db ->prepare("SELECT * FROM keijiban");
		    $postedino_stmt -> execute();
		    
                $edi_stmt = $db -> prepare("UPDATE keijiban SET id=:id, name=:name, comment=:comment, date=:date, pass=:pass WHERE id=$post_edino");
                $edi_stmt -> bindParam(':name', $post_name, PDO::PARAM_STR);
                $edi_stmt -> bindParam(':comment', $post_com, PDO::PARAM_STR);
                $edi_stmt -> bindParam(':date', $date, PDO::PARAM_STR);
                $edi_stmt -> bindParam(':pass', $post_pass, PDO::PARAM_STR);
                $edi_stmt -> bindParam(':id', $post_edino, PDO::PARAM_INT);
                $edi_stmt -> execute();
    		    
    		    $sql = 'SELECT * FROM keijiban';
                $stmt = $db -> query($sql);
                $results = $stmt -> fetchAll();

            
        }else{
            //新規投稿
            $sql = $db -> prepare("INSERT INTO keijiban (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
            $sql -> bindParam(':name', $post_name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $post_com, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
            $sql -> bindParam(':pass', $post_pass, PDO::PARAM_STR);
            $sql -> execute();
            $sql = 'SELECT * FROM keijiban';
            $stmt = $db -> query($sql);
            $results = $stmt -> fetchAll();
    
        }
    
    }
    
    
    
    //削除機能
    if(!empty($_POST["post_del"]) && !empty($_POST["del_pass"])){
        $post_del = $_POST["post_del"];
        $del_pass = $_POST["del_pass"];
        
        $postdel_stmt = $db ->prepare("SELECT * FROM keijiban");
		$postdel_stmt -> execute();
		
		foreach($postdel_stmt as $del_row){
		    if($del_row["id"] == $post_del && $del_row["pass"] == $del_pass){
		        $sql = "DELETE FROM keijiban WHERE id=$post_del";
		        $stmt = $db -> prepare($sql);
		        $stmt -> bindValue(':id', $post_del, PDO::PARAM_INT);
		        $stmt -> execute();
		    
                    echo "メッセージを削除しました" . "<br>";
                echo "<hr>";
                break;
		    }elseif($del_row["id"] == $post_del && $del_row["pass"] != $del_pass){
		            echo "パスワードが違います" . "<br>";
		        echo "<hr>";
		        break;
		    }
		}
    }
    
    //投稿内容の表示
    try{
        $sql = "SELECT COUNT(*) FROM keijiban";
        $count = $db->query($sql);
        $count = $count->fetchColumn();
        
        $tb = "SELECT * FROM keijiban";
        $stmt = $db->query($tb);
        
        if($count == 0){
            
            echo "まだ投稿はありません";
            
        }else{
            foreach($stmt as $row){
                echo $row['id'].',';
                echo $row['name'].',';
                echo $row['comment'].',';
                echo $row['date'].'<br>';
            echo "<hr>";
            }
            
        }
    }catch(PDOException $e){
        print( "表示エラー:" . $e->getmessage());
        die();
    }
    
    
    ?>
    

</body>
</html>
