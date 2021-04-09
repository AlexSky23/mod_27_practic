<?php
include 'config.php';

//Соединяемся с базой данных используя наши доступы:

$db = new PDO("mysql:host=$host;dbname=$db_name", $user, $password);
$stmt = $db->query("SELECT * FROM users WHERE user_id = '".intval($_COOKIE['id'])."'");
$result = $stmt->FETCH(PDO::FETCH_LAZY);
    //echo $result->user_id;
    $u_id = $result->user_id;
    $user1 = $result->user_login;


$err = [];
$msg = [];

$imageFileName = $_GET['name'];
//$commentFilePath = COMMENT_DIR . '/' . $imageFileName . '.txt';

//если комм-т был отправлен
if (!empty($_POST['comment'])) {
    $comment = trim($_POST['comment']);

    //валидация коммента
    if ($comment === '') {
        $err[] = 'Не введен комментарий!';
    }

    //если нет ош., то записать комм.
    if (empty($err)) {
        
        $comment = strip_tags($comment);
        $comment = str_replace(array(["/r/n", "/r", "/n", "//r", "//n", "//r//n"]), "<br/>", $comment);
        $comment = $user1 .": ". $comment . ' : ' . date('d.m.Y H:i');

        // Дозапись текста в файл
    
        $stmt = $db->query("SELECT * FROM comment WHERE user_id = '".intval($_COOKIE['id'])."'") or die ( mysqli_error($link) );
        $result = $stmt->FETCH(PDO::FETCH_LAZY);
        $u_id1 = $result->user_id;
        $f_n = $result->img_name;
      //  print_r( "sdfsdfd". $u_id1) ;
        if(($u_id1 > 0) && ($f_n > 0)){
        $stmt = $db->prepare("UPDATE `comment` SET `comment`='$comment' WHERE user_id = '".intval($_COOKIE['id'])."' AND img_name = '$imageFileName'") or die ( mysqli_error($link) );
    }
        else{
            $stmt = $db->prepare("INSERT INTO comment (user_id, img_name, comment) VALUES (:u_id, :i_n, :comm)") or die ( mysqli_error($link) );
            $stmt->bindParam(':u_id', $u_id);
            $stmt->bindParam(':i_n', $imageFileName);
            $stmt->bindParam(':comm', $comment);
        }
        $stmt->execute();
        $msg[] = 'Комментарий добавлен';
    }
};

// delete comment
if (!empty($_POST['name'])) {
    $a = $_POST['name'];
    //echo $a;
    $stmt = $db->prepare("UPDATE `comment` SET `comment`= '' WHERE user_id = '".intval($_COOKIE['id'])."' AND img_name = '$imageFileName' AND comment = '$a'") or die ( mysqli_error($link) );
    $stmt->execute();
    $stmt = $db->exec("DELETE FROM comment WHERE `comment` = ''");

$msg[] = 'Коммент удалён';
};

// получение списка комментов

    $stmt = $db->query("SELECT * FROM comment WHERE img_name = '$imageFileName'");
    $result = $stmt->FetchAll(PDO::FETCH_ASSOC);

foreach($result as $user) {
    
    $comments [] = $user["comment"]; 
};
    ?>

<!doctype html>
<html lang="en">

<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>PHP_practic - Галлерея картинок</title>
    <link rel="stylesheet" href="style.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" 
    integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

</head>
<body>
<div class="container pt-4">
        <h1 class="mb-4"><a href="<?php echo URL; ?>">Галлерея картинок</a></h1>
        
                <!-- message error on/off-->
                <?php foreach($err as $er):?>
        <div class="alert alert-danger"><?php echo $er; ?></div>
        <?php endforeach; ?>

        <?php foreach($msg as $ms):?>
        <div class="alert alert-danger"><?php echo $ms; ?></div>
        <?php endforeach; ?>

        <h2 class="mb-4">Файл <?php echo $imageFileName; ?>:</h2>

        <div class="row">
            <div class="col-12 col-sm-8 offset-sm-2">
                <img src="<?php echo URL . '/' . UPLOAD_DIR . '/' . $imageFileName ?>" 
                class="img-thumbnail mb-4" alt="<?php echo $imageFileName ?> ">

                <h3>Комментарии</h3>

                <?php if (!empty($comments)): ?>
                    <?php foreach ($comments as $key => $comment): ?>
                        <p class="<?php echo (($key % 2) > 0)? 'bg-light' : ''; ?>" >
                        <form method="post">
                            <input type="hidden" name="name" value="<?php echo $comment; ?>">
        <?php
        if (isset($_COOKIE['id']) and isset($_COOKIE['hash']))
        {
          echo '<button type="submit" class="close" aria-label="close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </form>        ';
                    }
                    ?>
                        <?php echo $comment; ?>

                        </p>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">Оставьте первый коммент!</p>
                <?php endif; ?>

                <!--добавление коммента -->
                <?php
        if (isset($_COOKIE['id']) and isset($_COOKIE['hash']))
        {
          echo '
                    <form method="post">
                        <div class="form-group">
                            <label for="comment">Ваш коммент</label>
                            <textarea class="form-control" name="comment" id="comment" rows="3" required></textarea>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary">Отправить</button>
                    </form>';
                }
                ?>
            </div>
        </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
    integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
    integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN"
    crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"
    integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV"
    crossorigin="anonymous"></script>
</body>
</html>