<?php

   include 'config.php';

   session_start();

   $admin_id = $_SESSION['admin_id']; //tạo session admin

    if(!isset($admin_id)){// session không tồn tại => quay lại trang đăng nhập
        header('location:login.php');
    }

    if(isset($_POST['add_new'])){//Thêm loại sách vào tin tức từ submit có name='add_new'

        $content = mysqli_real_escape_string($conn, $_POST['content']);
        $image = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = 'uploaded_img/'.$image;

        $add_new_query = mysqli_query($conn, "INSERT INTO `news`(content, image) VALUES('$content', '$image')") or die('query failed');

        if($add_new_query){
            move_uploaded_file($image_tmp_name, $image_folder);//lưu file ảnh xuống
            $message[] = 'Thêm tin tức thành công!';
        }else{
            $message[] = 'Thêm tin tức không thành công!';
        }
    }

   if(isset($_GET['delete'])){//Xóa loại sách từ onclick <a></a> có href='delete'
      $delete_id = $_GET['delete'];
      try {
         mysqli_query($conn, "DELETE FROM `news` WHERE id = '$delete_id'") or die('query failed');
         $message[] = 'Xóa tin tức thành công';
      } catch(Exception) {
         $message[] = 'Xóa tin tức không thành công';
      }
   }

   if(isset($_POST['update_new'])){//Cập nhật loại sách vào tin tức từ submit có name='update_new'

      $update_id = $_POST['update_id'];
      $update_content = $_POST['update_content'];

      mysqli_query($conn, "UPDATE `news` SET content = '$update_content' WHERE id = '$update_id'") or die('query failed');

      $message[] = "Cập nhật tin tức thành công";
      header('location:admin_news.php');
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
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="add-products">

   <h1 class="title">Tin tức</h1>

   <form action="" method="post" enctype="multipart/form-data">
      <h3>Thêm tin tức</h3>
      <textarea name="content" class="box" placeholder="Nội dung" required></textarea>
      <input type="file" name="image" class="box" required>
      <input type="submit" value="Thêm tin tức " name="add_new" class="btn">
   </form>

</section>

<section class="show-products">

   <div class="box-container">

        <?php
            $select_news = mysqli_query($conn, "SELECT * FROM `news`") or die('query failed');
            if(mysqli_num_rows($select_news) > 0){
            while($fetch_news = mysqli_fetch_assoc($select_news)){
        ?>
        <div style="height: -webkit-fill-available;" class="box">
            <img style="height: 23rem !important" style="border-radius: 4px;" src="uploaded_img/<?php echo $fetch_news['image']; ?>" alt="">
            <div class="sub-name">Nội dung: <?php echo $fetch_news['content']; ?></div>
            <a href="admin_news.php?update=<?php echo $fetch_news['id']; ?>" class="option-btn">Cập nhật</a>
            <a href="admin_news.php?delete=<?php echo $fetch_news['id']; ?>" class="delete-btn" onclick="return confirm('Xóa tin tức này?');">Xóa</a>
        </div>
        <?php
            }
        }else{
            echo '<p class="empty">Không có tin tức nào được thêm!</p>';  
        }
        ?>
   </div>

</section>

<section class="edit-product-form">

   <?php
      if(isset($_GET['update'])){//Hiện form cập nhật thông tin loại sách từ <a></a> có href='update'
         $update_id = $_GET['update'];
         $update_query = mysqli_query($conn, "SELECT * FROM `news` WHERE id = '$update_id'") or die('query failed');//lấy ra thông tin loại sách cần cập nhật
         if(mysqli_num_rows($update_query) > 0){
            while($fetch_update = mysqli_fetch_assoc($update_query)){
   ?>
               <form action="" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="update_id" value="<?php echo $fetch_update['id']; ?>">
                 <img style="height: 23rem !important" style="border-radius: 4px;" src="uploaded_img/<?php echo $fetch_update['image']; ?>" alt="">
                  <input type="text" name="update_content" value="<?php echo $fetch_update['content']; ?>" class="box" required placeholder="Nội dung">
                  <input type="submit" value="Cập nhật" name="update_new" class="btn"> <!-- submit form cập nhật -->
                  <input type="reset" value="Hủy"  onclick="window.location.href = 'admin_news.php'" class="option-btn">
               </form>
   <?php
            }
         }
      }else{
         echo '<script>document.querySelector(".edit-product-form").style.display = "none";</script>';
      }
   ?>

</section>

<script src="js/admin_script.js"></script>

</body>
</html>