<?php

use \service\App;
$app =  App::get();

include($app::$pathList['secret']."/secret.php");

// permet de faire les initialisations
if (PHP_SAPI !== 'cli'){
    include_once '../TDS/urlRewriting.php'; // permet de faire le routage
}

$app::setLongName('Tableau de service de Foire');
$app::setWebmaster('<a href=\'mailto:Olivier.Cardoso@u-paris.fr?subject=[TDS/foire&body='.urlencode("\n\n\n\n-- Merci de ne pas supprimer les lignes ci-dessous --\n".(print_r($_SERVER,true))).'>Message au Webmaster</a>');


$keepDevForMe = true;
/* $serverProd= false; */

$app::setProdMode($serverProd);
if (!$keepDevForMe){
    if ( (!$serverProd) && (!$userDev)) {
        header('Location: https://foire.physique.u-paris.fr'.$_SERVER['REQUEST_URI']);
        exit();
    }
}

$today = new DateTime();

$basculeFoire = DateTime::createFromFormat('Y-m-d H:i:s', '2025-05-12 08:00:00'); 

// 2025 est officiel
$officialYear = 2025; //$today>$basculeFoire?2025:2024;
$officialYear = filter_input(INPUT_GET, 'year', FILTER_VALIDATE_INT)??$officialYear;

$historyFirstYear = 2025;
$firstYear = 2025;
$lastYear = 2025; #$officialYear; # 2024; 

$app::$historyYearList = [];
for($year = $officialYear-1 ;  $year >= $historyFirstYear; $year--){
    $app::$historyYearList[] = $year;
}

$yearList = [];
for($year = $firstYear; $year < $lastYear+0.1; $year++){
    $yearList[]=$year;
}
$year = $app::setYear($officialYear, $yearList);
$nextYear=$year+1;

//$app::$vacationYear = '2025';

App::openDatabase();

App::$hETD['cm']= 1.5;
App::$hETD['ctd']= 1.25;
App::$hETD['td']= 1;
App::$hETD['tp']= 1;
App::$hETD['extra']= 1;
$app::$hETD['bonus']= 1;

if ($year == '2025'){
    App::$texte = [
        'chargeReference' => 192,
        'chargeReferenceNm' => 192,
        'debutPanier' => 'lundi 12/05/2025',
        'debutVoeux' => 'lundi 26/05/2025',
        'limiteVoeux' => 'dimanche 1/06/2025',
        'debutDiagonalisation' => 'lundi 2/06/2025',
        'finDiagonalisation' => 'lundi 9/06/2025',
        'dateFoire' => 'mardi 10 juin 2025',
        'lieuFoire' => 'dans le Hall du bâtiment Condorcet',
        'exempleServiceNm' => 180,
        'exempleService' => 205,
        'correspondants' => 'correspondants',
    ];

    $debutPanier = DateTime::createFromFormat('Y-m-d H:i:s', '2025-05-12 08:00:00'); 
    $finPanier = DateTime::createFromFormat('Y-m-d H:i:s', '2025-05-25 07:59:59');
    
    $debutVoeux = clone $finPanier;
    $debutVoeux->add(new DateInterval('PT1S'));
    $finVoeux = DateTime::createFromFormat('Y-m-d H:i:s', '2025-06-01 23:59:59');
    
    $debutDiagonalisation = clone $finVoeux;
    $debutDiagonalisation->add(new DateInterval('PT1S'));
    $finDiagonalisation = DateTime::createFromFormat('Y-m-d H:i:s', '2025-06-09 17:59:59');    
}




App::$phaseList = [
    'maintenance' => (object)[
        "message" => "Le site est en maintenance jusqu'à nouvel ordre",
    ],
    'future' => (object)[
        'voeuxPersonneLabel' => "Reports pour l'année {$year}-{$nextYear}",
        'voeuxEnseignantLabel' => "Reports pour l'année {$year}-{$nextYear}",
        'withAjouterVoeux' => false,
        'withPanier' => false,
        'withStages' => TRUE,
        'withEditStages' => TRUE,
    ],
        'avant' => (object)[
        'voeuxPersonneLabel' => "Reports pour l'année {$year}-{$nextYear}",
        'voeuxEnseignantLabel' => "Reports pour l'année {$year}-{$nextYear}",
        'withAjouterVoeux' => false,
        'withPanier' => false,
        'withStages' => TRUE,
        'withEditStages' => TRUE,
    ],
    'avantAvecStage' => (object)[
        'voeuxPersonneLabel' => "Reports pour l'année {$year}-{$nextYear}",
        'voeuxEnseignantLabel' => "Reports pour l'année {$year}-{$nextYear}",
        'withAjouterVoeux' => false,
        'withPanier' => false,
        'withStages' => TRUE,
        'withEditStages' => TRUE,
    ],
    'paniers' => (object)[
        'voeuxPersonneLabel' => "Reports pour l'année {$year}-{$nextYear}",
        'voeuxEnseignantLabel' => "Reports pour l'année {$year}-{$nextYear}",
        'withAjouterVoeux' => false,
        'withPanier' => true,
        'withStages' => TRUE,
        'withEditStages' => TRUE,
    ],
    'voeux' => (object)[
        'voeuxPersonneLabel' => "Vœux pour l'année {$year}-{$nextYear}",
        'voeuxEnseignantLabel' => "Vœux pour l'année {$year}-{$nextYear}",
        'withAjouterVoeux' => true,
        'withPanier' => true,
        'ajouterVoeu' => 'Ajouter ce Voeu',
        'modifierVoeu' => 'Modifier ce Voeu',
        'supprimerVoeu' => 'Supprimer ce Voeu',
        'messageSupprimerVoeu' => 'Confirmer la suppression du voeu',
        'withStages' => TRUE,
        'withEditStages' => TRUE,
    ],
    'diagonalisation' => (object)[
        'voeuxPersonneLabel' => "Tableau de service probable pour l'année {$year}-{$nextYear}",
        'voeuxEnseignantLabel' => "Équipe probable pour l'année {$year}-{$nextYear}",
        'withAjouterVoeux' => false,
        'withPanier' => true,
        'withStages' => TRUE,
        'withEditStages' => TRUE,
    ],
    'après' => (object)[
        'voeuxPersonneLabel' => "Tableau de service pour l'année {$year}-{$nextYear}",
        'voeuxEnseignantLabel' => "Équipe pour l'année {$year}-{$nextYear}",
        'withAjouterVoeux' => false,
        'withPanier' => false,
        'withStages' => TRUE,
        'withEditStages' => TRUE,
    ],
    'passée' => (object)[
        'voeuxPersonneLabel' => "Tableau de service pour l'année {$year}-{$nextYear}",
        'voeuxEnseignantLabel' => "Équipe pour l'année {$year}-{$nextYear}",
        'withAjouterVoeux' => false,
        'withPanier' => false,
        'withStages' => false,
        'withEditStages' => false,
    ],
];

App::$phase= 'avant';

if ( ($today >= $debutPanier)  && ($today < $finPanier) ) {
    App::$phase = 'paniers';
}

if ( ($today >= $debutVoeux)  && ($today < $finVoeux) ) {
    App::$phase = 'voeux';
}

if ( ($today >= $debutDiagonalisation)  && ($today < $finDiagonalisation) ) {
    App::$phase = 'diagonalisation';
}

if ( ($today > $finDiagonalisation) ) {
    App::$phase = 'après';
}

if (App::$currentYear < App::$officialYear){
    App::$phase = 'passée';
}

if (App::$currentYear > App::$officialYear){
    App::$phase = 'future';
}

$app = \TDS\App::get();

