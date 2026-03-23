

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
  WHERE v.id > 0 AND E.actif;



/*  enseignant_charge */
CREATE VIEW personne_charge AS
SELECT 
     P.id,
     (ST.obligation-SI.reduction) as charge
  FROM personne as P
    LEFT JOIN statut  as ST  on ST.id    = P.statut
    LEFT JOIN situation as SI on SI.id = P.situation
WHERE P.id >0 AND P.actif;


/* voeu_personne_heures_temp */
CREATE VIEW voeu_personne_heures_temp AS
SELECT 
    E.id,
    sum(VBL.heures) as heures
FROM enseignement as E
    LEFT JOIN voeu as V on E.id = V.enseignement
    LEFT JOIN voeu_bilan_ligne as VBL on V.id = VBL.id
WHERE E.id >0
AND E.actif
AND (V.id>0 OR V.id is null)
GROUP BY E.id;

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
WHERE P.id>0
AND P.actif;

/* voeu_personne_bilan */
CREATE VIEW voeu_personne_bilan AS 
SELECT
    PC.id,
	PC.charge - VPH.heures as heures
FROM personne_charge as PC
LEFT JOIN voeu_personne_heures as VPH on PC.id = VPH.id;


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
WHERE (E.id>0 AND E.actif)
  AND (V.personne>0 or V.personne is null)
  AND (V.id>0 or V.id is null)
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
WHERE E.id>0
AND E.actif;


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
WHERE E.id>0
AND   E.actif;

/* voeu_enseignement_heures_temp */
CREATE VIEW voeu_enseignement_heures_temp AS
SELECT E.id,
    sum(EB.besoins) as heures
FROM enseignement as E
LEFT JOIN voeu as V on E.id = V.enseignement
LEFT JOIN enseignement_besoins as EB on EB.id = E.id 
WHERE E.id >0
AND E.actif
AND (V.id>0 OR V.id is null)
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
WHERE E.id >0
AND E.actif;

/* voeu_enseignement_bilan */
CREATE VIEW voeu_enseignement_bilan AS
SELECT E.id,
	E.cm   *E.i_cm    - VED.cm    as cm,
	E.ctd  *E.i_ctd   - VED.ctd   as ctd,
	E.td   *E.i_td    - VED.td    as td,
	E.tp   *E.i_tp    - VED.tp    as tp,
	E.extra*E.i_extra - VED.extra as extra,
	E.bonus           - VED.bonus as bonus,
	1 - VED.correspondant as correspondant,
	EB.besoins - VEH.heures as heures
  FROM enseignement as E
    LEFT JOIN voeu_enseignement_detail as VED on  E.id = VED.id
    LEFT JOIN enseignement_besoins as EB on E.id = EB.id
    LEFT JOIN voeu_enseignement_heures as VEH on E.id = VEH.id
WHERE E.id >0
AND E.actif;

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

