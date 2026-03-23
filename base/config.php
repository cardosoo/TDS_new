<?php
$app = \TDS\App::get();
include($app::$pathList['secret']."/secret.php");

// permet de faire les initialisations
if (PHP_SAPI !== 'cli'){
    include_once '../TDS/urlRewriting.php'; // permet de faire le routage
}


// mettre ci-dessous ce qui doit être fait après les initialisation
$app::setLongName('Tableau de service de Base');
$app::setWebmaster('<a href=\'mailto:Olivier.Cardoso@u-paris.fr?subject=[TDS/base]&body='.urlencode("\n\n\n\n-- Merci de ne pas supprimer les lignes ci-dessous --\n".(print_r($_SERVER,true))).'>Message au Webmaster</a>');

$app::setProdMode(!in_array($_SERVER['SERVER_ADDR'], [ '192.168.1.81', '87.91.168.14']));

$firstYear = 2023;
$lastYear = 2024;

$officialYear = 2024;

$yearList = [];
for($year = $firstYear; $year < $lastYear+0.1; $year++){
    $yearList[]=$year;
}
$year = $app::setYear($officialYear, $yearList);

// $app::setCurrentYear("{$year}"); // on peut prévoir ici un mécanisme permettant de faire en sorte que l'année courante soit fixée dans les variables de session <?php

$app::openDatabase();

$app::$hETD['cm']= 1.5;
$app::$hETD['ctd']= 1.25;
$app::$hETD['td']= 1;
$app::$hETD['tp']= 1;
$app::$hETD['extra']= 1;
$app::$hETD['bonus']= 1;


$app::$texte = [
    'correspondants' => 'correspondants',
];


$nextYear=$year+1;
$app::$phaseList = [
    'maintenance' => (object)[
        "message" => "Le site est en maintenance jusqu'à nouvel ordre",
    ],
    'avant' => (object)[
        'voeuxPersonneLabel' => "Reports pour l'année {$year}-{$nextYear}",
        'voeuxEnseignantLabel' => "Reports pour l'année {$year}-{$nextYear}",
        'withAjouterVoeux' => false,
        'withStages' => false,
        'withEditStages' => false,
    ],
    'pre' => (object)[
        'voeuxPersonneLabel' => "Vœux pour l'année {$year}-{$nextYear}",
        'voeuxEnseignantLabel' => "Vœux pour l'année {$year}-{$nextYear}",
        'withAjouterVoeux' => true,
        'ajouterVoeu' => 'Ajouter ce Voeu',
        'modifierVoeu' => 'Modifier ce Voeu',
        'supprimerVoeu' => 'Supprimer ce Voeu',
        'withStages' => false,
        'withEditStages' => false,
    ],
    'post' => (object)[
        'voeuxPersonneLabel' => "Tableau de service probable pour l'année {$year}-{$nextYear}",
        'voeuxEnseignantLabel' => "Équipe probable pour l'année {$year}-{$nextYear}",
        'withAjouterVoeux' => false,
        'withStages' => false,
        'withEditStages' => false,
    ],
    'après' => (object)[
        'voeuxPersonneLabel' => "Tableau de service pour l'année {$year}-{$nextYear}",
        'voeuxEnseignantLabel' => "Équipe pour l'année {$year}-{$nextYear}",
        'withAjouterVoeux' => false,
        'withStages' => false,
        'withEditStages' => false,
    ],
];


//$app::$phase = 'maintenance';
//$app::$phase = 'avant';
$app::$phase = 'pre';
//$app::$phase = 'post';
//$app::$phase = 'après';

//var_dump($app::$phaseList[$app::$phase], $app::$phase);
