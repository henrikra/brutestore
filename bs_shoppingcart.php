<?php
echo '<div id="ostoskori">';
if (isset($_GET["emptyShoppingCartBtn"])) {
    unset($_SESSION['product_amount']);
    unset($_SESSION['product_price']);
    unset($_SESSION['product_name']);
    unset($_SESSION['product_id']);
    $_SESSION['shopping_cart_amount'] = 0;
    $_SESSION['shopping_cart_price']  = 0.0;
}
echo '<div class="form-element">';
echo <<<END
<table><tr><td>
<form action="bs_kassa.php" method="post">
<input type="submit" name="kassalle" value="Mene kassalle"/>
</form>
END;
echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="get">';
echo '<input type="submit" name="emptyShoppingCartBtn" value="Tyhjennä ostoskori"/>';
if (isset($id)) {
    echo '<input type="hidden" name="id" value="' . $id . '">';
}
echo '</form>';
echo '</td><td>';
echo 'Ostoskorissa tuotteita: ' . $_SESSION['shopping_cart_amount'];
echo ' kpl, Kokonaishinta: ' . number_format($_SESSION['shopping_cart_price'], 2) . ' €';
echo '</td></tr></table>';
echo '</div>';
echo '</div>';
?>