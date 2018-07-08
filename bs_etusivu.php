<?php
require_once 'Validate.php';
include 'bs_otsikko.php';
$validate = new Validate();
if (!isset($_SESSION['product_amount'])) {
    $_SESSION['product_amount'][]     = array();
    $_SESSION['product_price'][]      = array();
    $_SESSION['product_name'][]       = array();
    $_SESSION['product_id'][]         = array();
    $_SESSION['shopping_cart_amount'] = 0;
    $_SESSION['shopping_cart_price']  = 0.0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql2 = $dbh->prepare('SELECT Tuotenumero FROM Tuote');
    $ok   = $sql2->execute();
    
    if (!$ok) {
        print_r($sql->errorInfo());
    }
    while ($row = $sql2->fetch(PDO::FETCH_ASSOC)) {
        if (isset($_POST['submit' . $row["Tuotenumero"]])) {
            $etunimi = $_POST['products' . $row["Tuotenumero"]];
            if (!($validate->string($etunimi, array(
                'format' => VALIDATE_NUM
            )))) {
                
            } else {
                foreach ($_POST['id'] as $key => $value) {
                    if ($value == $row["Tuotenumero"]) {
                        $avain = $key;
                    }
                }
                $tuotenumero = $_POST['id'][$avain];
                $stmt        = $dbh->prepare("SELECT Nimi,Hinta FROM Tuote where Tuotenumero=:tn");
                $stmt->bindValue(':tn', $tuotenumero, PDO::PARAM_INT);
                $ok = $stmt->execute();
                if (!$ok) {
                    print_r($stmt->errorInfo());
                }
                $rows = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (isset($_SESSION['product_name'])) {
                    $key = array_search($rows['Nimi'], array_values($_SESSION['product_name']));
                    if ($key != 0) {
                        
                        $_SESSION['product_amount'][$key] += $_POST['products' . $row["Tuotenumero"]];
                        $_SESSION['product_id'][$key] = $row["Tuotenumero"];
                        
                        $_SESSION['shopping_cart_amount'] += $_POST['products' . $row["Tuotenumero"]];
                        $_SESSION['shopping_cart_price'] += $_SESSION["product_price"][$key] * $_POST['products' . $row["Tuotenumero"]];
                    } else {
                        
                        $_SESSION['product_name'][]   = $rows['Nimi'];
                        $_SESSION['product_price'][]  = $rows['Hinta'];
                        $_SESSION['product_amount'][] = $_POST['products' . $row["Tuotenumero"]];
                        $_SESSION['product_id'][]     = $row["Tuotenumero"];
                        
                        $_SESSION['shopping_cart_amount'] += end($_SESSION["product_amount"]);
                        $_SESSION['shopping_cart_price'] += (end($_SESSION["product_amount"]) * end($_SESSION["product_price"]));
                        
                    }
                }
            }
        }
    }
    
}
include 'bs_haku.php';
include 'bs_shoppingcart.php';
echo '<div class="tuotetaulukko">';
echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';

if (isset($_POST['searchBtn'])) {
    if ($_POST['searchBox'] != '' && $_POST['categoryDropDown'] != '') {
        $sql = $dbh->prepare("SELECT * FROM `Tuote` WHERE Kategoria = :category AND Nimi like :productName");
        $sql->bindValue(':category', $_POST['categoryDropDown'], PDO::PARAM_STR);
        $sql->bindValue(':productName', '%' . $_POST['searchBox'] . '%', PDO::PARAM_STR);
        $ok = $sql->execute();
    }
    
    elseif ($_POST['categoryDropDown'] != '') {
        $sql = $dbh->prepare("SELECT * FROM `Tuote` WHERE Kategoria = :category");
        $sql->bindValue(':category', $_POST['categoryDropDown'], PDO::PARAM_STR);
        $ok = $sql->execute();
    } elseif ($_POST['searchBox'] != '') {
        $sql = $dbh->prepare("SELECT * FROM `Tuote` WHERE Nimi like :productName");
        $sql->bindValue(':productName', '%' . $_POST['searchBox'] . '%', PDO::PARAM_STR);
        $ok = $sql->execute();
    } else {
		$sql = $dbh->prepare('SELECT * FROM Tuote ORDER BY Tuotenumero DESC');
		$ok  = $sql->execute();
	}
} else {
    $sql = $dbh->prepare('SELECT * FROM Tuote ORDER BY Tuotenumero DESC');
    $ok  = $sql->execute();
}

echo '<table class="tuote_taulukko">';

if (!$ok) {
    print_r($sql->errorInfo());
}
while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
    echo '<tr>';
    echo '<td><table border="1" cellpadding="4" cellspacing="0"><tr><td><div class="taulukkokuvadiv"><span class="helper"></span><a href="bs_product.php?id='.$row["Tuotenumero"].'"><img src="kuvat/' . $row["Tiedostonimi"] . '.png" alt="' . $row["Nimi"] . '_' . $row["Tuotenumero"] . '"></img></a></div></td></tr></table></td>';
    echo '<td class="tuotekuvaus"><div class="tuotekuvausdiv"><p><b><a href="bs_product.php?id='.$row["Tuotenumero"].'" style="color: #000000">' . $row["Nimi"] . '</a></b></p><p>' . $row["Kuvaus"] . '</p></div></td>';
    echo "<td><div><p><b>Varastossa</b><br/>" . $row["Varastossa"] . " kpl</p></div></td>";
    echo '<td><div><p><b>Hinta<br/><font color="ED1C24" size="5">' . $row["Hinta"] . ' e</font></b></p><input type="text" name="products' . $row["Tuotenumero"] . '" class="lisaa_maara_teksti" value="1"/>';
    echo '<input type="hidden" name="id[]" value="' . $row["Tuotenumero"] . '" /><input type="submit" name="submit' . $row["Tuotenumero"] . '" value="Lisää ostoskoriin" id="lisaa_ostoskoriin_nappi' . $row["Tuotenumero"] . '"/></div></td>';
    echo '</tr>';
}

echo '</table></form>';
echo '</div>';

?>
</body>
</html>