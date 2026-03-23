
-- ****************************** Labo as labo
CREATE TABLE "public".labo(
    num                     integer PRIMARY KEY,

    nom_long                character varying(100)   DEFAULT ''::character varying,
    nom_court               character varying(100)   DEFAULT ''::character varying,
    url                     character varying(30)    DEFAULT ''::character varying    
);

-- ****************************** Statut as statut
CREATE TABLE "public".statut(
    num                     integer PRIMARY KEY,

    nom_court               character varying(100)   DEFAULT ''::character varying,
    obligation              integer                  DEFAULT 192::integer    
);

-- ****************************** Situation as situation
CREATE TABLE "public".situation(
    num                     integer PRIMARY KEY,

    nom_court               character varying(100)   DEFAULT ''::character varying,
    reduction               integer                  DEFAULT 0::integer    
);

-- ****************************** Enseignant as enseignant
CREATE TABLE "public".enseignant(
    num                     integer PRIMARY KEY,

    harpege                 character varying(100)   DEFAULT ''::character varying,
    nom                     character varying(100)   DEFAULT ''::character varying,
    prenom                  character varying(100)   DEFAULT ''::character varying,
    prof_adr1               text                     DEFAULT ''::text,
    prof_tel1               character varying(20)    DEFAULT ''::character varying,
    prof_tel2               character varying(20)    DEFAULT ''::character varying,
    prof_mail               character varying(50)    DEFAULT ''::character varying,
    pers_adr1               text                     DEFAULT ''::text,
    statut                  integer                  DEFAULT 0::integer,
    FOREIGN KEY(statut) REFERENCES statut(num),
    situation               integer                  DEFAULT 0::integer,
    FOREIGN KEY(situation) REFERENCES situation(num),
    labo                    integer                  DEFAULT 0::integer,
    FOREIGN KEY(labo) REFERENCES labo(num)    
);

-- ****************************** Enseignement as enseignement
CREATE TABLE "public".enseignement(
    num                     integer PRIMARY KEY,

    nuac                    character varying(100)   DEFAULT ''::character varying,
    intitule                character varying(30)    DEFAULT ''::character varying,
    intitulelong            character varying(30)    DEFAULT ''::character varying,
    cours                   real                     DEFAULT '0'::real,
    td                      real                     DEFAULT '0'::real,
    ctd                     real                     DEFAULT '0'::real,
    tp                      real                     DEFAULT '0'::real,
    colle                   real                     DEFAULT '0'::real,
    s_cours                 real                     DEFAULT '0'::real,
    s_td                    real                     DEFAULT '0'::real,
    s_ctd                   real                     DEFAULT '0'::real,
    s_tp                    real                     DEFAULT '0'::real,
    s_colle                 real                     DEFAULT '0'::real,
    i_cours                 real                     DEFAULT '0'::real,
    i_td                    real                     DEFAULT '0'::real,
    i_ctd                   real                     DEFAULT '0'::real,
    i_tp                    real                     DEFAULT '0'::real,
    i_colle                 real                     DEFAULT '0'::real,
    d_cours                 real                     DEFAULT '0'::real,
    d_td                    real                     DEFAULT '0'::real,
    d_ctd                   real                     DEFAULT '0'::real,
    d_tp                    real                     DEFAULT '0'::real,
    d_colle                 real                     DEFAULT '0'::real,
    n_cours                 real                     DEFAULT '0'::real,
    n_td                    real                     DEFAULT '0'::real,
    n_ctd                   real                     DEFAULT '0'::real,
    n_tp                    real                     DEFAULT '0'::real,
    n_colle                 real                     DEFAULT '0'::real,
    m_cours                 real                     DEFAULT '0'::real,
    m_td                    real                     DEFAULT '0'::real,
    m_ctd                   real                     DEFAULT '0'::real,
    m_tp                    real                     DEFAULT '0'::real,
    m_colle                 real                     DEFAULT '0'::real,
    bonus                   real                     DEFAULT '0'::real,
    information             text                     DEFAULT ''::text,
    url                     text                     DEFAULT ''::text    
);

-- ****************************** Role as role
CREATE TABLE "public".role(
    num                     integer PRIMARY KEY,

    nom                     character varying(30)    DEFAULT ''::character varying    
);

-- ****************************** actAs as actas
CREATE TABLE "public".actas(
    num                     integer PRIMARY KEY,

    enseignant              integer                  DEFAULT 0::integer,
    FOREIGN KEY(enseignant) REFERENCES enseignant(num),
    role                    integer                  DEFAULT 0::integer,
    FOREIGN KEY(role) REFERENCES role(num)    
);

-- ****************************** Voeu as voeu
CREATE TABLE "public".voeu(
    num                     integer PRIMARY KEY,

    enseignant              integer                  DEFAULT 0::integer,
    FOREIGN KEY(enseignant) REFERENCES enseignant(num),
    enseignement            integer                  DEFAULT 0::integer,
    FOREIGN KEY(enseignement) REFERENCES enseignement(num),
    cours                   real                     DEFAULT '0'::real,
    ctd                     real                     DEFAULT '0'::real,
    td                      real                     DEFAULT '0'::real,
    tp                      real                     DEFAULT '0'::real,
    bonus                   real                     DEFAULT '0'::real,
    colle                   real                     DEFAULT '0'::real,
    responsable             boolean                  DEFAULT 'FALSE'::boolean    
);

-- ****************************** Composante as departement
CREATE TABLE "public".departement(
    num                     integer PRIMARY KEY,

    ordre                   integer                  DEFAULT 0::integer,
    nom_court               character varying(15)    DEFAULT 'Nom du cursus'::character varying,
    nom_long                character varying(100)   DEFAULT 'intitulé du cursus'::character varying    
);

-- ****************************** Cursus as cursus
CREATE TABLE "public".cursus(
    num                     integer PRIMARY KEY,

    nom_court               character varying(15)    DEFAULT 'Nom du cursus'::character varying,
    nom_long                character varying(100)   DEFAULT 'intitulé du cursus'::character varying    
);

-- ****************************** Maquette as maquette
CREATE TABLE "public".maquette(
    num                     integer PRIMARY KEY,

    ordre                   integer                  DEFAULT 0::integer,
    code                    character varying(15)    DEFAULT 'Code'::character varying,
    version                 character varying(15)    DEFAULT 'Version'::character varying,
    nom                     character varying(100)   DEFAULT 'Nom de la maquette'::character varying,
    gestionnaire            integer                  DEFAULT 0::integer,
    FOREIGN KEY(gestionnaire) REFERENCES enseignant(num),
    responsable1            integer                  DEFAULT 0::integer,
    FOREIGN KEY(responsable1) REFERENCES enseignant(num),
    responsable2            integer                  DEFAULT 0::integer,
    FOREIGN KEY(responsable2) REFERENCES enseignant(num),
    departement             integer                  DEFAULT 0::integer,
    FOREIGN KEY(departement) REFERENCES departement(num)    
);

-- ****************************** Diplome as diplome
CREATE TABLE "public".diplome(
    num                     integer PRIMARY KEY,

    ordre                   integer                  DEFAULT 0::integer,
    code                    character varying(15)    DEFAULT 'Code'::character varying,
    nom                     character varying(100)   DEFAULT 'Nom du diplome'::character varying,
    maquette                integer                  DEFAULT 0::integer,
    FOREIGN KEY(maquette) REFERENCES maquette(num)    
);

-- ****************************** Etape as etape
CREATE TABLE "public".etape(
    num                     integer PRIMARY KEY,

    ordre                   integer                  DEFAULT 0::integer,
    code                    character varying(15)    DEFAULT 'Code'::character varying,
    nom                     character varying(100)   DEFAULT 'Nom del ''étape'::character varying,
    nbetu                   integer                  DEFAULT 0::integer,
    diplome                 integer                  DEFAULT 0::integer,
    FOREIGN KEY(diplome) REFERENCES diplome(num),
    cursus                  integer                  DEFAULT 0::integer,
    FOREIGN KEY(cursus) REFERENCES cursus(num)    
);

-- ****************************** responsable as responsable
CREATE TABLE "public".responsable(
    num                     integer PRIMARY KEY,

    etape                   integer                  DEFAULT 0::integer,
    FOREIGN KEY(etape) REFERENCES etape(num),
    enseignant              integer                  DEFAULT 0::integer,
    FOREIGN KEY(enseignant) REFERENCES enseignant(num)    
);

-- ****************************** Semestre as semestre
CREATE TABLE "public".semestre(
    num                     integer PRIMARY KEY,

    ordre                   integer                  DEFAULT 0::integer,
    code                    character varying(15)    DEFAULT 'Code'::character varying,
    nom                     character varying(100)   DEFAULT 'Nom du semestre'::character varying,
    peretu                  integer                  DEFAULT 100::integer,
    periode                 integer                  DEFAULT 0::integer,
    etape                   integer                  DEFAULT 0::integer,
    FOREIGN KEY(etape) REFERENCES etape(num)    
);

-- ****************************** UE as ue
CREATE TABLE "public".ue(
    num                     integer PRIMARY KEY,

    ordre                   integer                  DEFAULT 0::integer,
    code                    character varying(15)    DEFAULT 'Code'::character varying,
    nom                     character varying(100)   DEFAULT 'Nom de l''UE'::character varying,
    peretu                  integer                  DEFAULT 100::integer,
    ects                    real                     DEFAULT '0'::real,
    semestre                integer                  DEFAULT 0::integer,
    FOREIGN KEY(semestre) REFERENCES semestre(num)    
);

-- ****************************** ECUE as ecue
CREATE TABLE "public".ecue(
    num                     integer PRIMARY KEY,

    ordre                   integer                  DEFAULT 0::integer,
    code                    character varying(15)    DEFAULT 'Code'::character varying,
    nom                     character varying(100)   DEFAULT 'Nom de l''ECUE'::character varying,
    peretu                  integer                  DEFAULT 100::integer,
    ects                    real                     DEFAULT '0'::real,
    ue                      integer                  DEFAULT 0::integer,
    FOREIGN KEY(ue) REFERENCES ue(num),
    enseignement            integer                  DEFAULT 0::integer,
    FOREIGN KEY(enseignement) REFERENCES enseignement(num)    
);

-- ****************************** voeu_bilan_ligne as voeu_bilan_ligne
-- ***** C'est une VUE alors je ne sais pas encore faire...

-- ****************************** voeu_enseignant_bilan as voeu_enseignant_bilan
-- ***** C'est une VUE alors je ne sais pas encore faire...

-- ****************************** voeu_enseignement_bilan as voeu_enseignement_bilan
-- ***** C'est une VUE alors je ne sais pas encore faire...