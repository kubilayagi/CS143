insert into Actor values (1,'Test','Test','Male','2000-01-01','2000-01-02');
-- Actor IDs should be unique and not null
-- Actor id of 1 is already loaded into the table
-- ERROR 1062 (23000): Duplicate entry '1' for key 'PRIMARY'

insert into Movie values (2,'Test', 2000,'G','Test');
-- Movie IDs should be unique and not null
-- Movie id of 2 is already loaded into the table
-- ERROR 1062 (23000): Duplicate entry '2' for key 'PRIMARY'

insert into Director values (16,'Test','Test','2000-01-01','2000-01-02');
-- Director IDs should be unique and not null
-- Director id of 16 is already loaded into the table
-- ERROR 1062 (23000): Duplicate entry '16' for key 'PRIMARY'

insert into MovieGenre values (1,'Test');
-- MovieGenre's mid is a foreign key referencing Movie's id
-- Movie id of 1 does not exist in the Movie table
-- ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143`.`MovieGenre`, CONSTRAINT `MovieGenre_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))

insert into Review values ('Test','2000-01-01 00:00:00',1,5,'Test');
-- Review's mid is a foreign key referencing Movie's id
-- Movie id of 1 does not exist in the Movie table
-- ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143`.`Review`, CONSTRAINT `Review_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))

insert into MovieDirector values (1,16);
-- MovieDirector's mid is a foreign key referencing Movie's id
-- Movie id of 1 does not exist in the Movie table
-- ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143`.`MovieDirector`, CONSTRAINT `MovieDirector_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))

insert into MovieActor values (1, 1,'Test');
-- MovieActor's mid is a foreign key referencing Movie's id
-- Movie id of 1 does not exist in the Movie table
-- ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143`.`MovieActor`, CONSTRAINT `MovieActor_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))

insert into MovieActor values (2, 2,'Test');
-- MovieActor's aid is a foreign key referencing Actor's id
-- Actor id of 2 does not exist in the Actor table
-- ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143`.`MovieActor`, CONSTRAINT `MovieActor_ibfk_2` FOREIGN KEY (`aid`) REFERENCES `Actor` (`id`))

insert into MovieDirector values (2,15);
-- MovieDirector's did is a foreign key referencing Director's id
-- Director id of 15 does not exist in the Director table
-- ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143`.`MovieDirector`, CONSTRAINT `MovieDirector_ibfk_2` FOREIGN KEY (`did`) REFERENCES `Director` (`id`))

insert into Movie values (1,'Test', 1000,'G','Test');
-- Movie release year should not be before 1800s, considering movies were invented in late 19th century
-- Movie year cannot possibly have been released in the year of 1000

insert into Movie values (1,'Test', 2000,'Test','Test');
-- Movie MPAA rating should be either null or one of the possible categories, 'G','PG','PG-13','R','NC-17','NR','UR'
-- Movie MPAA ratings do not include the option 'Test', it is not a valid rating

insert into Director values (15,'Test','Test','2000-01-01','1998-01-01');
-- Director's date of death should either be null or after the date of birth
-- Director could not have possibly died two years before he was born

insert into Actor values (2,'Test','Test','Male','2000-01-01','1998-01-02');
-- Actor's date of death should either be null or after the date of birth
-- Actor could not have possibly died two years before he was born

insert into Review values ('Test','01-01-2000 00:00:00',2,100,'Test');
-- Review rating is out of 5 (according to specs like Amazon's review system) so should be 1 through 5
-- The rating is out of 5, so it cannot possibly be given a rating of 100

