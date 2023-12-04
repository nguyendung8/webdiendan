<?php
   include 'config.php';

   session_start();

   $user_id = $_SESSION['user_id']; //tạo session người dùng thường

   if(!isset($user_id)){// session không tồn tại => quay lại trang đăng nhập
      header('location:login.php');
   }
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Thông tin</title>

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

<section class="about">

   <div class="flex">

      <div class="image">
         <img style="border-radius: 4px;" height="350px" src="images/info-img.jpg" alt="">
      </div>

      <div class="content">
         <h3>Tại sao lại có ToCoToCo.</h3>
         <p>Hành trình đầy đam mê và tâm huyết này sẽ tiếp tục nhân rộng để lan tỏa những ly trà thuần khiết nông sản Việt đến mọi miền trên Việt Nam và thế giới.</p>
         <p>Sảng khoái mỗi ngày, tươi trẻ mỗi ngày.</p>
         <a href="contact.php" class="btn">Liên hệ</a>
      </div>

   </div>

</section>


<section class="authors">

   <h1 class="title">Thành viên của ToCoToCo</h1>

   <div class="box-container">

      <div class="box">
         <img src="images/huynhnhu.jpg" alt="">
         <div class="share">
            <a href="#" class="fab fa-facebook-f" target="_blank"></a>
            <a href="#" class="fab fa-instagram"></a>
         </div>
         <h3>Huỳnh Như </h3>
      </div>

      <div class="box">
         <img src="images/xuanha.jpg" alt="">
         <div class="share">
            <a href="#" class="fab fa-facebook-f" target="_blank"></a>
            <a href="#" class="fab fa-instagram"></a>
         </div>
         <h3>Xuân Hà </h3>
      </div>
   </div>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>