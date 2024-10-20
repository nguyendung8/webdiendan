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
    $created_at = date('Y-m-d H:i:s');

    $select_post_title = mysqli_query($conn, "SELECT title FROM `posts` WHERE title = '$title'") or die('query failed'); // kiểm tra bài đăng đã tồn tại chưa

    if (mysqli_num_rows($select_post_title) > 0) {
        $message[] = 'Bài đăng đã tồn tại.';
    } else { // nếu chưa tồn tại thì thêm mới
        $add_post_query = mysqli_query($conn, "INSERT INTO `posts`(user_id, group_id, title, image, created_at, status) VALUES('$user_id', '$group_id', '$title', '$image', '$created_at', 'Đã duyệt')") or die('query failed');

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
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);



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

        header('Location: admin_posts.php');
        exit(); // Đảm bảo dừng script sau khi redirect

    } catch (mysqli_sql_exception $e) {
        $message[] = 'Không thể xóa bài đăng này!';
    }
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

  // Xử lý hành động like/dislike
  if (isset($_POST['like']) || isset($_POST['dislike'])) {
    $post_id = $_POST['post_id'];
    $action = isset($_POST['like']) ? 'like' : 'dislike';

    // Kiểm tra nếu user đã like/dislike bài viết này chưa
    $check_action = mysqli_query($conn, "SELECT * FROM likes_dislikes WHERE post_id='$post_id' AND user_id='$admin_id'");
    
    if (mysqli_num_rows($check_action) > 0) {
        $update_action = mysqli_query($conn, "UPDATE likes_dislikes SET action='$action' WHERE post_id='$post_id' AND user_id='$admin_id'");
    } else {
        $insert_action = mysqli_query($conn, "INSERT INTO likes_dislikes (post_id, user_id, action) VALUES ('$post_id', '$admin_id', '$action')");
    }

    // Cập nhật số lượng like/dislike
    if ($action == 'like') {
        mysqli_query($conn, "UPDATE posts SET likes = likes + 1 WHERE id='$post_id'");
    } else {
        mysqli_query($conn, "UPDATE posts SET dislikes = dislikes + 1 WHERE id='$post_id'");
    }
}

// Xử lý thêm bình luận
if (isset($_POST['comment'])) {
    $post_id = $_POST['post_id'];
    $comment = mysqli_real_escape_string($conn, $_POST['comment_text']);
    
    mysqli_query($conn, "INSERT INTO comments (post_id, user_id, content) VALUES ('$post_id', '$admin_id', '$comment')");
}

// Xử lý cập nhật bình luận
if (isset($_POST['update_comment'])) {
    $comment_id = $_POST['comment_id'];
    $comment = mysqli_real_escape_string($conn, $_POST['edit_comment_text']);
    
    mysqli_query($conn, "UPDATE comments SET content='$comment' WHERE id='$comment_id'");
    header('location:admin_posts.php');
}


if (isset($_GET['cmt_id'])) { 
    $delete_id = $_GET['cmt_id'];

    try {
        // Vô hiệu hóa ràng buộc khóa ngoại
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0") or die('Query failed: Disable foreign key checks');

        mysqli_query($conn, "DELETE FROM comments WHERE id='$delete_id'");

        // Kích hoạt lại ràng buộc khóa ngoại
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1") or die('Query failed: Enable foreign key checks');

        header('Location: admin_posts.php');
        exit(); // Đảm bảo dừng script sau khi redirect

    } catch (mysqli_sql_exception $e) {
        $message[] = 'Không thể xóa bình luận này!';
    }
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
      .post .actions {
        margin-top: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .post .actions form {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .post .actions button {
        background-color: transparent;
        border: none;
        font-size: 14px;
        color: #606770;
        cursor: pointer;
        padding: 5px 10px;
        border-radius: 5px;
        transition: background-color 0.3s;
    }

    .post .actions button:hover {
        background-color: #e4e6eb;
    }

    .post .actions span {
        font-size: 13px;
        color: #606770;
    }

    .post .comment-section {
        margin-top: 15px;
    }

    .post .comment-section h4 {
        font-size: 14px;
        color: #1c1e21;
        margin-bottom: 10px;
        font-weight: bold;
    }

    .post .comment-section .comment {
        display: flex;
        flex-direction: column;
        background-color: #f0f2f5;
        padding: 10px;
        border-radius: 5px;
        margin-top: 10px;
    }

    .post .comment-section .comment strong {
        color: #1c1e21;
        font-size: 13px;
        font-weight: bold;
    }

    .post .comment-section .comment p {
        font-size: 13px;
        color: #4b4f56;
        margin-top: 5px;
    }

    .post .comment-section form {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }

    .post .comment-section input[type="text"] {
        flex: 1;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 20px;
        background-color: #f0f2f5;
        font-size: 14px;
        color: #1c1e21;
    }

    .post .comment-section button {
        background-color: #1877f2;
        color: white;
        padding: 5px 15px;
        border: none;
        border-radius: 20px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s;
    }

    .post .comment-section button:hover {
        background-color: #166fe5;
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
        $select_posts = mysqli_query($conn, "
        SELECT posts.*, users.name AS user_name, groups.group_name, 
        (SELECT COUNT(*) FROM likes_dislikes WHERE post_id = posts.id AND action = 'like') AS total_likes, 
        (SELECT COUNT(*) FROM likes_dislikes WHERE post_id = posts.id AND action = 'dislike') AS total_dislikes 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        JOIN groups ON posts.group_id = groups.id
        WHERE posts.status = 'Đã duyệt'
    ") or die('query failed');
    if (mysqli_num_rows($select_posts) > 0) {
        while ($post = mysqli_fetch_assoc($select_posts)) {
    ?>
            <div style="flex-direction:column; align-items: self-start" class="post">
                <div style="display: flex;">
                    <img src="uploaded_img/<?php echo $post['image']; ?>" alt="Post Image">
                    <div style="flex: 2">
                        <div class="title"><?php echo $post['title']; ?></div>
                        <div class="sub-info">Người đăng: <?php echo $post['user_name']; ?></div>
                        <div class="sub-info">Nhóm: <?php echo $post['group_name']; ?></div>
                        <div class="sub-info">Ngày tạo: <?php echo $post['created_at']; ?></div>
                    </div>
                    <div class="action">
                        <a href="admin_posts.php?update=<?php echo $post['id']; ?>" class="option-btn">Cập nhật</a>
                        <a href="admin_posts.php?delete=<?php echo $post['id']; ?>" class="delete-btn" onclick="return confirm('Xóa bài đăng này?');">Xóa</a>
                    </div>
                </div>

                <!-- Like, Dislike Buttons and Counts -->
                 <div style="width: 100%;">
                    <div class="actions">
                        <form action="" method="POST">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <button type="submit" name="like">Like</button>
                            <button type="submit" name="dislike">Dislike</button>
                            <span style="color: #3dcd3d;"><?php echo $post['total_likes']; ?> Likes</span>
                            <span style="color: #e03333;"><?php echo $post['total_dislikes']; ?> Dislikes</span>
                        </form>
                    </div>
    
                    <!-- Comment Section -->
                    <div class="comment-section">
                        <h4>Bình luận:</h4>
                        <?php
                        $select_comments = mysqli_query($conn, "
                            SELECT comments.*, users.name AS user_name 
                            FROM comments 
                            JOIN users ON comments.user_id = users.id 
                            WHERE comments.post_id = '".$post['id']."' 
                            ORDER BY comments.created_at ASC
                        ");
    
                        if (mysqli_num_rows($select_comments) > 0) {
                            while ($comment = mysqli_fetch_assoc($select_comments)) {
                        ?>
                            <div class="comment">
                                <strong><?php echo $comment['user_name']; ?></strong>
                                <p style="font-size: 14px;"><?php echo $comment['content']; ?></p>
    
                                <div style="display: flex; gap: 5px; font-size: 13px;">
                                    <?php if ($comment['user_id'] == $_SESSION['admin_id']) { ?>
                                        <a href="?edit=<?php echo $comment['id']; ?>" class="btn-edit">Sửa</a>
                                    <?php } ?>
                                        <a style="color: red;" href="?cmt_id=<?php echo $comment['id']; ?>" class="btn-delete" 
                                        onclick="return confirm('Bạn có chắc muốn xóa bình luận này?');">Xóa
                                        </a>
                                    </div>
                            </div>
    
                            <?php if (isset($_GET['edit']) && $_GET['edit'] == $comment['id']) { ?>
                                <form action="" method="POST">
                                    <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                    <input type="text" name="edit_comment_text" value="<?php echo $comment['content']; ?>" required>
                                    <button type="submit" name="update_comment">Cập nhật</button>
                                </form>
                            <?php } ?>
                        <?php
                            }
                        } else {
                            echo '<p>Chưa có bình luận nào.</p>';
                        }
                        ?>
    
                        <!-- Add New Comment -->
                        <form action="" method="POST">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <input type="text" name="comment_text" placeholder="Viết bình luận..." required>
                            <button type="submit" name="comment">Bình luận</button>
                        </form>
                    </div>
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
