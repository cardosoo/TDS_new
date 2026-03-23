<?php
namespace base\Model;

use \TDS\Table;
use TDS\Query;

class Enseignement extends Table implements \Model\_Enseignement_interface_ {
    use \Model\_Enseignement_;

    /**
     * @var  ECUE[] $structEcueList
     */    
    public $structEcueList = null;
    /**
     * @var  Etape[] $structEtapeList
     */    
    public $structEtapeList = null;


    protected static function getFilterString($key, $value){
        if (is_array($value)) {
            return self::getFilterStringFromArray($key, $value);
        } else {
            return self::getFilterStringFromString($key, $value);
        }
    }
    
    protected static function getFilterStringFromString($key, $value){
        $v = (int)$value;
        if ($v>0){
            return "AND SE.{$key} = {$value}";
        } else {
            return "";
        } 
    }
    
    protected static function getFilterStringFromArray($key, $value){
        $filterArray = "";
        foreach($value as $val){
            $v = (int)$val;
            if ($v>0){
                if ($filterArray !==""){
                    $filterArray .= ", ";
                }
                $filterArray .= $v;
                
            }
            if ($filterArray !== ""){
                $filterArray = " AND SE.{$key} IN ({$filterArray})";
            }
            return $filterArray;
        }
    }


    //protected static function getModaliteSelector($modalite, $withSousEffectif, $nonPrioritaire){
     protected static function getModaliteSelector($modalite, $with){
         if (is_null($modalite)){
            $modalite=[0];
        }
        $T = "ENS";
        if ($with->withSousEffectif){
            if ($with->nonPrioritaire){
                $T="VEB";
            } else {
                $T = "VEBP";
            }
        }
        
        $selector = '';
        foreach($modalite as $key=>$value){
            switch (intval($value)){
                case 0 : 
                case 1 :                    
                    $selector .= ($selector===''?'':' OR ')." ({$T}.cm>0 )";
            }    
            switch (intval($value)){
                case 0 : 
                case 2 : 
                    $selector .= ($selector===''?'':' OR ')." ({$T}.ctd>0 )";
            }    
            switch (intval($value)){
                case 0 : 
                case 3 : 
                    $selector .= ($selector===''?'':' OR ')." ({$T}.td>0 )";
            }    
            switch (intval($value)){
                case 0 : 
                case 4 : 
                    $selector .= ($selector===''?'':' OR ')." ({$T}.tp>0 )";
            }            
            switch (intval($value)){
                case 0 : 
                case 5 : 
                    $selector .= ($selector===''?'':' OR ')." ({$T}.bonus>0 )";
            }            
        }
        if ($selector != "" ){
            $selector = "AND ({$selector})";
        }
        return $selector;
    }

/*
    protected static function complement1Search(){
        return "
        round(ENS.cm*100)/100 as mCM,
        round(ENS.ctd*100)/100 as mCTD,
        round(ENS.td*100)/100 as mTD,
        round(ENS.tp*100)/100 as mTP,
        round(ENS.bonus)/100 as mBonus
        ";
    }
    protected static function complement2Search(){
        return "
        ";
    }
*/    
    public function canEdit(){
        $app = \TDS\App::get();

        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin;

    }

// c'est la version qui allait bien avec searchMoodle
    public static function getIdList($codeList, $modalite, $with){
        $app = \TDS\App::get();

        $codeJoin = join("|", $codeList);

        $where = "string_to_array(code,'|')::text[] && string_to_array('{$codeJoin}','|')::text[]";
        //$selectorModalite = self::getModaliteSelector($modalite, $with->withSousEffectif, $with->nonPrioritaire);        
        $selectorModalite = self::getModaliteSelector($modalite, $with);        



        $sql = "
            SELECT ENS.id
            FROM enseignement as ENS
            LEFT JOIN voeu_enseignement_bilan as VEB on VEB.id = ENS.id
            LEFT JOIN voeu_enseignement_bilan_prioritaire as VEBP on VEBP.id = ENS.id
            WHERE {$where}
            {$selectorModalite}
        ";

        $rep =  $app::$db->getAll($sql);
        $idList = [];
        foreach($rep as $r){
            $idList[]=$r->id;
        }
        return $idList;
    }

    protected static function getJoinList(){
        $joinList = [];
        $joinList[] = ['ENS.voeu_enseignement_bilan', 'VEB'];
        $joinList[] = ['ENS.voeu_enseignement_bilan_prioritaire', 'VEBP'];
        return $joinList;
    }


    /**
     * C'est la fonction qui fait effectivement la fonction de recherche dans la base de données.
     * Elle permet de faire la recherche sur :
     * - $searchString : la chaine de recherche (dans intitulé, nom, nuac, code)
     * - modalités CM/CMTD/TD/TP
     * - actif (il faut que le actif soit dans $with)
     */
    public static function getIdCodeVarianteList($searchString, $modalite, $with){
        $app = \TDS\App::get();

        $where = [];
        if (! empty($searchString)){
            $where[] = "AND unaccent(ENS.intitule || ENS.nom || ENS.nuac || ENS.code) ILIKE unaccent('%{$searchString}%')";
        }
        //$selectorModalite = self::getModaliteSelector($modalite, $with->withSousEffectif, $with->nonPrioritaire);        
        $selectorModalite = self::getModaliteSelector($modalite, $with);        
        if ($with->withInactif){ //
            if ($with->onlyInactif){ //
                $where[] = 'AND NOT ENS.actif';
            } // sinon on prend tout donc pas besoin de clause
        } else {
            $where[] = 'AND ENS.actif';
        }
        
// var_dump(self::class);
        $joinList = static::getJoinList(); 
//var_dump($joinList);
        $app = \TDS\App::get();
        $q = new \TDS\Query($app::NS('Enseignement'), 'ENS');
        //$q->join('ENS.voeu_enseignement_bilan', 'VEB');
        //$q->join('ENS.voeu_enseignement_bilan_prioritaire', 'VEBP');
        foreach($joinList as $join){
            $q->join($join[0], $join[1]);        
        } 
        $q->addSQL("WHERE TRUE\n");
        $q->addSQL("{$selectorModalite}\n");
        foreach($where as $W){
            $q->addSQL("{$W}\n");
        }
//var_dump($q->getSQL());
        $rep = $q->exec();
        $idCodeVarianteList = [];
        foreach($rep as $re){
            $r = $re['ens'];
            if (is_null($r->code)){
                $r->code="XXXXXXXXXXXXXX";
            }
            if (empty($r->variante)){
                $r->variante = "";
            }
            foreach(explode('|', $r->variante) as $variante){
                foreach(explode('|', $r->code) as $code){ // cette boucle là est pour traiter l'éventualité d'un enseignement qui aurait plusieurs code ecue (normalement cela ne devrait pas arrivé !)
                    $idCodeVarianteList[$code.$variante]=[
                        'id' => $r->id,
                        'code' => $r->code,
                        'nuac' => $r->nuac,
                        'intitule' => $r->intitule,
                        'variante' => $code.$variante,
                    ];    
                }    
            }
        }
        return $idCodeVarianteList;
    }

/*
    public static function getIdCodeVarianteList_withoutQuery($searchString, $modalite, $with){
        $app = \TDS\App::get();

        $where = [];
        if (! empty($searchString)){
            $where[] = "AND unaccent(ENS.intitule || ENS.nom || ENS.nuac || ENS.code) ILIKE unaccent('%{$searchString}%')";
        }
        //$selectorModalite = self::getModaliteSelector($modalite, $with->withSousEffectif, $with->nonPrioritaire);        
        $selectorModalite = self::getModaliteSelector($modalite, $with);
        if ($with->withInactif){ //
            if ($with->onlyInactif){ //
                $where[] = 'AND NOT ENS.actif';
            } // sinon on prend tout donc pas besoin de clause
        } else {
            $where[] = 'AND ENS.actif';
        }

        $sql = "
            SELECT 
               ENS.id, 
               ENS.code,
               ENS.variante,
               ENS.nuac,
               ENS.intitule
            FROM enseignement as ENS
            LEFT JOIN voeu_enseignement_bilan as VEB on VEB.id = ENS.id
            LEFT JOIN voeu_enseignement_bilan_prioritaire as VEBP on VEBP.id = ENS.id
            WHERE TRUE
            {$selectorModalite}
        ";
        foreach($where as $W){
            $sql .= "{$W}\n";
        }

        $rep =  $app::$db->getAll($sql);
        $idCodeVarianteList = [];
        foreach($rep as $r){
            if (is_null($r->code)){
                $r->code="XXXXXXXXXXXXXX";
            }
            if (empty($r->variante)){
                $r->variante = "";
            }
            foreach(explode('|', $r->variante) as $variante){
                foreach(explode('|', $r->code) as $code){ // cette boucle là est pour traiter l'éventualité d'un enseignement qui aurait plusieurs code ecue (normalement cela ne devrait pas arrivé !)
                    $idCodeVarianteList[$code.$variante]=[
                        'id' => $r->id,
                        'code' => $r->code,
                        'nuac' => $r->nuac,
                        'intitule' => $r->intitule,
                        'variante' => $code.$variante,
                    ];    
                }    
            }
        }
        return $idCodeVarianteList;
    }
  */  


    /**
     * $searchString chaine de recherche
     * $filter tableau des filtres de recherche
     * $with objet indiquant les options pour la sortie
     * la clé de chaque élément contient le nom la table liée à filtrer
     * la valeur est soit un entier qui permet d'indiquer le num de la table liée à filter
     * ou un tableau d'entier, il s'agit alors de la liste des num de la table liée à filtrer
     * 
     * @param string $searchString
     * @param array $filter
     * @param object $with 
     * @param array $manque
     * @return array
     *
     * Version qui permet de faire la recherche directement sur la base de données 
     * puis filtre en utilisant les données issues de Moodle !
    */
    public static function search($searchString, $selectors, $modalite, $with){ 
        $app = \TDS\App::get();
        $struct = new \base\Struct();
        
        $idCodeVarianteList = static::getIdCodeVarianteList($searchString, $modalite, $with);
        $filter = $struct->buildFilterFromSelectors($selectors);
        $idCodeVarianteList = $struct->filterIdCodeList($filter, $idCodeVarianteList);

        $retList = [];
        foreach($idCodeVarianteList as $idCodeVariante){            
            $idCodeVariante['enseignement'] = ($app::NS('Enseignement'))::load($idCodeVariante['id']);

            $idCodeVariante['ecue'] = $struct->getECUEByCode( explode('|', $idCodeVariante['code'])[0]); // on ne prend que l'ECUE du premier code de la  liste
            $retList[] = $idCodeVariante;
        }
        return $retList;
    }

    //     
    /**
     * getStuctEcueList
     * renvoie la liste des ECUE auxquelles l'enseingment est rattaché
     * sous forme d'un tableau dont l'index est le codeECUE associé à l'enseignement 
     * 
     * @return \ECUE[]
     */
    function getStructEcueList(){
        if (!is_null($this->structEcueList)){
            return $this->structEcueList;
        }

        $app = \TDS\App::get();
        $struct = new \base\Struct();

        $codeList = explode('|', $this->code);
        $this->structEcueList = [];
        foreach($codeList as $code){
            $code = trim($code);
            $this->structEcueList[$code] = $struct->getECUEByCode($code);
        }
        return $this->structEcueList;
    }
   
    /**
     * getStructEtapeList()
     * renvoie les étapes auxquelles l'enseignement est attaché
     * sous forme d'un tableau dont le premier index est le codeECUE associé à l'enseignement 
     * 
     * @return \Etape[][]
     */
    function getStructEtapeList(){
        if (! is_null($this->structEtapeList)){
            return $this->structEtapeList;
        }

        $app = \TDS\App::get();
        $struct = new \base\Struct();

        $this->structEtapeList = [];

        foreach($this->getStructEcueList() as $code => $ecue){
            if (is_null($ecue)){
                $etapeList =  [];
            } else {
                $codeList = explode("|", $this->variante);
                if (count($codeList) == 1){
                    $etapeList = $ecue->getEtapes();
                } else {
                    $etapeList = [];
                    foreach($codeList as $code){
                        $code=trim($code);
                        $etapeList[] = $struct->getEtapeByCode($code);
                    }
                }
            }
            $this->structEtapeList[$code] = $etapeList;
        }
        return $this->structEtapeList;
    }


    function getGenericWithLink(){
        $gen = parent::getGeneric();
        $app = \TDS\App::get();
        return "<a href='/{$app::$appName}/enseignement/{$this->id}'>$gen</a>";
    }

    function getVoeu(){
        $app = \TDS\App::get();

        if (! $app::$auth->inBase) return null; // OC 
        $userId = $app::$auth->user->id;
        $q = new Query(Voeu::class, 'V');
        return $q->addSQL("
            WHERE {$q->V_personne} = {$userId}
            AND {$q->V_enseignement} = {$this->id}
        ")->getOne();    
    }

    function getNbEtu(){
        $nbEtu = 0;
        foreach( $this->__get('ecueList') as $ecue){
            $nbEtu += $ecue->nbetu();
        }
        return $nbEtu;
    }

    function getCorrespondantList(){
        $rep = [];
        foreach($this->__get("voeuList") as $V){
            if ($V->correspondant){
                $rep[] = $V->personne;
            }
        }
        return $rep;
    }

    function getMailCorrespondantList(){
        $rep = [];
        foreach( $this->getCorrespondantList() as $P){
            $rep[] = $P->email;
        }
        return $rep;
    }


    function getCharge(){
        $app = \TDS\App::get();

        // Calcul de la charge en hETD pour 1 élément du type
        $C = new \stdClass();
        $C->cm    = $this->s_cm   *$this->d_cm    *$app::$hETD['cm'];
        $C->ctd   = $this->s_ctd  *$this->d_ctd   *$app::$hETD['ctd'];
        $C->td    = $this->s_td   *$this->d_td    *$app::$hETD['td'];
        $C->tp    = $this->s_tp   *$this->d_tp    *$app::$hETD['tp'];
        $C->extra = $this->s_extra*$this->d_extra *$app::$hETD['extra'];

        return $C;
    }


    function getBesoins(){
        return $this->__get("enseignement_besoins")->besoins;
    }

    function getEquipeFromOSE(){
        $app = \TDS\App::get();

        $oseNS = "{$app::$appName}\OSE";

        $ose = new $oseNS;
        $fromOSE = [];
        $ecue =   $this->__get("enseignement_structure")->ecue;
        $codeList = explode('|', $ecue);
        foreach($codeList as $code){

            $equipe = $ose->getEquipe($code);
            $enseignement = $ose->findECUE($code);

            $enseignement['equipe']=$equipe;
            $fromOSE[$code]=$enseignement;
        }
        return $fromOSE;
    }

    function getEcueList(){
        return join("', '", explode('|', $this->__get("enseignement_structure")->ecue));
    }

    function completeForCreation(){
        $this->__set("typeue", 1);
        $this->__set("payeur",1);
    }



    function getIdWihtSameEcue(){
        $app = \TDS\App::get();

        $ecueList = $this->getEcueList();

        $tmp = $app::$db->getAll("
            SELECT DISTINCT
                SE.enseignement as id
            FROM structure_enseignement as SE
            WHERE SE.code_ecue in ('{$ecueList}')
        ");

        $idList = [];
        foreach($tmp as $t){
            $idList[]=$t->id;
        }
        $idList = join(",",  $idList);
        return $idList;
    }

    function getEnseignementWithSameEcue() {
        $app = \TDS\App::get();

        $idList = $this->getIdWihtSameEcue();
        return  ($app::NS('Enseignement'))::loadWhere(" id in ({$idList}) ");
    }





    function comparaisonOSE(){

        $oseNS = (\TDS\App::$appName)."\OSE";
        $ose = new $oseNS;

        $enseignement_structure = $this->__get('enseignement_structure');
        $ecue = $enseignement_structure->ecue;
        $ecueList =  explode('|', $ecue);
        $ecue = $ecueList[0]; // Attention, si il y a plusieurs ECUE on ne garde que le premier ! 

        $details = $ose->getDetails($ecue); 
        $EList = $this->getEnseignementWithSameEcue();
        $fromOSEList = $this->getEquipeFromOSE();

        $match = [];
        $inOSE = [];
        $inDB = [];
        $fOSE=[];

        foreach($fromOSEList as $fromOSE){

            foreach($fromOSE['equipe'] as $voeu){
                $fOSE[$voeu['ose']]=$voeu;
            }
        }

        foreach($this->__get('voeuList') as $voeu){
            $ose = $voeu->personne->ose;
            if (isset($fOSE[$ose])){
                $match[$ose]=[
                    'fromOSE' => $fOSE[$ose],
                    'fromDB' => $voeu,
                ];
            } else {
                $inDB[$ose]=$voeu;
            }
        }

        foreach($fOSE as $voeu){
            if ( ! isset($match[$voeu['ose']])){
                $inOSE[$voeu['ose']] = $voeu;
            }        
        }

        return [
            'details' => $details,
            'from' => $fromOSEList, // c'est l'équipe depuis OSE
            'Elist' => $EList,      // ce sont les enseignments avec la même ECUE
            'match' => $match,      // l'équpe qui coincide ans OSE et dans la DB
            'inOSE' => $inOSE,      // la partie de l'équipe qu'on trouve dans OSE seulement
            'inDB' => $inDB,        // la partie de l'équipe qu'on trouve dans la DB seulement
        ];
    }

    // indique si l'utilisateur désigné est correspondant d'au moins un des domaines auquel l'enseignement est rattaché
    // si $userId est null alors c'est l'utilisateur loggué qui est testé
    public  function isCorrespondantEnseignement( $userId = null){
        $app = \TDS\App::get();
        if (is_null($userId)){
            $userId = $app::$auth->user->id;
        }

        foreach($this->__get("voeuList") as $V ){
            if ( ($V->personne->id == $userId) & ($V->correspondant) )
                return true;
        }
        return false;
    }


    function isAttribuable(){
        return $this->attribuable and $this->actif;
    }

    public function withCommentaires(){
        $app = \TDS\App::get();
        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin;
    }

    public function withDocuments(){
        $app = \TDS\App::get();
        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin;
    }

    public function canEditDocuments(){
        $app = \TDS\App::get();
        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin;
    }

    public function canEditCommentaires(){
        $app = \TDS\App::get();
        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin;
    }

    // withOSE()
    public function withOSE(){
        return false;

        $app = \TDS\App::get();
        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin;
    }

    public function canEditOSE(){
        return false;

        $app = \TDS\App::get();
        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin;
    }

    // withDetails()
    public function withDetails(){
        $app = \TDS\App::get();
        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin;
    }

    public function canEditDetails(){
        $app = \TDS\App::get();
        return $app::$auth->isAdmin || $app::$auth->isSuperAdmin;
    }

    public function whichDetails(){
        return [
            'syllabus' => true,
            'besoins' => true,
        ];
    }
}        
        
