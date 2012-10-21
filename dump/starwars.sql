CREATE TABLE IF NOT EXISTS people
(
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`name` varchar(255)
);

CREATE TABLE IF NOT EXISTS planets
(
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`planet` varchar(255)
);

CREATE TABLE IF NOT EXISTS dates
(
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`date` varchar(255)
);

CREATE TABLE IF NOT EXISTS activities
(
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`desc` varchar(255)
);

CREATE TABLE IF NOT EXISTS items
(
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`name` varchar(255)
);

CREATE TABLE IF NOT EXISTS people_planets
(
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`people_id` INT,
`planets_id` INT
);

CREATE TABLE IF NOT EXISTS people_activities
(
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`people_id` INT,
`activities_id` INT
);

CREATE TABLE IF NOT EXISTS people_items
(
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`people_id` INT,
`items_id` INT
);

CREATE TABLE IF NOT EXISTS items_activities
(
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`items_id` INT,
`activities_id` INT
);

CREATE TABLE IF NOT EXISTS items_planets
(
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`items_id` INT,
`planets_id` INT
);

CREATE TABLE IF NOT EXISTS dates_activities
(
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`dates_id` INT,
`activities_id` INT
);

CREATE TABLE IF NOT EXISTS planets_activities
(
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`planets_id` INT,
`activities_id` INT
);

INSERT INTO people VALUES (1,"Obi-Wan Kenobi");
INSERT INTO people VALUES (2,"R2-D2");
INSERT INTO people VALUES (3,"Yoda");
INSERT INTO people VALUES (4,"Jar Jar Binks");
INSERT INTO people VALUES (5,"Jabba the Hutt");
INSERT INTO people VALUES (6,"Jango Fett");
INSERT INTO people VALUES (7,"Admiral Ackbar");
INSERT INTO people VALUES (8,"Chewbacca");
INSERT INTO people VALUES (9,"Han Solo");
INSERT INTO people VALUES (10,"Qui-Gon Jinn");
INSERT INTO people VALUES (11,"Padme Amidala");

INSERT INTO planets VALUES (1,"Alderaan");
INSERT INTO planets VALUES (2,"Ansion");
INSERT INTO planets VALUES (3,"Bespin");
INSERT INTO planets VALUES (4,"Boz Pity");
INSERT INTO planets VALUES (5,"Cato Neimoidia");
INSERT INTO planets VALUES (6,"Coruscant");
INSERT INTO planets VALUES (7,"Dagobah");
INSERT INTO planets VALUES (8,"Dantooine");
INSERT INTO planets VALUES (9,"Endor (moon)");
INSERT INTO planets VALUES (10,"Felucia");
INSERT INTO planets VALUES (11,"Geonosis");
INSERT INTO planets VALUES (12,"Hoth");
INSERT INTO planets VALUES (13,"Iego");
INSERT INTO planets VALUES (14,"Kamino");
INSERT INTO planets VALUES (15,"Kashyyyk");
INSERT INTO planets VALUES (16,"Kessel");
INSERT INTO planets VALUES (17,"Malastare");
INSERT INTO planets VALUES (18,"Mustafar");
INSERT INTO planets VALUES (19,"Mygeeto");
INSERT INTO planets VALUES (20,"Naboo");
INSERT INTO planets VALUES (21,"Nar Shaddaa aka Smugglers moon");
INSERT INTO planets VALUES (22,"Ord Mantell aka Ord Mandell");
INSERT INTO planets VALUES (23,"Polis Massa (asteroid)");
INSERT INTO planets VALUES (24,"Saleucami");
INSERT INTO planets VALUES (25,"Subterrel");
INSERT INTO planets VALUES (26,"Tatooine");
INSERT INTO planets VALUES (27,"Tund");
INSERT INTO planets VALUES (28,"Utapau");
INSERT INTO planets VALUES (29,"Yavin");
INSERT INTO planets VALUES (30,"Yavin");

INSERT INTO dates VALUES (1,"32 BBY");
INSERT INTO dates VALUES (2,"31 BBY");
INSERT INTO dates VALUES (3,"30 BBY");
INSERT INTO dates VALUES (4,"29 BBY");
INSERT INTO dates VALUES (5,"28 BBY");
INSERT INTO dates VALUES (6,"27 BBY");
INSERT INTO dates VALUES (7,"26 BBY");
INSERT INTO dates VALUES (8,"25 BBY");
INSERT INTO dates VALUES (9,"24 BBY");
INSERT INTO dates VALUES (10,"23 BBY");
INSERT INTO dates VALUES (11,"22 BBY");
INSERT INTO dates VALUES (12,"21 BBY");
INSERT INTO dates VALUES (13,"20 BBY");
INSERT INTO dates VALUES (14,"19 BBY");
INSERT INTO dates VALUES (15,"18 BBY");
INSERT INTO dates VALUES (16,"17 BBY");
INSERT INTO dates VALUES (17,"16 BBY");
INSERT INTO dates VALUES (18,"15 BBY");
INSERT INTO dates VALUES (19,"14 BBY");
INSERT INTO dates VALUES (20,"13 BBY");
INSERT INTO dates VALUES (21,"12 BBY");
INSERT INTO dates VALUES (22,"11 BBY");
INSERT INTO dates VALUES (23,"10 BBY");
INSERT INTO dates VALUES (24,"9 BBY");
INSERT INTO dates VALUES (25,"8 BBY");
INSERT INTO dates VALUES (26,"7 BBY");
INSERT INTO dates VALUES (27,"6 BBY");
INSERT INTO dates VALUES (28,"5 BBY");
INSERT INTO dates VALUES (29,"4 BBY");
INSERT INTO dates VALUES (30,"3 BBY");
INSERT INTO dates VALUES (31,"2 BBY");
INSERT INTO dates VALUES (32,"1 BBY");
INSERT INTO dates VALUES (33,"0 BBY/ABY");
INSERT INTO dates VALUES (34,"0 ABY");
INSERT INTO dates VALUES (35,"1 ABY");
INSERT INTO dates VALUES (36,"2 ABY");
INSERT INTO dates VALUES (37,"3 ABY");

INSERT INTO activities VALUES (1,"Looking for droids");
INSERT INTO activities VALUES (2,"Bullzeye some womp rats");
INSERT INTO activities VALUES (3,"Joining The empire");
INSERT INTO activities VALUES (4,"Starting a rebellion");
INSERT INTO activities VALUES (5,"Partying with wall-e");
INSERT INTO activities VALUES (6,"Cloning people");
INSERT INTO activities VALUES (7,"Sneaking");
INSERT INTO activities VALUES (8,"Using the force");
INSERT INTO activities VALUES (9,"Joining the dark side");
INSERT INTO activities VALUES (10,"Setting up an ambush");
INSERT INTO activities VALUES (11,"Telling people to move along");
INSERT INTO activities VALUES (12,"Fixing Robots");

INSERT INTO items VALUES (1,"X-wing");
INSERT INTO items VALUES (2,"Death Star");
INSERT INTO items VALUES (3,"Millennium Falcon");
INSERT INTO items VALUES (4,"Naboo royal cruiser");
INSERT INTO items VALUES (5,"Starfreighter");
INSERT INTO items VALUES (6,"Trade Federation battleship");
INSERT INTO items VALUES (7,"E-11 Blaster");
INSERT INTO items VALUES (8,"Bowcasters");
INSERT INTO items VALUES (9,"BlasTech DL-44");
INSERT INTO items VALUES (10,"Lightsaber");
INSERT INTO items VALUES (11,"Landspeeder");
INSERT INTO items VALUES (12,"AT-AT");
INSERT INTO items VALUES (13,"AT-ST");
INSERT INTO items VALUES (14,"Snowspeeder");
INSERT INTO items VALUES (15,"Tauntaun");
INSERT INTO items VALUES (16,"Jawa");

INSERT INTO people_planets VALUES (1,1,4);
INSERT INTO people_planets VALUES (2,1,16);
INSERT INTO people_planets VALUES (3,1,30);
INSERT INTO people_planets VALUES (4,2,6);
INSERT INTO people_planets VALUES (5,2,8);
INSERT INTO people_planets VALUES (6,2,10);
INSERT INTO people_planets VALUES (7,2,14);
INSERT INTO people_planets VALUES (8,2,15);
INSERT INTO people_planets VALUES (9,2,24);
INSERT INTO people_planets VALUES (10,2,26);
INSERT INTO people_planets VALUES (11,2,28);
INSERT INTO people_planets VALUES (12,2,30);
INSERT INTO people_planets VALUES (13,3,3);
INSERT INTO people_planets VALUES (14,3,4);
INSERT INTO people_planets VALUES (15,3,8);
INSERT INTO people_planets VALUES (16,3,19);
INSERT INTO people_planets VALUES (17,3,21);
INSERT INTO people_planets VALUES (18,3,27);
INSERT INTO people_planets VALUES (19,3,28);
INSERT INTO people_planets VALUES (20,3,30);
INSERT INTO people_planets VALUES (21,4,3);
INSERT INTO people_planets VALUES (22,4,7);
INSERT INTO people_planets VALUES (23,4,14);
INSERT INTO people_planets VALUES (24,4,22);
INSERT INTO people_planets VALUES (25,4,27);
INSERT INTO people_planets VALUES (26,4,28);
INSERT INTO people_planets VALUES (27,4,29);
INSERT INTO people_planets VALUES (28,4,30);
INSERT INTO people_planets VALUES (29,5,23);
INSERT INTO people_planets VALUES (30,5,24);
INSERT INTO people_planets VALUES (31,5,26);
INSERT INTO people_planets VALUES (32,5,27);
INSERT INTO people_planets VALUES (33,5,28);
INSERT INTO people_planets VALUES (34,6,2);
INSERT INTO people_planets VALUES (35,6,5);
INSERT INTO people_planets VALUES (36,6,6);
INSERT INTO people_planets VALUES (37,6,9);
INSERT INTO people_planets VALUES (38,6,11);
INSERT INTO people_planets VALUES (39,6,14);
INSERT INTO people_planets VALUES (40,6,17);
INSERT INTO people_planets VALUES (41,6,19);
INSERT INTO people_planets VALUES (42,7,1);
INSERT INTO people_planets VALUES (43,7,7);
INSERT INTO people_planets VALUES (44,7,12);
INSERT INTO people_planets VALUES (45,7,20);
INSERT INTO people_planets VALUES (46,7,22);
INSERT INTO people_planets VALUES (47,7,26);
INSERT INTO people_planets VALUES (48,8,8);
INSERT INTO people_planets VALUES (49,8,12);
INSERT INTO people_planets VALUES (50,8,16);
INSERT INTO people_planets VALUES (51,8,20);
INSERT INTO people_planets VALUES (52,8,24);
INSERT INTO people_planets VALUES (53,8,25);
INSERT INTO people_planets VALUES (54,9,2);
INSERT INTO people_planets VALUES (55,9,4);
INSERT INTO people_planets VALUES (56,9,7);
INSERT INTO people_planets VALUES (57,9,8);
INSERT INTO people_planets VALUES (58,9,21);
INSERT INTO people_planets VALUES (59,9,25);
INSERT INTO people_planets VALUES (60,10,7);
INSERT INTO people_planets VALUES (61,10,9);
INSERT INTO people_planets VALUES (62,10,15);
INSERT INTO people_planets VALUES (63,10,16);
INSERT INTO people_planets VALUES (64,10,19);
INSERT INTO people_planets VALUES (65,10,21);
INSERT INTO people_planets VALUES (66,10,30);
INSERT INTO people_planets VALUES (67,11,1);
INSERT INTO people_planets VALUES (68,11,19);
INSERT INTO people_planets VALUES (69,11,20);
INSERT INTO people_planets VALUES (70,11,21);
INSERT INTO people_activities VALUES (1,1,3);
INSERT INTO people_activities VALUES (2,2,1);
INSERT INTO people_activities VALUES (3,3,5);
INSERT INTO people_activities VALUES (4,4,2);
INSERT INTO people_activities VALUES (5,4,4);
INSERT INTO people_activities VALUES (6,4,6);
INSERT INTO people_activities VALUES (7,5,6);
INSERT INTO people_activities VALUES (8,5,7);
INSERT INTO people_activities VALUES (9,5,10);
INSERT INTO people_activities VALUES (10,6,2);
INSERT INTO people_activities VALUES (11,6,6);
INSERT INTO people_activities VALUES (12,8,8);
INSERT INTO people_activities VALUES (13,9,2);
INSERT INTO people_activities VALUES (14,9,7);
INSERT INTO people_activities VALUES (15,9,8);
INSERT INTO people_activities VALUES (16,9,10);
INSERT INTO people_activities VALUES (17,9,11);
INSERT INTO people_activities VALUES (18,9,12);
INSERT INTO people_activities VALUES (19,10,1);
INSERT INTO people_activities VALUES (20,10,2);
INSERT INTO people_activities VALUES (21,10,10);
INSERT INTO people_activities VALUES (22,11,9);
INSERT INTO people_items VALUES (1,1,4);
INSERT INTO people_items VALUES (2,1,5);
INSERT INTO people_items VALUES (3,1,9);
INSERT INTO people_items VALUES (4,1,12);
INSERT INTO people_items VALUES (5,1,16);
INSERT INTO people_items VALUES (6,2,11);
INSERT INTO people_items VALUES (7,3,1);
INSERT INTO people_items VALUES (8,3,2);
INSERT INTO people_items VALUES (9,3,7);
INSERT INTO people_items VALUES (10,3,9);
INSERT INTO people_items VALUES (11,3,16);
INSERT INTO people_items VALUES (12,4,2);
INSERT INTO people_items VALUES (13,4,16);
INSERT INTO people_items VALUES (14,5,4);
INSERT INTO people_items VALUES (15,5,6);
INSERT INTO people_items VALUES (16,5,8);
INSERT INTO people_items VALUES (17,5,9);
INSERT INTO people_items VALUES (18,6,2);
INSERT INTO people_items VALUES (19,6,3);
INSERT INTO people_items VALUES (20,6,15);
INSERT INTO people_items VALUES (21,7,2);
INSERT INTO people_items VALUES (22,7,7);
INSERT INTO people_items VALUES (23,7,10);
INSERT INTO people_items VALUES (24,7,12);
INSERT INTO people_items VALUES (25,8,2);
INSERT INTO people_items VALUES (26,8,5);
INSERT INTO people_items VALUES (27,8,7);
INSERT INTO people_items VALUES (28,8,8);
INSERT INTO people_items VALUES (29,8,11);
INSERT INTO people_items VALUES (30,9,6);
INSERT INTO people_items VALUES (31,9,10);
INSERT INTO people_items VALUES (32,9,11);
INSERT INTO people_items VALUES (33,9,14);
INSERT INTO people_items VALUES (34,10,2);
INSERT INTO people_items VALUES (35,10,5);
INSERT INTO people_items VALUES (36,10,13);
INSERT INTO people_items VALUES (37,10,16);
INSERT INTO people_items VALUES (38,11,4);
INSERT INTO people_items VALUES (39,11,7);
INSERT INTO people_items VALUES (40,11,11);
INSERT INTO items_activities VALUES (1,1,4);
INSERT INTO items_activities VALUES (2,1,7);
INSERT INTO items_activities VALUES (3,1,9);
INSERT INTO items_activities VALUES (4,1,11);
INSERT INTO items_activities VALUES (5,3,5);
INSERT INTO items_activities VALUES (6,4,1);
INSERT INTO items_activities VALUES (7,4,3);
INSERT INTO items_activities VALUES (8,4,12);
INSERT INTO items_activities VALUES (9,5,5);
INSERT INTO items_activities VALUES (10,5,6);
INSERT INTO items_activities VALUES (11,5,11);
INSERT INTO items_activities VALUES (12,6,2);
INSERT INTO items_activities VALUES (13,7,7);
INSERT INTO items_activities VALUES (14,7,10);
INSERT INTO items_activities VALUES (15,7,12);
INSERT INTO items_activities VALUES (16,8,2);
INSERT INTO items_activities VALUES (17,8,3);
INSERT INTO items_activities VALUES (18,9,6);
INSERT INTO items_activities VALUES (19,9,10);
INSERT INTO items_activities VALUES (20,9,11);
INSERT INTO items_activities VALUES (21,10,7);
INSERT INTO items_activities VALUES (22,10,9);
INSERT INTO items_activities VALUES (23,11,2);
INSERT INTO items_activities VALUES (24,11,7);
INSERT INTO items_activities VALUES (25,12,10);
INSERT INTO items_activities VALUES (26,12,12);
INSERT INTO items_activities VALUES (27,13,7);
INSERT INTO items_activities VALUES (28,13,9);
INSERT INTO items_activities VALUES (29,13,11);
INSERT INTO items_activities VALUES (30,14,4);
INSERT INTO items_activities VALUES (31,14,9);
INSERT INTO items_activities VALUES (32,15,5);
INSERT INTO items_activities VALUES (33,16,12);
INSERT INTO items_planets VALUES (1,1,12);
INSERT INTO items_planets VALUES (2,1,13);
INSERT INTO items_planets VALUES (3,1,24);
INSERT INTO items_planets VALUES (4,1,25);
INSERT INTO items_planets VALUES (5,1,26);
INSERT INTO items_planets VALUES (6,2,3);
INSERT INTO items_planets VALUES (7,2,5);
INSERT INTO items_planets VALUES (8,2,8);
INSERT INTO items_planets VALUES (9,2,14);
INSERT INTO items_planets VALUES (10,2,15);
INSERT INTO items_planets VALUES (11,2,17);
INSERT INTO items_planets VALUES (12,3,1);
INSERT INTO items_planets VALUES (13,3,11);
INSERT INTO items_planets VALUES (14,3,13);
INSERT INTO items_planets VALUES (15,3,14);
INSERT INTO items_planets VALUES (16,3,15);
INSERT INTO items_planets VALUES (17,3,17);
INSERT INTO items_planets VALUES (18,3,19);
INSERT INTO items_planets VALUES (19,3,24);
INSERT INTO items_planets VALUES (20,4,16);
INSERT INTO items_planets VALUES (21,4,20);
INSERT INTO items_planets VALUES (22,4,23);
INSERT INTO items_planets VALUES (23,4,27);
INSERT INTO items_planets VALUES (24,5,2);
INSERT INTO items_planets VALUES (25,5,13);
INSERT INTO items_planets VALUES (26,5,14);
INSERT INTO items_planets VALUES (27,5,15);
INSERT INTO items_planets VALUES (28,5,18);
INSERT INTO items_planets VALUES (29,6,4);
INSERT INTO items_planets VALUES (30,6,6);
INSERT INTO items_planets VALUES (31,6,10);
INSERT INTO items_planets VALUES (32,6,12);
INSERT INTO items_planets VALUES (33,6,14);
INSERT INTO items_planets VALUES (34,6,16);
INSERT INTO items_planets VALUES (35,6,17);
INSERT INTO items_planets VALUES (36,6,20);
INSERT INTO items_planets VALUES (37,6,23);
INSERT INTO items_planets VALUES (38,6,30);
INSERT INTO items_planets VALUES (39,7,2);
INSERT INTO items_planets VALUES (40,7,6);
INSERT INTO items_planets VALUES (41,7,9);
INSERT INTO items_planets VALUES (42,7,10);
INSERT INTO items_planets VALUES (43,7,12);
INSERT INTO items_planets VALUES (44,7,16);
INSERT INTO items_planets VALUES (45,7,20);
INSERT INTO items_planets VALUES (46,7,21);
INSERT INTO items_planets VALUES (47,7,26);
INSERT INTO items_planets VALUES (48,8,1);
INSERT INTO items_planets VALUES (49,8,7);
INSERT INTO items_planets VALUES (50,8,12);
INSERT INTO items_planets VALUES (51,8,23);
INSERT INTO items_planets VALUES (52,8,24);
INSERT INTO items_planets VALUES (53,9,2);
INSERT INTO items_planets VALUES (54,9,11);
INSERT INTO items_planets VALUES (55,9,24);
INSERT INTO items_planets VALUES (56,9,29);
INSERT INTO items_planets VALUES (57,9,30);
INSERT INTO items_planets VALUES (58,10,5);
INSERT INTO items_planets VALUES (59,10,11);
INSERT INTO items_planets VALUES (60,10,13);
INSERT INTO items_planets VALUES (61,10,14);
INSERT INTO items_planets VALUES (62,10,19);
INSERT INTO items_planets VALUES (63,10,29);
INSERT INTO items_planets VALUES (64,11,11);
INSERT INTO items_planets VALUES (65,11,16);
INSERT INTO items_planets VALUES (66,12,1);
INSERT INTO items_planets VALUES (67,12,23);
INSERT INTO items_planets VALUES (68,12,27);
INSERT INTO items_planets VALUES (69,13,13);
INSERT INTO items_planets VALUES (70,13,22);
INSERT INTO items_planets VALUES (71,13,30);
INSERT INTO items_planets VALUES (72,14,7);
INSERT INTO items_planets VALUES (73,14,10);
INSERT INTO items_planets VALUES (74,14,13);
INSERT INTO items_planets VALUES (75,14,24);
INSERT INTO items_planets VALUES (76,15,2);
INSERT INTO items_planets VALUES (77,15,3);
INSERT INTO items_planets VALUES (78,15,6);
INSERT INTO items_planets VALUES (79,15,7);
INSERT INTO items_planets VALUES (80,15,17);
INSERT INTO items_planets VALUES (81,15,26);
INSERT INTO items_planets VALUES (82,16,4);
INSERT INTO items_planets VALUES (83,16,11);
INSERT INTO items_planets VALUES (84,16,13);
INSERT INTO items_planets VALUES (85,16,20);
INSERT INTO items_planets VALUES (86,16,22);
INSERT INTO items_planets VALUES (87,16,23);
INSERT INTO items_planets VALUES (88,16,26);
INSERT INTO items_planets VALUES (89,16,30);
INSERT INTO dates_activities VALUES (1,1,1);
INSERT INTO dates_activities VALUES (2,1,3);
INSERT INTO dates_activities VALUES (3,1,4);
INSERT INTO dates_activities VALUES (4,1,7);
INSERT INTO dates_activities VALUES (5,1,8);
INSERT INTO dates_activities VALUES (6,1,9);
INSERT INTO dates_activities VALUES (7,2,2);
INSERT INTO dates_activities VALUES (8,2,8);
INSERT INTO dates_activities VALUES (9,2,12);
INSERT INTO dates_activities VALUES (10,3,2);
INSERT INTO dates_activities VALUES (11,3,11);
INSERT INTO dates_activities VALUES (12,3,12);
INSERT INTO dates_activities VALUES (13,4,11);
INSERT INTO dates_activities VALUES (14,5,2);
INSERT INTO dates_activities VALUES (15,5,5);
INSERT INTO dates_activities VALUES (16,5,7);
INSERT INTO dates_activities VALUES (17,5,8);
INSERT INTO dates_activities VALUES (18,5,9);
INSERT INTO dates_activities VALUES (19,5,11);
INSERT INTO dates_activities VALUES (20,6,6);
INSERT INTO dates_activities VALUES (21,6,10);
INSERT INTO dates_activities VALUES (22,6,12);
INSERT INTO dates_activities VALUES (23,7,2);
INSERT INTO dates_activities VALUES (24,7,12);
INSERT INTO dates_activities VALUES (25,8,2);
INSERT INTO dates_activities VALUES (26,8,9);
INSERT INTO dates_activities VALUES (27,9,2);
INSERT INTO dates_activities VALUES (28,11,2);
INSERT INTO dates_activities VALUES (29,11,7);
INSERT INTO dates_activities VALUES (30,11,11);
INSERT INTO dates_activities VALUES (31,11,12);
INSERT INTO dates_activities VALUES (32,12,7);
INSERT INTO dates_activities VALUES (33,12,11);
INSERT INTO dates_activities VALUES (34,13,4);
INSERT INTO dates_activities VALUES (35,14,4);
INSERT INTO dates_activities VALUES (36,14,9);
INSERT INTO dates_activities VALUES (37,14,10);
INSERT INTO dates_activities VALUES (38,14,11);
INSERT INTO dates_activities VALUES (39,14,12);
INSERT INTO dates_activities VALUES (40,15,3);
INSERT INTO dates_activities VALUES (41,15,9);
INSERT INTO dates_activities VALUES (42,15,11);
INSERT INTO dates_activities VALUES (43,16,1);
INSERT INTO dates_activities VALUES (44,16,4);
INSERT INTO dates_activities VALUES (45,16,11);
INSERT INTO dates_activities VALUES (46,17,5);
INSERT INTO dates_activities VALUES (47,17,7);
INSERT INTO dates_activities VALUES (48,17,11);
INSERT INTO dates_activities VALUES (49,18,1);
INSERT INTO dates_activities VALUES (50,18,5);
INSERT INTO dates_activities VALUES (51,18,10);
INSERT INTO dates_activities VALUES (52,19,6);
INSERT INTO dates_activities VALUES (53,19,9);
INSERT INTO dates_activities VALUES (54,19,10);
INSERT INTO dates_activities VALUES (55,20,3);
INSERT INTO dates_activities VALUES (56,20,9);
INSERT INTO dates_activities VALUES (57,21,8);
INSERT INTO dates_activities VALUES (58,22,2);
INSERT INTO dates_activities VALUES (59,22,6);
INSERT INTO dates_activities VALUES (60,22,9);
INSERT INTO dates_activities VALUES (61,23,3);
INSERT INTO dates_activities VALUES (62,23,4);
INSERT INTO dates_activities VALUES (63,24,4);
INSERT INTO dates_activities VALUES (64,24,5);
INSERT INTO dates_activities VALUES (65,24,6);
INSERT INTO dates_activities VALUES (66,24,12);
INSERT INTO dates_activities VALUES (67,25,3);
INSERT INTO dates_activities VALUES (68,25,4);
INSERT INTO dates_activities VALUES (69,25,5);
INSERT INTO dates_activities VALUES (70,25,6);
INSERT INTO dates_activities VALUES (71,25,7);
INSERT INTO dates_activities VALUES (72,25,8);
INSERT INTO dates_activities VALUES (73,26,4);
INSERT INTO dates_activities VALUES (74,27,11);
INSERT INTO dates_activities VALUES (75,28,1);
INSERT INTO dates_activities VALUES (76,28,3);
INSERT INTO dates_activities VALUES (77,28,4);
INSERT INTO dates_activities VALUES (78,28,5);
INSERT INTO dates_activities VALUES (79,28,11);
INSERT INTO dates_activities VALUES (80,29,8);
INSERT INTO dates_activities VALUES (81,29,9);
INSERT INTO dates_activities VALUES (82,29,12);
INSERT INTO dates_activities VALUES (83,30,3);
INSERT INTO dates_activities VALUES (84,30,5);
INSERT INTO dates_activities VALUES (85,30,10);
INSERT INTO dates_activities VALUES (86,30,12);
INSERT INTO dates_activities VALUES (87,31,1);
INSERT INTO dates_activities VALUES (88,31,3);
INSERT INTO dates_activities VALUES (89,31,12);
INSERT INTO dates_activities VALUES (90,32,2);
INSERT INTO dates_activities VALUES (91,32,11);
INSERT INTO dates_activities VALUES (92,33,1);
INSERT INTO dates_activities VALUES (93,33,2);
INSERT INTO dates_activities VALUES (94,33,4);
INSERT INTO dates_activities VALUES (95,34,2);
INSERT INTO dates_activities VALUES (96,34,4);
INSERT INTO dates_activities VALUES (97,34,12);
INSERT INTO dates_activities VALUES (98,36,5);
INSERT INTO dates_activities VALUES (99,36,7);
INSERT INTO dates_activities VALUES (100,37,4);
INSERT INTO dates_activities VALUES (101,37,8);
INSERT INTO planets_activities VALUES (1,1,6);
INSERT INTO planets_activities VALUES (2,1,7);
INSERT INTO planets_activities VALUES (3,1,8);
INSERT INTO planets_activities VALUES (4,2,1);
INSERT INTO planets_activities VALUES (5,2,2);
INSERT INTO planets_activities VALUES (6,2,10);
INSERT INTO planets_activities VALUES (7,2,11);
INSERT INTO planets_activities VALUES (8,2,12);
INSERT INTO planets_activities VALUES (9,3,11);
INSERT INTO planets_activities VALUES (10,4,3);
INSERT INTO planets_activities VALUES (11,4,6);
INSERT INTO planets_activities VALUES (12,5,6);
INSERT INTO planets_activities VALUES (13,5,9);
INSERT INTO planets_activities VALUES (14,7,1);
INSERT INTO planets_activities VALUES (15,7,2);
INSERT INTO planets_activities VALUES (16,7,5);
INSERT INTO planets_activities VALUES (17,7,6);
INSERT INTO planets_activities VALUES (18,7,10);
INSERT INTO planets_activities VALUES (19,8,6);
INSERT INTO planets_activities VALUES (20,9,3);
INSERT INTO planets_activities VALUES (21,9,9);
INSERT INTO planets_activities VALUES (22,10,2);
INSERT INTO planets_activities VALUES (23,10,6);
INSERT INTO planets_activities VALUES (24,10,7);
INSERT INTO planets_activities VALUES (25,10,8);
INSERT INTO planets_activities VALUES (26,10,11);
INSERT INTO planets_activities VALUES (27,11,2);
INSERT INTO planets_activities VALUES (28,12,2);
INSERT INTO planets_activities VALUES (29,12,4);
INSERT INTO planets_activities VALUES (30,12,9);
INSERT INTO planets_activities VALUES (31,12,10);
INSERT INTO planets_activities VALUES (32,12,11);
INSERT INTO planets_activities VALUES (33,13,3);
INSERT INTO planets_activities VALUES (34,13,5);
INSERT INTO planets_activities VALUES (35,13,8);
INSERT INTO planets_activities VALUES (36,13,11);
INSERT INTO planets_activities VALUES (37,14,3);
INSERT INTO planets_activities VALUES (38,14,7);
INSERT INTO planets_activities VALUES (39,15,2);
INSERT INTO planets_activities VALUES (40,15,4);
INSERT INTO planets_activities VALUES (41,15,7);
INSERT INTO planets_activities VALUES (42,15,8);
INSERT INTO planets_activities VALUES (43,16,1);
INSERT INTO planets_activities VALUES (44,16,6);
INSERT INTO planets_activities VALUES (45,16,8);
INSERT INTO planets_activities VALUES (46,16,10);
INSERT INTO planets_activities VALUES (47,17,6);
INSERT INTO planets_activities VALUES (48,18,3);
INSERT INTO planets_activities VALUES (49,18,9);
INSERT INTO planets_activities VALUES (50,19,10);
INSERT INTO planets_activities VALUES (51,20,5);
INSERT INTO planets_activities VALUES (52,20,9);
INSERT INTO planets_activities VALUES (53,20,10);
INSERT INTO planets_activities VALUES (54,21,1);
INSERT INTO planets_activities VALUES (55,21,3);
INSERT INTO planets_activities VALUES (56,21,8);
INSERT INTO planets_activities VALUES (57,22,7);
INSERT INTO planets_activities VALUES (58,22,10);
INSERT INTO planets_activities VALUES (59,22,11);
INSERT INTO planets_activities VALUES (60,23,2);
INSERT INTO planets_activities VALUES (61,23,9);
INSERT INTO planets_activities VALUES (62,24,1);
INSERT INTO planets_activities VALUES (63,24,2);
INSERT INTO planets_activities VALUES (64,24,4);
INSERT INTO planets_activities VALUES (65,24,7);
INSERT INTO planets_activities VALUES (66,24,9);
INSERT INTO planets_activities VALUES (67,24,10);
INSERT INTO planets_activities VALUES (68,25,9);
INSERT INTO planets_activities VALUES (69,26,10);
INSERT INTO planets_activities VALUES (70,27,7);
INSERT INTO planets_activities VALUES (71,28,2);
INSERT INTO planets_activities VALUES (72,28,6);
INSERT INTO planets_activities VALUES (73,28,7);
INSERT INTO planets_activities VALUES (74,28,10);
INSERT INTO planets_activities VALUES (75,28,12);
INSERT INTO planets_activities VALUES (76,29,3);
INSERT INTO planets_activities VALUES (77,29,7);
INSERT INTO planets_activities VALUES (78,30,2);
INSERT INTO planets_activities VALUES (79,30,5);
INSERT INTO planets_activities VALUES (80,30,10);
INSERT INTO planets_activities VALUES (81,30,11);
