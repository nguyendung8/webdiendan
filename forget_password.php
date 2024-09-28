<?php
   include 'config.php';

   session_start();

   if(isset($_POST['submit'])) {
        $email =  mysqli_real_escape_string($conn, $_POST['email']);
        $new_password =  mysqli_real_escape_string($conn, md5($_POST['new_password']));

        $checkEmail = "SELECT * from users where email = '".$email."'";
        $result = $conn->query($checkEmail);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user_id = $row['id'];
            $email_origin = $row['email'];
    
            // Kiểm tra email có tồn tại trong hệ thống không
            if ($email === $email_origin) {
                // Emaill có tồn tại , cập nhật mật khẩu mới
                $updatePasswordQuery = "UPDATE users SET password = '$new_password' WHERE id = $user_id";
                
                if ($conn->query($updatePasswordQuery) === TRUE) {
                    $message[] = 'Cập nhật mật khẩu thành công';
                } else {
                    $message[] = 'Cập nhậ mật khẩu không thành công';
                }
            } else {
                $message[] = 'Email không tồn tại trên hệ thống, vui lòng nhập lại';
            }
        } else {
            $message[] = 'Không tìm thấy người dùng';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quên mật khẩu</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
   <link rel="stylesheet" href="css/style.css">
   <style>
    .change-password {
        width: fit-content;
        margin: auto;
        font-size: 20px;
        border: 1px solid #ddd;
        padding: 25px;
        margin-top: 32px;
        margin-bottom: 32px;
        border-radius: 5px;
        box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
    }
    .cp-title {
        font-size: 25px;
        text-align: center;
    }
    .submit-btn {
        display: flex;
        margin: auto;
    }
    .form-control {
        height: 45px;
        font-size: 20px;
        width: 400px;
    }
    .back {
        color: #673AB7;
        font-size: 19px;
    }
   </style>
</head>
<body>
    <?php
    //nhúng vào các trang bán hàng
    if(isset($message)){//hiển thị thông báo sau khi thao tác với biến message được gán giá trị
        foreach($message as $message){
            echo '
            <div class="message">
                <span>'.$message.'</span>
                <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
            </div>';//đóng thẻ này
        }
    }
    ?>
   <div class="change-password">
    <a href="./login.php" style="color: blue;" class="back"><i class="fa fa-arrow-left" aria-hidden="true"></i> Quay lại</a>
    <h1 class="cp-title">Quên mật khẩu</h1>
    <form method="POST">
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control" placeholder="Nhập email..." required>
    </div>
    <div class="form-group">
        <label>Mật khẩu mới</label>
        <input type="password" name="new_password" class="form-control" placeholder="Nhập mật khẩu mới" required>
    </div>
    <input type="submit" name="submit" style="padding: 10px 13px; text-decoration: none; font-size: 18px; margin-bottom: 7px; border-radius: 4px;" class="btn-primary submit-btn" value="Gửi">
    </form>
</div>

</body>
</html>