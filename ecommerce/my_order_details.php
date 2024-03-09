<?php 
require('top.php');
if(!isset($_SESSION['USER_LOGIN'])){
	?>
	<script>
	window.location.href='index.php';
	</script>
	<?php
}
$order_id=get_safe_value($con,$_GET['id']);

$coupon_details=mysqli_fetch_assoc(mysqli_query($con,"select coupon_value from `order` where id='$order_id'"));
$coupon_value=$coupon_details['coupon_value'];
if($coupon_value==''){
	$coupon_value=0;	
}
?>

<div class="ht__bradcaump__area bg__white">
    <div class="ht__bradcaump__wrap">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="bradcaump__inner">
                        <nav class="bradcaump-inner">
                            <a class="breadcrumb-item" href="index.html">Home</a>
                            <span class="brd-separetor"><i class="zmdi zmdi-chevron-right"></i></span>
                            <span class="breadcrumb-item active">Thank You</span>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- cart-main-area -->
<div class="wishlist-area ptb--100 bg__white" style="margin-top: -80px;">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="wishlist-content">
                    <form action="#">
                        <div class="wishlist-table table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="product-thumbnail">Product Name</th>
                                        <th class="product-thumbnail">Product Image</th>
                                        <th class="product-name">Quality</th>
                                        <th class="product-price">Price</th>
                                        <th class="product-price">Total Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $uid=$_SESSION['USER_ID'];
                                        $res=mysqli_query($con,"select distinct(order_detail.id) ,order_detail.*,product.name,product.image from order_detail,product ,`order` where order_detail.order_id='$order_id' and `order`.user_id='$uid' and order_detail.product_id=product.id");
                                        $total_price=0;
                                        while($row=mysqli_fetch_assoc($res)){
                                        $total_price=$total_price+($row['qty']*$row['price']);
                                    ?>
                                    <tr>
                                        <td class="product-name"><?php echo $row['name']?></td>
                                        <td class="product-name"> <img src="<?php echo PRODUCT_IMAGE_SITE_PATH.$row['image']?>"></td>
                                        <td class="product-name"><?php echo $row['qty']?></td>
                                        <td class="product-name"><?php echo formatMoney($row['price'])?> đ</td>
                                        <td class="product-name"><?php echo formatMoney($row['qty']*$row['price'])?> đ</td>
                                        
                                    </tr>
                                    <?php } 
                                    if($coupon_value!='') {
                                    ?>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td class="product-name">Coupon Value</td>
                                        <td class="product-name"><?php echo formatMoney($coupon_value)?> đ</td>
                                        
                                    </tr>
                                    <?php } ?>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td class="product-name">Total Price</td>
                                        <td class="product-name">
                                            <?php 
                                                echo formatMoney($total_price-$coupon_value);
                                            ?> đ
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>  
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>      
        						
<?php require('footer.php')?>        