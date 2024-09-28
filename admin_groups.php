<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id']; // Tạo session admin

if(!isset($admin_id)){ // Nếu session không tồn tại, quay lại trang đăng nhập
   header('location:login.php');
}

// Thêm nhóm mới
if(isset($_POST['add_group'])){

   $group_name = mysqli_real_escape_string($conn, $_POST['group_name']);
   $description = mysqli_real_escape_string($conn, $_POST['description']);

   $select_group_name = mysqli_query($conn, "SELECT group_name FROM `groups` WHERE group_name = '$group_name'") or die('query failed');

   if(mysqli_num_rows($select_group_name) > 0){ // Nếu nhóm đã tồn tại
      $message[] = 'Nhóm đã tồn tại.';
   }else{ // Nếu chưa tồn tại thì thêm nhóm mới
      $add_group_query = mysqli_query($conn, "INSERT INTO `groups`(group_name, description, created_at) VALUES('$group_name', '$description', NOW())") or die('query failed');

      if($add_group_query){
         $message[] = 'Thêm nhóm thành công!';
      }else{
         $message[] = 'Không thể thêm nhóm!';
      }
   }
}

// Xóa nhóm
if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM `groups` WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_groups.php');
}

// Cập nhật nhóm
if(isset($_POST['update_group'])){

   $update_g_id = $_POST['update_g_id'];
   $update_group_name = $_POST['update_group_name'];
   $update_description = $_POST['update_description'];

   mysqli_query($conn, "UPDATE `groups` SET group_name = '$update_group_name', description = '$update_description' WHERE id = '$update_g_id'") or die('query failed');

   header('location:admin_groups.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quản lý nhóm</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="add-products">

   <h1 class="title">Danh sách nhóm</h1>

   <form action="" method="post" enctype="multipart/form-data">
      <h3>Thêm nhóm</h3>
      <input type="text" name="group_name" class="box" placeholder="Tên nhóm" required>
      <input type="text" name="description" class="box" placeholder="Mô tả nhóm" required>
      <input type="submit" value="Thêm nhóm" name="add_group" class="btn">
   </form>

</section>

<section class="show-products">

   <div class="box-container">

      <?php
         $select_groups = mysqli_query($conn, "SELECT * FROM `groups`") or die('query failed');
         if(mysqli_num_rows($select_groups) > 0){
            while($fetch_groups = mysqli_fetch_assoc($select_groups)){
      ?>
      <div class="box">
         <div class="name"><?php echo $fetch_groups['group_name']; ?></div>
         <div class="sub-name">Mô tả: <?php echo $fetch_groups['description']; ?></div>
         <a href="admin_groups.php?update=<?php echo $fetch_groups['id']; ?>" class="option-btn">Cập nhật</a>
         <a href="admin_groups.php?delete=<?php echo $fetch_groups['id']; ?>" class="delete-btn" onclick="return confirm('Xóa nhóm này?');">Xóa</a>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">Chưa có nhóm nào được thêm!</p>';  
      }
      ?>
   </div>

</section>

<section class="edit-product-form">

   <?php
      if(isset($_GET['update'])){ // Hiển thị form cập nhật nhóm
         $update_id = $_GET['update'];
         $update_query = mysqli_query($conn, "SELECT * FROM `groups` WHERE id = '$update_id'") or die('query failed');
         if(mysqli_num_rows($update_query) > 0){
            while($fetch_update = mysqli_fetch_assoc($update_query)){
   ?>
               <form action="" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="update_g_id" value="<?php echo $fetch_update['id']; ?>">
                  <input type="text" name="update_group_name" value="<?php echo $fetch_update['group_name']; ?>" class="box" required placeholder="Tên nhóm">
                  <input type="text" name="update_description" value="<?php echo $fetch_update['description']; ?>" class="box" required placeholder="Mô tả">
                  <input type="submit" value="Cập nhật" name="update_group" class="btn">
                  <input type="reset" value="Hủy" onclick="window.location.href = 'admin_groups.php'" class="option-btn">
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
