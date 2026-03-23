/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(4) "Labo"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(6) "Statut"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(9) "Situation"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(8) "Personne"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(12) "Enseignement"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(6) "TypeUE"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(6) "Payeur"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(4) "Role"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(5) "actAs"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(4) "Voeu"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(10) "Composante"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(6) "Cursus"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(8) "Maquette"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(7) "Diplome"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(5) "Etape"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(11) "responsable"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(8) "Semestre"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(2) "UE"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(4) "ECUE"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(8) "EtapeOSE"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(14) "etape_personne"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(18) "etape_enseignement"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(20) "commentaire_personne"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(24) "commentaire_enseignement"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(20) "commentaire_maquette"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(22) "commentaire_composante"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(18) "personne_situation"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(7) "Cat1Ref"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(6) "CatRef"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(11) "Referentiel"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(7) "FoncRef"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(16) "personne_foncRef"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(16) "voeu_bilan_ligne"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(19) "voeu_personne_bilan"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(18) "voeu_detail_heures"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(23) "voeu_enseignement_bilan"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(35) "voeu_enseignement_bilan_prioritaire"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(20) "enseignement_besoins"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(22) "structure_enseignement"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(22) "enseignement_structure"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(29) "enseignement_etudiant_details"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(20) "enseignement_periode"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(15) "personne_charge"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(28) "personne_situation_reduction"
/home/olivier/pourGIT/TDS2024/src/TDS/TDS/Classes/Model/Model.php:68:
string(27) "personne_referentiel_heures"

-- ****************************** Labo as labo
CREATE TABLE labo(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    nom                     character varying(100)   DEFAULT ''::character varying,
    acronyme                character varying(100)   DEFAULT ''::character varying,
    url                     character varying(100)   DEFAULT ''::character varying    
);

-- ****************************** Statut as statut
CREATE TABLE statut(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    nom                     character varying(100)   DEFAULT ''::character varying,
    obligation              integer                  DEFAULT 192::integer    
);

-- ****************************** Situation as situation
CREATE TABLE situation(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    nom                     character varying(100)   DEFAULT ''::character varying,
    ose                     character varying(100)   DEFAULT ''::character varying,
    reduction               integer                  DEFAULT 0::integer,
    public                  boolean                  DEFAULT 'FALSE'::boolean    
);

-- ****************************** Personne as personne
CREATE TABLE personne(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    uid                     character varying(100)   DEFAULT ''::character varying,
    ose                     character varying(100)   DEFAULT ''::character varying,
    nom                     character varying(100)   DEFAULT ''::character varying,
    prenom                  character varying(100)   DEFAULT ''::character varying,
    adresse                 text                     DEFAULT ''::text,
    tel1                    character varying(100)   DEFAULT ''::character varying,
    tel2                    character varying(100)   DEFAULT ''::character varying,
    email                   character varying(100)   DEFAULT ''::character varying,
    info                    text                     DEFAULT ''::text,
    statut                  integer                  DEFAULT 0::integer,
    FOREIGN KEY(statut) REFERENCES statut(id),
    situation               integer                  DEFAULT 0::integer,
    FOREIGN KEY(situation) REFERENCES situation(id),
    labo                    integer                  DEFAULT 0::integer,
    FOREIGN KEY(labo) REFERENCES labo(id)    
);

-- ****************************** Enseignement as enseignement
CREATE TABLE enseignement(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    nuac                    character varying(100)   DEFAULT ''::character varying,
    code                    character varying(100)   DEFAULT ''::character varying,
    nom                     character varying(100)   DEFAULT ''::character varying,
    intitule                character varying(100)   DEFAULT ''::character varying,
    attribuable             boolean                  DEFAULT 'FALSE'::boolean,
    cm                      real                     DEFAULT '0'::real,
    td                      real                     DEFAULT '0'::real,
    ctd                     real                     DEFAULT '0'::real,
    tp                      real                     DEFAULT '0'::real,
    extra                   real                     DEFAULT '0'::real,
    s_cm                    real                     DEFAULT '1'::real,
    s_td                    real                     DEFAULT '1'::real,
    s_ctd                   real                     DEFAULT '1'::real,
    s_tp                    real                     DEFAULT '1'::real,
    s_extra                 real                     DEFAULT '1'::real,
    i_cm                    real                     DEFAULT '1'::real,
    i_td                    real                     DEFAULT '1'::real,
    i_ctd                   real                     DEFAULT '1'::real,
    i_tp                    real                     DEFAULT '1'::real,
    i_extra                 real                     DEFAULT '1'::real,
    d_cm                    real                     DEFAULT '0'::real,
    d_td                    real                     DEFAULT '0'::real,
    d_ctd                   real                     DEFAULT '0'::real,
    d_tp                    real                     DEFAULT '0'::real,
    d_extra                 real                     DEFAULT '0'::real,
    n_cm                    real                     DEFAULT '1'::real,
    n_td                    real                     DEFAULT '1'::real,
    n_ctd                   real                     DEFAULT '1'::real,
    n_tp                    real                     DEFAULT '1'::real,
    n_extra                 real                     DEFAULT '1'::real,
    m_cm                    real                     DEFAULT '0'::real,
    m_td                    real                     DEFAULT '0'::real,
    m_ctd                   real                     DEFAULT '0'::real,
    m_tp                    real                     DEFAULT '0'::real,
    m_extra                 real                     DEFAULT '0'::real,
    bonus                   real                     DEFAULT '0'::real,
    syllabus                text                     DEFAULT ''::text,
    url                     text                     DEFAULT ''::text,
    typeue                  integer                  DEFAULT 0::integer,
    FOREIGN KEY(typeue) REFERENCES typeue(id),
    payeur                  integer                  DEFAULT 0::integer,
    FOREIGN KEY(payeur) REFERENCES payeur(id)    
);

-- ****************************** TypeUE as typeue
CREATE TABLE typeue(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    nom                     character varying(100)   DEFAULT ''::character varying    
);

-- ****************************** Payeur as payeur
CREATE TABLE payeur(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    nom                     character varying(100)   DEFAULT ''::character varying    
);

-- ****************************** Role as role
CREATE TABLE role(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    nom                     character varying(100)   DEFAULT ''::character varying,
    auth                    text                     DEFAULT ''::text    
);

-- ****************************** actAs as actas
CREATE TABLE actas(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    personne                integer                  DEFAULT 0::integer,
    FOREIGN KEY(personne) REFERENCES personne(id),
    role                    integer                  DEFAULT 0::integer,
    FOREIGN KEY(role) REFERENCES role(id)    
);

-- ****************************** Voeu as voeu
CREATE TABLE voeu(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    personne                integer                  DEFAULT 0::integer,
    FOREIGN KEY(personne) REFERENCES personne(id),
    enseignement            integer                  DEFAULT 0::integer,
    FOREIGN KEY(enseignement) REFERENCES enseignement(id),
    cm                      real                     DEFAULT '0'::real,
    ctd                     real                     DEFAULT '0'::real,
    td                      real                     DEFAULT '0'::real,
    tp                      real                     DEFAULT '0'::real,
    bonus                   real                     DEFAULT '0'::real,
    extra                   real                     DEFAULT '0'::real,
    correspondant           boolean                  DEFAULT 'FALSE'::boolean    
);

-- ****************************** Composante as composante
CREATE TABLE composante(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    ordre                   integer                  DEFAULT 0::integer,
    nom                     character varying(100)   DEFAULT ''::character varying,
    intitule                character varying(100)   DEFAULT ''::character varying    
);

-- ****************************** Cursus as cursus
CREATE TABLE cursus(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    nom                     character varying(100)   DEFAULT ''::character varying,
    intitule                character varying(100)   DEFAULT ''::character varying    
);

-- ****************************** Maquette as maquette
CREATE TABLE maquette(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    ordre                   integer                  DEFAULT 0::integer,
    code                    character varying(100)   DEFAULT ''::character varying,
    version                 character varying(100)   DEFAULT ''::character varying,
    nom                     character varying(100)   DEFAULT ''::character varying,
    gestionnaire            integer                  DEFAULT 0::integer,
    FOREIGN KEY(gestionnaire) REFERENCES personne(id),
    responsable             integer                  DEFAULT 0::integer,
    FOREIGN KEY(responsable) REFERENCES personne(id),
    co_responsable          integer                  DEFAULT 0::integer,
    FOREIGN KEY(co_responsable) REFERENCES personne(id),
    composante              integer                  DEFAULT 0::integer,
    FOREIGN KEY(composante) REFERENCES composante(id)    
);

-- ****************************** Diplome as diplome
CREATE TABLE diplome(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    ordre                   integer                  DEFAULT 0::integer,
    code                    character varying(100)   DEFAULT ''::character varying,
    nom                     character varying(100)   DEFAULT ''::character varying,
    maquette                integer                  DEFAULT 0::integer,
    FOREIGN KEY(maquette) REFERENCES maquette(id)    
);

-- ****************************** Etape as etape
CREATE TABLE etape(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    ordre                   integer                  DEFAULT 0::integer,
    code                    character varying(100)   DEFAULT ''::character varying,
    nom                     character varying(100)   DEFAULT ''::character varying,
    nbetu                   integer                  DEFAULT 0::integer,
    diplome                 integer                  DEFAULT 0::integer,
    FOREIGN KEY(diplome) REFERENCES diplome(id),
    cursus                  integer                  DEFAULT 0::integer,
    FOREIGN KEY(cursus) REFERENCES cursus(id)    
);

-- ****************************** responsable as responsable
CREATE TABLE responsable(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    etape                   integer                  DEFAULT 0::integer,
    FOREIGN KEY(etape) REFERENCES etape(id),
    personne                integer                  DEFAULT 0::integer,
    FOREIGN KEY(personne) REFERENCES personne(id)    
);

-- ****************************** Semestre as semestre
CREATE TABLE semestre(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    ordre                   integer                  DEFAULT 0::integer,
    code                    character varying(100)   DEFAULT ''::character varying,
    nom                     character varying(100)   DEFAULT ''::character varying,
    peretu                  integer                  DEFAULT 100::integer,
    periode                 integer                  DEFAULT 0::integer,
    etape                   integer                  DEFAULT 0::integer,
    FOREIGN KEY(etape) REFERENCES etape(id)    
);

-- ****************************** UE as ue
CREATE TABLE ue(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    ordre                   integer                  DEFAULT 0::integer,
    code                    character varying(100)   DEFAULT ''::character varying,
    nom                     character varying(100)   DEFAULT ''::character varying,
    peretu                  integer                  DEFAULT 100::integer,
    ects                    real                     DEFAULT '0'::real,
    semestre                integer                  DEFAULT 0::integer,
    FOREIGN KEY(semestre) REFERENCES semestre(id)    
);

-- ****************************** ECUE as ecue
CREATE TABLE ecue(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    ordre                   integer                  DEFAULT 0::integer,
    code                    character varying(100)   DEFAULT ''::character varying,
    nom                     character varying(100)   DEFAULT ''::character varying,
    peretu                  integer                  DEFAULT 100::integer,
    ects                    real                     DEFAULT '0'::real,
    ue                      integer                  DEFAULT 0::integer,
    FOREIGN KEY(ue) REFERENCES ue(id),
    enseignement            integer                  DEFAULT 0::integer,
    FOREIGN KEY(enseignement) REFERENCES enseignement(id)    
);

-- ****************************** EtapeOSE as etapeose
CREATE TABLE etapeose(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    code                    character varying(100)   DEFAULT ''::character varying,
    nom                     character varying(100)   DEFAULT ''::character varying,
    nbetu                   integer                  DEFAULT 0::integer    
);

-- ****************************** etape_personne as etape_personne
CREATE TABLE etape_personne(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    etapeose                integer                  DEFAULT 0::integer,
    FOREIGN KEY(etapeose) REFERENCES etapeose(id),
    personne                integer                  DEFAULT 0::integer,
    FOREIGN KEY(personne) REFERENCES personne(id)    
);

-- ****************************** etape_enseignement as etape_enseignement
CREATE TABLE etape_enseignement(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    etapeose                integer                  DEFAULT 0::integer,
    FOREIGN KEY(etapeose) REFERENCES etapeose(id),
    enseignement            integer                  DEFAULT 0::integer,
    FOREIGN KEY(enseignement) REFERENCES enseignement(id)    
);

-- ****************************** commentaire_personne as commentaire_personne
CREATE TABLE commentaire_personne(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    personne                integer                  DEFAULT 0::integer,
    FOREIGN KEY(personne) REFERENCES personne(id),
    auteur                  integer                  DEFAULT 0::integer,
    FOREIGN KEY(auteur) REFERENCES personne(id),
    date                    date                    DEFAULT 'now'::date,
    commentaire             text                     DEFAULT ''::text    
);

-- ****************************** commentaire_enseignement as commentaire_enseignement
CREATE TABLE commentaire_enseignement(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    enseignement            integer                  DEFAULT 0::integer,
    FOREIGN KEY(enseignement) REFERENCES enseignement(id),
    auteur                  integer                  DEFAULT 0::integer,
    FOREIGN KEY(auteur) REFERENCES personne(id),
    date                    date                    DEFAULT 'now'::date,
    commentaire             text                     DEFAULT ''::text    
);

-- ****************************** commentaire_maquette as commentaire_maquette
CREATE TABLE commentaire_maquette(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    maquette                integer                  DEFAULT 0::integer,
    FOREIGN KEY(maquette) REFERENCES maquette(id),
    auteur                  integer                  DEFAULT 0::integer,
    FOREIGN KEY(auteur) REFERENCES personne(id),
    date                    date                    DEFAULT 'now'::date,
    commentaire             text                     DEFAULT ''::text    
);

-- ****************************** commentaire_composante as commentaire_composante
CREATE TABLE commentaire_composante(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    composante              integer                  DEFAULT 0::integer,
    FOREIGN KEY(composante) REFERENCES composante(id),
    auteur                  integer                  DEFAULT 0::integer,
    FOREIGN KEY(auteur) REFERENCES personne(id),
    date                    date                    DEFAULT 'now'::date,
    commentaire             text                     DEFAULT ''::text    
);

-- ****************************** personne_situation as personne_situation
CREATE TABLE personne_situation(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    personne                integer                  DEFAULT 0::integer,
    FOREIGN KEY(personne) REFERENCES personne(id),
    situation               integer                  DEFAULT 0::integer,
    FOREIGN KEY(situation) REFERENCES situation(id),
    debut                   date                    DEFAULT 'now'::date,
    fin                     date                    DEFAULT 'now'::date,
    reduction               integer                  DEFAULT 0::integer,
    commentaire             text                     DEFAULT ''::text    
);

-- ****************************** Cat1Ref as cat1ref
CREATE TABLE cat1ref(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    description             character varying(100)   DEFAULT ''::character varying    
);

-- ****************************** CatRef as catref
CREATE TABLE catref(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    description             text                     DEFAULT ''::text,
    cat1ref                 integer                  DEFAULT 0::integer,
    FOREIGN KEY(cat1ref) REFERENCES cat1ref(id)    
);

-- ****************************** Referentiel as referentiel
CREATE TABLE referentiel(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    code                    character varying(100)   DEFAULT ''::character varying,
    description             text                     DEFAULT ''::text,
    calcul                  text                     DEFAULT ''::text,
    observations            text                     DEFAULT ''::text,
    catref                  integer                  DEFAULT 0::integer,
    FOREIGN KEY(catref) REFERENCES catref(id)    
);

-- ****************************** FoncRef as foncref
CREATE TABLE foncref(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    intitule                text                     DEFAULT ''::text,
    referentiel             integer                  DEFAULT 0::integer,
    FOREIGN KEY(referentiel) REFERENCES referentiel(id)    
);

-- ****************************** personne_foncRef as personne_foncref
CREATE TABLE personne_foncref(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    personne                integer                  DEFAULT 0::integer,
    FOREIGN KEY(personne) REFERENCES personne(id),
    foncref                 integer                  DEFAULT 0::integer,
    FOREIGN KEY(foncref) REFERENCES foncref(id),
    commentaire             text                     DEFAULT ''::text,
    volume                  real                     DEFAULT '0'::real    
);

-- ****************************** voeu_bilan_ligne as voeu_bilan_ligne
-- ***** C'est une VUE alors je ne sais pas encore faire...

-- ****************************** voeu_personne_bilan as voeu_personne_bilan
-- ***** C'est une VUE alors je ne sais pas encore faire...

-- ****************************** voeu_detail_heures as voeu_detail_heures
-- ***** C'est une VUE alors je ne sais pas encore faire...

-- ****************************** voeu_enseignement_bilan as voeu_enseignement_bilan
-- ***** C'est une VUE alors je ne sais pas encore faire...

-- ****************************** voeu_enseignement_bilan_prioritaire as voeu_enseignement_bilan_prioritaire
-- ***** C'est une VUE alors je ne sais pas encore faire...

-- ****************************** enseignement_besoins as enseignement_besoins
-- ***** C'est une VUE alors je ne sais pas encore faire...

-- ****************************** structure_enseignement as structure_enseignement
-- ***** C'est une VUE alors je ne sais pas encore faire...

-- ****************************** enseignement_structure as enseignement_structure
-- ***** C'est une VUE alors je ne sais pas encore faire...

-- ****************************** enseignement_etudiant_details as enseignement_etudiant_details
-- ***** C'est une VUE alors je ne sais pas encore faire...

-- ****************************** enseignement_periode as enseignement_periode
-- ***** C'est une VUE alors je ne sais pas encore faire...

-- ****************************** personne_charge as personne_charge
-- ***** C'est une VUE alors je ne sais pas encore faire...

-- ****************************** personne_situation_reduction as personne_situation_reduction
-- ***** C'est une VUE alors je ne sais pas encore faire...

-- ****************************** personne_referentiel_heures as personne_referentiel_heures
-- ***** C'est une VUE alors je ne sais pas encore faire...