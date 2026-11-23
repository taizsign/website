/* Schéma de la base du portfolio (SQLite 3) */
/* La base est reconstruite automatiquement à partir de ce fichier */
/* et des fichiers de database/content/ dès que l'un d'eux change. */

PRAGMA foreign_keys = ON;

CREATE TABLE meta (
    key   TEXT PRIMARY KEY,
    value TEXT NOT NULL
);

/* Projets */

CREATE TABLE project (
    id                   INTEGER PRIMARY KEY,
    slug                 TEXT    NOT NULL UNIQUE,
    position             INTEGER NOT NULL,
    title                TEXT    NOT NULL,
    tagline              TEXT    NOT NULL,
    summary              TEXT    NOT NULL,
    year                 INTEGER NOT NULL CHECK (year BETWEEN 2000 AND 2100),
    context              TEXT    NOT NULL,
    role                 TEXT    NOT NULL,
    video_id             TEXT,
    architecture         TEXT    NOT NULL CHECK (json_valid(architecture)),
    architecture_caption TEXT    NOT NULL
);

CREATE TABLE project_link (
    project_id INTEGER NOT NULL REFERENCES project (id) ON DELETE CASCADE,
    position   INTEGER NOT NULL,
    label      TEXT    NOT NULL,
    url        TEXT    NOT NULL CHECK (url LIKE 'https://%'),
    PRIMARY KEY (project_id, position)
);

/* brief = le contexte, build = ce que j'ai construit, retro = ce que je referais */
CREATE TABLE project_section (
    id         INTEGER PRIMARY KEY,
    project_id INTEGER NOT NULL REFERENCES project (id) ON DELETE CASCADE,
    kind       TEXT    NOT NULL CHECK (kind IN ('brief', 'build', 'retro')),
    position   INTEGER NOT NULL,
    title      TEXT    NOT NULL,
    body       TEXT    NOT NULL
);

CREATE TABLE technology (
    id   INTEGER PRIMARY KEY,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE project_technology (
    project_id    INTEGER NOT NULL REFERENCES project (id) ON DELETE CASCADE,
    technology_id INTEGER NOT NULL REFERENCES technology (id),
    layer         TEXT    NOT NULL,
    position      INTEGER NOT NULL,
    PRIMARY KEY (project_id, technology_id)
);

CREATE TABLE code_excerpt (
    id         INTEGER PRIMARY KEY,
    project_id INTEGER NOT NULL REFERENCES project (id) ON DELETE CASCADE,
    position   INTEGER NOT NULL,
    title      TEXT    NOT NULL,
    language   TEXT    NOT NULL CHECK (language IN ('sql', 'yaml', 'csharp', 'php', 'powershell')),
    source     TEXT    NOT NULL,
    code       TEXT    NOT NULL,
    caption    TEXT    NOT NULL
);

/* filename NULL = document annoncé mais pas encore publié */
CREATE TABLE document (
    id         INTEGER PRIMARY KEY,
    slug       TEXT    NOT NULL UNIQUE,
    project_id INTEGER NOT NULL REFERENCES project (id) ON DELETE CASCADE,
    position   INTEGER NOT NULL,
    label      TEXT    NOT NULL,
    meta       TEXT    NOT NULL,
    filename   TEXT
);

/* Référentiel BTS SIO */

CREATE TABLE bts_block (
    code  TEXT PRIMARY KEY,
    label TEXT NOT NULL
);

CREATE TABLE bts_competency (
    code       TEXT PRIMARY KEY,
    block_code TEXT NOT NULL REFERENCES bts_block (code),
    label      TEXT NOT NULL
);

CREATE TABLE project_competency (
    project_id      INTEGER NOT NULL REFERENCES project (id) ON DELETE CASCADE,
    competency_code TEXT    NOT NULL REFERENCES bts_competency (code),
    PRIMARY KEY (project_id, competency_code)
);

/* Compétences */

CREATE TABLE skill (
    id     INTEGER PRIMARY KEY,
    name   TEXT    NOT NULL UNIQUE,
    domain TEXT    NOT NULL CHECK (domain IN ('systèmes', 'réseaux', 'bdd', 'dev', 'outils')),
    level  INTEGER NOT NULL CHECK (level BETWEEN 0 AND 5),
    status TEXT    NOT NULL CHECK (status IN ('acquis', 'en cours', 'objectif')),
    detail TEXT    NOT NULL,
    logo   TEXT    NOT NULL CHECK (logo GLOB '[a-z0-9]*')  /* fichier public/assets/img/logos/<logo>.svg */
);

/* Une compétence se prouve : par un projet du site, ou par un contexte (stage, homelab…) */
CREATE TABLE skill_proof (
    skill_id   INTEGER NOT NULL REFERENCES skill (id) ON DELETE CASCADE,
    position   INTEGER NOT NULL,
    label      TEXT    NOT NULL,
    project_id INTEGER REFERENCES project (id),
    PRIMARY KEY (skill_id, position)
);

/* Vue interrogée par la console de la page Compétences : */
/* la requête affichée à l'écran est exactement celle exécutée sur cette vue. */
CREATE VIEW competence AS
SELECT s.name   AS nom,
       s.domain AS domaine,
       s.level  AS niveau,
       s.status AS statut,
       s.detail AS detail,
       s.logo   AS logo,
       (SELECT group_concat(p.label, ' | ')
          FROM (SELECT label FROM skill_proof WHERE skill_id = s.id ORDER BY position) AS p) AS preuve,
       (SELECT group_concat(pr.slug, ',')
          FROM skill_proof sp JOIN project pr ON pr.id = sp.project_id
         WHERE sp.skill_id = s.id) AS projets
  FROM skill s;

/* La même vue pour la version anglaise du site : mêmes lignes, colonnes et valeurs en anglais. */
CREATE VIEW skills AS
SELECT nom AS name,
       CASE domaine WHEN 'systèmes' THEN 'systems' WHEN 'réseaux' THEN 'networks'
                    WHEN 'bdd' THEN 'db' WHEN 'outils' THEN 'tools' ELSE domaine END AS domain,
       niveau AS level,
       CASE statut WHEN 'acquis' THEN 'acquired' WHEN 'en cours' THEN 'learning' ELSE 'goal' END AS status,
       detail,
       logo,
       preuve  AS proof,
       projets AS projects
  FROM competence;

/* Parcours */

CREATE TABLE milestone (
    id       INTEGER PRIMARY KEY,
    position INTEGER NOT NULL,
    period   TEXT    NOT NULL,
    kind     TEXT    NOT NULL,
    title    TEXT    NOT NULL,
    body     TEXT    NOT NULL,
    tags     TEXT    NOT NULL DEFAULT '',
    state    TEXT    NOT NULL CHECK (state IN ('done', 'current', 'next')),
    at       REAL    NOT NULL CHECK (at BETWEEN 2000 AND 2100)  /* position sur le profil (année décimale) */
);

/* Veille */

CREATE TABLE watch_item (
    id       INTEGER PRIMARY KEY,
    kind     TEXT    NOT NULL CHECK (kind IN ('intro', 'axis', 'event', 'source', 'insight')),
    position INTEGER NOT NULL,
    title    TEXT    NOT NULL,
    body     TEXT    NOT NULL,
    meta     TEXT    NOT NULL DEFAULT '',
    url      TEXT
);

/* Labo */

CREATE TABLE lab_unit (
    id       INTEGER PRIMARY KEY,
    position INTEGER NOT NULL,
    height   INTEGER NOT NULL CHECK (height BETWEEN 1 AND 4),  /* hauteur en U dans la baie */
    role     TEXT    NOT NULL,
    name     TEXT    NOT NULL,
    status   TEXT    NOT NULL CHECK (status IN ('en place', 'en cours', 'prévu')),
    note     TEXT    NOT NULL
);

/* Relevés de disponibilité de mes services en ligne (sonde HTTP côté serveur) */
CREATE TABLE probe (
    id         INTEGER PRIMARY KEY,
    target     TEXT    NOT NULL,
    checked_at TEXT    NOT NULL,
    http_code  INTEGER NOT NULL,
    ms         REAL    NOT NULL,
    ok         INTEGER NOT NULL CHECK (ok IN (0, 1))
);

CREATE INDEX probe_target_time ON probe (target, checked_at DESC);
