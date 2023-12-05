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
   <title>Tin tức</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .head {
         background: url(./images/head_img.png) no-repeat;
         background-size: cover;
         background-position: center;
      }
      .new-title {
        text-align: center;
        font-size: 30px;
		margin-bottom: 10px;
      }
	  .new-item {
		display: flex;
		gap: 20px;
		font-size: 23px;
		border: 1px solid #ddd;
		padding: 13px;
		margin-bottom: 10px;
		border-radius: 4px;
		box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
	  }
   </style>
</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading head">
</div>

<section class="contact">
    <h1 class="new-title">Danh sách tin tức đáng chú ý</h1>
    <div class="list-news">
		<?php
            $select_news = mysqli_query($conn, "SELECT * FROM `news`") or die('query failed');
            if(mysqli_num_rows($select_news) > 0){
            while($fetch_news = mysqli_fetch_assoc($select_news)){
			?>
			<div style="height: -webkit-fill-available;" class="new-item">
				<img style="height: 129px !important; border-radius: 13px;" src="uploaded_img/<?php echo $fetch_news['image']; ?>" alt="">
				<div class="sub-name"><?php echo $fetch_news['content']; ?></div>
			</div>
			<?php
				}
			}else{
				echo '<p class="empty">Không có tin tức nào được thêm!</p>';  
			}
        ?>
    </div>
</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>