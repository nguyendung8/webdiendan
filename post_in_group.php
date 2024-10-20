<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id']; // Lấy session user

// Kiểm tra nếu user chưa đăng nhập thì chuyển hướng về trang đăng nhập
if (!isset($user_id)) {
    header('location:login.php');
    exit;
}

// Lấy group_id từ URL
if (isset($_GET['group_id'])) {
    $group_id = $_GET['group_id'];
} else {
    // Nếu không có group_id thì chuyển hướng hoặc thông báo lỗi
    header('location:groups.php'); // Điều hướng về trang danh sách nhóm
    exit;
}

if (isset($_POST['add_post'])) { // Xử lý khi form được submit
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image;
    $created_at = date('Y-m-d H:i:s'); // Lấy thời gian hiện tại

    $select_post_title = mysqli_query($conn, "SELECT title FROM `posts` WHERE title = '$title'") or die('query failed');
    
    if (mysqli_num_rows($select_post_title) > 0) {
        $message[] = 'Bài đăng đã tồn tại.';
    } else {
        // Thêm bài đăng mới vào database
        $add_post_query = mysqli_query($conn, "INSERT INTO `posts`(user_id, group_id, title, image, created_at) VALUES('$user_id', '$group_id', '$title', '$image', '$created_at')") or die('query failed');

        if ($add_post_query) {
            if ($image_size > 2000000) { // Kiểm tra kích thước ảnh
                $message[] = 'Kích thước ảnh quá lớn, hãy cập nhật lại ảnh!';
            } else {
                move_uploaded_file($image_tmp_name, $image_folder); // Lưu ảnh vào thư mục
                $message[] = 'Thêm bài đăng thành công!';
                header('location: user_groups.php');
            }
        } else {
            $message[] = 'Thêm bài đăng không thành công!';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Đăng bài viết</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'header.php'; ?>

<section class="add-products">

    <h1 class="title">Đăng bài viết trong nhóm</h1>

    <form action="" method="post" enctype="multipart/form-data">
         <input type="text" name="title" class="box" placeholder="Tiêu đề bài đăng" required>
        <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required>
        <input type="submit" value="Đăng bài" name="add_post" class="btn">
    </form>

</section>

<script src="js/script.js"></script>
</body>
</html>
