<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id']; // Lấy user_id từ session

// Kiểm tra nếu user chưa đăng nhập thì chuyển hướng về trang đăng nhập
if (!isset($user_id)) {
    header('location:login.php');
    exit;
}

if (isset($_GET['delete'])) { // Xóa bài đăng
    $delete_id = $_GET['delete'];

    try {
        // Vô hiệu hóa ràng buộc khóa ngoại
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0") or die('Query failed: Disable foreign key checks');

        // Xóa bài đăng
        $query = "DELETE FROM `posts` WHERE id = '$delete_id'";
        mysqli_query($conn, $query) or die('Query failed: Delete post');

        // Kích hoạt lại ràng buộc khóa ngoại
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1") or die('Query failed: Enable foreign key checks');

        header('Location: user_posts.php');
        exit(); // Đảm bảo dừng script sau khi redirect

    } catch (mysqli_sql_exception $e) {
        $message[] = 'Không thể xóa bài đăng này!';
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Danh sách bài viết của tôi</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
    .post-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        width: 100%;
        justify-content: space-between;
    }

    /* Các box chứa bài đăng */
    .post {
        display: flex;
        margin: auto;
        gap: 10px;
        background-color: #f5f5f5;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        width: 70%;
        align-items: center;
        border: 1px solid #ccc;
    }

    /* Ảnh bài đăng */
    .post img {
        max-width: 200px;
        max-height: 150px;
        margin-right: 20px;
        border-radius: 10px;
        object-fit: cover;
    }

    /* Các thông tin còn lại */
    .post .title {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 10px;
        text-align: unset;
    }

    .post .sub-info {
        font-size: 16px;
        color: #666;
        margin-bottom: 5px;
    }

    /* Căn chỉnh thông tin và nút tùy chọn */
    .post div {
        flex: 1;
    }
   </style>
</head>
<body>

<?php include 'header.php'; ?>

<section class="show-products">

    <h1 class="title">Bài viết của tôi</h1>

    <div class="post-container">

        <?php
        // Lấy danh sách bài viết của user đang đăng nhập
        $select_posts = mysqli_query($conn, "SELECT posts.*, users.name AS user_name, groups.group_name 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        JOIN groups ON posts.group_id = groups.id 
        WHERE posts.user_id = '$user_id'") or die('query failed');
        
        if (mysqli_num_rows($select_posts) > 0) {
            while ($fetch_posts = mysqli_fetch_assoc($select_posts)) {
        ?>
                <div class="post" style="display: flex; gap: 20px; margin-bottom: 20px;">
                    <img src="uploaded_img/<?php echo $fetch_posts['image']; ?>" alt="" style="width: 150px; height: 150px;">
                    <div style="flex: 2">
                        <div class="title"><?php echo $fetch_posts['title']; ?></div>
                        <div class="sub-info">Người đăng: <?php echo $fetch_posts['user_name']; ?></div>
                        <div class="sub-info">Nhóm: <?php echo $fetch_posts['group_name']; ?></div>
                        <div class="sub-info">Ngày tạo: <?php echo $fetch_posts['created_at']; ?></div>
                        <div class="sub-info">Trạng thái:
                            <span style="color: <?php if($fetch_posts['status'] == 'Chờ duyệt') { echo 'red'; } else { echo 'green'; } ?>">
                                <?php echo $fetch_posts['status']; ?>
                            </span>
                        </div>
                    </div>
                    <div class="action">
                       <a href="user_posts.php?delete=<?php echo $fetch_posts['id']; ?>" class="delete-btn" onclick="return confirm('Xóa bài đăng này?');">Xóa</a>
                    </div>
                </div>
        <?php
            }
        } else {
            echo '<p class="empty">Bạn chưa có bài đăng nào!</p>';
        }
        ?>

    </div>

</section>

<script src="js/script.js"></script>
</body>
</html>
