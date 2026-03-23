
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
    reduction               integer                  DEFAULT 0::integer    
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
    FOREIGN KEY(labo) REFERENCES labo(id),
    etat_ts                 boolean                  DEFAULT 'FALSE'::boolean    
);

-- ****************************** Enseignement as enseignement
CREATE TABLE enseignement(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    nuac                    character varying(100)   DEFAULT ''::character varying,
    nom                     character varying(100)   DEFAULT ''::character varying,
    intitule                character varying(100)   DEFAULT ''::character varying,
    attribuable             boolean                  DEFAULT 'FALSE'::boolean,
    cm                      real                     DEFAULT '0'::real,
    td                      real                     DEFAULT '0'::real,
    ctd                     real                     DEFAULT '0'::real,
    tp                      real                     DEFAULT '0'::real,
    extra                   real                     DEFAULT '0'::real,
    s_cm                    real                     DEFAULT '0'::real,
    s_td                    real                     DEFAULT '0'::real,
    s_ctd                   real                     DEFAULT '0'::real,
    s_tp                    real                     DEFAULT '0'::real,
    s_extra                 real                     DEFAULT '0'::real,
    i_cm                    real                     DEFAULT '0'::real,
    i_td                    real                     DEFAULT '0'::real,
    i_ctd                   real                     DEFAULT '0'::real,
    i_tp                    real                     DEFAULT '0'::real,
    i_extra                 real                     DEFAULT '0'::real,
    d_cm                    real                     DEFAULT '0'::real,
    d_td                    real                     DEFAULT '0'::real,
    d_ctd                   real                     DEFAULT '0'::real,
    d_tp                    real                     DEFAULT '0'::real,
    d_extra                 real                     DEFAULT '0'::real,
    n_cm                    real                     DEFAULT '0'::real,
    n_td                    real                     DEFAULT '0'::real,
    n_ctd                   real                     DEFAULT '0'::real,
    n_tp                    real                     DEFAULT '0'::real,
    n_extra                 real                     DEFAULT '0'::real,
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
    FOREIGN KEY(payeur) REFERENCES payeur(id),
    etat_ts                 boolean                  DEFAULT 'FALSE'::boolean    
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
    correspondant           boolean                  DEFAULT 'FALSE'::boolean,
    etat_ts                 integer                  DEFAULT 0::integer    
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

-- ****************************** voeu_bilan_ligne as voeu_bilan_ligne
-- ***** C'est une VUE alors je ne sais pas encore faire...

-- ****************************** voeu_personne_bilan as voeu_personne_bilan
-- ***** C'est une VUE alors je ne sais pas encore faire...

-- ****************************** voeu_enseignement_bilan as voeu_enseignement_bilan
-- ***** C'est une VUE alors je ne sais pas encore faire...

-- ****************************** enseignement_besoins as enseignement_besoins
-- ***** C'est une VUE alors je ne sais pas encore faire...

-- ****************************** structure_enseignement as structure_enseignement
-- ***** C'est une VUE alors je ne sais pas encore faire...

-- ****************************** enseignement_structure as enseignement_structure
-- ***** C'est une VUE alors je ne sais pas encore faire...

-- ****************************** enseignement_periode as enseignement_periode
-- ***** C'est une VUE alors je ne sais pas encore faire...

-- ****************************** commentaire_personne as commentaire_personne
CREATE TABLE commentaire_personne(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    personne                integer                  DEFAULT 0::integer,
    FOREIGN KEY(personne) REFERENCES personne(id),
    auteur                  integer                  DEFAULT 0::integer,
    FOREIGN KEY(auteur) REFERENCES personne(id),
    date                    date                    DEFAULT 'now()',
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
    date                    date                    DEFAULT 'now()',
    commentaire             text                     DEFAULT ''::text    
);

-- ****************************** enseignement_besoins_detail as enseignement_besoins_detail
-- ***** C'est une VUE alors je ne sais pas encore faire...

-- ****************************** Domaine as domaine
CREATE TABLE domaine(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    nom                     character varying(100)   DEFAULT ''::character varying,
    acronyme                character varying(100)   DEFAULT ''::character varying    
);

-- ****************************** domaine_personne as domaine_personne
CREATE TABLE domaine_personne(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    domaine                 integer                  DEFAULT 0::integer,
    FOREIGN KEY(domaine) REFERENCES domaine(id),
    personne                integer                  DEFAULT 0::integer,
    FOREIGN KEY(personne) REFERENCES personne(id),
    ordre                   integer                  DEFAULT 0::integer,
    quotite                 real                     DEFAULT '0'::real    
);

-- ****************************** domaine_enseignement as domaine_enseignement
CREATE TABLE domaine_enseignement(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    domaine                 integer                  DEFAULT 0::integer,
    FOREIGN KEY(domaine) REFERENCES domaine(id),
    enseignement            integer                  DEFAULT 0::integer,
    FOREIGN KEY(enseignement) REFERENCES enseignement(id),
    ordre                   integer                  DEFAULT 0::integer,
    quotite                 real                     DEFAULT '0'::real    
);

-- ****************************** domaine_responsable as domaine_responsable
CREATE TABLE domaine_responsable(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    domaine                 integer                  DEFAULT 0::integer,
    FOREIGN KEY(domaine) REFERENCES domaine(id),
    responsable             integer                  DEFAULT 0::integer,
    FOREIGN KEY(responsable) REFERENCES personne(id)    
);