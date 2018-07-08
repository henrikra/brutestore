<?php
include 'bs_otsikko.php';
echo '<div class="tuotetaulukko">';
$onnistuminen = true; //Onko tilaus onnistunut

//Tarkistetaan, että tavaraa ylipäätään tilattiin. Muuten sivulla ei tehdä tietokantajuttuja.
if (!(empty($_SESSION["etunimi"]) || empty($_SESSION["sukunimi"]) || empty($_SESSION["katuosoite"]) || empty($_SESSION["postinumero"]) || empty($_SESSION["epost"]) || empty($_SESSION["puhelinnumero"]) || count($_SESSION['product_amount']) == 0 || count($_SESSION['product_price']) == 0 || count($_SESSION['product_id']) == 0 || count($_SESSION['product_name']) == 0)) {
    $dbh->beginTransaction();
    //Asiakkaan lisäys
    $stmt = $dbh->prepare("INSERT INTO Asiakas (Asiakasnumero, Etunimi, Sukunimi, Katuosoite, Postinumero, Email, Puhelinnumero) 
VALUES (null, :etunimi, :sukunimi, :katuosoite, :postinumero, :email, :puhelinnumero);");
    $stmt->bindValue(':etunimi', $_SESSION["etunimi"], PDO::PARAM_STR);
    $stmt->bindValue(':sukunimi', $_SESSION["sukunimi"], PDO::PARAM_STR);
    $stmt->bindValue(':katuosoite', $_SESSION["katuosoite"], PDO::PARAM_STR);
    $stmt->bindValue(':postinumero', $_SESSION["postinumero"], PDO::PARAM_STR);
    $stmt->bindValue(':email', $_SESSION["epost"], PDO::PARAM_STR);
    $stmt->bindValue(':puhelinnumero', $_SESSION["puhelinnumero"], PDO::PARAM_STR);
    $ok               = $stmt->execute();
    $previousCustomer = $dbh->lastInsertId(); //Äskettäin lisätyn tilaajan pääavain id
    
    if (!$ok) {
        print_r($stmt->errorInfo());
        echo 'Jotain pielessä asiakasta lisättäessä';
        $onnistuminen = false; //Tilaus epäonnistuu. Ei kiitetä tilauksesta lopussa
    } else {
        $stmt = $dbh->prepare("INSERT INTO Tilaus (Tilausnumero, Tilaaja, Status) VALUES (null, :orderer, :orderStatus);");
        $stmt->bindValue(':orderer', $previousCustomer, PDO::PARAM_INT);
        $stmt->bindValue(':orderStatus', "Käsitteillä", PDO::PARAM_STR);
        $ok            = $stmt->execute();
        $previousOrder = $dbh->lastInsertId(); //Äskettäin lisätyn tilauksen pääavaimen id
        if (!$ok) {
            print_r($stmt->errorInfo());
            echo 'Jotain pielessä tilausta lisättäessä';
            $onnistuminen = false;
            
        } else {
            //Tilauksen_tuotteet lisäys
            for ($i = 1; $i < count($_SESSION['product_amount']); $i++) {
                $stmt = $dbh->prepare("INSERT INTO Tilauksen_tuotteet VALUES (:orderNumber, :productID, :productAmount);");
                $stmt->bindValue(':orderNumber', $previousOrder, PDO::PARAM_INT);
                $stmt->bindValue(':productID', ($_SESSION['product_id'][$i]), PDO::PARAM_INT); //Tähän tarvitaan se sessiomuuttujasta saatu tuotenumero :)
                $stmt->bindValue(':productAmount', ($_SESSION['product_amount'][$i]), PDO::PARAM_INT);
                $ok = $stmt->execute();
                if (!$ok) {
                    print_r($stmt->errorInfo());
                    echo 'Jotain pielessä tilausta lisättäessä';
                    $onnistuminen = false;
                    $i            = count($_SESSION['product_amount']); //Tuotteiden lisäys loppuu, kun tietokannassa menee jokin pieleen
                }
                //Vähennetään varastosta
                $stmt = $dbh->prepare("UPDATE Tuote SET Varastossa= Varastossa - :maara WHERE Tuotenumero=:tuotenumero;");
                $stmt->bindValue(':maara', $_SESSION['product_amount'][$i], PDO::PARAM_INT);
                $stmt->bindValue(':tuotenumero', $_SESSION['product_id'][$i], PDO::PARAM_INT);
                $ok = $stmt->execute();
                if (!$ok) {
                    print_r($stmt->errorInfo());
                    echo 'Jotain pielessä tuotetta vähentäessä';
                    $onnistuminen = false;
                    $i            = count($_SESSION['product_amount']);
                }
            }
        }
    }
    //Tilaus onnistuu. Transaktio sitoutetaan tietokantaan
    if ($onnistuminen) {
        $dbh->commit();
        $etunimi  = $_SESSION["etunimi"];
        $sukunimi = $_SESSION["sukunimi"];
        $epost    = $_SESSION["epost"];
        echo '<p>Kiitos tilauksestasi, '.$etunimi.' '.$sukunimi.'!</p>';
        echo '<p>Laitoimme sinulle tilausvarmisteen osoitteeseen '.$epost.'</p>';
        echo '<p>Tilausnumerosi on:<b>' . $previousOrder . '</b>';
        echo '<a href="bs_etusivu.php">Palaa etusivulle</a>';
    }
    //Transaktio epäonnistuu jollain tasolla. Rollbäckätään!
    else {
        $dbh->rollback();
    }
}
//Jokin sessiomuuttuja puuttui. Tietokantaan ei edes kosketa.
else {
    echo 'Tilauksesta puuttui tietoja';
}
echo '</div>';
//Poistetaan sessiomuuttujat
session_unset();
?>
</body>
</html>