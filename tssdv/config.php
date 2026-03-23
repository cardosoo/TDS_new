<?php
use \tssdv\App;

// include  App::$basePath."/base/maintenance.php"; exit();

$app = App::get();
include($app::$pathList['secret']."/secret.php");

// permet de faire les initialisations
if (PHP_SAPI !== 'cli'){
    include_once '../TDS/urlRewriting.php'; // permet de faire le routage
}

// include  App::$basePath."/base/maintenance.php"; exit();

$historyFirstYear = 2020;
$firstYear = 2020;
$lastYear = 2025;

$formatter = new \IntlDateFormatter(
    'fr_FR',
    \IntlDateFormatter::FULL,
    \IntlDateFormatter::FULL,
    null, //'Europe/Paris',
    \IntlDateFormatter::GREGORIAN,
    'd LLLL Y'
);

$today = new DateTime();
$date2023 = DateTime::createFromFormat('Y-m-d H:i:s', '2023-11-01 05:00:00');
$date2024 = DateTime::createFromFormat('Y-m-d H:i:s', '2024-10-24 05:00:00');
$date2025 = DateTime::createFromFormat('Y-m-d H:i:s', '2025-10-22 05:00:00');


$officialYear = $today<=$date2025?2024:2025;
$officialYear = 2025;

App::$historyYearList = [];
for($year = $lastYear-1 ;  $year >= $historyFirstYear; $year--){
    App::$historyYearList[] = $year;
}

$yearList = [];
for($year = $firstYear; $year < $lastYear+0.1; $year++){
    $yearList[]=$year;
}
$year = App::setYear($officialYear, $yearList);

App::openDatabase();
App::$mail = 'ts.sdv.up@gmail.com';        

App::$hETD['cm']    = 1.5;
App::$hETD['ctd']   = 1.25;
App::$hETD['td']    = 1;
App::$hETD['tp']    = 1;
App::$hETD['extra'] = 1;
$app::$hETD['bonus']= 1;

App::$etatTS = [
    1 => 'en latence',
    2 => 'à valider',
    3 => 'validé'
];



if ($year <= '2022'){
    //$today = new DateTime();
    $debutSaisie = DateTime::createFromFormat('Y-m-d H:i:s', '2022-11-01 08:00:00');
    $finSaisie = DateTime::createFromFormat('Y-m-d H:i:s', '2023-06-09 23:59:59');
    $debutValidation = clone $finSaisie;
    $debutValidation->add(new DateInterval('PT1S'));
    $finValidation = DateTime::createFromFormat('Y-m-d H:i:s', '2023-06-15 23:59:59');

    App::$texte = [
        'debutSaisie' => $formatter->format($debutSaisie), 
        'finSaisie' => $formatter->format($finSaisie),
        'debutValidation' => $formatter->format($debutValidation),
        'finValidation' => $formatter->format($finValidation),
        'correspondants' => 'responsables',
        'mailSDV' => App::$mail,
        'mailRHE' => 'rhe.sdv@u-paris.fr',
        //'sendMailOnModif' => false,
        'sendMailOnModif' => $serverProd?'rhe.sdv@u-paris.fr':'olivier.cardoso@gmail.com',
        ];
    

}

if ($year == '2023'){
    //$today = new DateTime();
    $debutSaisie = $date2023; //DateTime::createFromFormat('Y-m-d H:i:s', '2023-11-01 08:00:00');
    $finSaisie = DateTime::createFromFormat('Y-m-d H:i:s', '2023-06-09 23:59:59');
    $debutValidation = clone $debutSaisie;
    $debutValidation->add(new DateInterval('PT1S'));
    $finValidation = DateTime::createFromFormat('Y-m-d H:i:s', '2023-06-15 23:59:59');

    App::$texte = [
        'debutSaisie' => $formatter->format($debutSaisie), 
        'finSaisie' => $formatter->format($finSaisie),
        'debutValidation' => $formatter->format($debutValidation),
        'finValidation' => $formatter->format($finValidation),
        'correspondants' => 'responsables',
        'mailSDV' => App::$mail,
        'mailRHE' => 'rhe.sdv@u-paris.fr',
        //'sendMailOnModif' => false,
        'sendMailOnModif' => $serverProd?'rhe.sdv@u-paris.fr':'olivier.cardoso@gmail.com',
        ];
    
}


if ($year == '2024'){
    //$today = new DateTime();
    $debutSaisie = $date2024;
    $finSaisie = DateTime::createFromFormat('Y-m-d H:i:s', '2025-06-09 23:59:59');
    $debutValidation = clone $debutSaisie;
    $debutValidation->add(new DateInterval('PT1S'));
    $finValidation = DateTime::createFromFormat('Y-m-d H:i:s', '2025-06-15 23:59:59');

    App::$texte = [
        'debutSaisie' => $formatter->format($debutSaisie), 
        'finSaisie' => $formatter->format($finSaisie),
        'debutValidation' => $formatter->format($debutValidation),
        'finValidation' => $formatter->format($finValidation),
        'correspondants' => 'responsables',
        'mailSDV' => App::$mail,
        'mailRHE' => 'rhe.sdv@u-paris.fr',
        'sendMailOnModif' => false,
        //'sendMailOnModif' => $serverProd?'rhe.sdv@u-paris.fr':'olivier.cardoso@gmail.com',
        ];
    
}

if ($year == '2025'){
    //$today = new DateTime();
    $debutSaisie = $date2025;
    $finSaisie = DateTime::createFromFormat('Y-m-d H:i:s', '2026-06-09 23:59:59');
    $debutValidation = clone $debutSaisie;
    $debutValidation->add(new DateInterval('PT1S'));
    $finValidation = DateTime::createFromFormat('Y-m-d H:i:s', '2026-06-15 23:59:59');

    App::$texte = [
        'debutSaisie' => $formatter->format($debutSaisie), 
        'finSaisie' => $formatter->format($finSaisie),
        'debutValidation' => $formatter->format($debutValidation),
        'finValidation' => $formatter->format($finValidation),
        'correspondants' => 'responsables',
        'mailSDV' => App::$mail,
        'mailRHE' => 'rhe.sdv@u-paris.fr',
        'sendMailOnModif' => false,
        //'sendMailOnModif' => $serverProd?'rhe.sdv@u-paris.fr':'olivier.cardoso@gmail.com',
        ];
    
}


$nextYear = $year+1;
App::$phaseList = [
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
    'saisie' => (object)[
        'voeuxPersonneLabel' => "Temps de service prévisionnels  pour l'année {$year}-{$nextYear}",
        'voeuxEnseignantLabel' => "Temps de service prévisionnels  pour l'année {$year}-{$nextYear}",
        'withAjouterVoeux' => true,
        'ajouterVoeu' => 'Ajouter cet enseignement',
        'modifierVoeu' => 'Modifier cet enseignement',
        'supprimerVoeu' => 'Supprimer cet enseignement',
        'messageSupprimerVoeu' => 'Confirmer la suppression de cet enseignement',
        'withStages' => true,
        'withEditStages' => true,
    ],
    'validation' => (object)[
        'voeuxPersonneLabel' => "Temps de service en cours de validation  pour l'année {$year}-{$nextYear}",
        'voeuxEnseignantLabel' => "Temps de service en cours de validation  pour l'année  {$year}-{$nextYear}",
        'withAjouterVoeux' => true,
        'ajouterVoeu' => 'Ajouter cet enseignement',
        'modifierVoeu' => 'Modifier cet enseignement',
        'supprimerVoeu' => 'Supprimer cet enseignement',
        'messageSupprimerVoeu' => 'Confirmer la suppression de cet enseignement',
        'withStages' => true,
        'withEditStages' => true,
    ],
    'après' => (object)[
        'voeuxPersonneLabel' => "Temps de service pour l'année {$year}-{$nextYear}",
        'voeuxEnseignantLabel' => "Temps de service  pour l'année  {$year}-{$nextYear}",
        'withAjouterVoeux' => false,
        'withStages' => true,
        'withEditStages' => true,
    ],
    'future' => (object)[
        'voeuxPersonneLabel' => "Future {$year}-{$nextYear}",
        'voeuxEnseignantLabel' => "Future {$year}-{$nextYear}",
        'withAjouterVoeux' => false,
        'withStages' => false,
        'withEditStages' => false,
    ],
];



App::$phase= 'avant';

if ( ($today >= $debutSaisie)  && ($today < $finSaisie) ) {
    App::$phase = 'saisie';
}

if ( ($today >= $debutValidation)  && ($today < $finValidation) ) {
    App::$phase = 'validation';
}

if ( ($today > $finValidation) ) {
    App::$phase = 'après';
}


if (App::$currentYear < App::$officialYear){
    App::$phase = 'passée';
}

if (App::$currentYear > App::$officialYear){
    App::$phase = 'future';
}

//App::$phase = 'maintenance';
//App::$phase = 'avant';
//App::$phase = 'saisie';
//App::$phase = 'validation';
//App::$phase = 'après';
//App::$phase = 'passée';
//App::$phase = 'future';

//var_dump(App::$phaseList[App::$phase], App::$phase);
//exit();