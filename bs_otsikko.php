<?php
echo '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">';
?>

<head>
<title>BruteStore.com</title>
<link rel = "stylesheet" type = "text/css" href = "bs_tyylit.css" />
<link rel="icon" 
      type="image/png" 
      href="favicon3.ico"/>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body>

<?php
error_reporting(E_ALL); // raportoidaan virheet
ini_set('display_errors', 'On');
session_start();
echo '<div id="otsikko">';
echo '<h1><a href="bs_etusivu.php">BruteStore.com</a></h1>';
echo '</div>';
// avataan tietokantayhteys
try {
    $dbh = new PDO('mysql:host=sql111.epizy.com;dbname=epiz_22254384_henrikra', "epiz_22254384", "2sVRzZrsH0n3");
	$dbh->query('SET CHARACTER SET utf8');
}
catch (PDOException $e) {
    die("Virhe: " . $e->getMessage());
}

?>