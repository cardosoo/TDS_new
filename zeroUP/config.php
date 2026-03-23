<?php

// permet de faire les initialisations
if (PHP_SAPI !== 'cli'){
    include_once '../TDS/urlRewriting.php'; // permet de faire le routage
}

$app = \TDS\App::get();

// mettre ci-dessous ce qui doit être fait après les initialisation
$app::setSecretkey('secret');
$app::setLongName('Tableau de service de Base');
$app::setWebmaster('<a href=\'mailto:Olivier.Cardoso@u-paris.fr?subject=[TDS/zeroUP&body='.urlencode("\n\n\n\n-- Merci de ne pas supprimer les lignes ci-dessous --\n".(print_r($_SERVER,true))).'>Message au Webmaster</a>');
$app::setProdMode(false);
$app::setCurrentYear(''); // on peut prévoir ici un mécanisme permettant de faire en sorte que l'année courante soit fixée dans les variables de session <?php


$app::$baseUser = 'xxxxxx';
$app::$basePwd = 'xxxxxx';
$app::openDatabase();
