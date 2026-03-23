/* Spécifique TSSDV */
CREATE OR REPLACE FUNCTION public.eq_ctd(ctd real) RETURNS real AS $$
    BEGIN
        RETURN ctd * 1.25;
    END;
$$ LANGUAGE plpgsql ;

CREATE OR REPLACE VIEW voeu_detail_heures AS
SELECT 
    v.id,
    v.cm AS cm,
    v.ctd AS ctd,
    v.td AS td,
    v.tp AS tp,
    v.extra AS extra,
    v.bonus
   FROM voeu v
     LEFT JOIN enseignement e ON e.id = v.enseignement
  WHERE v.id > 0 AND e.id > 0;


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
    LEFT JOIN domaine_enseignement de ON de.enseignement = ecue.enseignement;
