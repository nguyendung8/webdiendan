<?php

   include 'config.php';

   session_start();

   $user_id = $_SESSION['user_id']; //tạo session người dùng thường

   if(!isset($user_id)){// session không tồn tại => quay lại trang đăng nhập
      header('location:login.php');
   }

   if(isset($_POST['add_to_cart'])){//thêm sách vào giỏi hàng từ form submit name='add_to_cart'

      $product_name = $_POST['product_name'];
      $product_price = $_POST['product_price'];
      $product_image = $_POST['product_image'];
      $product_quantity = $_POST['product_quantity'];

      if($product_quantity==0){
         $message[] = 'Sản phẩm đã hết hàng!';
      }
      else{
         $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

         if(mysqli_num_rows($check_cart_numbers) > 0){//kiểm tra sách có trogn giỏ hàng chưa và tăng số lượng
            $result=mysqli_fetch_assoc($check_cart_numbers);
            $num=$result['quantity']+$product_quantity;
            $select_quantity = mysqli_query($conn, "SELECT * FROM `products` WHERE name='$product_name'");
            $fetch_quantity = mysqli_fetch_assoc($select_quantity);
            if($num>$fetch_quantity['quantity']){
               $num=$fetch_quantity['quantity'];
            }
            mysqli_query($conn, "UPDATE `cart` SET quantity='$num' WHERE name = '$product_name' AND user_id = '$user_id'");
            $message[] = 'Đã có trong giỏ hàng!';
         }else{
            mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image) VALUES('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('query failed');
            $message[] = 'Đã được thêm trong giỏ hàng!';
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
   <title>Trang tìm kiếm</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .head {
         background: url(./images/head_img.png) no-repeat;
         background-size: cover;
         background-position: center;
      }
   </style>
</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading head">
</div>

<section class="search-form">
   <form action="" method="post">
      <input type="text" name="search" placeholder="Tìm sản phẩm..." class="box" value="<?php if(isset($_POST['submit'])) echo $_POST['search'] ?>">
      <input type="submit" name="submit" value="Tìm kiếm" class="btn">
   </form>
</section>

<section class="products" style="padding-top: 0;">

   <div class="box-container">
      <?php
         if(isset($_POST['submit'])){
            $search_item = $_POST['search'];
            $select_products = mysqli_query($conn, "SELECT * FROM `products` WHERE name LIKE '%{$search_item}%'") or die('query failed');
            if(mysqli_num_rows($select_products) > 0){
               while($fetch_products = mysqli_fetch_assoc($select_products)){
      ?>
                  <form style="height: -webkit-fill-available;" action="" method="post" class="box">
                     <img  width="207px" height="224px" src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
                     <div class="name"><?php echo $fetch_products['name']; ?></div>
                     <div class="price"><span style="text-decoration-line:line-through; text-decoration-thickness: 2px; text-decoration-color: grey"><?php echo number_format($fetch_products['price'],0,',','.' ); ?></span> <u style="text-decoration: underline !important;">đ</u> /<?php echo number_format($fetch_products['newprice'],0,',','.' ); ?> <u style="text-decoration: underline !important;">đ</u> (-<?php echo $fetch_products['discount']; ?>%)</div>
                     <span style="font-size: 17px; display: flex;">Số lượng mua:</span>
                     <input type="number" min="<?=($fetch_products['quantity']>0) ? 1:0 ?>" max="<?php echo $fetch_products['quantity']; ?>" name="product_quantity" value="<?=($fetch_products['quantity']>0) ? 1:0 ?>" class="qty">
                     <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
                     <input type="hidden" name="product_price" value="<?php echo $fetch_products['newprice']; ?>">
                     <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
                     <input type="submit" value="Thêm vào giỏ hàng" name="add_to_cart" class="btn">
                  </form>
      <?php
               }
            }else{
               echo '<p class="empty">Không tìm thấy!</p>';
            }
         }else{
            echo '<p class="empty"">Hãy tìm kiếm gì đó!</p>';
         }
      ?>
   </div>
  

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>