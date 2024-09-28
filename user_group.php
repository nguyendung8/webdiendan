<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id']; // lấy ID người dùng từ session

if (!isset($user_id)) { // nếu không có session user thì quay lại trang đăng nhập
    header('location:login.php');
}

// Nếu người dùng nhấn vào nút "Tham gia"
if (isset($_GET['join'])) {
    $group_id = $_GET['join']; // lấy ID nhóm từ URL
    // Kiểm tra xem người dùng đã tham gia nhóm chưa
    $check_membership = mysqli_query($conn, "SELECT * FROM `user_groups` WHERE user_id = '$user_id' AND group_id = '$group_id'") or die('query failed');
    
    if (mysqli_num_rows($check_membership) > 0) {
        $message[] = 'Bạn đã tham gia nhóm này rồi!';
    } else {
        // Thêm người dùng vào nhóm
        mysqli_query($conn, "INSERT INTO `user_groups`(user_id, group_id) VALUES('$user_id', '$group_id')") or die('query failed');
        $message[] = 'Tham gia nhóm thành công!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Danh sách nhóm</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="./css/new_style.css">
   <style>
      .post {
         border: 1px solid #ccc;
      }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<section class="group-list">

    <h1 class="title">Danh sách các nhóm</h1>

    <div class="post-container">

        <?php
        // Lấy danh sách nhóm
        $select_groups = mysqli_query($conn, "SELECT * FROM `groups`") or die('query failed');
        
        if (mysqli_num_rows($select_groups) > 0) {
            while ($fetch_groups = mysqli_fetch_assoc($select_groups)) {
                // Kiểm tra xem người dùng đã tham gia nhóm này chưa
                $is_member = mysqli_query($conn, "SELECT * FROM `user_groups` WHERE user_id = '$user_id' AND group_id = '" . $fetch_groups['id'] . "'") or die('query failed');
        ?>
                <div class="post" style="width: 50%;">
                     <div>
                        <div class="sub-info" class="group-name">Tên nhóm: <?php echo $fetch_groups['group_name']; ?></div>
                        <div class="sub-info" class="group-description">Mô tả: <?php echo $fetch_groups['description']; ?></div>
                     </div>
                    
                    <?php if (mysqli_num_rows($is_member) > 0) { ?>
                        <button style="padding: 10px 13px; text-decoration: none; font-size: 18px; margin-bottom: 7px; border-radius: 4px;" class="btn-success" disabled>Đã tham gia</button>
                    <?php } else { ?>
                        <a style="padding: 10px 13px; text-decoration: none; font-size: 18px; margin-bottom: 7px; border-radius: 4px;" class="btn-primary" href="home.php?join=<?php echo $fetch_groups['id']; ?>" >Tham gia</a>
                    <?php } ?>
                </div>
        <?php
            }
        } else {
            echo '<p class="empty">Không có nhóm nào!</p>';
        }
        ?>

    </div>

</section>

<script src="js/script.js"></script>

</body>
</html>
