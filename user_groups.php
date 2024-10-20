<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id']; // lấy ID người dùng từ session

if (!isset($user_id)) { // nếu không có session user thì quay lại trang đăng nhập
    header('location:login.php');
}

// Nếu người dùng nhấn vào nút "Rời nhóm"
if (isset($_GET['leave'])) {
    $group_id = $_GET['leave']; // lấy ID nhóm từ URL
    // Xóa người dùng khỏi nhóm
    mysqli_query($conn, "DELETE FROM `user_groups` WHERE user_id = '$user_id' AND group_id = '$group_id'") or die('query failed');
    $message[] = 'Đã rời nhóm thành công!';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách nhóm của bạn</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="./css/new_style.css">
    <style>
        .post {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<section class="group-list">

    <h1 class="title">Danh sách nhóm của bạn</h1>

    <div class="post-container">

        <?php
        $select_user_groups = mysqli_query($conn,
        "SELECT g.* FROM `groups` g JOIN `user_groups` ug ON g.id = ug.group_id  WHERE ug.user_id = '$user_id'") or die('query failed');
        if (mysqli_num_rows($select_user_groups) > 0) {
            while ($fetch_groups = mysqli_fetch_assoc($select_user_groups)) {
        ?>
                <div class="post" style="width: 50%;">
                    <div>
                        <div class="sub-info" class="group-name">Tên nhóm: <?php echo $fetch_groups['group_name']; ?></div>
                        <div class="sub-info" class="group-description">Mô tả: <?php echo $fetch_groups['description']; ?></div>
                    </div>

                    <a style="padding: 10px 13px; text-decoration: none; font-size: 18px; margin-bottom: 7px; border-radius: 4px;" 
                       class="btn-success" href="list_posts.php?group_id=<?php echo $fetch_groups['id']; ?>">
                       Xem bài viết
                    </a>
                    
                    <a style="padding: 10px 13px; text-decoration: none; font-size: 18px; margin-bottom: 7px; border-radius: 4px;" 
                       class="btn-primary" href="post_in_group.php?group_id=<?php echo $fetch_groups['id']; ?>">
                       Đăng bài
                    </a>
                    
                    <a style="padding: 10px 13px; text-decoration: none; font-size: 18px; margin-bottom: 7px; border-radius: 4px;" 
                       class="btn-danger" href="user_groups.php?leave=<?php echo $fetch_groups['id']; ?>">
                       Rời nhóm
                    </a>
                </div>
        <?php
            }
        } else {
            echo '<p class="empty">Bạn chưa tham gia nhóm nào!</p>';
        }
        ?>

    </div>

</section>

<script src="js/script.js"></script>

</body>
</html>
