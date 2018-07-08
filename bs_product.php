<?php
include 'bs_otsikko.php';
if (!isset($_SESSION['product_amount'])) {
    $_SESSION['product_amount'][]     = array();
    $_SESSION['product_price'][]      = array();
    $_SESSION['product_name'][]       = array();
    $_SESSION['product_id'][]         = array();
    $_SESSION['shopping_cart_amount'] = 0;
    $_SESSION['shopping_cart_price']  = 0.0;
}
if (isset($_GET['id'])) {
    $productID = $_GET['id'];
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productID = $_POST['id'];
}
$sql = $dbh->prepare('SELECT * FROM Tuote WHERE Tuotenumero= :productID');
$sql->bindValue(':productID', $productID, PDO::PARAM_STR);
$ok  = $sql->execute();
$row = $sql->fetch(PDO::FETCH_ASSOC);

if (isset($_GET['addToCartBtn']) && isset($_SESSION['product_name'])) {
    
    $key = array_search($row['Nimi'], array_values($_SESSION['product_name']));
    if ($key != 0) {
        
        $_SESSION['product_amount'][$key] += $_GET['product'];
        $_SESSION['product_id'][$key] = $row["Tuotenumero"];
        
        $_SESSION['shopping_cart_amount'] += $_GET['product'];
        $_SESSION['shopping_cart_price'] += $_SESSION["product_price"][$key] * $_GET['product'];
        
    } else {
        
        $_SESSION['product_name'][]   = $row['Nimi'];
        $_SESSION['product_price'][]  = $row['Hinta'];
        $_SESSION['product_amount'][] = $_GET['product'];
        $_SESSION['product_id'][]     = $row["Tuotenumero"];
        
        $_SESSION['shopping_cart_amount'] += end($_SESSION["product_amount"]);
        $_SESSION['shopping_cart_price'] += (end($_SESSION["product_amount"]) * end($_SESSION["product_price"]));
        
    }
    echo '<meta http-equiv="refresh" content="0;bs_product.php?id=' . $productID . '">';
    
}
include 'bs_shoppingcart.php';
echo '<div class="isotuoteinfo">';
if (!$ok) {
    print_r($sql->errorInfo());
}

echo '<div><h2><font color="FFFFFF" size="5">' . $row["Nimi"] . '</font></h2></div>';
echo '<table border="1" cellpadding="4" cellspacing="0"><tr>';
echo '<td><div id="isokuvadiv"><span class="helper"></span><a href="kuvat/' . $row["Tiedostonimi"] . '"><img src="kuvat/' . $row["Tiedostonimi"] . '.png" alt="' . $row["Nimi"] . '_' . $row["Tuotenumero"] . '"></a><br/>Klikkaa kuva suuremmaksi</div></td>';
echo '<td>';
echo 'Tuotenumero: <b>' . $row["Tuotenumero"] . '</b>';
echo '<form action="' . $_SERVER['PHP_SELF'] . '" method=get>';
echo '<input type="hidden" value=' . $row["Tuotenumero"] . ' name="id">';
echo '<div>Varastossa: <b>' . $row["Varastossa"] . ' kpl</b></div>';
echo '<div><p><b>Hinta<br/><font color="ED1C24" size="5">' . $row["Hinta"] . ' e</font></b></p><input type="text" name="product" rows="1" cols="10" value="1" id="lisaa_maara_teksti"></textarea>';
echo '<input type="submit" name="addToCartBtn" value="Lisää ostoskoriin" id="lisaa_ostoskoriin_nappi"/></form></div>';
echo '</td>';
echo '</tr></table>';
echo '<div id="tuotekuvausdiv"><p><b>' . $row["Nimi"] . '</b></p><p>' . $row["Kuvaus"] . '</p><br/>';
echo '<form action="bs_etusivu.php" method="post">';
echo '<input type="submit" name="back" value="Takaisin"/>';
echo '</form>';
echo '</div></div>';
?>
</body>
</html>