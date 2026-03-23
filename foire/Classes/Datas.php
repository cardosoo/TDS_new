<?php


namespace foire;

class Datas extends \base\Datas {

    public static array $codeEOTPList = [
        'AGPCI0' => ['etape' => 'AG', 'EOTP' => 'P7GSPHI99C36', 'ETOP_name' => 'EOTP - PREPA AGREG SP PHYSI - S36FPRO'],
        'DUS231' => ['etape' => 'DU', 'EOTP' => 'FS36I99LGPH3', 'ETOP_name' => 'EOTP - SUIVI HC LICENCE 3 - S36HC'],
        'DUS232' => ['etape' => 'DU', 'EOTP' => 'FS36I99LGPH2', 'ETOP_name' => 'EOTP - SUIVI HC LICENCE 2 - S36HC'],
        'DUS233' => ['etape' => 'DU', 'EOTP' => 'FS36I99LGPH3', 'ETOP_name' => 'EOTP - SUIVI HC LICENCE 3 - S36HC'],
        'IGSPF1' => ['etape' => 'IG', 'EOTP' => 'P7ISUPF99C36', 'ETOP_name' => 'EOTP - MASTER 2 ISUPFERE - S36FPRO'],
        'IGSPF2' => ['etape' => 'IG', 'EOTP' => 'P7ISUPF99C36', 'ETOP_name' => 'EOTP - MASTER 2 ISUPFERE - S36FPRO'],
        'LGPH01' => ['etape' => 'LG', 'EOTP' => 'FS36I99LGPH1', 'ETOP_name' => 'EOTP - SUIVI HC LICENCE 1 - S36HC'],
        'LGPH51' => ['etape' => 'LG', 'EOTP' => 'FS51I99LIC01', 'ETOP_name' => 'EOTP - LICENCE 1 CUPGE DSE - S36HC'],
        'LGPH91' => ['etape' => 'LG', 'EOTP' => 'FS36I99LGPH1', 'ETOP_name' => 'EOTP - SUIVI HC LICENCE 1 - S36HC'],
        'LGPH81' => ['etape' => 'LG', 'EOTP' => 'FS36I99LGPH1', 'ETOP_name' => 'EOTP - SUIVI HC LICENCE 1 - S36HC'],
        'LGPH41' => ['etape' => 'LG', 'EOTP' => 'FS36I99LGPH1', 'ETOP_name' => 'EOTP - SUIVI HC LICENCE 1 - S36HC'],
        'LGPH92' => ['etape' => 'LG', 'EOTP' => 'FS36I99LGPH2', 'ETOP_name' => 'EOTP - SUIVI HC LICENCE 2 - S36HC'],
        'LGPH22' => ['etape' => 'LG', 'EOTP' => 'FS36I99LGPH2', 'ETOP_name' => 'EOTP - SUIVI HC LICENCE 2 - S36HC'],
        'LGPH02' => ['etape' => 'LG', 'EOTP' => 'FS36I99LGPH2', 'ETOP_name' => 'EOTP - SUIVI HC LICENCE 2 - S36HC'],
        'LGPH42' => ['etape' => 'LG', 'EOTP' => 'FS36I99LGPH2', 'ETOP_name' => 'EOTP - SUIVI HC LICENCE 2 - S36HC'],
        'LGPH52' => ['etape' => 'LG', 'EOTP' => 'FS51I99LIC02', 'ETOP_name' => 'EOTP - LICENCE 2 CUPGE DSE - S36HC'],
        'LGPH82' => ['etape' => 'LG', 'EOTP' => 'FS36I99LGPH2', 'ETOP_name' => 'EOTP - SUIVI HC LICENCE 2 - S36HC'],
        'LGPH03' => ['etape' => 'LG', 'EOTP' => 'FS36I99LGPH3', 'ETOP_name' => 'EOTP - SUIVI HC LICENCE 3 - S36HC'],
        'LGPH43' => ['etape' => 'LG', 'EOTP' => 'FS36I99LGPH3', 'ETOP_name' => 'EOTP - SUIVI HC LICENCE 3 - S36HC'],
        'LGPH63' => ['etape' => 'LG', 'EOTP' => 'FS36I99LGPH3', 'ETOP_name' => 'EOTP - SUIVI HC LICENCE 3 - S36HC'],
        'LGPH83' => ['etape' => 'LG', 'EOTP' => 'FS36I99LGPH3', 'ETOP_name' => 'EOTP - SUIVI HC LICENCE 3 - S36HC'],
        'LGPH93' => ['etape' => 'LG', 'EOTP' => 'FS36I99LGPH3', 'ETOP_name' => 'EOTP - SUIVI HC LICENCE 3 - S36HC'],
        'LPCP13' => ['etape' => 'LP', 'EOTP' => 'FS36P99LPCP1', 'ETOP_name' => 'EOTP - LICENCE PRO ANALYSE DES MATERIAUX - S36FPRO'],
        'LPEE23' => ['etape' => 'LP', 'EOTP' => 'P7LPSTP99A36', 'ETOP_name' => 'EOTP - LP TPE - S36FPRO'],
        'MASD61' => ['etape' => 'MA', 'EOTP' => 'FS36I99MAS64', 'ETOP_name' => 'EOTP - SUIVI HC MEEF1 - S36HC'],
        'MAPFF1' => ['etape' => 'MA', 'EOTP' => 'FS36P99MAPF1', 'ETOP_name' => 'EOTP - M1 IPE - Ingenierie Physique des Energie - S36FPRO'],
        'MAPFG1' => ['etape' => 'MA', 'EOTP' => 'FS36I99MAS01', 'ETOP_name' => 'EOTP - SUIVI HC MASTER 1 - S36HC'],
        'MAPF11' => ['etape' => 'MA', 'EOTP' => 'FS36I99MAS01', 'ETOP_name' => 'EOTP - SUIVI HC MASTER 1 - S36HC'],
        'MAPF21' => ['etape' => 'MA', 'EOTP' => 'FS36I99MAS01', 'ETOP_name' => 'EOTP - SUIVI HC MASTER 1 - S36HC'],
        'MAPF31' => ['etape' => 'MA', 'EOTP' => 'FS36I99MAS01', 'ETOP_name' => 'EOTP - SUIVI HC MASTER 1 - S36HC'],
        'MASD62' => ['etape' => 'MA', 'EOTP' => 'FS36I99MAS65', 'ETOP_name' => 'EOTP - SUIVI HC MEEF2 - S36HC'],
        'MAPFR2' => ['etape' => 'MA', 'EOTP' => 'FS36I99MAS02', 'ETOP_name' => 'EOTP - SUIVI HC MASTER 2 - S36HC'],
        'MAPFB2' => ['etape' => 'MA', 'EOTP' => 'FS36I99MAS02', 'ETOP_name' => 'EOTP - SUIVI HC MASTER 2 - S36HC'],
        'MAPFK2' => ['etape' => 'MA', 'EOTP' => 'FS36I99MAS02', 'ETOP_name' => 'EOTP - SUIVI HC MASTER 2 - S36HC'],
        'MAPFS2' => ['etape' => 'MA', 'EOTP' => 'FS36I99MAS02', 'ETOP_name' => 'EOTP - SUIVI HC MASTER 2 - S36HC'],
        'MAPFO2' => ['etape' => 'MA', 'EOTP' => 'FS36I99MAS02', 'ETOP_name' => 'EOTP - SUIVI HC MASTER 2 - S36HC'],
        'MAPFD2' => ['etape' => 'MA', 'EOTP' => 'FS36I99MAS02', 'ETOP_name' => 'EOTP - SUIVI HC MASTER 2 - S36HC'],
        'MAPFL2' => ['etape' => 'MA', 'EOTP' => 'FS36I99MAS02', 'ETOP_name' => 'EOTP - SUIVI HC MASTER 2 - S36HC'],
        'MAPFP2' => ['etape' => 'MA', 'EOTP' => 'FS36I99MAS02', 'ETOP_name' => 'EOTP - SUIVI HC MASTER 2 - S36HC'],
        'MAPFQ2' => ['etape' => 'MA', 'EOTP' => 'FS36I99MAS02', 'ETOP_name' => 'EOTP - SUIVI HC MASTER 2 - S36HC'],
        'MAPFG2' => ['etape' => 'MA', 'EOTP' => 'FS36I99MAS02', 'ETOP_name' => 'EOTP - SUIVI HC MASTER 2 - S36HC'],
        'MAPFF2' => ['etape' => 'MA', 'EOTP' => 'FS36P99MAPF2', 'ETOP_name' => 'EOTP - M2 IPE - Ingenierie Physique des Energie - S36FPRO'],
        'MAPFT2' => ['etape' => 'MA', 'EOTP' => 'FS36I99MAS02', 'ETOP_name' => 'EOTP - SUIVI HC MASTER 2 - S36HC'],
    ];
}
