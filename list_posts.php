<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id']; // Lấy user_id từ session

if(isset($_GET['group_id'])) {
    $group_id = $_GET['group_id'];
}

// Kiểm tra nếu user chưa đăng nhập thì chuyển hướng về trang đăng nhập
if (!isset($user_id)) {
    header('location:login.php');
    exit;
}

    // Xử lý hành động like/dislike
    if (isset($_POST['like']) || isset($_POST['dislike'])) {
        $post_id = $_POST['post_id'];
        $action = isset($_POST['like']) ? 'like' : 'dislike';

        // Kiểm tra nếu user đã like/dislike bài viết này chưa
        $check_action = mysqli_query($conn, "SELECT * FROM likes_dislikes WHERE post_id='$post_id' AND user_id='$user_id'");
        
        if (mysqli_num_rows($check_action) > 0) {
            $update_action = mysqli_query($conn, "UPDATE likes_dislikes SET action='$action' WHERE post_id='$post_id' AND user_id='$user_id'");
        } else {
            $insert_action = mysqli_query($conn, "INSERT INTO likes_dislikes (post_id, user_id, action) VALUES ('$post_id', '$user_id', '$action')");
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
        
        mysqli_query($conn, "INSERT INTO comments (post_id, user_id, content) VALUES ('$post_id', '$user_id', '$comment')");
    }

    // Xử lý cập nhật bình luận
    if (isset($_POST['update_comment'])) {
        $comment_id = $_POST['comment_id'];
        $comment = mysqli_real_escape_string($conn, $_POST['edit_comment_text']);
        
        mysqli_query($conn, "UPDATE comments SET content='$comment' WHERE id='$comment_id'");
        header('location:list_posts.php');
    }

    // Xử lý xóa bình luận
    if (isset($_GET['delete'])) {
        $comment_id = $_GET['delete'];
        
        mysqli_query($conn, "DELETE FROM comments WHERE id='$comment_id'");
        header('location:list_posts.php?group_id=' . $group_id);
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Bài viết trong nhóm</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f2f5;
        margin: 0;
        padding: 0;
    }
    p {
        font-size: 14px;
    }
    .post-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
        width: 100%;
        max-width: 600px;
        margin: 20px auto;
    }

    .post {
        background-color: #fff;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        border: 1px solid #ddd;
    }

    .post .user-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .post .user-info img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .post .user-info span {
        font-weight: bold;
        font-size: 14px;
        color: #333;
    }

    .post .post-content {
        margin-top: 10px;
        line-height: 1.5;
    }

    .post .post-content h3 {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #1c1e21;
    }

    .post .post-content img {
        max-width: 100%;
        max-height: 400px;
        height: auto;
        border-radius: 5px;
        margin-top: 10px;
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

<?php include 'header.php'; ?>

    <section class="post-container">

        <?php
        // Lấy danh sách các bài viết từ các nhóm mà user đã tham gia
        $select_posts = mysqli_query($conn, "SELECT posts.*, users.name AS user_name, 
            (SELECT COUNT(*) FROM likes_dislikes WHERE post_id=posts.id AND action='like') AS total_likes,
            (SELECT COUNT(*) FROM likes_dislikes WHERE post_id=posts.id AND action='dislike') AS total_dislikes
            FROM posts
            JOIN user_groups ON posts.group_id = user_groups.group_id
            JOIN users ON posts.user_id = users.id
            WHERE user_groups.user_id = '$user_id'
            AND posts.group_id = '$group_id'
            AND posts.status = 'Đã duyệt'
            ORDER BY posts.created_at DESC") or die('query failed');

        if (mysqli_num_rows($select_posts) > 0) {
            while ($post = mysqli_fetch_assoc($select_posts)) {
        ?>
            <div class="post">
                <div class="user-info">
                    <span>Người đăng: <?php echo $post['user_name']; ?></span>
                </div>

                <div class="post-content">
                    <h3>Tiêu đề: <?php echo $post['title']; ?></h3>
                    <?php if (!empty($post['image'])) { ?>
                        <img src="uploaded_img/<?php echo $post['image']; ?>" alt="Post Image">
                    <?php } ?>
                </div>

                <div class="actions">
                    <form action="" method="POST">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <button type="submit" name="like">Like</button>
                        <button type="submit" name="dislike">Dislike</button>
                        <span style="color: #3dcd3d;"><?php echo $post['total_likes']; ?> Likes</span>
                        <span style="color: #e03333;"><?php echo $post['total_dislikes']; ?> Dislikes</span>
                    </form>
                </div>

                <div class="comment-section">
                    <h4>Bình luận:</h4>

                    <?php
                    // Lấy danh sách các bình luận của bài viết
                    $select_comments = mysqli_query($conn, "SELECT comments.*, users.name AS user_name 
                        FROM comments 
                        JOIN users ON comments.user_id = users.id 
                        WHERE comments.post_id = '".$post['id']."' 
                        ORDER BY comments.created_at ASC");

                    if (mysqli_num_rows($select_comments) > 0) {
                        while ($comment = mysqli_fetch_assoc($select_comments)) {
                    ?>
                        <div class="comment">
                            <strong><?php echo $comment['user_name']; ?></strong>
                            <p style="font-size: 14px;"><?php echo $comment['content']; ?></p>
                            
                            <!-- Edit and Delete buttons, assuming logged-in user can only edit/delete their own comments -->
                            <?php if ($comment['user_id'] == $_SESSION['user_id']) { ?>
                                <div style="display: flex; gap: 5px; font-size: 13px;">
                                    <a href="?edit=<?php echo $comment['id']; ?>" class="btn-edit">Sửa</a>
                                    <a style="color: red;" href="?group_id=<?php echo $group_id ?>&delete=<?php echo $comment['id']; ?>" class="btn-delete" onclick="return confirm('Bạn có chắc muốn xóa bình luận này?');">Xóa</a>
                                </div>
                            <?php } ?>
                        </div>

                        <!-- Edit form (only visible if editing this comment) -->
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

                    <!-- Form to add a new comment -->
                    <form action="" method="POST">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <input type="text" name="comment_text" placeholder="Viết bình luận..." required>
                        <button type="submit" name="comment">Bình luận</button>
                    </form>
                </div>
            </div>
        <?php
            }
        } else {
            echo '<p>Bạn chưa tham gia nhóm nào hoặc không có bài viết nào.</p>';
        }
        ?>

    </section>

<script src="js/script.js"></script>
</body>
</html>
