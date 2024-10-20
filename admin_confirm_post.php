<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id']; // Tạo session admin

if(!isset($admin_id)){ // Nếu session không tồn tại, quay lại trang đăng nhập
   header('location:login.php');
}

// Xử lý duyệt bài
if (isset($_GET['approve'])) {
    $post_id = $_GET['approve'];
    $update_query = mysqli_query($conn, "UPDATE posts SET status = 'Đã duyệt' WHERE id = '$post_id'") or die('query failed');
    if ($update_query) {
        header('location:admin_confirm_post.php'); // Chuyển hướng về trang duyệt bài sau khi cập nhật
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Duyệt bài viết</title>

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

<?php include 'admin_header.php'; ?>

<section class="show-products">

    <h1 style="margin-top: 20px;" class="title">Danh sách bài viết chờ duyệt</h1>

    <div class="post-container">

        <?php
        // Lấy danh sách bài viết đang chờ duyệt
        $select_posts = mysqli_query($conn, "SELECT posts.*, users.name AS user_name, groups.group_name 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        JOIN groups ON posts.group_id = groups.id 
        WHERE posts.status = 'Chờ duyệt'") or die('query failed');
        
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
                        <div class="sub-info">Trạng thái:
                            <span style="color: <?php if($fetch_posts['status'] == 'Chờ duyệt') { echo 'red'; } else { echo 'green'; } ?>">
                                <?php echo $fetch_posts['status']; ?>
                            </span>
                        </div>
                    </div>
                    <div class="action">
                        <a href="admin_confirm_post.php?approve=<?php echo $fetch_posts['id']; ?>" class="option-btn">Duyệt bài</a>
                    </div>
                </div>
        <?php
            }
        } else {
            echo '<p class="empty">Không có bài viết nào chờ duyệt!</p>';
        }
        ?>

    </div>

</section>

<script src="js/script.js"></script>
</body>
</html>
