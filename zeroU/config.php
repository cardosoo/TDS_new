<?php
use \zeroU\App;

// permet de faire les initialisations
if (PHP_SAPI !== 'cli'){
    include_once '../TDS/urlRewriting.php'; // permet de faire le routage
}

// mettre ci-dessous ce qui doit être fait après les initialisation
App::setSecretkey('secret');
App::setLongName('Tableau de service de Base');
App::setWebmaster('<a href=\'mailto:Olivier.Cardoso@u-paris.fr?subject=[TDS/zeroU&body='.urlencode("\n\n\n\n-- Merci de ne pas supprimer les lignes ci-dessous --\n".(print_r($_SERVER,true))).'>Message au Webmaster</a>');
App::setProdMode(false);

$officialYear = '';
$yearList = [];
$year = App::setYear($officialYear, $yearList);

App::$baseUser = 'xxxxxx';
App::$basePwd = 'xxxxxx';
App::openDatabase();
