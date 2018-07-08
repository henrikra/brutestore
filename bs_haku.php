<?php
$sql = $dbh->prepare("SELECT DISTINCT Kategoria FROM `Tuote`"); //haetaan eri kategoriat distinct kyselyllä
$ok = $sql->execute();

if (!$ok) {
    print_r($sql->errorInfo());
}

echo '<div id="searchBar">';
echo <<<END
<form action="$_SERVER[PHP_SELF]" method="post">
Tuotenimihaku: <input type="text" name="searchBox"/>
<select name="categoryDropDown">
<option value="" selected="selected">Valitse kategoria</option>
END;
while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
	echo '<option value="'.$row["Kategoria"].'">'.$row["Kategoria"].'</option>';
}
echo <<<END
</select>
<input type="submit" name="searchBtn" value="Hae"/> 
</form></div>
END;

?>