<?php
include 'bs_otsikko.php';
echo <<<END
<div class="tuotetaulukko">
<h2> Tilatut tuotteet: </h2>
END;
$productTotalPrice = 0;


echo <<<END
<table id="kassa_taulukko" align="center" cellpadding="10">
<tr>
<th padding>Tuotenumero</th>
<th id="vasen_solu">Tuote</th>
<th id="keski_solu">Kpl</th>
<th id="oikea_solu">Hinta</th>
</tr>
END;

//Tähän tulostetaan sessiomuuttujista tilattut tuotteet ja niiden määrä. Ne sijoitetaan taulukkoon, jonka kolmelle solulle täytyy antaa ID järjestyksessä "vasen_solu, keski_solu, oikea_solu"
//jotta teksti on oikeassa paikassa.

if (isset($_SESSION['product_id'])) { //tämä estää unidentified index errorin jos mennään ostoskoriin ilman että siellä on tuotteita
    if (count($_SESSION['product_amount']) > 0) { //varmistetaan että käyttäjä on laittanut koriin jotain
        for ($i = 1; $i < count($_SESSION['product_amount']); $i++) {
            echo '<tr>';
            echo '<td>' . $_SESSION['product_id'][$i] . '</td>';
            echo '<td id="vasen_solu">' . $_SESSION['product_name'][$i] . '</td>';
            echo '<td id="keski_solu">' . $_SESSION['product_amount'][$i] . '</td>';
            echo '<td id="oikea_solu">' . $_SESSION['product_price'][$i] . " €";
            echo '</td>';
            echo '</tr>';
            $productTotalPrice = $productTotalPrice + ((int) $_SESSION['product_amount'][$i] * (float) $_SESSION['product_price'][$i]);
        }
    }
}
echo <<<END
<tr class="tyhja_rivi"/><tr/>
<tr>
<td id="vasen_solu"></td>
<td id="keski_solu"></td>
<td id="oikea_solu"><b>Yhteensä:</b></td>
<td>$productTotalPrice €</td></tr>
<tr>
<td>
<form action="bs_etusivu.php" method="get">
<input type="submit" name="back" value="Takaisin"/>
</form>
</td>
<td/><td/>
END;
if (isset($_SESSION['product_id'])) {
    echo <<<END
<td>
<form action="bs_tiedot.php" method="get">
<input type="submit" name="submit" value="Seuraava"/>
</form>
</td>
END;
}
echo <<<END
</tr>
</table>
<br/>
END;
if (isset($_SESSION['product_id'])) {
    $considerationsHeaderDone = false;
    for ($j = 1; $j < count($_SESSION['product_amount']); $j++) {
        $stmt = $dbh->prepare("SELECT Varastossa FROM Tuote WHERE Tuotenumero = :productID;");
        $stmt->bindValue(':productID', $_SESSION['product_id'][$j], PDO::PARAM_INT);
        $ok = $stmt->execute();
        if (!$ok) {
            print_r($stmt->errorInfo());
        }
        $row     = $stmt->fetch(PDO::FETCH_ASSOC);
        $tilatut = $_SESSION["product_amount"][$j];
        if (($row['Varastossa'] - $tilatut) < 0) { //jos jotain tuotetta ei ole tarpeeksi varastossa tulostetaan huomioitavaa-taulukko
            if (!$considerationsHeaderDone) {
                echo '<h2> Huomioitavaa </h2>';
                echo 'Seuraavia tuotteiden toimitus saattaa viedä tavallista pidemmän aikaa,';
                echo ' koska niitä ei ole tarpeeksi varastossa:';
                echo '<table id="kassa_taulukko" align="center">';
                echo '<tr class="tyhja_rivi"/>';
                $considerationsHeaderDone = true;
            }
            $stmt = $dbh->prepare('SELECT Nimi FROM Tuote WHERE Tuotenumero = :productID;');
            $stmt->bindValue(':productID', $_SESSION['product_id'][$j], PDO::PARAM_INT);
            $ok          = $stmt->execute();
            $row         = $stmt->fetch(PDO::FETCH_ASSOC);
            $productName = $row['Nimi'];
            echo '<tr><td>' . $productName . '</td></tr>';
        }
    }
    if ($considerationsHeaderDone) {
        echo '</table>';
    }
}
echo '</div>';
?>
</body>
</html>