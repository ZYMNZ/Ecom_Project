<?php
include_once "Views/General/session.php";

//include_once "Models/Order.php";
//$order = new Order();
//$order->displayCart($_SESSION['user_id']);
//var_dump($order);
?>

<html lang="en">
    <head>
        <title>Shopping Cart</title>
        <link rel="stylesheet" type="text/css" href="Views/styles/generalstyles.css">
        <link rel="stylesheet" type="text/css" href="Views/styles/navbar.css">
        <link rel="stylesheet" type="text/css" href="Views/styles/login.css">
        <link rel="stylesheet" type="text/css" href="Views/styles/footer.css">
        <link rel="stylesheet" type="text/css" href="Views/styles/home.css">
        <link rel="stylesheet" type="text/css" href="Views/styles/cart.css">
    </head>

    <?php $subPrice=0 ?>
    <body>
    <div class="mainContentWrapper">
        <main>
            <?php
            include_once "Views/General/navbar.php";
            ?>

            <div style="width: 50%">
                <header class="shoppingCartHeader fontWeightBold">
                    <label>Shopping Cart</label>
                </header>
                <?php
                if (isset($_SESSION['error'])) {
                    echo "<label>{$_SESSION['error']}</label>";
                }
                if (isset($dataToSend['display'])) {
                ?>
                <div class="wrapper">

                    <?php foreach ($dataToSend['display'] as $data ) : ?>

                    <div class="cartItemBlock">
                        <div class="firstHalfCartItemBlock displayInlineBlock">
                            <label class="fontWeightBold displayBlock categoryLabel"><?php echo htmlentities($data['category'], ENT_QUOTES) ?></label>
                            <label class="productTitle" name="productTitle"> <?php echo htmlentities($data['title'], ENT_QUOTES) ?></label>
                        </div>

                        <div class="secondHalfCartItemBlock displayInlineBlock">
                            <div class="expandButtonContainer displayInlineFlex width100Percent justifyContentEnd">
                                <div class="cartItemExpandButton cursorPointer textAlignCenter displayInlineBlock">
                                    <a class="deleteButton" href="?controller=cart&action=deleteCartProduct&id=<?php echo htmlentities($data['productId'], ENT_QUOTES); ?>"><button class="deleteButton">Delete</button></a>

                                </div>
                            </div>


                            <div class="priceContainer displayInlineFlex width100Percent justifyContentEnd">
                                <div>

                                    <label name="price"><?php echo "$" . number_format($data['price'], 2, '.', ',') ?></label>
                                </div>
                            </div>
                            <?php $subPrice +=$data['price']; ?>
                        </div>

                        <div class="displayBlock requestService" >
                            <label>Request for this service:</label>
                        </div>

                        <div class="displayBlock textAreaDiv" >
                            <textarea name="requestService" ></textarea>
                        </div>
                    </div>
                    <?php endforeach; ?>

                </div>
                <?php
                } else {
                    echo "<label>Your shopping cart is currently empty</label>";
                }
                ?>
            </div>




            <div class="priceContainerBlock">
                <div class="subTotal">
                    <label>Subtotal</label>
                    <label>CAD $<?php echo number_format($subPrice, 2, '.', ',');?></label>

                </div>

                <div class="estimatedTax" style="top: 8%">
                    <label>Estimated Tax</label>
                    <label>CAD $<?php $estimatedtax = number_format($subPrice * 0.15,2, '.', ','); echo $estimatedtax;?></label>

                </div>

                <div class="totalPrice">
                    <label>Total Price</label>
                    <label>CAD $<?php $totalPrice = $subPrice + $estimatedtax; echo number_format($totalPrice, 2, '.', ','); ?></label>
                </div>
                <div style="padding: 220px 0 0;">
                    <?php if ($totalPrice > 0){ ?>
                    <a href="?controller=order&action=orderConfirmed"><button class="confirmButton">CHECKOUT</button></a>
                    <?php } ?>
                </div>
            </div>
        </main>
    </div>

    <?php
    unset($_SESSION['error']);
    include_once "Views/General/footer.php";
    ?>
    </body>
</html>