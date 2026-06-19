Les 4 étapes proposées pour SDV en 2025 :


### Étape 1 : Remise à zéro des voeux

Cette opération doit-être un fois après avoir copié la base de données depuis l'année précédente vers cette nouvelle année. Opération réalisées :

- remise à 0 des charges de CM, TD, TP pour les enseignements qui sont attribuables
- remise de l'état de validation à « en latence »

### Étape 2 : Calcul des reports

Cette opération doit-être un fois après avoir copié la base de données depuis l'année précédente vers cette nouvelle année. Elle ne doit-être réalisée qu'à ce moment là.

Opérations réalisées :
- suppression des reports avec une valeur > 0,
- conversion des reports ayant une valeur < 0 en report avec une valeur > 0

### Étape 3 : Migration des situations individuelles

Cette opération doit-être un fois après avoir copié la base de données depuis l'année précédente vers cette nouvelle année. Elle pré-suppose que les Situations particulières de l'année précédente sont présentes.
Attention, cette opération doit être effectué après avoir fait le transfert des reports

Opération réalisées :

- suppression des situations particulières qui sont cochées dans la liste ci-dessous,
par défaut, les situations particulières cochées sont celles pour lesquelles les dates de validaté encadre la date du 12 décembre de l'année de l'année courante,
- les situations particulières sont classées par type de situation,
- le button de validation du formulaire se situe tout en bas de cette page.

### Étape 4 : Migration des fonctions du référentiel

Cette opération doit-être un fois après avoir copié la base de données depuis l'année précédente vers cette nouvelle année. Elle pré-suppose que les fonctions du référentiel de l'année précédente sont présentes. Opération réalisées :

- suppression des stages depuis les fonctions du référentiel






























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
