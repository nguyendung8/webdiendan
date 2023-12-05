<?php

   include 'config.php';

   session_start();

   $admin_id = $_SESSION['admin_id']; //tạo session admin

   if(!isset($admin_id)){// session không tồn tại => quay lại trang đăng nhập
      header('location:login.php');
   }

   $sql_total_price = "SELECT SUM(total_price) AS TotalPrice FROM orders WHERE payment_status = 'Hoàn thành'";
   $total_price = $conn->query($sql_total_price);
   $sql_out_of_stock = "SELECT * FROM products WHERE quantity = 0";
   $result_stock = $conn->query($sql_out_of_stock);
   $out_of_stock = [];
    if ($result_stock->num_rows > 0) {
        while ($row = $result_stock->fetch_assoc()) {
            $out_of_stock[] = $row;
        }
    }
	$sql_best_seller = "SELECT * FROM products WHERE initial_quantity - quantity > 10";
   	$result_seller = $conn->query($sql_best_seller);
	$best_seller = [];
	if ($result_seller->num_rows > 0) {
		while ($row = $result_seller->fetch_assoc()) {
			$best_seller[] = $row;
		}
	}

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Thống kê</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="./css/admin_style.css">
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        .total_price {
            display:flex;
            align-items: center;
            gap: 10px;
        }
        .out_of_stock {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
   
<?php include 'admin_header.php'; ?>

    <h1 style="margin-top: 25px;" class="title">Thống kê</h1>

   <div class="total_money">
    <h1 class="statis_title">Tổng doanh thu</h1>
    <div class="total_price">
        <h4>Tổng doanh thu từ các sản phẩm đã bán được: </h4>
        <div style="font-size: 17px;">
            <?php echo number_format($total_price->fetch_object()->TotalPrice, 0,',','.') . ' VND'; ?>
        </div>
    </div>
   </div>
   <div class="best_seller">
   <h1 class="statis_title">Thống kê sản phẩm bán chạy</h1>
   <?php if (count($best_seller) > 0): ?>
      <div class="table-responsive card mt-2">
          <table style="width: 69% !important; margin: auto;" class="table table-bordered statistical_table">
              <tr>
                  <th>ID</th>
                  <th>Tên sản phẩm</th>
                  <th>Mô tả</th>
                  <th>Số lượng sản phẩm đã bán</th>
              </tr>
				<?php foreach ($best_seller as $item): ?>
					<tr>
						<td>
							<label style="width: auto"><?php echo $item['id']?></label>
						</td>
						<td>
							<label style="width: auto"><?php echo $item['name']; ?></label>
						</td>
						<td>
							<label style="width: auto"><?php echo $item['describes']; ?></label>
						</td>
						<td>
							<label style="width: auto"><?php echo $item['initial_quantity'] - $item['quantity']; ?></label>
						</td>
					</tr>
				<?php endforeach; ?>
          	</table>
      	</div>
    <?php else: ?>
        <p class="alert alert-danger">Danh sách trống</p>
    <?php endif; ?>
   </div>
   <div class="out_of_stock">
   <h1 class="statis_title">Thống kê sản phẩm đã hết trong kho</h1>
    <?php if (count($out_of_stock) > 0): ?>
      <div class="table-responsive card mt-2">
          <table style="width: 69% !important; margin: auto;" class="table table-bordered statistical_table">
              <tr>
                  <th>ID</th>
                  <th>Tên sản phẩm</th>
                  <th>Mô tả</th>
                  <th>Số lượng còn</th>
              </tr>
				<?php foreach ($out_of_stock as $item): ?>
					<tr>
						<td>
							<label style="width: auto"><?php echo $item['id']?></label>
						</td>
						<td>
							<label style="width: auto"><?php echo $item['name']; ?></label>
						</td>
						<td>
							<label style="width: auto"><?php echo $item['describes']; ?></label>
						</td>
						<td>
							<label style="width: auto"><?php echo $item['quantity']; ?></label>
						</td>
					</tr>
				<?php endforeach; ?>
          	</table>
      	</div>
    <?php else: ?>
        <p class="alert alert-danger">Danh sách trống</p>
    <?php endif; ?>
   </div>







<script src="js/admin_script.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

</body>
</html>