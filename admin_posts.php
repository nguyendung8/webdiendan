<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id']; // tạo session admin

if (!isset($admin_id)) { // nếu session không tồn tại, quay lại trang đăng nhập
    header('location:login.php');
}

if (isset($_POST['add_post'])) { // thêm bài đăng mới từ submit form name='add_post'

    $user_id = $admin_id;
    $group_id = $_POST['group_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image;
    $created_at = date('Y-m-d H:i:s'); // thời gian hiện tại

    $select_post_title = mysqli_query($conn, "SELECT title FROM `posts` WHERE title = '$title'") or die('query failed'); // kiểm tra bài đăng đã tồn tại chưa

    if (mysqli_num_rows($select_post_title) > 0) {
        $message[] = 'Bài đăng đã tồn tại.';
    } else { // nếu chưa tồn tại thì thêm mới
        $add_post_query = mysqli_query($conn, "INSERT INTO `posts`(user_id, group_id, title, image, created_at) VALUES('$user_id', '$group_id', '$title', '$image', '$created_at')") or die('query failed');

        if ($add_post_query) {
            if ($image_size > 2000000) { // kiểm tra kích thước ảnh
                $message[] = 'Kích thước ảnh quá lớn, hãy cập nhật lại ảnh!';
            } else {
                move_uploaded_file($image_tmp_name, $image_folder); // lưu file ảnh xuống
                $message[] = 'Thêm bài đăng thành công!';
            }
        } else {
            $message[] = 'Thêm bài đăng không thành công!';
        }
    }
}

if (isset($_GET['delete'])) { // xóa bài đăng
    $delete_id = $_GET['delete'];
    $delete_image_query = mysqli_query($conn, "SELECT image FROM `posts` WHERE id = '$delete_id'") or die('query failed');
    $fetch_delete_image = mysqli_fetch_assoc($delete_image_query);
    unlink('uploaded_img/' . $fetch_delete_image['image']); // xóa file ảnh của bài đăng
    mysqli_query($conn, "DELETE FROM `posts` WHERE id = '$delete_id'") or die('query failed');
    header('location:admin_posts.php');
}

if (isset($_POST['update_post'])) { // cập nhật bài đăng

    $update_p_id = $_POST['update_p_id'];
    $update_group_id = $_POST['update_group_id'];
    $update_title = $_POST['update_title'];
    $update_image = $_FILES['update_image']['name'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_image_size = $_FILES['update_image']['size'];
    $update_folder = 'uploaded_img/' . $update_image;
    $update_old_image = $_POST['update_old_image'];

    mysqli_query($conn, "UPDATE `posts` SET  group_id = '$update_group_id', title = '$update_title' WHERE id = '$update_p_id'") or die('query failed');

    if (!empty($update_image)) { // kiểm tra có file ảnh mới không
        if ($update_image_size > 2000000) {
            $message[] = 'Kích thước ảnh quá lớn!';
        } else {
            mysqli_query($conn, "UPDATE `posts` SET image = '$update_image' WHERE id = '$update_p_id'") or die('query failed');
            move_uploaded_file($update_image_tmp_name, $update_folder); // lưu file ảnh mới
            unlink('uploaded_img/' . $update_old_image); // xóa file ảnh cũ
        }
    }

    header('location:admin_posts.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quản lý bài đăng</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
   <link rel="stylesheet" href="./css/new_style.css">
   <style>
      .post {
         border: 1px solid #ccc;
      }
   </style>
    
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="add-products">

    <h1 class="title">Thêm bài đăng</h1>

    <form action="" method="post" enctype="multipart/form-data">
         <input type="text" name="title" class="box" placeholder="Tiêu đề bài đăng" required>
         <select name="group_id" class="box" required>
            <option value="" disabled selected>Chọn nhóm</option>
            <?php
            $select_groups = mysqli_query($conn, "SELECT * FROM `groups`") or die('query failed');
            if (mysqli_num_rows($select_groups) > 0) {
                  while ($fetch_groups = mysqli_fetch_assoc($select_groups)) {
                     echo "<option value='{$fetch_groups['id']}'>{$fetch_groups['group_name']}</option>";
                  }
            } else {
                  echo '<option value="" disabled>Không có nhóm</option>';
            }
            ?>
         </select>
        <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required>
        <input type="submit" value="Thêm" name="add_post" class="btn">
    </form>

</section>

<section class="show-products">

    <div class="post-container">

        <?php
        $select_posts = mysqli_query($conn, "SELECT posts.*, users.name AS user_name, groups.group_name 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        JOIN groups ON posts.group_id = groups.id ") or die('query failed');
        if (mysqli_num_rows($select_posts) > 0) {
            while ($fetch_posts = mysqli_fetch_assoc($select_posts)) {
        ?>
                <div class="post">
                    <img src="uploaded_img/<?php echo $fetch_posts['image']; ?>" alt="">
                    <div style="flex: 2">
                        <div class="title"><?php echo $fetch_posts['title']; ?></div>
                        <div class="sub-info">Người đăng: <?php echo $fetch_posts['user_name']; ?></div>
                        <div class="sub-info">Nhóm: <?php echo $fetch_posts['group_name']; ?></div>
                        <div class="sub-info">Ngày tạo: <?php echo $fetch_posts['created_at']; ?></div>
                    </div>
                    <div class="action">
                       <a href="admin_posts.php?update=<?php echo $fetch_posts['id']; ?>" class="option-btn">Cập nhật</a>
                       <a href="admin_posts.php?delete=<?php echo $fetch_posts['id']; ?>" class="delete-btn" onclick="return confirm('Xóa bài đăng này?');">Xóa</a>
                    </div>
                </div>
        <?php
            }
        } else {
            echo '<p class="empty">Không có bài đăng nào!</p>';
        }
        ?>

    </div>

</section>

<section class="edit-product-form">

    <?php
    if (isset($_GET['update'])) { // hiện form update
        $update_id = $_GET['update'];
        $update_query = mysqli_query($conn, "SELECT * FROM `posts` WHERE id = '$update_id'") or die('query failed');
        if (mysqli_num_rows($update_query) > 0) {
            while ($fetch_update = mysqli_fetch_assoc($update_query)) {
    ?>
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['id']; ?>">
                    <input type="hidden" name="update_old_image" value="<?php echo $fetch_update['image']; ?>">
                    <img src="uploaded_img/<?php echo $fetch_update['image']; ?>" alt="">
                    <input type="text" name="update_title" value="<?php echo $fetch_update['title']; ?>" class="box" required>
                     <select name="update_group_id" class="box" required>
                        <option value="" disabled>Chọn nhóm</option>
                        <?php
                        $select_groups = mysqli_query($conn, "SELECT * FROM `groups`") or die('query failed');
                        if (mysqli_num_rows($select_groups) > 0) {
                              while ($fetch_groups = mysqli_fetch_assoc($select_groups)) {
                                 $selected = ($fetch_update['group_id'] == $fetch_groups['id']) ? 'selected' : '';
                                 echo "<option value='{$fetch_groups['id']}' $selected>{$fetch_groups['group_name']}</option>";
                              }
                        } else {
                              echo '<option value="" disabled>Không có nhóm</option>';
                        }
                        ?>
                     </select>
                    <input type="submit" value="Cập nhật" name="update_post" class="btn">
                    <input type="reset" value="Hủy" id="close-update-post" class="option-btn">
                </form>
    <?php
            }
        }
    } else {
        echo '<script>document.querySelector(".edit-product-form").style.display = "none";</script>';
    }
    ?>

</section>

<script src="js/admin_script.js"></script>
<script>
   document.querySelector('#close-update-post').onclick = () =>{
   document.querySelector('.edit-product-form').style.display = 'none';
   window.location.href = 'admin_posts.php';
}
</script>

</body>
</html>
