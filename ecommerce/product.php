<?php 
ob_start();
require('top.php');
if(isset($_GET['id'])){
	$product_id=mysqli_real_escape_string($con,$_GET['id']);
	if($product_id>0){
		$get_product=get_product($con,'','',$product_id);
	}else{
		?>
		<script>
		window.location.href='index.php';
		</script>
		<?php
	}
	
	$resMultipleImages=mysqli_query($con,"select product_images from product_images where product_id='$product_id'");
	$multipleImages=[];
	if(mysqli_num_rows($resMultipleImages)>0){
		while($rowMultipleImages=mysqli_fetch_assoc($resMultipleImages)){
			$multipleImages[]=$rowMultipleImages['product_images'];
		}
	}
	
	$resAttr=mysqli_query($con,"select product_attributes.*,color_master.color,size_master.size from product_attributes 
	left join color_master on product_attributes.color_id=color_master.id and color_master.status=1 
	left join size_master on product_attributes.size_id=size_master.id and size_master.status=1
	where product_attributes.product_id='$product_id'");
	$productAttr=[];
	$colorArr=[];
	$sizeArr=[];
	if(mysqli_num_rows($resAttr)>0){
		while($rowAttr=mysqli_fetch_assoc($resAttr)){
			$productAttr[]=$rowAttr;
			$colorArr[$rowAttr['color_id']][]=$rowAttr['color'];
			$sizeArr[$rowAttr['size_id']][]=$rowAttr['size'];
			
			$colorArr1[]=$rowAttr['color'];
			$sizeArr1[]=$rowAttr['size'];
		}
	}
	$is_size=count(array_filter($sizeArr1));
	$is_color=count(array_filter($colorArr1));
	//$colorArr=array_unique($colorArr);
	//$sizeArr=array_unique($sizeArr1);
} else {
	?>
	<script>
		window.location.href='index.php';
	</script>
	<?php
}

if(isset($_POST['review_submit'])){
	$rating=get_safe_value($con,$_POST['rating']);
	$review=get_safe_value($con,$_POST['review']);
	
	$added_on=date('Y-m-d h:i:s');
	mysqli_query($con,"insert into product_review(product_id,user_id,rating,review,status,added_on) values('$product_id','".$_SESSION['USER_ID']."','$rating','$review','1','$added_on')");
	header('location:product.php?id='.$product_id);
	die();
}

$product_review_res=mysqli_query($con,"select users.name,product_review.id,product_review.rating,product_review.review,product_review.added_on from users,product_review where product_review.status=1 and product_review.user_id=users.id and product_review.product_id='$product_id' order by product_review.added_on desc");
?>


<!-- Bradcaump area -->
<div class="ht__bradcaump__area bg__white">
	<div class="ht__bradcaump__wrap">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<div class="bradcaump__inner">
						<nav class="bradcaump-inner">
							<a class="breadcrumb-item" href="index.php">Home</a>
							<span class="brd-separetor"><i class="zmdi zmdi-chevron-right"></i></span>
							<a class="breadcrumb-item" href="categories.php?id=<?php echo $get_product['0']['categories_id']?>"><?php echo $get_product['0']['categories']?></a>
							<!-- <span class="brd-separetor"><i class="zmdi zmdi-chevron-right"></i></span>
							<span class="breadcrumb-item active"><?php echo $get_product['0']['name']?></span> -->
						</nav>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!--Product Details Area -->
<section class="htc__product__details bg__white ptb--60" style="margin-top: -20px;">
	<!--Product Details Top -->
	<div class="htc__product__details__top">
		<div class="container">
			<div class="row">
				<div class="col-md-5 col-lg-5 col-sm-12 col-xs-12 ml--40">
					<div class="htc__product__details__tab__content">
						<!--Product Big Images -->
						<div class="product__big__images big-image-3d">
							<div class="portfolio-full-image tab-content">
								<div role="tabpanel" class="tab-pane fade in active" id="img-tab-1">
									<div id="model3D">
										<model-viewer id="move" auto-rotate camera-controls xr-environment disable-zoom rotation-per-second="350%"
											loading="eager" poster="./media/product/loading.gif"            
											src="./media/product/<?php echo $get_product['0']['image3d']?>"
											ios-src="./media/product/<?php echo $get_product['0']['image3d_usdz']?>">
										</model-viewer>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-7 col-lg-7 col-sm-12 col-xs-12 smt-40 xmt-40 ml--60 col-5">
					<div class="ht__product__dtl">
						<h2><?php echo $get_product['0']['name']?></h2>
						<ul  class="pro__prize">
							<strike><li class="old__prize" style="font-size: 13px;"><?php echo formatMoney($get_product['0']['mrp'])?></li></strike>
							<li><?php echo formatMoney($get_product['0']['price'])?> đ</li>
						</ul>
						<p class="pro__info"><?php echo $get_product['0']['short_desc']?></p>

						<div class="ht__pro__desc">
							<?php 
								$cart_show='yes';
								$is_cart_box_show="hide";
								if($is_color==0 && $is_size==0) {
									$is_cart_box_show="";
							?>
							<div class="sin__desc">
								<?php
									$getProductAttr=getProductAttr($con,$get_product['0']['id']);
									$productSoldQtyByProductId=productSoldQtyByProductId($con,$get_product['0']['id'],$getProductAttr);
									$pending_qty=$get_product['0']['qty']-$productSoldQtyByProductId;
									
									$cart_show='yes';
									if($get_product['0']['qty']>$productSoldQtyByProductId) {
										$stock='In Stock';			
									}else{
										$stock='Not in Stock';
										$cart_show='';
									}
								?>
								<p><span>Availability:</span> <?php echo $stock?></p>
							</div>
							<?php } ?>
							
							<?php if($is_color>0) { ?>
								<div class="sin__desc align--left">
									<p><span>color:</span></p>
									<ul class="pro__color">
										<?php 
										foreach($colorArr as $key=>$val){
											echo "<li style='background:".$val[0]." none repeat scroll 0 0'><a href='javascript:void(0)' onclick=loadAttr('".$key."','".$get_product['0']['id']."','color')>".$val[0]."</a></li>";
										}
										?>
									</ul>
								</div>
							<?php } ?>
							
							<?php if($is_size>0) { ?>
								<div class="sin__desc align--left">
									<p><span>Dist:</span></p>
									<select class="select__size" id="size_attr" onchange="showQty()">
										<option value="">Dist</option>
										<?php 
										foreach($sizeArr as $key=>$val){
											echo "<option value='".$key."'>".$val[0]."</option>";
										}
										?>
									</select>
								</div>
							<?php } ?>
							
							<?php
								$isQtyHide="hide";
								if($is_color==0 && $is_size==0) {
									$isQtyHide="";
								}
							?>
							
							<div class="sin__desc align--left <?php echo $isQtyHide?>" id="cart_qty" style="display: none;">
								<?php
									if($cart_show!='') {
								?>
								<p>
									<span>Quality:</span> 
									<select id="qty" class="select__size">
										<?php
											for($i=1;$i<=$pending_qty;$i++){
												echo "<option>$i</option>";
											}
										?>
									</select>
								</p>
								<?php } ?>
							</div>
							
							<div id="cart_attr_msg"></div>
							
							<div class="sin__desc align--left">
								<p><span>Categories:</span></p>
								<ul class="pro__cat__list">
									<li><a href="categories.php?id=<?php echo $get_product['0']['categories_id']?>"><?php echo $get_product['0']['categories']?></a></li>
								</ul>
							</div>
						</div>
					</div>
					
					<div id="is_cart_box_show">
						<a class="fr__btn buy_now" href="javascript:void(0)" onclick="manage_cart('<?php echo $get_product['0']['id']?>','add','yes')">Buy Now</a>
						<a class="fr__btn" href="javascript:void(0)" onclick="manage_cart('<?php echo $get_product['0']['id']?>','add')">Add to cart</a>
						<a class="fr__btn" href="product_3d.php?id=<?php echo $get_product['0']['id']?>">See in 3D</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<input type="hidden" id="cid"/>
<input type="hidden" id="sid"/>
		
<!--Product Description -->
<section class="htc__produc__decription bg__white">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<!-- List And Grid View -->
				<ul class="pro__details__tab" role="tablist">
					<li role="presentation" class="description active"><a href="#description" role="tab" data-toggle="tab">description</a></li>
					<li role="presentation" class="review"><a href="#review" role="tab" data-toggle="tab" class="active show" aria-selected="true">review</a></li>
				</ul>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<div class="ht__pro__details__content">
					<!-- Single Content -->
					<div role="tabpanel" id="description" class="pro__single__content tab-pane fade in active">
						<div class="pro__tab__content__inner" style="text-align: justify;">
							<?php echo $get_product['0']['description']?>
							<div class="sub_des">
								<br> Màn hình: <?php echo $get_product['0']['des_screen']?>
								<br> Cpu: <?php echo $get_product['0']['des_cpu']?>
								<br> Ram: <?php echo $get_product['0']['des_ram']?>
								<br> Bộ nhớ: <?php echo $get_product['0']['des_memory']?>
								<br> Card đồ họa: <?php echo $get_product['0']['des_graphics']?>
								<br> Trọng lượng: <?php echo $get_product['0']['des_weight']?>
							</div>
						</div>
					</div>
					
					<div role="tabpanel" id="review" class="pro__single__content tab-pane fade">
						<div class="pro__tab__content__inner">
							<?php 
								if(mysqli_num_rows($product_review_res)>0) {
								while($product_review_row=mysqli_fetch_assoc($product_review_res)){
							?>
							
							<article class="row">
								<div class="col-md-12 col-sm-12">
									<div class="panel panel-default arrow left">
										<div class="panel-body">
											<header class="text-left">
												<div>
													<span class="comment-rating">
														<?php echo $product_review_row['rating']?>
													</span>
													(<?php echo $product_review_row['name']?>)
												</div>
												<time class="comment-date"> 
													<?php
														$added_on=strtotime($product_review_row['added_on']);
														echo date('d M Y',$added_on);
													?>
												</time>
											</header>
											<div class="comment-post">
												<p><?php echo $product_review_row['review']?></p>
											</div>
										</div>
									</div>
								</div>
							</article>

							<?php } } else { 
								echo "<h3 class='submit_review_hint'>No review added</h3><br/>";
							}
							?>

							<h3 class="review_heading">Enter your review</h3><br/>
							<?php
								if(isset($_SESSION['USER_LOGIN'])){
							?>
							<div class="row" id="post-review-box" style=>
								<div class="col-md-12">
									<form action="" method="post">
										<select class="form-control" name="rating" required>
											<option value="">Select Rating</option>
											<option>Worst</option>
											<option>Bad</option>
											<option>Good</option>
											<option>Very Good</option>
											<option>Fantastic</option>
										</select> <br/>
										<textarea class="form-control" cols="50" id="new-review" name="review" placeholder="Enter your review here..." rows="5"></textarea>
										<div class="text-right mt10">
											<button class="btn btn-success btn-lg" type="submit" name="review_submit">Submit</button>
										</div>
									</form>
								</div>
							</div>

							<?php } else {
								echo "<span class='submit_review_hint'>Please <a href='login.php'>login</a> to submit your review</span>";
								}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
	if(isset($_COOKIE['recently_viewed'])) {
		$arrRecentView=unserialize($_COOKIE['recently_viewed']);
		$countRecentView=count($arrRecentView);
		$countStartRecentView=$countRecentView-4;
		$arrRecentView=array_slice($arrRecentView,$countStartRecentView,4);
		$recentViewId=implode(",",$arrRecentView);
		$res=mysqli_query($con,"select * from product where id IN ($recentViewId) and status=1");
?>

<!-- Recently Viewed -->
<section class="htc__produc__decription bg__white">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<h3 style="font-size: 20px;font-weight: bold;">Recently Viewed</h3>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<div class="ht__pro__details__content">
					<div class="row">
						<?php while($list=mysqli_fetch_assoc($res)) { ?>
							<div class="col-xs-3">
								<div class="category">
									<div class="ht__cat__thumb">
										<a href="product.php?id=<?php echo $list['id']?>">
											<img src="./media/product/<?php echo $list['image']?>" alt="product images">
										</a>
									</div>
									<div class="fr__hover__info">
										<ul class="product__action">
											<li><a href="javascript:void(0)" onclick="wishlist_manage('<?php echo $list['id']?>','add')"><i class="icon-heart icons"></i></a></li>
											<li><a href="javascript:void(0)" onclick="manage_cart('<?php echo $list['id']?>','add')"><i class="icon-handbag icons"></i></a></li>
										</ul>
									</div>
									<div class="fr__product__inner">
										<h4><a href="product.php?id=<?php echo $list['id']?>"><?php echo $list['name']?></a></h4>
										<ul class="fr__pro__prize">
											<strike><li class="old__prize" style="font-size: 13px;"><?php echo formatMoney($list['mrp'])?></li></strike>
											<li><?php echo formatMoney($list['price'])?> đ</li>
										</ul>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php 
		$arrRec=unserialize($_COOKIE['recently_viewed']);
		if(($key=array_search($product_id,$arrRec))!==false) {
			unset($arrRec[$key]);
		}
		$arrRec[]=$product_id;
	} else {
		$arrRec[]=$product_id;
		}
	setcookie('recently_viewed',serialize($arrRec),time()+60*60*24*365);
?>

<script>
	function showMultipleImage(im) {
		jQuery('#img-tab-1').html("<img src='"+im+"' data-origin='"+im+"'/>");
	}
	let is_color='<?php echo $is_color?>';
	let is_size='<?php echo $is_size?>';
	let pid='<?php echo $product_id?>';
</script>			

<?php 
require('footer.php');
ob_flush();
?>        