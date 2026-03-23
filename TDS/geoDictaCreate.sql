
-- ****************************** Personne as personne
CREATE TABLE personne(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    uid                     character varying(100)   DEFAULT ''::character varying,
    pseudo                  character varying(100)   DEFAULT ''::character varying,
    nom                     character varying(100)   DEFAULT ''::character varying,
    prenom                  character varying(100)   DEFAULT ''::character varying,
    email                   character varying(100)   DEFAULT ''::character varying    
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

-- ****************************** Session as session
CREATE TABLE session(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    nom                     character varying(100)   DEFAULT ''::character varying,
    date                    date                    DEFAULT 'now'::date,
    personne                integer                  DEFAULT 0::integer,
    FOREIGN KEY(personne) REFERENCES personne(id)    
);

-- ****************************** Record as record
CREATE TABLE record(
    id                      integer PRIMARY KEY,

    actif                   boolean                  DEFAULT 'TRUE'::boolean,
    nom                     character varying(100)   DEFAULT ''::character varying,
    type                    integer                  DEFAULT 0::integer,
    date                    date                    DEFAULT 'now'::date,
    latitude                real                     DEFAULT '0'::real,
    longitude               real                     DEFAULT '0'::real,
    session                 integer                  DEFAULT 0::integer,
    FOREIGN KEY(session) REFERENCES session(id)    
);

INSERT INTO Personne (id, uid, pseudo, nom, prenom, email) VALUES (100, 'ocardoso', 'ocardoso', 'Cardoso', 'Olivier', 'Olivier.Cardoso@gmail.com');

INSERT INTO Role  (id, nom) VALUES (1, 'SuperAdmin');
INSERT INTO Role  (id, nom) VALUES (2, 'Admin');
INSERT INTO Role  (id, nom) VALUES (3, 'CRUD');

INSERT INTO actAs (id, personne, role) VALUES ( 1, 100, 1);
INSERT INTO actAs (id, personne, role) VALUES ( 2, 100, 2);
INSERT INTO actAs (id, personne, role) VALUES ( 3, 100, 3);
