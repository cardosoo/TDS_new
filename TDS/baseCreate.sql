/*****************************

  Construction des tables

*****************************/

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
    FOREIGN KEY(labo) REFERENCES labo(id)    
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
  GROUP BY enseignement.id





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
INSERT INTO labo (SELECT num, actif, nom_long, nom_court from ancien.labo where num>=0);
INSERT INTO statut (SELECT num, actif, nom_court, obligation from ancien.statut where num>=0);
INSERT INTO situation (SELECT num, actif, nom_court, reduction from ancien.situation where num>=0);
INSERT INTO enseignement (SELECT num, actif, nuac, intitule, intitulelong, foirable=1, cours, td, ctd, tp, colle, s_cours, s_td, s_ctd, s_tp, s_colle, i_cours, i_td, i_ctd, i_tp, i_colle, d_cours, d_td, d_ctd, d_tp, d_colle, n_cours, n_td, n_ctd, n_tp, n_colle, m_cours, m_td, m_ctd, m_tp, m_colle, bonus, information, url  from ancien.enseignement where num>=0);
INSERT INTO voeu (SELECT num, actif, enseignant, enseignement, cours, ctd, td, tp, bonus, colle, responsable from ancien.voeu where num>=0);
INSERT INTO personne (SELECT num, actif, harpege, nom, prenom, prof_adr1, prof_tel1, prof_tel2, prof_mail, pers_adr1, statut, situation, labo  from ancien.enseignant where num>=0);
INSERT INTO ecue (SELECT num, actif, ordre, code, nom, peretu, ects, ue, enseignement  from ancien.ecue where num>=0);
INSERT INTO ue (SELECT num, actif, ordre, code, nom, peretu, ects, semestre  from ancien.ue where num>=0);
INSERT INTO diplome (SELECT num, actif, ordre, code, nom, maquette  from ancien.diplome where num>=0);
INSERT INTO responsable (SELECT num, actif, etape, enseignant  from ancien.responsable where num>=0);
INSERT INTO maquette (SELECT num, actif, ordre, code, version, nom, gestionnaire, responsable1, responsable2, departement  from ancien.maquette where num>=0);
INSERT INTO cursus (SELECT num, actif, nom_court, nom_long  from ancien.cursus where num>=0);
INSERT INTO composante (SELECT num, actif, ordre, nom_court, nom_long  from ancien.departement where num>=0);
INSERT INTO semestre (SELECT num, actif, ordre, code, nom, peretu, periode, etape  from ancien.semestre where num>=0);
INSERT INTO etape (SELECT num, actif, ordre, code, nom, nbetu, diplome, cursus  from ancien.etape where num>=0);



ALTER TABLE role ENABLE TRIGGER ALL;
ALTER TABLE actas ENABLE TRIGGER ALL;
ALTER TABLE labo ENABLE TRIGGER ALL;
ALTER TABLE statut ENABLE TRIGGER ALL;
ALTER TABLE situation ENABLE TRIGGER ALL;
ALTER TABLE enseignement ENABLE TRIGGER ALL;
ALTER TABLE voeu ENABLE TRIGGER ALL;
ALTER TABLE personne ENABLE TRIGGER ALL;
ALTER TABLE ecue ENABLE TRIGGER ALL;
ALTER TABLE ue ENABLE TRIGGER ALL;
ALTER TABLE semestre ENABLE TRIGGER ALL;
ALTER TABLE etape ENABLE TRIGGER ALL;
ALTER TABLE diplome ENABLE TRIGGER ALL;
ALTER TABLE maquette ENABLE TRIGGER ALL;
ALTER TABLE responsable ENABLE TRIGGER ALL;
ALTER TABLE cursus ENABLE TRIGGER ALL;
ALTER TABLE composante ENABLE TRIGGER ALL;



INSERT INTO role (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO labo (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO statut (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO situation (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO enseignement (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO personne (id) VALUES (0) ON CONFLICT do nothing;

INSERT INTO composante (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO cursus (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO maquette (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO diplome (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO etape (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO semestre (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO ue (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO ecue (id) VALUES (0) ON CONFLICT do nothing;


INSERT INTO responsable (id) VALUES (0) ON CONFLICT do nothing;


INSERT INTO actas (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO voeu (id) VALUES (0) ON CONFLICT do nothing;

/* Pour nettoyer les voeux qui ne sont pas actifs */
UPDATE voeu SET actif = 't';