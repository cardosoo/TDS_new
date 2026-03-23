<?php
namespace foire;

use \foire\Model\User;

class App extends \base\App {
    static string $service = 'UPCite';
    static string $structure = 'UPCite';
    public static int $chargeUFR; // cette valeur est mise à jour dans le config.php
}