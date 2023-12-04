<?php

   include 'config.php';

   session_start();

   $user_id = $_SESSION['user_id']; //tạo session người dùng thường

   if(!isset($user_id)){// session không tồn tại => quay lại trang đăng nhập
      header('location:login.php');
   }
   $product_id = $_GET['product_id'];

   $sql = "SELECT * FROM products WHERE id = $product_id";
   $result = $conn->query($sql);
   $productItem = $result->fetch_assoc();

   if(isset($_POST['add_to_cart'])){//thêm sách vào giỏi hàng từ form submit name='add_to_cart'

    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = 1;

    if($product_quantity==0){
       $message[] = 'Sản phẩm đã hết hàng!';
    }
    else{
       $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

       if(mysqli_num_rows($check_cart_numbers) > 0){//kiểm tra sách có trong giỏ hàng chưa và tăng số lượng
          $result=mysqli_fetch_assoc($check_cart_numbers);
          $num=$result['quantity']+$product_quantity;
          $select_quantity = mysqli_query($conn, "SELECT * FROM `products` WHERE name='$product_name'");
          $fetch_quantity = mysqli_fetch_assoc($select_quantity);
          if($num>$fetch_quantity['quantity']){
             $num=$fetch_quantity['quantity'];
          }
          mysqli_query($conn, "UPDATE `cart` SET quantity='$num' WHERE name = '$product_name' AND user_id = '$user_id'");
          $message[] = 'Sản phẩm đã có trong giỏ hàng và được thêm số lượng!';
       }else{
          mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image) VALUES('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('query failed');
          $message[] = 'Sản phẩm đã được thêm vào giỏ hàng!';
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
   <title>Xem thông tin sản phẩm</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .view-product {
         padding: 15px;
      }
      .modal{
         width: 500px;
         margin: auto;
         border: 2px solid #eee;
         padding-bottom: 27px;
         box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
         border-radius: 5px;
      }
      .modal-container{
         background-color:#fff;
         text-align: center;
      }
      .peoductdetail-title {
         font-size: 21px;
         padding-top: 10px;
         color: #9e1ed4;
      }
      .peoductdetail-img {
         margin-top: 18px;
         width: 230px;
      }
      .peoductdetail-author {
         margin-top: 19px;
         font-size: 20px;
      }
      .peoductdetail-desc {
         margin-top: 20px;
         font-size: 16px;
      }
      .add_btn {
        display: flex;
        margin: auto;
        margin-top: 17px;
      }
   </style>
</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>Xem thông tin sản phẩm</h3>
   <p> <a href="home.php">Trang chủ</a> / Xem thông tin sản phẩm </p>
</div>

<section class="view-product">
   <?php if ($productItem) : ?>
         <!-- Modal View Detail Book -->
        <form method="post" class="modal">
            <div class="modal-container">
                <h3 class="peoductdetail-title">Xem sản phẩm <?php echo($productItem['name']) ?></h3>
                <div>
                    <img class="peoductdetail-img" src="uploaded_img/<?php echo $productItem['image']; ?>" alt="">
                </div>
                <p class="peoductdetail-author">
                    Số lượng còn: 
                    <?php echo ($productItem['quantity']) ?>
                </p>
                <p class="peoductdetail-desc">
                    Mô tả: 
                    <?php echo($productItem['describes'])  ?>
                </p>
            </div>
            <input type="hidden" name="product_name" value="<?php echo $productItem['name']; ?>">
            <input type="hidden" name="product_price" value="<?php echo $productItem['newprice']; ?>">
            <input type="hidden" name="product_image" value="<?php echo $productItem['image']; ?>">
            <input type="submit" value="Thêm vào giỏ hàng" name="add_to_cart" class="btn add_btn">
        </form>
   <?php else : ?>
      <p style="font-size: 20px; text-align: center;">Không xem được chi tiết sản phẩm này</p>
   <?php endif; ?>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>