### suppression des enseignements sans voeux et sans responsable

- il est nécessaire de supprimer aussi le rattachement aux domaines
- il est nécessaire de supprimer aussi le lien avec les maquettes (ECUE)
- il est nécessaire de supprimer aussi les commentaires associés aux enseigneemnts
    - il faudrait essayer de voir quels sont les commentaires rattachés avant de poursuivre
```sql
CREATE temporary view resp2 AS
SELECT 
  V.enseignement as id,
  string_agg(P.prenom || ' ' || P.nom, ', ') as resp
FROM voeu as V
LEFT JOIN personne as P on P.id = V.personne
WHERE V.correspondant 
GROUP BY V.enseignement
;

CREATE temporary view RESP AS
SELECT
  E.id,
  bool_or(V.correspondant) as resp,
  sum(V.id - V.id+1) as somme,
  string_agg(P.prenom || ' ' || P.nom, ', ') as resp2
FROM enseignement as E
LEFT JOIN voeu as V on E.id = V.enseignement
LEFT JOIN personne as P on P.id = V.personne
GROUP BY E.id
ORDER BY somme ;

CREATE temporary view ENSEIGNEMENTS_SANS_VOEUX AS
SELECT 
  E.id
FROM resp as R
LEFT JOIN Enseignement as E on R.id = E.id
WHERE resp IS NULL;


-- il faut les supprimer des maquettes
UPDATE ECUE
SET enseignement = 0
WHERE enseignement in (
    SELECT id FROM ENSEIGNEMENTS_SANS_VOEUX
);

-- il faut aussi les supprimer des domaines
DELETE FROM domaine_enseignement
WHERE enseignement in (
    SELECT id FROM ENSEIGNEMENTS_SANS_VOEUX
);

-- il faut aussi supprimer les commentaire sur les enseignements
DELETE FROM commentaire_enseignement
WHERE enseignement in (
    SELECT id FROM ENSEIGNEMENTS_SANS_VOEUX
);

-- enfin on peut supprimer les enseignements
DELETE FROM enseignement
WHERE id in (
    SELECT id FROM ENSEIGNEMENTS_SANS_VOEUX
);
```

### Suppression des situations particulières
- Ici, il est dommage de ne pas utiliser les dates qui sont présentent dans les situations particulières
pour décider ou pas de conserver les situations particulières 
```sql
DELETE FROM personne_foncref 
WHERE id > 0;
```

### Suppression des éléments du référentiel
```sql
DELETE FROM personne_situation 
WHERE id > 0;
```
### Remise à 1 de l'état de validation
```sql
UPDATE VOEU
SET etat_ts = 1
WHERE id > 0;
```
