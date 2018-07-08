<?php
include 'bs_otsikko.php';
//Tehdään sessiomuuttujista paikallisia muuttujia. 
$yhthinta      = 0;
$etunimi       = $_SESSION["etunimi"];
$sukunimi      = $_SESSION["sukunimi"];
$katuosoite    = $_SESSION["katuosoite"];
$postinumero   = $_SESSION["postinumero"];
$epost         = $_SESSION["epost"];
$puhelinnumero = $_SESSION["puhelinnumero"];

$taulukko = array(
    $etunimi,
    $sukunimi,
    $katuosoite,
    $postinumero,
    $epost,
    $puhelinnumero
); //Asiakkaan syöttämät tiedot tiedot.php:ssa
$nimet    = array(
    "Etunimi:",
    "Sukunimi:",
    "Katuosoite:",
    "Postinumero:",
    "Sähköpostiosoite:",
    "Puhelinnumero:"
); //Taulukkoon vasempaan soluun laitettavat tekstit

//Tulostetaan ensin tilatut tuotteet, niiden määrät ja yhteishinta. Sitten kirjoitetut yhteystiedot.
echo <<<END
<div class="tuotetaulukko">
<h2> Tilatut tuotteet </h2>
END;

echo <<<END
<table id="kassa_taulukko" align="center" cellpadding="10">
<tr>

<th padding>
Tuotenumero
</th>

<th id="vasen_solu">
Tuote
</th>

<th id="keski_solu">
Kpl
</th>

<th id="oikea_solu">
Hinta 
</th>

</tr>
END;

for ($i = 1; $i < count($_SESSION['product_amount']); $i++) {
    echo '<tr>';
    echo '<td>';
    echo $_SESSION['product_id'][$i];
    echo '</td>';
    echo '<td id="vasen_solu">';
    echo $_SESSION['product_name'][$i];
    echo '</td>';
    echo '<td id="keski_solu">';
    echo $_SESSION['product_amount'][$i];
    echo '</td>';
    echo '<td id="oikea_solu">';
    echo $_SESSION['product_price'][$i];
    $yhthinta = $yhthinta + ($_SESSION['product_amount'][$i] * $_SESSION['product_price'][$i]);
    echo '</td>';
    echo '</tr>';
}

//Viimeisen rivin tulostus, johon tuotteiden yhteenlaskettu hinta
echo <<<END
<tr class="tyhja_rivi"/>
<tr/>
<tr>
<td id="vasen_solu">

</td>

<td id="keski_solu">

</td>

<td id="oikea_solu">
<b>Yhteensä:</b>
</td>

<td>
END;
echo $yhthinta . " €";
echo <<<END
</td>

</tr>
</table>
END;

//Yhteystietoalue alkaa
echo <<<END
<h2> Syötetyt yhteystiedot </h2>

<table id="yhteystieto_taulukko_yhteystiedot" align="center">
END;
for ($i = 0; $i < count($taulukko); $i++) {
    echo <<<END
<tr>
<td id="vasen_solu">
$nimet[$i]
</td>
<td id="oikea_solu">
$taulukko[$i]
</td>
</tr>
END;
}
echo <<<END
<tr class="tyhja_rivi"/>
<tr>

<td id="vasen_solu">
<form action="bs_tiedot.php" method="get">
<input type="submit" name="submit" value="Edellinen"/>
</form>
</td>

<td id="oikea_solu">
<form action="bs_kiitostilauksesta.php" method="get">
<input type="submit" name="back" value="Tilaa tuotteet"/>
</form>
</td>

</table>
</div>
END;


?>
</body>
</html>