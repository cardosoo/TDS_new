CREATE OR REPLACE FUNCTION eq_cm(cm real) RETURNS real AS $$
    BEGIN
        RETURN cm * 1.5;
    END; 
$$ LANGUAGE plpgsql ;

CREATE OR REPLACE FUNCTION eq_ctd(ctd real) RETURNS real AS $$
    BEGIN
        RETURN ctd * 1.14;
    END;
$$ LANGUAGE plpgsql ;

CREATE OR REPLACE FUNCTION eq_td(td real) RETURNS real AS $$
    BEGIN
        RETURN td * 1.0;
    END;
$$ LANGUAGE plpgsql ;

CREATE OR REPLACE FUNCTION eq_tp(tp real) RETURNS real AS $$
    BEGIN
        RETURN tp * 1.0;
    END;
$$ LANGUAGE plpgsql ;

CREATE OR REPLACE FUNCTION eq_extra(extra real) RETURNS real AS $$
    BEGIN
        RETURN extra * 1.0;
    END;
$$ LANGUAGE plpgsql ;

CREATE OR REPLACE FUNCTION eq_bonus(bonus real) RETURNS real AS $$
    BEGIN
        RETURN bonus * 1.0;
    END;
$$ LANGUAGE plpgsql ;


CREATE OR REPLACE VIEW correspondant_enseignement AS
    SELECT
        e.id,
        v.personne AS correspondant
    FROM enseignement e
    LEFT JOIN voeu v ON v.enseignement = e.id AND v.correspondant;

CREATE OR REPLACE VIEW enseignement_etudiant_details AS
    SELECT 
        e.id,
        e.s_cm    * e.d_cm    AS cm,
        e.s_ctd   * e.d_ctd   AS ctd,
        e.s_td    * e.d_td    AS td,
        e.s_tp    * e.d_tp    AS tp,
        e.s_extra * e.d_extra AS extra,
        e.bonus
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

CREATE OR REPLACE VIEW enseignement_besoins AS
    SELECT 
        ebd.id,
        eq_cm(ebd.cm) + eq_ctd(ebd.ctd) + eq_td(ebd.td) + eq_tp(ebd.tp) + eq_extra(ebd.extra) + eq_bonus(ebd.bonus) AS besoins
    FROM enseignement_besoins_details ebd;

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
        etape.nbetu * semestre.peretu / 100 * ue.peretu / 100 * ecue.peretu / 100 AS nbetu
    FROM maquette
    LEFT JOIN diplome ON maquette.id = diplome.maquette
    LEFT JOIN etape ON diplome.id = etape.diplome
    LEFT JOIN semestre ON etape.id = semestre.etape
    LEFT JOIN ue ON semestre.id = ue.semestre
    LEFT JOIN ecue ON ue.id = ecue.ue;    

CREATE OR REPLACE VIEW enseignement_periode AS
    SELECT DISTINCT 
        e.id,
        array_agg(DISTINCT sem.periode) AS periode
    FROM enseignement e
    LEFT JOIN structure_enseignement se ON se.enseignement = e.id
    LEFT JOIN semestre AS sem ON se.semestre = sem.id
    WHERE e.id > 0
    GROUP BY e.id;

CREATE OR REPLACE VIEW enseignement_structure AS
    SELECT DISTINCT 
        e.id,
        array_agg(DISTINCT sem.periode) AS periode,
        string_agg(DISTINCT concat(se.code_ue, '_', se.code_ecue), '|'::text) AS code,
        string_agg(DISTINCT cur.nom::text, '|'::text) AS cursus,
        string_agg(DISTINCT eta.nom::text, '|'::text) AS etape,
        string_agg(DISTINCT maq.nom::text, '|'::text) AS maquette,
        string_agg(DISTINCT comp.nom::text, '|'::text) AS composante,
        array_agg(se.nbetu) AS nbetu,
        sum(se.nbetu) AS netu
    FROM enseignement e
    LEFT JOIN structure_enseignement se ON se.enseignement = e.id
    LEFT JOIN semestre AS sem ON se.semestre = sem.id
    LEFT JOIN cursus AS cur ON se.cursus = cur.id
    LEFT JOIN etape AS eta ON se.etape = eta.id
    LEFT JOIN maquette AS maq ON se.maquette = maq.id
    LEFT JOIN composante AS comp ON se.composante = comp.id
    WHERE e.id > 0
    GROUP BY e.id;

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
    LEFT JOIN statut AS st ON st.id = p.statut
    LEFT JOIN personne_situation_reduction AS str ON str.id = p.id
    LEFT JOIN personne_referentiel_heures AS prh ON prh.id = p.id
    LEFT JOIN situation AS si ON si.id = p.situation;


CREATE OR REPLACE VIEW voeu_detail_heures AS
    SELECT
        v.id,
        v.cm * e.s_cm * e.d_cm AS cm,
        v.ctd * e.s_ctd * e.d_ctd AS ctd,
        v.td * e.s_td * e.d_td AS td,
        v.tp * e.s_tp * e.d_tp AS tp,
        v.extra * e.s_extra * e.d_extra AS extra,
        v.bonus
    FROM voeu v
    LEFT JOIN enseignement e ON e.id = v.enseignement
    WHERE v.id > 0 AND e.id > 0;

CREATE OR REPLACE VIEW voeu_detail_htd AS
    SELECT 
        vdh.id,
        eq_cm(vdh.cm) AS cm,
        eq_ctd(vdh.ctd) AS ctd,
        eq_td(vdh.td) AS td,
        eq_tp(vdh.tp) AS tp,
        eq_extra(vdh.extra) AS extra,
        eq_bonus(vdh.bonus) AS bonus
    FROM voeu_detail_heures as vdh
    WHERE vdh.id > 0;


CREATE OR REPLACE VIEW voeu_bilan_ligne AS
    SELECT 
        vdhtd.id,
        vdhtd.cm + vdhtd.ctd + vdhtd.td + vdhtd.tp + vdhtd.extra + vdhtd.bonus AS heures
    FROM voeu_detail_htd as vdhtd;


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

/* c'est sans doute la version pour SDV
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
*/

/* ici c'est la version pour la foire */
CREATE OR REPLACE VIEW voeu_enseignement_bilan AS
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


COMMENT ON FUNCTION eq_cm    IS 'Permet de convertir les hCM en HETD';
COMMENT ON FUNCTION eq_ctd   IS 'Permet de convertir les hCTD en HETD';
COMMENT ON FUNCTION eq_td    IS 'Permet de convertir les hTD en HETD';
COMMENT ON FUNCTION eq_tp    IS 'Permet de convertir les hTP en HETD';
COMMENT ON FUNCTION eq_extra IS 'Permet de convertir les hExtra en HETD';
COMMENT ON FUNCTION eq_bonus IS 'Permet de convertir les hBonus en HETD';


COMMENT ON VIEW correspondant_enseignement IS 'associe  un enseignement à un correspondant (mais à quoi cela sert t''il ?)';
COMMENT ON VIEW enseignement_etudiant_details IS 'Les volumes pour un étudiant en hCM, hCTD, hTD, hTP, hExtra, hBonus';
COMMENT ON VIEW enseignement_besoins_details IS 'Les besoins pour un enseignement en hCM, hCTD, hTD, hTP, hExtra, hBonus';
COMMENT ON VIEW enseignement_besoins IS 'Les besoins pour un enseignement en hETD pour CM, CTD, TD, TP, Extra, Bonus';
COMMENT ON VIEW structure_enseignement IS 'Donne pour chaque ECUE des maquettes les différentes informations liées aux maquettes. une même ECUE si elle est présente dans plusieurs maquettes va apparaitre plusieurs fois';
COMMENT ON VIEW enseignement_periode IS 'Donne la periode pour un enseignement donné sous forme d''un tableau. Normalement un seul élément sauf problème dans les maquettes';
COMMENT ON VIEW enseignement_structure IS 'associe les informations des maquettes associé à un enseignement donné. Un seul enregistrement par enseignement avec une aggrégation des différents élement des structures associées';
COMMENT ON VIEW personne_situation_reduction IS 'Donne la réduction horaire en hETD aux différentes situations particulières pour une personne';
COMMENT ON VIEW personne_referentiel_heures IS 'Donne le volume horaire en hETD due aux différentes fonctions du référentiel pour une personne';
COMMENT ON VIEW personne_charge IS 'Calcule la charge effective d''une personne en hETD une fois pris en compte les situations particulières et les fonctions du référentiel';
COMMENT ON VIEW voeu_detail_heures IS 'Donne le détail des heures en hCM, hCTD, hTD, hTP, hExtra, hBonus pour un voeu';
COMMENT ON VIEW voeu_detail_htd IS 'Donne le détail des hETD des CM, CTD, TD, TP, Extra, Bonus pour un voeu';
COMMENT ON VIEW voeu_bilan_ligne IS 'Donne le bilan pour une ligne en hETD ';
COMMENT ON VIEW voeu_enseignement_detail_temp IS 'Meriterait d''être fusionné avec voeu_enseignements_details. Calcule la somme des voeux pour un enseignement en groupes des CM, CTD, TD, TP, Extra, Bonus, correspondant';
COMMENT ON VIEW voeu_enseignement_detail IS 'Calcule la somme des voeux pour un enseignement en groupes des CM, CTD, TD, TP, Extra, Bonus, correspondant';
COMMENT ON VIEW voeu_enseignement_heures_temp IS 'Calcule le bilan des heures dans les voeux pour un enseignement donné';
COMMENT ON VIEW voeu_enseignement_heures IS 'Meriterait d''être fusionné avec voeu_enseignement_heures :Calcule le bilan des heures dans les voeux pour un enseignement donné';
COMMENT ON VIEW voeu_enseignement_bilan IS 'Fait le décompte des groupes restant à pourvoir pour un enseignement';
COMMENT ON VIEW voeu_personne_heures_temp IS 'Meriterait d''être fusionné avec voeu_personne_heures : Calcule le nombre d''hETD engagées dans les voeux pour une personne';
COMMENT ON VIEW voeu_personne_heures IS 'Calcule le nombre d''hETD engagées dans les voeux pour une personne';
COMMENT ON VIEW voeu_personne_bilan IS 'Calcule l''écart en hETD entre la charge effective et le service engagé dans les voeux';
