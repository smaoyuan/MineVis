/*
 *
 * Normalize DBLP Database Script
 * patrick fiaux, pfiaux@vt.edu
 *
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 * STEP 1. Generate simple tables and extract data
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/*
 * Authors Table
 */
/* Create authors table */
CREATE TABLE `dblp`.`authors` (
`id` INT( 8 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`author` VARCHAR( 255 ) NOT NULL
) ENGINE = MYISAM ;

/* Populate it with each unique author */
INSERT INTO authors( author )
SELECT DISTINCT author
FROM dblp_author_ref_new ;

/*
 * Source Table
 */
CREATE TABLE `dblp`.`source_conference` (
`id` INT( 8 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`conf_name` VARCHAR( 255 ) NOT NULL
) ENGINE = MYISAM ;

/* Populate it with each unique author */
INSERT INTO source_conference( conf_name )
SELECT DISTINCT `source` AS conf_name
FROM dblp_pub_new ;

/*
 * Type Table
 */
CREATE TABLE `dblp`.`pub_types` (
`id` INT( 8 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`type_name` VARCHAR( 255 ) NOT NULL
) ENGINE = MYISAM ;

/* fill it up */
INSERT INTO pub_types( type_name )
SELECT DISTINCT `type` AS type_name
FROM dblp_pub_new ;

/*
 * Years Table
 */
CREATE TABLE `dblp`.`pub_years` (
`id` INT( 8 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`pub_year` VARCHAR( 4 ) NOT NULL
) ENGINE = MYISAM ;

/* fill it up */
INSERT INTO pub_years( pub_year )
SELECT DISTINCT `year` AS pub_year
FROM dblp_pub_new ;

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 * STEP 2. Generate mapping tables and join data to normalized tables
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/*
 * Authors to Pubs
 */
CREATE TABLE `dblp`.`authors_pubs` (
`id` INT( 8 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`author_id` INT( 8 ) NOT NULL ,
`pub_id` INT( 8 ) NOT NULL ,
`editor` INT( 1 ) NOT NULL ,
`author_num` INT( 3 ) NOT NULL
) ENGINE = MYISAM ;

/* Populate it with the dblp_author entries joining on author table for ids */
INSERT INTO authors_pubs( author_id, pub_id, editor, author_num )
SELECT dblp_author_ref_new.id AS pub_id, dblp_author_ref_new.editor AS editor, dblp_author_ref_new.author_num as author_num, authors.id as author_id
FROM dblp_author_ref_new
LEFT JOIN authors
ON dblp_author_ref_new.author=authors.author ;