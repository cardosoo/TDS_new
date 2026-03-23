/* Les vues pour TS SDV */

CREATE OR REPLACE FUNCTION public.eq_cm(cm real) RETURNS real AS $$
    BEGIN
        RETURN cm * 1.5;
    END; 
$$ LANGUAGE plpgsql ;


CREATE OR REPLACE FUNCTION public.eq_ctd(ctd real) RETURNS real AS $$
    BEGIN
        RETURN ctd * 1.25;
    END;
$$ LANGUAGE plpgsql ;

CREATE OR REPLACE FUNCTION public.eq_extra(extra real) RETURNS real AS $$
    BEGIN
        RETURN extra * 1.0;
    END;
$$ LANGUAGE plpgsql ;

CREATE OR REPLACE FUNCTION public.eq_td(td real) RETURNS real AS $$
    BEGIN
        RETURN td * 1.0;
    END;
$$ LANGUAGE plpgsql ;

CREATE OR REPLACE FUNCTION public.eq_tp(tp real) RETURNS real AS $$
    BEGIN
        RETURN tp * 1.0;
    END;
$$ LANGUAGE plpgsql ;


CREATE OR REPLACE VIEW correspondant_enseignement AS
    SELECT
        e.id,
        v.personne AS correspondant
    FROM enseignement e
    LEFT JOIN voeu v ON v.enseignement = e.id AND v.correspondant;


CREATE OR REPLACE VIEW enseignement_besoins AS
    SELECT 
        e.id,
        eq_cm(e.cm * e.s_cm * e.d_cm * e.i_cm) + eq_ctd(e.ctd * e.s_ctd * e.d_ctd * e.i_ctd) + eq_td(e.td * e.s_td * e.d_td * e.i_td) + eq_tp(e.tp * e.s_tp * e.d_tp * e.i_tp) + eq_extra(e.extra * e.s_extra * e.d_extra * e.i_extra) + e.bonus AS besoins
    FROM enseignement e;

CREATE OR REPLACE VIEW enseignement_besoins_details AS
    SELECT 
        e.id,
        e.cm * e.s_cm * e.d_cm * e.i_cm AS cm,
        e.ctd * e.s_ctd * e.d_ctd * e.i_ctd AS ctd,
        e.td * e.s_td * e.d_td * e.i_td AS td,
        e.tp * e.s_tp * e.d_tp * e.i_tp AS tp,
        e.extra * e.s_extra * e.d_extra * e.i_extra AS extra,
        e.bonus
    FROM enseignement e;

CREATE OR REPLACE VIEW enseignement_domaine AS
    SELECT 
        e.id,
        string_agg(((round(de.quotite / s.sq * 100::real * 100::real) / 100::real) || '% '::text) || d.nom::text, '|'::text) AS quotite
    FROM enseignement e
    LEFT JOIN domaine_enseignement de ON de.enseignement = e.id
    LEFT JOIN domaine d ON de.domaine = d.id
    LEFT JOIN ( 
        SELECT 
            de_1.enseignement AS id,
            sum(de_1.quotite) AS sq
        FROM domaine_enseignement de_1
        GROUP BY de_1.enseignement
    ) s ON e.id = s.id
    GROUP BY e.id;

CREATE OR REPLACE VIEW structure_enseignement AS
    SELECT 
        ecue.enseignement,
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
        etape.nbetu * semestre.peretu / 100 * ue.peretu / 100 * ecue.peretu / 100 AS nbetu,
        de.domaine
    FROM maquette
    LEFT JOIN diplome ON maquette.id = diplome.maquette
    LEFT JOIN etape ON diplome.id = etape.diplome
    LEFT JOIN semestre ON etape.id = semestre.etape
    LEFT JOIN ue ON semestre.id = ue.semestre
    LEFT JOIN ecue ON ue.id = ecue.ue
    LEFT JOIN domaine_enseignement de ON de.enseignement = ecue.enseignement ; 


CREATE OR REPLACE VIEW enseignement_periode AS
    SELECT DISTINCT 
        enseignement.id,
        array_agg(DISTINCT semestre.periode) AS periode
    FROM enseignement
    LEFT JOIN structure_enseignement se ON se.enseignement = enseignement.id
    LEFT JOIN semestre ON se.semestre = semestre.id
    WHERE enseignement.id > 0
    GROUP BY enseignement.id;

CREATE OR REPLACE VIEW enseignement_structure AS
    SELECT DISTINCT 
        enseignement.id,
        array_agg(DISTINCT semestre.periode) AS periode,
        string_agg(DISTINCT concat(se.code_ue, '_', se.code_ecue), '|'::text) AS code,
        string_agg(DISTINCT cursus.nom::text, '|'::text) AS cursus,
        string_agg(DISTINCT etape.nom::text, '|'::text) AS etape,
        string_agg(DISTINCT maquette.nom::text, '|'::text) AS maquette,
        string_agg(DISTINCT composante.nom::text, '|'::text) AS composante,
        array_agg(se.nbetu) AS nbetu,
        sum(se.nbetu) AS netu,
        string_agg(DISTINCT se.code_ecue, '|'::text) AS ecue
    FROM enseignement
    LEFT JOIN structure_enseignement se ON se.enseignement = enseignement.id
    LEFT JOIN semestre ON se.semestre = semestre.id
    LEFT JOIN cursus ON se.cursus = cursus.id
    LEFT JOIN etape ON se.etape = etape.id
    LEFT JOIN maquette ON se.maquette = maquette.id
    LEFT JOIN composante ON se.composante = composante.id
    WHERE enseignement.id > 0
    GROUP BY enseignement.id;


CREATE OR REPLACE VIEW personne_situation_reduction AS
    SELECT
        p.id,
        COALESCE(sum(ps.reduction), 0::bigint) AS reduction
    FROM personne p
    LEFT JOIN personne_situation ps ON ps.personne = p.id
    WHERE ps.id IS NULL OR ps.actif
    GROUP BY p.id;

CREATE OR REPLACE VIEW personne_referentiel_heures AS
    SELECT
        p.id,
        COALESCE(sum(pf.volume), 0::bigint) AS heures
    FROM personne p
    LEFT JOIN personne_foncref pf ON pf.personne = p.id
    WHERE pf.id IS NULL OR pf.actif
    GROUP BY p.id;

CREATE OR REPLACE VIEW personne_charge AS
    SELECT 
        p.id,
        (st.obligation - si.reduction - COALESCE(str.reduction, 0::bigint) - prh.heures)::integer AS charge
    FROM personne p
    LEFT JOIN statut st ON st.id = p.statut
    LEFT JOIN personne_situation_reduction str ON str.id = p.id
    LEFT JOIN personne_referentiel_heures prh ON prh.id = p.id
    LEFT JOIN situation si ON si.id = p.situation;

/* version pour foire 
CREATE OR REPLACE VIEW voeu_detail_heures AS
SELECT v.id,
    v.cm * e.s_cm * e.d_cm  AS cm,
    v.ctd * e.s_ctd * e.d_ctd AS ctd,
    v.td * e.s_td * e.d_td AS td,
    v.tp * e.s_tp * e.d_tp AS tp,
    v.extra * e.s_extra * e.d_extra AS extra,
    v.bonus   
  FROM voeu v
     LEFT JOIN enseignement e ON e.id = v.enseignement
  WHERE v.id > 0 AND e.id > 0;
*/

/* version pour TSOnline */

CREATE OR REPLACE VIEW voeu_detail_heures AS
SELECT v.id,
    v.cm AS cm,
    v.ctd AS ctd,
    v.td AS td,
    v.tp AS tp,
    v.extra AS extra,
    v.bonus
   FROM voeu v
WHERE v.id > 0;

CREATE OR REPLACE VIEW voeu_detail_htd AS
SELECT 
    VDH.id,
    eq_cm(VDH.cm) AS cm,
    eq_ctd(VDH.ctd) AS ctd,
    eq_td(VDH.td) AS td,
    eq_tp(VDH.tp) AS tp,
    eq_extra(VDH.extra) AS extra,
    VDH.bonus
   FROM voeu_detail_heures as VDH;


CREATE OR REPLACE VIEW voeu_bilan_ligne AS
SELECT 
    VDH.id,
    VDH.cm + VDH.ctd + VDH.td+ VDH.tp + VDH.extra + VDH.bonus AS heures
   FROM voeu_detail_htd as VDH;


CREATE OR REPLACE VIEW voeu_enseignement_detail_temp AS
    SELECT 
        e.id,
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
            END
        ) AS correspondant
    FROM enseignement e
    LEFT JOIN voeu v ON v.enseignement = e.id
    WHERE (v.id > 0 OR v.id IS NULL) AND v.actif
    GROUP BY e.id;


CREATE OR REPLACE VIEW voeu_enseignement_detail AS
    SELECT
        e.id,
        COALESCE(vedt.cm, 0::real) AS cm,
        COALESCE(vedt.ctd, 0::real) AS ctd,
        COALESCE(vedt.td, 0::real) AS td,
        COALESCE(vedt.tp, 0::real) AS tp,
        COALESCE(vedt.extra, 0::real) AS extra,
        COALESCE(vedt.bonus, 0::real) AS bonus,
        COALESCE(vedt.correspondant, 0::bigint) AS correspondant
    FROM enseignement e
    LEFT JOIN voeu_enseignement_detail_temp vedt ON e.id = vedt.id;

CREATE OR REPLACE VIEW voeu_enseignement_heures_temp AS
    SELECT 
        e.id,
        sum(vbl.heures) AS heures
    FROM enseignement e
    LEFT JOIN voeu v ON e.id = v.enseignement
    LEFT JOIN voeu_bilan_ligne vbl ON vbl.id = v.id
    LEFT JOIN personne p ON p.id = v.personne
    WHERE (v.id > 0 OR v.id IS NULL) AND v.actif AND p.actif
    GROUP BY e.id;

CREATE OR REPLACE VIEW voeu_enseignement_heures AS
    SELECT 
        e.id,
        CASE
            WHEN veht.heures IS NULL THEN 0::real
            ELSE veht.heures
        END AS heures
    FROM enseignement e
    LEFT JOIN voeu_enseignement_heures_temp veht ON e.id = veht.id;    
    


CREATE OR REPLACE VIEW voeu_enseignement_bilan AS
    SELECT 
        e.id,
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
    LEFT JOIN voeu_enseignement_heures veh ON e.id = veh.id;

CREATE OR REPLACE VIEW voeu_personne_heures_temp AS
    SELECT
        p.id,
        sum(vbl.heures) AS heures
    FROM personne p
    LEFT JOIN voeu v ON p.id = v.personne
    LEFT JOIN voeu_bilan_ligne vbl ON v.id = vbl.id
    LEFT JOIN enseignement e ON e.id = v.enseignement
    WHERE (v.id > 0 OR v.id IS NULL) AND v.actif AND e.actif
    GROUP BY p.id;

    
CREATE OR REPLACE VIEW voeu_personne_heures AS
    SELECT 
        p.id,
        CASE
            WHEN vpht.heures IS NULL THEN 0::real
            ELSE vpht.heures
        END AS heures
    FROM personne p
    LEFT JOIN voeu_personne_heures_temp vpht ON vpht.id = p.id;

    
CREATE OR REPLACE VIEW voeu_personne_bilan AS
    SELECT 
        pc.id,
        pc.charge::real - vph.heures AS heures
    FROM personne_charge pc
    LEFT JOIN voeu_personne_heures vph ON pc.id = vph.id;


