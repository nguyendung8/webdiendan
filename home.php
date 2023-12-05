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
   <title>Trang chủ</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .box p {
         font-size: 17px;
         padding-bottom: 5px;
      }
      .action {
         display: flex;
         align-items: center;
      }
      .view-product {
         margin-top: 5px;
         padding: 5px 20px;
         background-color: burlywood;
         font-size: 16px;
         color: #fff;
         border-radius: 6px;
      }
      .view-product:hover {
         opacity: 0.9;
      }
      .slideshow-container {
         position: relative;
         max-width: 800px;
         margin: 0 auto;
         overflow: hidden; /* Để ẩn phần ngoài khung hình ảnh */
      }
      .slide {
         display: none;
         animation: fade 2s ease-in-out infinite; /* Sử dụng animation để thêm hiệu ứng lướt sang */
      }
      @keyframes fade {
         0%, 100% {
            opacity: 0;
         }
         25%, 75% {
            opacity: 1;
         }
      }
      .slide img {
         width: 100%;
         height: 485px;
         border-radius: 9px;
      }
      .borrow_book:hover { 
         opacity: 0.9;
      }
      .borrow_book {
         padding: 5px 25px;
         background-image: linear-gradient(to right, #ff9800, #F7695D);
         border-radius: 4px;
         cursor: pointer;
         font-size: 20px;
         color: #fff;
         font-weight: 700;
      }
      .home-banner {
         min-height: 70vh;
         background:linear-gradient(rgba(0,0,0,.1), rgba(0,0,0,.1)), url(./images/about-home.png) no-repeat;
         background-size: cover;
         background-position: center;
         display: flex;
         align-items: center;
         justify-content: center;
      }
      .discount {
         font-weight: 800;
         background: #282828;
         border-radius: 50%;
         color: #d3b673;
         width: 40px;
         height: 40px;
         display: flex;
         justify-content: center;
         align-items: center;
         position: absolute;
         top: 10px;
         right: 19px;
      }
   </style>
</head>
<body>
   
<?php include 'header.php'; ?>

<section class="home home-banner">

<div class="content">
      <div class="slideshow-container">
         <div class="slide fade">
            <img src="./images/slide1.png" alt="slide 1">
         </div>
         <div class="slide fade">
            <img src="./images/slide2.jpg" alt="slide 2">
         </div>
         <div class="slide fade">
            <img src="./images/slide3.jpg" alt="slide 3">
         </div>
         <div class="slide fade">
            <img src="./images/slide4.jpg" alt="slide 3">
         </div>
         <div class="slide fade">
            <img src="./images/slide5.jpg" alt="slide 3">
         </div>
         <div class="slide fade">
            <img src="./images/slide6.jpg" alt="slide 3">
         </div>
         <div class="slide fade">
            <img src="./images/slide7.png" alt="slide 3">
         </div>
      </div>
   </div>

</section>

<section class="products">

   <h1 class="title">Sản phẩm mới nhất</h1>

   <div class="box-container">

      <?php  
         $select_products = mysqli_query($conn, "SELECT * FROM `products` ORDER BY id DESC  LIMIT 8") or die('query failed');
         if(mysqli_num_rows($select_products) > 0){
            while($fetch_products = mysqli_fetch_assoc($select_products)){
      ?>
               <form style="height: -webkit-fill-available;" action="" method="post" class="box">
                  <img width="207px" height="224px" src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
                  <?php
                     $cate_id =  $fetch_products['cate_id'];
                     $result= mysqli_query($conn, "SELECT * FROM `categorys` WHERE id = $cate_id") or die('Query failed');
                     $cate_name = mysqli_fetch_assoc($result)
                   ?>
                  <div class="name"><?php echo $fetch_products['name']; ?></div>
                  <div class="price"><span style="text-decoration-line:line-through; text-decoration-thickness: 2px; text-decoration-color: grey"><?php echo number_format($fetch_products['price'],0,',','.' ); ?></span> <u style="text-decoration: underline !important;">đ</u> /<?php echo number_format($fetch_products['newprice'],0,',','.' ); ?> <u style="text-decoration: underline !important;">đ</u></div>
                  <div class="discount"> <?php echo $fetch_products['discount']; ?>%</div>
                  <span style="font-size: 17px; display: flex;">Số lượng mua:</span>
                  <input type="number" min="<?=($fetch_products['quantity']>0) ? 1:0 ?>" max="<?php echo $fetch_products['quantity']; ?>" name="product_quantity" value="<?=($fetch_products['quantity']>0) ? 1:0 ?>" class="qty">
                  <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
                  <input type="hidden" name="product_price" value="<?php echo $fetch_products['newprice']; ?>">
                  <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
                  <a href="product_detail.php?product_id=<?php echo $fetch_products['id'] ?>" class="view-product" >Xem thông tin</a>
                  <input type="submit" value="Thêm vào giỏ hàng" name="add_to_cart" class="btn">
               </form>
      <?php
            }
         }else{
            echo '<p class="empty">Chưa có sản phẩm được bán!</p>';
         }
      ?>
   </div>

   <div class="load-more" style="margin-top: 2rem; text-align:center">
      <a href="shop.php" class="option-btn">Xem thêm sản phẩm</a>
   </div>

</section>

<section class="about">

   <div class="flex">

      <div class="image">
         <img height="307px;" style="border-radius: 4px;" src="images/home-gioithieu.jpg" alt="">
      </div>

      <div class="content">
         <h3>ToCo Toco</h3>
         <p> 
            Bên cạnh niềm tự hào về những ly trà sữa ngon – sạch – tươi, chúng tôi luôn tự tin
            mang đến khách hàng những trải nghiệm tốt nhất về dịch vụ và không gian.
         </p>
      </div>

   </div>

</section>

<section class="home-contact">

   <div class="content">
      <h3>Bạn có thắc mắc?</h3>
      <p>Hãy để lại những điều bạn còn thắc mắc, băn khoăn hay muốn chia sẻ thêm về những quyển truyện cho chúng mình tại đây để chúng mình có thể giải đáp giúp bạn</p>
      <a href="contact.php" class="white-btn">Liên hệ</a>
   </div>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>
<script src="js/slide_show.js"></script>

</body>
</html>