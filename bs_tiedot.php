<?php
include 'bs_otsikko.php';
require_once 'Validate.php';
require_once 'FI.php';

/*Tekstikentistä asiakkaan tiedot. 
Etunimi ja sukunimi eivät salli numeroita
Sähköpostin täytyy olla oikean muotoinen
Kaikissa kentissä täytyy olla tekstiä

Kummatkin nappulat käynnistävät sivun uudestaan. Sen jälkeen käytetään alempana olevia isset funktioita sen perusteella kumpaa nappia painettiin.
*/
$etunimi       = '';
$sukunimi      = '';
$katuosoite    = '';
$postinumero   = '';
$epost         = '';
$puhelinnumero = '';

if (isset($_POST["submit"])) {
    $etunimi       = trim(strip_tags($_POST["etunimi"]));
    $sukunimi      = trim(strip_tags($_POST["sukunimi"]));
    $katuosoite    = trim(strip_tags($_POST["katuosoite"]));
    $postinumero   = trim(strip_tags($_POST["postinumero"]));
    $epost         = trim(strip_tags($_POST["epost"]));
    $puhelinnumero = trim(strip_tags($_POST["puhelinnumero"]));
    $tarkistukset  = true;
}

echo <<<END

<div class="tuotetaulukko">
</p>
<div class="tiedot">
<h2>Yhteystiedot</h2>
<form action="$_SERVER[PHP_SELF]" method="post">
<div class="yhteystiedot">
Kirjoita seuraaviin kenttiin yhteystietosi.
</div>
<table id="kassa_taulukko" align="center">

<tr><td id="vasen_solu">Etunimi</td><td id="oikea_solu"> <input type="text" name="etunimi" value="$etunimi" rows="1" cols="15"></textarea> </td></tr>
<tr>
<td id="vasen_solu">Sukunimi</td> <td id="oikea_solu"><input type="text" name="sukunimi" value="$sukunimi" rows="1" cols="15"></textarea> </td>
</tr>
<tr>
<td id="vasen_solu">Katuosoite</td> <td id="oikea_solu"><input type="text" name="katuosoite" value="$katuosoite" rows="1" cols="20"></textarea> </td>
</tr>
<tr>
<td id="vasen_solu">Postinumero</td> <td id="oikea_solu"><input type="text" name="postinumero" value="$postinumero" rows="1" cols="10"></textarea> </td>
</tr>
<tr>
<td id="vasen_solu">Sähköpostiosoite</td> <td id="oikea_solu"><input type="text" name="epost" value="$epost" rows="1" cols="20"></textarea> </td>
</tr>
<tr>
<td id="vasen_solu">Puhelinnumero</td> <td id="oikea_solu"><input type="text" name="puhelinnumero" value="$puhelinnumero" rows="1" cols="10"></textarea> </td>
</tr>
<tr>
<td id="vasen_solu"><input type="submit" name="submit2" value="Keskeytä tilaus"/> </td><td id="oikea_solu"> <input type="submit" name="submit" value="Seuraava"/> </td>
</form>
</tr>
</div>
</p>
</form>
</div>
END;


//Tämä tapahtuu kun painetaan "Seuraava". Aluksi tarkistetaan syötteet. Jos ne eivät ole kunnossa, tulostetaan virheilmoituksia tarpeen mukaan. 
//Jos syötteet ovat kunnossa, siirrytään varmistus-sivulle.
if (isset($_POST["submit"])) {
    $etunimi       = trim(strip_tags($_POST["etunimi"]));
    $sukunimi      = trim(strip_tags($_POST["sukunimi"]));
    $katuosoite    = trim(strip_tags($_POST["katuosoite"]));
    $postinumero   = trim(strip_tags($_POST["postinumero"]));
    $epost         = trim(strip_tags($_POST["epost"]));
    $puhelinnumero = trim(strip_tags($_POST["puhelinnumero"]));
    $tarkistukset  = true;
	echo <<<END
	<div class="yhteystiedot">
END;
    if (empty($etunimi) || empty($sukunimi) || empty($katuosoite) || empty($postinumero) || empty($epost) || empty($puhelinnumero)) {
        $tarkistukset = false;
        echo '<b>Et syöttänyt kaikkia yhteystietoja! </b></p>';
        
    }
    
    $validate = new Validate();
	$validate_fi = new Validate_FI();
	

	$ehdot = array("check_domain"=>true,"use_rfc822"=>true);
    //Varmistetaan että email on OK
	//Validate.php muokattu tätä varten riveillä 586-588
	$test = $validate->email($epost,$ehdot);
    if (!($test)) {
        $tarkistukset = false;
        echo '<b>Sähköposti on väärässä muodossa!</b><p/>';
    }
    
    //Etunimessä ei saa olla numeroita
    if (!($validate->string($etunimi, array(
        'format' => VALIDATE_EALPHA
    )))) {
        echo '<b>Etunimi on väärässä muodossa!</b> <p/>';
        $tarkistukset = false;
        
    }
    
    //Sukunimessä ei myöskään saa olla numeroita
    if (!($validate->string($sukunimi, array(
        'format' => VALIDATE_EALPHA
    )))) {
        echo '<b>Sukunimi on väärässä muodossa! </b><p/>';
        $tarkistukset = false;
        
    }
    
    //Postinumeron täytyy olla 5 numeroa pitkä ja siinä saa olla vain numeroita
    if (!($validate_fi->postalCode($postinumero))) {
        
        echo '<b>Postinumero on väärässä muodossa! </b></p>';
        $tarkistukset = false;
        
    }
	//Tuotteita toimitetaan vain postitustaulun postinumeroille
	if($validate_fi->postalCode($postinumero)){
    $stmt = $dbh->prepare("SELECT Postinumero FROM Postitus where Postinumero=:postinumero");
	$stmt->bindValue(':postinumero', $postinumero, PDO::PARAM_STR);
	$ok   = $stmt->execute();    
	if (!$ok) {
        print_r($sql->errorInfo());
    }
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
    if(empty($row)){
	echo '<b>Brute Store toimittaa tuotteita vain Helsinkiin ja Espooseen! </b><p/>';
	$tarkistukset = false;
	}
        
    }
	
	//Puhelinnumero suomen systeemin mukaisesti
    if (!($validate_fi->phoneNumber($puhelinnumero))) {
        
        echo '<b>Puhelinnumero on väärässä muodossa! </b></p>';
        $tarkistukset = false;
        
    }
    
    
    //Valmistaudutaan siirtymiseen kohti viimeistä sivua ennen lopullista tilaamista!
    if ($tarkistukset) {
        $_SESSION["etunimi"]       = $etunimi;
        $_SESSION["sukunimi"]      = $sukunimi;
        $_SESSION["katuosoite"]    = $katuosoite;
        $_SESSION["postinumero"]   = $postinumero;
        $_SESSION["epost"]         = $epost;
        $_SESSION["puhelinnumero"] = $puhelinnumero;
        ob_start();
        
        
        $url = 'bs_varmistus.php';
        
        
        while (ob_get_status()) {
            ob_end_clean();
			
        }
        
        header("Location: $url");
    }
	echo <<<END
	<div class="yhteystiedot">
END;
}

//Tämä tapahtuu, kun painetaan "Keskeytä tilaus". Siinä palataan yksinkertaisesti Verkkostoren etusivulle.
//Tässä ei nollata sessiomuuttujia!
if (isset($_POST["submit2"])) {
    ob_start();
    
    
    $url = 'bs_etusivu.php';
    
    
    while (ob_get_status()) {
        ob_end_clean();
		
    }
    
    header("Location: $url");
    
}

?>
</body>
</html>