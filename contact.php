<?php

   include 'config.php';

   session_start();

   $user_id = $_SESSION['user_id']; //tạo session người dùng thường

   if(!isset($user_id)){// session không tồn tại => quay lại trang đăng nhập
      header('location:login.php');
   }

   if(isset($_POST['send'])){//lưu tin nhắn từ form submit name='send'

      $name = mysqli_real_escape_string($conn, $_POST['name']);
      $email = mysqli_real_escape_string($conn, $_POST['email']);
      $number = $_POST['number'];
      $msg = mysqli_real_escape_string($conn, $_POST['message']);

      $select_message = mysqli_query($conn, "SELECT * FROM `message` WHERE name = '$name' AND email = '$email' AND number = '$number' AND message = '$msg'") or die('query failed');

      if(mysqli_num_rows($select_message) > 0){
         $message[] = 'Tin nhắn đã được gửi trước đó!';
      }else{
         mysqli_query($conn, "INSERT INTO `message`(user_id, name, email, number, message) VALUES('$user_id', '$name', '$email', '$number', '$msg')") or die('query failed');
         $message[] = 'Tin nhắn đã được gửi thành công!';
      }

   }

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Liên hệ</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .head {
         background: url(./images/head_img.png) no-repeat;
         background-size: cover;
         background-position: center;
      }
   </style>
</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading head">
</div>

<section class="contact">

   <form action="" method="post">
      <h3>Điều bạn muốn chia sẻ</h3>
      <input type="text" name="name" required placeholder="Nhập Họ tên" class="box">
      <input type="email" name="email" required placeholder="Nhập Email" class="box">
      <input type="number" name="number" required placeholder="Nhập số điện thoại" class="box">
      <textarea name="message" class="box" placeholder="Tin nhắn" id="" cols="30" rows="10"></textarea>
      <input type="submit" value="Gửi" name="send" class="btn">
   </form>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>