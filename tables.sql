DROP TABLE IF EXISTS articles;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS role;
DROP TABLE IF EXISTS categories;



CREATE TABLE role
(
  rolecode        varchar(50) NOT NULL,
  rolename        varchar(50) NOT NULL,

  PRIMARY KEY     (rolecode)
);
INSERT INTO role VALUES('super','Administrator');
INSERT INTO role VALUES('user','Author');


CREATE TABLE users
(
  name            varchar(255) NOT NULL,
  password        varchar(60) NOT NULL,
  rolecode        varchar(50) NOT NULL,

  PRIMARY KEY     (name),
  FOREIGN KEY     (rolecode) REFERENCES role(rolecode)
);
INSERT INTO users (name,password,rolecode) VALUES("admin","pass","super");


CREATE TABLE articles
(
  id              smallint unsigned NOT NULL auto_increment,
  author          varchar(255) NOT NULL,
  publicationDate date NOT NULL,                              # When the article was published
  title           varchar(255) NOT NULL,                      # Full title of the article
  summary         text NOT NULL,                              # A short summary of the article
  content         mediumtext NOT NULL,                        # The HTML content of the article
  PRIMARY KEY     (id),
  FOREIGN KEY     (author) REFERENCES users(name)
);

CREATE TABLE categories
(
  id              smallint unsigned NOT NULL auto_increment,
  name            varchar(255) NOT NULL,                      # Name of the category
  description     text NOT NULL,                              # A short description of the category

  PRIMARY KEY     (id)
);
ALTER TABLE articles ADD categoryId smallint unsigned NOT NULL AFTER publicationDate;
