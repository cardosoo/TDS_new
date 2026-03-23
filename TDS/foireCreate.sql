/*****************************

  Construction des tables

*****************************/


CREATE TABLE historique (
    qui integer,
    date timestamp without time zone,
    requete text,
    result text,
    ip character varying(100)
);

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
    reduction               integer                  DEFAULT 0::integer,
    reduction_legale        character varying(100)   DEFAULT '0h'::character varying,
    ufr                     boolean                  DEFAULT 'FALSE'::boolean    
);

-- ****************************** Personne as personne
CREATE TABLE personne(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    uid                     character varying(100)   DEFAULT ''::character varying,
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
    nom                     character varying(100)   DEFAULT ''::character varying,
    intitule                character varying(100)   DEFAULT ''::character varying,
    attribuable             boolean                  DEFAULT 'TRUE'::boolean, 
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
    url                     text                     DEFAULT ''::text    
);

-- ****************************** Role as role
CREATE TABLE role(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    nom                     character varying(100)   DEFAULT ''::character varying    
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
    anciennete              integer                  DEFAULT 0::integer    
);

-- ****************************** Composante as composante
CREATE TABLE composante(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    ordre                   integer                  DEFAULT 0::integer,
    nom                     character varying(100)   DEFAULT ''::character varying,
    intitule                 character varying(100)   DEFAULT ''::character varying    
);

-- ****************************** Cursus as cursus
CREATE TABLE cursus(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    nom                     character varying(100)   DEFAULT ''::character varying,
    intitule                 character varying(100)   DEFAULT ''::character varying    
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

-- ****************************** commentaire_personne as commentaire_personne
CREATE TABLE commentaire_personne(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    personne                integer                  DEFAULT 0::integer,
    FOREIGN KEY(personne) REFERENCES personne(id),
    auteur                  integer                  DEFAULT 0::integer,
    FOREIGN KEY(auteur) REFERENCES personne(id),
    date                    date                     DEFAULT 'now'::date,
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
    date                    date                     DEFAULT 'now'::date,
    commentaire             text                     DEFAULT ''::text    
);
/*****************************

  Construction des vues

*****************************/

/* correspondant_enseignement */
CREATE VIEW correspondant_enseignement AS
 SELECT E.id,
    V.personne AS correspondant
   FROM enseignement AS E
     LEFT JOIN voeu AS V ON V.enseignement = E.id 
     AND v.correspondant;


/* enseignement_besoins */
CREATE VIEW enseignement_besoins AS
SELECT 
      E.id,
      E.cm   *E.s_cm   *E.d_cm   *E.i_cm   *1.5 
    + E.ctd  *E.s_ctd  *E.d_ctd  *E.i_ctd  *1.14
    + E.td   *E.s_td   *E.d_td   *E.i_td   *1
    + E.tp   *E.s_tp   *E.d_tp   *E.i_tp   *1
    + E.extra*E.s_extra*E.d_extra*E.i_extra*1
    + bonus 
    as besoins
FROM enseignement as E
;


/*  personne_charge */
CREATE VIEW personne_charge AS
SELECT 
     P.id,
     (ST.obligation-SI.reduction) as charge
  FROM personne as P
    LEFT JOIN statut  as ST  on ST.id    = P.statut
    LEFT JOIN situation as SI on SI.id = P.situation
;

/* structure_enseignement */
CREATE VIEW structure_enseignement AS
SELECT ecue.enseignement,
    semestre.periode,
    ue.code AS code_ue,
    ecue.code AS code_ecue,
    etape.cursus,
    maquette.id AS maquette,
    maquette.composante,
    ecue.id AS ecue,
    ecue.ue,
    ue.semestre,
    semestre.etape,
    etape.nbetu * semestre.peretu / 100 * ue.peretu / 100 * ecue.peretu / 100 AS nbetu
   FROM maquette
     LEFT JOIN diplome ON maquette.id = diplome.maquette
     LEFT JOIN etape ON diplome.id = etape.diplome
     LEFT JOIN semestre ON etape.id = semestre.etape
     LEFT JOIN ue ON semestre.id = ue.semestre
     LEFT JOIN ecue ON ue.id = ecue.ue;

/* enseignement_periode */
CREATE VIEW enseignement_periode AS
SELECT DISTINCT enseignement.id,
    array_agg(DISTINCT semestre.periode) AS periode
   FROM enseignement
     LEFT JOIN structure_enseignement as SE on SE.enseignement = enseignement.id
     LEFT JOIN semestre ON SE.semestre = semestre.id
  WHERE enseignement.id > 0
  GROUP BY enseignement.id;

/* enseignement_structure */
CREATE VIEW enseignement_structure AS
 SELECT DISTINCT enseignement.id,
    array_agg(DISTINCT semestre.periode) AS periode,
    string_agg(DISTINCT concat(code_ue, '_', code_ecue), '|') AS code,
    string_agg(DISTINCT cursus.nom, '|') AS cursus,
    string_agg(DISTINCT etape.nom, '|') AS etape,
    string_agg(DISTINCT maquette.nom, '|') AS maquette,
    string_agg(DISTINCT composante.nom, '|') AS composante,
    array_agg(se.nbetu) AS nbetu
   FROM enseignement
     LEFT JOIN structure_enseignement se ON se.enseignement = enseignement.id
     LEFT JOIN semestre ON se.semestre = semestre.id
     LEFT JOIN cursus ON se.cursus = cursus.id
     LEFT JOIN etape ON se.etape = etape.id
     LEFT JOIN maquette ON se.maquette = maquette.id
     LEFT JOIN composante ON se.composante = composante.id
  WHERE enseignement.id > 0
  GROUP BY enseignement.id;

/* voeu_bilan_ligne */
CREATE VIEW voeu_bilan_ligne AS
SELECT V.id,
      V.cm  * E.s_cm  * E.d_cm  * 1.5 
    + V.ctd * E.s_ctd * E.d_ctd * 1.14
    + V.td  * E.s_td  * E.d_td  * 1 
    + V.tp  * E.s_tp  * E.d_tp  * 1
    + V.extra * E.s_extra * E.d_extra * 1 + V.bonus
   AS heures
   FROM voeu as V
     LEFT JOIN enseignement E ON E.id = V.enseignement
;

CREATE VIEW voeu_detail_heures AS
 SELECT V.id,
    V.cm    * E.s_cm    * E.d_cm    * 1.5   AS cm,
    V.ctd   * E.s_ctd   * E.d_ctd   * 1.14  AS ctd,
    V.td    * E.s_td    * E.d_td    * 1     AS td,
    V.tp    * E.s_tp    * E.d_tp    * 1     AS tp,
    V.extra * E.s_extra * E.d_extra * 1     AS extra,
    V.bonus
   FROM voeu AS V
     LEFT JOIN enseignement AS E ON E.id = V.enseignement
  WHERE (V.id > 0) AND (E.id > 0);

/* voeu_enseignement_detail_temp */
CREATE VIEW voeu_enseignement_detail_temp AS
SELECT E.id,
	sum(V.cm) as cm,
	sum(V.ctd) as ctd,
	sum(V.td) as td,
	sum(V.tp) as tp,
	sum(V.extra) as extra,
	sum(V.bonus) as bonus,
	sum(case when V.correspondant then 1  else 0 end ) as correspondant
FROM enseignement as E
    LEFT JOIN voeu as V on V.enseignement=E.id
WHERE ( V.id>0 or V.id is null)
AND V.actif
GROUP BY E.id;


/* voeu_enseignement_detail */
CREATE VIEW voeu_enseignement_detail AS
SELECT E.id,
	COALESCE(VEDT.cm,0) as cm,
	COALESCE(VEDT.ctd,0) as ctd,
	COALESCE(VEDT.td,0) as td,
	COALESCE(VEDT.tp,0) as tp,
	COALESCE(VEDT.extra,0) as extra,
	COALESCE(VEDT.bonus,0) as bonus,
	COALESCE(VEDT.correspondant, 0) as correspondant
  FROM enseignement as E
    LEFT JOIN voeu_enseignement_detail_temp as VEDT on E.id = VEDT.id
;

/* voeu_enseignement_heures_temp */
CREATE VIEW voeu_enseignement_heures_temp AS
SELECT E.id,
    sum(VBL.heures) AS heures
   FROM enseignement E
     LEFT JOIN voeu V ON E.id = V.enseignement
     LEFT JOIN voeu_bilan_ligne as VBL ON VBL.id = V.id
     LEFT JOIN personne P ON P.id = V.personne 
  WHERE (V.id > 0 OR V.id IS NULL)
  AND V.actif AND P.actif
  GROUP BY E.id;

/* voeu_enseignement_heures */
CREATE VIEW voeu_enseignement_heures AS
SELECT E.id,
    CASE 
        WHEN VEHT.heures IS NULL THEN 0
        ELSE VEHT.heures
    END as heures   
FROM enseignement as E
LEFT JOIN voeu_enseignement_heures_temp as VEHT on E.id = VEHT.id
;

/* voeu_enseignement_bilan */
CREATE VIEW voeu_enseignement_bilan AS
SELECT E.id,
	E.cm   *E.i_cm    - VED.cm    as cm,
	E.ctd  *E.i_ctd   - VED.ctd   as ctd,
	E.td   *E.i_td    - VED.td    as td,
	E.tp   *E.i_tp    - VED.tp    as tp,
	E.extra*E.i_extra - VED.extra as extra,
	E.bonus           - VED.bonus as bonus,
	1 - VED.correspondant         as correspondant,
	EB.besoins - VEH.heures       as heures
  FROM enseignement as E
    LEFT JOIN voeu_enseignement_detail as VED on E.id = VED.id
    LEFT JOIN enseignement_besoins     as EB  on E.id = EB.id
    LEFT JOIN voeu_enseignement_heures as VEH on E.id = VEH.id
;


/* voeu_personne_heures_temp */
CREATE VIEW voeu_personne_heures_temp AS
SELECT 
    P.id,
    sum(VBL.heures) as heures
FROM personne as P
    LEFT JOIN voeu as V on P.id = V.personne
    LEFT JOIN voeu_bilan_ligne as VBL on V.id = VBL.id
    LEFT JOIN enseignement E ON E.id = V.enseignement
WHERE (V.id>0 OR V.id is null)
AND V.actif AND E.actif
GROUP BY P.id;

/* voeu_personne_heures` */
CREATE VIEW voeu_personne_heures AS
SELECT 
      P.id,
      CASE 
          WHEN VPHT.heures IS NULL THEN 0
          ELSE VPHT.heures
      END as heures
FROM  personne as P
LEFT JOIN voeu_personne_heures_temp as VPHT on VPHT.id = P.id  
;

/* voeu_personne_bilan */
CREATE VIEW voeu_personne_bilan AS 
SELECT
    PC.id,
	PC.charge - VPH.heures as heures
FROM personne_charge as PC
LEFT JOIN voeu_personne_heures as VPH on PC.id = VPH.id;



CREATE VIEW voeu_enseignement_bilan_prioritaire AS
  SELECT e.id,
    e.cm * e.i_cm - ved.cm AS cm,
    e.ctd * e.i_ctd - ved.ctd AS ctd,
    e.td * e.i_td - ved.td AS td,
    e.tp * e.i_tp - ved.tp AS tp,
    e.extra * e.i_extra - ved.extra AS extra,
    e.bonus - ved.bonus AS bonus,
    1 - ved.correspondant AS correspondant,
    eb.besoins - veh.heures AS heures
   FROM enseignement e
     LEFT JOIN voeu_enseignement_detail_prioritaire vedp ON e.id = vedp.id
     LEFT JOIN enseignement_besoins eb ON e.id = eb.id
     LEFT JOIN voeu_enseignement_heures veh ON e.id = veh.id;
     

CREATE VIEW voeu_enseignement_detail_prioritaire AS
 SELECT e.id,
    COALESCE(vedtp.cm, 0::real) AS cm,
    COALESCE(vedtp.ctd, 0::real) AS ctd,
    COALESCE(vedtp.td, 0::real) AS td,
    COALESCE(vedtp.tp, 0::real) AS tp,
    COALESCE(vedtp.extra, 0::real) AS extra,
    COALESCE(vedtp.bonus, 0::real) AS bonus,
    COALESCE(vedtp.correspondant, 0::bigint) AS correspondant
   FROM enseignement e
     LEFT JOIN voeu_enseignement_detail_temp_prioritaire vedtp ON e.id = vedtp.id;     

CREATE VIEW voeu_enseignement_detail_temp_prioritaire AS
SELECT e.id,
    sum(v.cm) AS cm,
    sum(v.ctd) AS ctd,
    sum(v.td) AS td,
    sum(v.tp) AS tp,
    sum(v.extra) AS extra,
    sum(v.bonus) AS bonus,
    sum(
        CASE
            WHEN v.correspondant THEN 1
            ELSE 0
        END) AS correspondant
   FROM enseignement e
     LEFT JOIN voeu v ON v.enseignement = e.id
  WHERE (v.id > 0 OR v.id IS NULL) AND v.actif AND ((V.anciennete>0) AND (V.anciennete<4))
  GROUP BY e.id;


CREATE VIEW voeu_enseignement_bilan_prioritaire AS
  SELECT e.id,
    e.cm * e.i_cm - vedp.cm AS cm,
    e.ctd * e.i_ctd - vedp.ctd AS ctd,
    e.td * e.i_td - vedp.td AS td,
    e.tp * e.i_tp - vedp.tp AS tp,
    e.extra * e.i_extra - vedp.extra AS extra,
    e.bonus - vedp.bonus AS bonus,
    1 - vedp.correspondant AS correspondant,
    eb.besoins - veh.heures AS heures
   FROM enseignement e
     LEFT JOIN voeu_enseignement_detail_prioritaire vedp ON e.id = vedp.id
     LEFT JOIN enseignement_besoins eb ON e.id = eb.id
     LEFT JOIN voeu_enseignement_heures veh ON e.id = veh.id;


CREATE TABLE heritage(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    parent                  integer                  DEFAULT 0::integer,
    FOREIGN KEY(parent) REFERENCES enseignement(id),
    enfant                  integer                  DEFAULT 0::integer,
    FOREIGN KEY(enfant) REFERENCES enseignement(id)    
);


/***************************************************/
CREATE TABLE panier(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    personne                integer                  DEFAULT 0::integer,
    FOREIGN KEY(personne) REFERENCES personne(id),
    enseignement            integer                  DEFAULT 0::integer,
    FOREIGN KEY(enseignement) REFERENCES enseignement(id),
    cm                      boolean                  DEFAULT 'FALSE'::boolean,
    ctd                     boolean                  DEFAULT 'FALSE'::boolean,
    td                      boolean                  DEFAULT 'FALSE'::boolean,
    tp                      boolean                  DEFAULT 'FALSE'::boolean,
    commentaire             text                     DEFAULT ''::text    
);

/*****************************

   Migration des tables

******************************/

TRUNCATE TABLE 
    role,
    actas,
    labo,
    statut,
    situation,
    enseignement,
    voeu,
    personne,
    ecue,
    ue,
    semestre,
    etape,
    diplome,
    maquette,
    responsable,
    cursus,
    composante
CASCADE;

/*
ALTER TABLE role DISABLE TRIGGER ALL;
ALTER TABLE actas DISABLE TRIGGER ALL;
ALTER TABLE labo DISABLE TRIGGER ALL;
ALTER TABLE statut DISABLE TRIGGER ALL;
ALTER TABLE situation DISABLE TRIGGER ALL;
ALTER TABLE enseignement DISABLE TRIGGER ALL;
ALTER TABLE voeu DISABLE TRIGGER ALL;
ALTER TABLE personne DISABLE TRIGGER ALL;
ALTER TABLE ecue DISABLE TRIGGER ALL;
ALTER TABLE ue DISABLE TRIGGER ALL;
ALTER TABLE semestre DISABLE TRIGGER ALL;
ALTER TABLE etape DISABLE TRIGGER ALL;
ALTER TABLE diplome DISABLE TRIGGER ALL;
ALTER TABLE maquette DISABLE TRIGGER ALL;
ALTER TABLE responsable DISABLE TRIGGER ALL;
ALTER TABLE cursus DISABLE TRIGGER ALL;
ALTER TABLE composante DISABLE TRIGGER ALL;
*/

ALTER TABLE role DISABLE TRIGGER ALL;
ALTER TABLE actas DISABLE TRIGGER ALL;
ALTER TABLE labo DISABLE TRIGGER ALL;
ALTER TABLE statut DISABLE TRIGGER ALL;
ALTER TABLE situation DISABLE TRIGGER ALL;
ALTER TABLE enseignement DISABLE TRIGGER ALL;
ALTER TABLE personne DISABLE TRIGGER ALL;
ALTER TABLE composante DISABLE TRIGGER ALL;
ALTER TABLE cursus DISABLE TRIGGER ALL;
ALTER TABLE ue DISABLE TRIGGER ALL;
ALTER TABLE ecue DISABLE TRIGGER ALL;
ALTER TABLE semestre DISABLE TRIGGER ALL;
ALTER TABLE etape DISABLE TRIGGER ALL;
ALTER TABLE diplome DISABLE TRIGGER ALL;
ALTER TABLE maquette DISABLE TRIGGER ALL;
ALTER TABLE responsable DISABLE TRIGGER ALL;
ALTER TABLE commentaire_personne DISABLE TRIGGER ALL;
ALTER TABLE commentaire_enseignement DISABLE TRIGGER ALL;
ALTER TABLE heritage DISABLE TRIGGER ALL;
ALTER TABLE voeu DISABLE TRIGGER ALL;
ALTER TABLE panier DISABLE TRIGGER ALL;



/*
ecue ( id, ordre, code, nom, peretu, ects, #ue, #enseignement )
ue ( id, ordre, code, nom, peretu, ects, #semestre )
semestre ( id, ordre, code, nom, peretu, periode, #etape )
etape ( id, ordre, code, nom, nbetu, #diplome, #cursus )
diplome ( id, ordre, code, nom, #maquette )
maquette ( id, ordre, code, version, nom, #gestionnaire, #responsable, #co_responsable, #composante )
responsable ( id, #etape, #personne )
cursus ( id, nom, initule )
composante ( id, ordre, nom, initule )


cursus ( num, nom_court, nom_long )
etape ( num, ordre, code, nom, nbetu, #diplome, #cursus )
semestre ( num, ordre, code, nom, peretu, periode, #etape )
ue ( num, ordre, code, nom, peretu, ects, #semestre )
ecue ( num, ordre, code, nom, peretu, ects, #ue, #enseignement )
diplome ( num, ordre, code, nom, #maquette )
responsable ( num, #etape, #enseignant )
departement ( num, ordre, nom_court, nom_long )
maquette ( num, ordre, code, version, nom, #gestionnaire, #responsable1, #responsable2, #departement )
voeu_enseignant_bilan ( num, heures )
voeu_bilan_ligne ( num, heures )
voeu_enseignement_bilan ( num, cours, ctd, td, tp, bonus, colle, responsable, heures )

*/
/*
INSERT INTO role (SELECT num, actif, nom from ancien.role where num>=0);
INSERT INTO actas (SELECT num, actif, enseignant, role FROM ancien.actas WHERE num>=0);
*/
INSERT INTO labo (SELECT num, actif, nom_long, nom_court from ancien.labo where num>0);
INSERT INTO statut (SELECT num, actif, nom_court, obligation from ancien.statut where num>0);
INSERT INTO situation (SELECT num, actif, nom_court, reduction, reduction_legale, ufr from ancien.situation where num>0);
INSERT INTO enseignement (SELECT num, actif, nuac, intitule, intitulelong, foirable=1, cours, td, ctd, tp, colle, s_cours, s_td, s_ctd, s_tp, s_colle, i_cours, i_td, i_ctd, i_tp, i_colle, d_cours, d_td, d_ctd, d_tp, d_colle, n_cours, n_td, n_ctd, n_tp, n_colle, m_cours, m_td, m_ctd, m_tp, m_colle, bonus, information, url  from ancien.enseignement where num>0);
INSERT INTO personne (SELECT num, actif, harpege, nom, prenom, prof_adr1, prof_tel1, prof_tel2, prof_mail, pers_adr1, statut, situation, labo  from ancien.enseignant where num>0);
INSERT INTO ecue (SELECT num, actif, ordre, code, nom, peretu, ects, ue, enseignement  from ancien.ecue where num>0);
INSERT INTO ue (SELECT num, actif, ordre, code, nom, peretu, ects, semestre  from ancien.ue where num>0);
INSERT INTO diplome (SELECT num, actif, ordre, code, nom, maquette  from ancien.diplome where num>0);
INSERT INTO responsable (SELECT num, actif, etape, enseignant  from ancien.responsable where num>0);
INSERT INTO maquette (SELECT num, actif, ordre, code, version, nom, gestionnaire, responsable1, responsable2, departement  from ancien.maquette where num>0);
INSERT INTO cursus (SELECT num, actif, nom_court, nom_long  from ancien.cursus where num>0);
INSERT INTO composante (SELECT num, actif, ordre, nom_court, nom_long  from ancien.departement where num>0);
INSERT INTO semestre (SELECT num, actif, ordre, code, nom, peretu, periode, etape  from ancien.semestre where num>0);
INSERT INTO etape (SELECT num, actif, ordre, code, nom, nbetu, diplome, cursus  from ancien.etape where num>0);
INSERT INTO commentaire_personne(SELECT num, actif, enseignant, 292, date, commentaire from ancien.commentaire_enseignant where num>0);
INSERT INTO commentaire_enseignement(SELECT num, actif, enseignement, 292, date, commentaire from ancien.commentaire_enseignement where num>0);
INSERT INTO heritage(SELECT num, actif, parent, enfant from ancien.heritage where num>0);
INSERT INTO voeu (SELECT num, actif, enseignant, enseignement, cours, ctd, td, tp, bonus, colle, responsable, anciennete from ancien.voeu where num>0);
INSERT INTO panier (SELECT num, actif, enseignant, enseignement, cours>0, ctd>0, td>0, tp>0, commentaire from ancien.panier where num>0);





INSERT INTO role (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO actas (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO labo (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO statut (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO situation (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO enseignement (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO personne (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO composante (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO cursus (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO ue (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO ecue (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO semestre (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO etape (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO diplome (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO maquette (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO responsable (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO commentaire_personne (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO commentaire_enseignement (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO heritage (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO voeu (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO panier (id) VALUES (0) ON CONFLICT do nothing;


ALTER TABLE role ENABLE TRIGGER ALL;
ALTER TABLE actas ENABLE TRIGGER ALL;
ALTER TABLE labo ENABLE TRIGGER ALL;
ALTER TABLE statut ENABLE TRIGGER ALL;
ALTER TABLE situation ENABLE TRIGGER ALL;
ALTER TABLE enseignement ENABLE TRIGGER ALL;
ALTER TABLE personne ENABLE TRIGGER ALL;
ALTER TABLE composante ENABLE TRIGGER ALL;
ALTER TABLE cursus ENABLE TRIGGER ALL;
ALTER TABLE ue ENABLE TRIGGER ALL;
ALTER TABLE ecue ENABLE TRIGGER ALL;
ALTER TABLE semestre ENABLE TRIGGER ALL;
ALTER TABLE etape ENABLE TRIGGER ALL;
ALTER TABLE diplome ENABLE TRIGGER ALL;
ALTER TABLE maquette ENABLE TRIGGER ALL;
ALTER TABLE responsable ENABLE TRIGGER ALL;
ALTER TABLE commentaire_personne ENABLE TRIGGER ALL;
ALTER TABLE commentaire_enseignement ENABLE TRIGGER ALL;
ALTER TABLE heritage ENABLE TRIGGER ALL;
ALTER TABLE voeu ENABLE TRIGGER ALL;
ALTER TABLE panier ENABLE TRIGGER ALL;


/* Pour nettoyer les voeux qui ne sont pas actifs */
UPDATE voeu SET actif = 't';

INSERT INTO Role  (id, nom) VALUES (1, 'SuperAdmin');
INSERT INTO Role  (id, nom) VALUES (2, 'Admin');
INSERT INTO Role  (id, nom) VALUES (3, 'Gestionnaire');

INSERT INTO actAs (id, personne, role) VALUES ( 1, 292, 1);
INSERT INTO actAs (id, personne, role) VALUES ( 2, 292, 2);
INSERT INTO actAs (id, personne, role) VALUES ( 3, 292, 3);