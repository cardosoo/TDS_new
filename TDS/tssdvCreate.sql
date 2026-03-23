CREATE EXTENSION IF NOT EXISTS unaccent;
ALTER EXTENSION unaccent SET SCHEMA public;

DROP FUNCTION IF EXISTS eq_cm(cm real) CASCADE;
DROP FUNCTION IF EXISTS eq_ctd(ctd real) CASCADE;
DROP FUNCTION IF EXISTS eq_td(td real) CASCADE;
DROP FUNCTION IF EXISTS eq_tp(tp real) CASCADE;
DROP FUNCTION IF EXISTS eq_extra(extra real) CASCADE;

CREATE OR REPLACE FUNCTION eq_cm(cm real) RETURNS real AS $$
BEGIN
    RETURN cm * 1.5;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION eq_ctd(ctd real) RETURNS real AS $$
BEGIN
    RETURN ctd * 1.25;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION eq_td(td real) RETURNS real AS $$
BEGIN
    RETURN td * 1.0;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION eq_tp(tp real) RETURNS real AS $$
BEGIN
    RETURN tp * 1.0;
END;
$$ LANGUAGE plpgsql;
    
CREATE OR REPLACE FUNCTION eq_extra(extra real) RETURNS real AS $$
BEGIN
    RETURN extra * 1.0;
END;
$$ LANGUAGE plpgsql;



-- ***************************
--  Construction des tables
-- ***************************
DROP TABLE IF EXISTS historique CASCADE;
CREATE TABLE historique (
    qui integer,
    date timestamp without time zone,
    requete text,
    result text,
    ip character varying(100)
);

-- ****************************** Labo as labo
DROP TABLE IF EXISTS labo CASCADE;
CREATE TABLE labo(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    nom                     character varying(100)   DEFAULT ''::character varying,
    acronyme                character varying(100)   DEFAULT ''::character varying,
    url                     character varying(100)   DEFAULT ''::character varying    
);

-- ****************************** Statut as statut
DROP TABLE IF EXISTS statut CASCADE;
CREATE TABLE statut(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    nom                     character varying(100)   DEFAULT ''::character varying,
    obligation              integer                  DEFAULT 192::integer    
);

-- ****************************** Situation as situation
DROP TABLE IF EXISTS situation CASCADE;
CREATE TABLE situation(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    nom                     character varying(100)   DEFAULT ''::character varying,
    reduction               integer                  DEFAULT 0::integer    
);

-- ****************************** TypeUE as typeue
DROP TABLE IF EXISTS typeue CASCADE;
CREATE TABLE typeue(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    nom                     character varying(100)   DEFAULT ''::character varying    
);

-- ****************************** Payeur as payeur
DROP TABLE IF EXISTS payeur CASCADE;
CREATE TABLE payeur(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    nom                     character varying(100)   DEFAULT ''::character varying    
);

-- ****************************** Personne as personne
DROP TABLE IF EXISTS personne CASCADE;
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
    FOREIGN KEY(labo) REFERENCES labo(id),    
    etat_ts                 boolean                  DEFAULT 'FALSE'::boolean    
);

-- ****************************** Enseignement as enseignement
DROP TABLE IF EXISTS enseignement CASCADE;
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
    url                     text                     DEFAULT ''::text,
    etat_ts                 boolean                  DEFAULT 'FALSE'::boolean,    
    typeue                  integer                  DEFAULT 0::integer,
    FOREIGN KEY(typeue) REFERENCES typeue(id),
    payeur                  integer                  DEFAULT 0::integer,
    FOREIGN KEY(payeur) REFERENCES payeur(id)    
);

-- ****************************** Role as role
DROP TABLE IF EXISTS role CASCADE;
CREATE TABLE role(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    nom                     character varying(100)   DEFAULT ''::character varying    
);

-- ****************************** actAs as actas
DROP TABLE IF EXISTS actas CASCADE;
CREATE TABLE actas(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    personne                integer                  DEFAULT 0::integer,
    FOREIGN KEY(personne) REFERENCES personne(id),
    role                    integer                  DEFAULT 0::integer,
    FOREIGN KEY(role) REFERENCES role(id)    
);

-- ****************************** Voeu as voeu
DROP TABLE IF EXISTS voeu CASCADE;
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
DROP TABLE IF EXISTS composante CASCADE;
CREATE TABLE composante(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    ordre                   integer                  DEFAULT 0::integer,
    nom                     character varying(100)   DEFAULT ''::character varying,
    intitule                 character varying(100)   DEFAULT ''::character varying    
);

-- ****************************** Cursus as cursus
DROP TABLE IF EXISTS cursus CASCADE;
CREATE TABLE cursus(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    nom                     character varying(100)   DEFAULT ''::character varying,
    intitule                 character varying(100)   DEFAULT ''::character varying    
);

-- ****************************** Maquette as maquette
DROP TABLE IF EXISTS maquette CASCADE;
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
DROP TABLE IF EXISTS diplome CASCADE;
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
DROP TABLE IF EXISTS etape CASCADE;
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
DROP TABLE IF EXISTS responsable CASCADE;
CREATE TABLE responsable(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    etape                   integer                  DEFAULT 0::integer,
    FOREIGN KEY(etape) REFERENCES etape(id),
    personne                integer                  DEFAULT 0::integer,
    FOREIGN KEY(personne) REFERENCES personne(id)    
);


-- ****************************** Semestre as semestre
DROP TABLE IF EXISTS semestre CASCADE;
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
DROP TABLE IF EXISTS ue CASCADE;
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
DROP TABLE IF EXISTS ecue CASCADE;
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
DROP TABLE IF EXISTS commentaire_personne CASCADE;
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
DROP TABLE IF EXISTS commentaire_enseignement CASCADE;
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


-- ****************************** Domaine as domaine
DROP TABLE IF EXISTS domaine CASCADE;
CREATE TABLE domaine(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    nom                     character varying(100)   DEFAULT ''::character varying,
    acronyme                character varying(100)   DEFAULT ''::character varying    
);

-- ****************************** domaine_personne as domaine_personne
DROP TABLE IF EXISTS domaine_personne CASCADE;
CREATE TABLE domaine_personne(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    domaine                 integer                  DEFAULT 0::integer,
    FOREIGN KEY(domaine) REFERENCES domaine(id),
    personne             integer                  DEFAULT 0::integer,
    FOREIGN KEY(personne) REFERENCES personne(id),
    ordre                   integer                  DEFAULT 0::integer,
    quotite                 real                     DEFAULT '0'::real    
);

-- ****************************** domaine_enseignement as domaine_enseignement
DROP TABLE IF EXISTS domaine_enseignement CASCADE;
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
DROP TABLE IF EXISTS domaine_responsable CASCADE;
CREATE TABLE domaine_responsable(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    domaine                 integer                  DEFAULT 0::integer,
    FOREIGN KEY(domaine) REFERENCES domaine(id),
    responsable                integer                  DEFAULT 0::integer,
    FOREIGN KEY(responsable) REFERENCES personne(id)    
);


-- *****************************
--  Construction des vues
-- *****************************

--  personne_charge
CREATE OR REPLACE VIEW personne_charge AS
SELECT 
     P.id,
     (ST.obligation-SI.reduction) as charge
  FROM personne as P
    LEFT JOIN statut  as ST  on ST.id    = P.statut
    LEFT JOIN situation as SI on SI.id = P.situation
;


-- correspondant_enseignement 
CREATE OR REPLACE  VIEW correspondant_enseignement AS
 SELECT E.id,
    V.personne AS correspondant
   FROM enseignement AS E
     LEFT JOIN voeu AS V ON V.enseignement = E.id 
     AND v.correspondant;


-- enseignement_besoins
CREATE OR REPLACE  VIEW enseignement_besoins AS
SELECT 
      E.id,
      eq_cm( E.cm   *E.s_cm   *E.d_cm   *E.i_cm )
    + eq_ctd(E.ctd  *E.s_ctd  *E.d_ctd  *E.i_ctd)
    + eq_td (E.td   *E.s_td   *E.d_td   *E.i_td )
    + eq_tp (E.tp   *E.s_tp   *E.d_tp   *E.i_tp )
    + eq_extra(E.extra*E.s_extra*E.d_extra*E.i_extra)
    + bonus 
    as besoins
FROM enseignement as E
;

-- enseignement_besoins_detail
CREATE OR REPLACE  VIEW enseignement_besoins_detail AS
 SELECT e.id,
    e.cm * e.s_cm * e.d_cm * e.i_cm AS cm,
    e.ctd * e.s_ctd * e.d_ctd * e.i_ctd AS ctd,
    e.td * e.s_td * e.d_td * e.i_td AS td,
    e.tp * e.s_tp * e.d_tp * e.i_tp AS tp,
    e.extra * e.s_extra * e.d_extra * e.i_extra AS extra,
    e.bonus
   FROM enseignement e;

-- structure_enseignement
CREATE OR REPLACE  VIEW structure_enseignement AS
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
    ((((((etape.nbetu * semestre.peretu) / 100) * ue.peretu) / 100) * ecue.peretu) / 100) AS nbetu,
    de.domaine
   FROM ((((((maquette
     LEFT JOIN diplome ON ((maquette.id = diplome.maquette)))
     LEFT JOIN etape ON ((diplome.id = etape.diplome)))
     LEFT JOIN semestre ON ((etape.id = semestre.etape)))
     LEFT JOIN ue ON ((semestre.id = ue.semestre)))
     LEFT JOIN ecue ON ((ue.id = ecue.ue)))
     LEFT JOIN domaine_enseignement de ON ((de.enseignement = ecue.enseignement)));

-- enseignement_structure
CREATE OR REPLACE  VIEW enseignement_structure AS
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


-- enseignement_periode
CREATE OR REPLACE  VIEW enseignement_periode AS
SELECT DISTINCT enseignement.id,
    array_agg(DISTINCT semestre.periode) AS periode
   FROM enseignement
     LEFT JOIN structure_enseignement as SE on SE.enseignement = enseignement.id
     LEFT JOIN semestre ON SE.semestre = semestre.id
  WHERE enseignement.id > 0
  GROUP BY enseignement.id;



-- voeu_bilan_ligne

CREATE OR REPLACE  VIEW voeu_bilan_ligne AS
 SELECT v.id,
      eq_cm (v.cm)
    + eq_ctd(v.ctd) 
    + eq_td(v.td)
    + eq_tp(v.tp) 
    + eq_extra(v.extra)
    + v.bonus AS heures
   FROM voeu v
   LEFT JOIN enseignement e ON e.id = v.enseignement;

--  voeu_detail_heures
CREATE OR REPLACE  VIEW voeu_detail_heures AS
SELECT v.id,
    eq_cm(v.cm) AS cm,
    eq_ctd(v.ctd) AS ctd,
    eq_td(v.td) AS td,
    eq_tp(v.tp) AS tp,
    eq_extra(v.extra) AS extra,
    v.bonus
   FROM voeu v
     LEFT JOIN enseignement e ON e.id = v.enseignement
  WHERE v.id > 0 AND e.id > 0;

-- voeu_enseignement_heures_temp
CREATE OR REPLACE  VIEW voeu_enseignement_heures_temp AS
SELECT E.id,
    sum(VBL.heures) AS heures
   FROM enseignement E
     LEFT JOIN voeu V ON E.id = V.enseignement
     LEFT JOIN voeu_bilan_ligne as VBL ON VBL.id = V.id
     LEFT JOIN personne P ON P.id = V.personne 
  WHERE (V.id > 0 OR V.id IS NULL)
  AND V.actif AND P.actif
  GROUP BY E.id;

-- voeu_enseignement_heures
CREATE OR REPLACE  VIEW voeu_enseignement_heures AS
SELECT E.id,
    CASE 
        WHEN VEHT.heures IS NULL THEN 0
        ELSE VEHT.heures
    END as heures   
FROM enseignement as E
LEFT JOIN voeu_enseignement_heures_temp as VEHT on E.id = VEHT.id
;


-- voeu_enseignement_detail_temp
CREATE OR REPLACE  VIEW voeu_enseignement_detail_temp AS
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


-- voeu_enseignement_detail
CREATE OR REPLACE  VIEW voeu_enseignement_detail AS
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


-- voeu_enseignement_bilan
CREATE OR REPLACE  VIEW voeu_enseignement_bilan AS
 SELECT e.id,
    e.cm * e.s_cm * e.d_cm * e.i_cm - ved.cm AS cm,
    e.ctd * e.s_ctd * e.d_ctd * e.i_ctd - ved.ctd AS ctd,
    e.td * e.s_td * e.d_td * e.i_td - ved.td AS td,
    e.tp * e.s_tp * e.d_tp * e.i_tp - ved.tp AS tp,
    e.extra * e.s_extra * e.d_extra * e.i_extra - ved.extra AS extra,
    e.bonus - ved.bonus AS bonus,
    1 - ved.correspondant AS correspondant,
    eb.besoins - veh.heures AS heures
   FROM enseignement e
     LEFT JOIN voeu_enseignement_detail ved ON e.id = ved.id
     LEFT JOIN enseignement_besoins eb ON e.id = eb.id
     LEFT JOIN voeu_enseignement_heures veh ON e.id = veh.id
;


-- voeu_personne_heures_temp
CREATE OR REPLACE  VIEW voeu_personne_heures_temp AS
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

-- voeu_personne_heures` 
CREATE OR REPLACE  VIEW voeu_personne_heures AS
SELECT 
      P.id,
      CASE 
          WHEN VPHT.heures IS NULL THEN 0
          ELSE VPHT.heures
      END as heures
FROM  personne as P
LEFT JOIN voeu_personne_heures_temp as VPHT on VPHT.id = P.id  
;

-- voeu_personne_bilan 
CREATE OR REPLACE  VIEW voeu_personne_bilan AS 
SELECT
    PC.id,
	PC.charge - VPH.heures as heures
FROM personne_charge as PC
LEFT JOIN voeu_personne_heures as VPH on PC.id = VPH.id;


-- voeu_personne_bilan 
CREATE OR REPLACE  VIEW enseignement_domaine AS 
 SELECT e.id,
    string_agg(((round(de.quotite / s.sq * 100::double precision * 100::double precision) / 100::double precision) || '% '::text) || d.nom::text, '|'::text) AS quotite
   FROM enseignement e
     LEFT JOIN domaine_enseignement de ON de.enseignement = e.id
     LEFT JOIN domaine d ON de.domaine = d.id
     LEFT JOIN ( SELECT de_1.enseignement AS id,
            sum(de_1.quotite) AS sq
           FROM domaine_enseignement de_1
          GROUP BY de_1.enseignement) s ON e.id = s.id
  GROUP BY e.id;

-- ****************************
--   Migration des tables
-- ****************************

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
    composante,
    domaine,
    domaine_personne,
    domaine_enseignement,
    domaine_responsable
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
ALTER TABLE domaine DISABLE TRIGGER ALL;
ALTER TABLE domaine_personne DISABLE TRIGGER ALL;
ALTER TABLE domaine_enseignement DISABLE TRIGGER ALL;
ALTER TABLE domaine_responsable DISABLE TRIGGER ALL;


INSERT INTO labo (SELECT num, actif, nom_long, nom_court from ancien.labo where num>=0);
INSERT INTO statut (SELECT num, actif, nom_court, obligation from ancien.statut where num>=0);
INSERT INTO situation (SELECT num, actif, nom_court, reduction from ancien.situation where num>=0);
INSERT INTO enseignement (SELECT num, actif, nuac, intitule, intitule, actif, cours, td, ctd, tp, colle  ,   1,    1,     1,    1,    1, i_cours, i_td, i_ctd, i_tp, i_colle, d_cours, d_td, d_ctd, d_tp, d_colle,  1,    1,     1,    1,       1, m_cours, m_td, m_ctd, m_tp, m_colle, bonus, '', ''  from ancien.enseignement where num>=0);
                                

INSERT INTO voeu (SELECT num, actif, enseignant, enseignement, cours, ctd, td, tp, bonus, colle, responsable from ancien.voeu where num>=0);
INSERT INTO personne (SELECT num, actif, harpege, nom, prenom, '', tel, '', mail, '', statut, situation, labo  from ancien.enseignant where num>=0);
INSERT INTO ecue (SELECT num, actif, ordre, code, nom, peretu, ects, ue, enseignement  from ancien.ecue where num>=0);
INSERT INTO ue (SELECT num, actif, ordre, code, nom, peretu, ects, semestre  from ancien.ue where num>=0);
INSERT INTO diplome (SELECT num, actif, ordre, code, nom, maquette  from ancien.diplome where num>=0);
INSERT INTO responsable (SELECT num, actif, etape, enseignant  from ancien.responsable where num>=0);
INSERT INTO maquette (SELECT num, actif, ordre, code, version, nom, gestionnaire, responsable1, responsable2, departement  from ancien.maquette where num>=0);
INSERT INTO cursus (SELECT num, actif, nom_court, nom_long  from ancien.cursus where num>=0);
INSERT INTO composante (SELECT num, actif, ordre, nom_court, nom_long  from ancien.departement where num>=0);
INSERT INTO semestre (SELECT num, actif, ordre, code, nom, peretu, periode, etape  from ancien.semestre where num>=0);
INSERT INTO etape (SELECT num, actif, ordre, code, nom, nbetu, diplome, cursus  from ancien.etape where num>=0);

INSERT INTO domaine (SELECT num, actif, nom, nom from ancien.domaine where num>=0);
INSERT INTO domaine_personne (SELECT num, actif, domaine, enseignant, ordre, quotite from ancien.domaine_enseignant where num>=0);
INSERT INTO domaine_enseignement (SELECT num, actif, domaine, enseignement, ordre, quotite from ancien.domaine_enseignement where num>=0);
INSERT INTO domaine_responsable (SELECT num, actif, domaine, enseignant from ancien.domaine_responsable where num>=0);


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
ALTER TABLE domaine ENABLE TRIGGER ALL;
ALTER TABLE domaine_personne ENABLE TRIGGER ALL;
ALTER TABLE domaine_enseignement ENABLE TRIGGER ALL;
ALTER TABLE domaine_responsable ENABLE TRIGGER ALL;


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
INSERT INTO domaine (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO domaine_personne (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO domaine_enseignement (id) VALUES (0) ON CONFLICT do nothing;
INSERT INTO domaine_responsable (id) VALUES (0) ON CONFLICT do nothing;

-- Pour nettoyer les voeux qui ne sont pas actifs
UPDATE voeu SET actif = 't';

INSERT INTO Role  (id, nom) VALUES (1, 'SuperAdmin');
INSERT INTO Role  (id, nom) VALUES (2, 'Admin');
INSERT INTO Role  (id, nom) VALUES (3, 'Gestionnaire');
INSERT INTO Role  (id, nom) VALUES (4, 'CRUD');
INSERT INTO Role  (id, nom) VALUES (5, 'respDomaine');
INSERT INTO Role  (id, nom) VALUES (6, 'respParcours');
INSERT INTO Role  (id, nom) VALUES (7, 'respDiplome');
INSERT INTO Role  (id, nom) VALUES (8, 'bureauRHE');

INSERT INTO actAs (id, personne, role) VALUES ( 1, 100, 1);
INSERT INTO actAs (id, personne, role) VALUES ( 2, 100, 2);
INSERT INTO actAs (id, personne, role) VALUES ( 3, 100, 3);
INSERT INTO actAs (id, personne, role) VALUES ( 4, 100, 4);
INSERT INTO actAs (id, personne, role) VALUES ( 5, 100, 5);
INSERT INTO actAs (id, personne, role) VALUES ( 6, 100, 6);
INSERT INTO actAs (id, personne, role) VALUES ( 7, 100, 7);
INSERT INTO actAs (id, personne, role) VALUES ( 8, 100, 8);
