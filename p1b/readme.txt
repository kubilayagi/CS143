# Project 1B

Enforcing Data Integrity
Primary Key Restraints:
	Actor IDs
	Movie IDs
	Director IDs
Referential Integrity Restraints
	MovieGenre's mid is a foreign key referencing Movie's id
	Review's mid is a foreign key referencing Movie's id
	MovieDirector's mid is a foreign key referencing Movie's id
	MovieActor's mid is a foreign key referencing Movie's id
	MovieActor's aid is a foreign key referencing Actor's id
	MovieDirector's did is a foreign key referencing Director's id
Check Constraints
	Movie release year should not be before 1800s, considering movies were invented in late 19th century
	Movie MPAA rating should be either null or one of the possible categories, 'G','PG','PG-13','R','NC-17','NR','UR'
	Director's date of death should either be null or after the date of birth
	Review rating is out of 5 (according to specs like Amazon's review system) so should be 1 through 5




For this project, we used several resources including Stack Overflow and W3Schools.
Many of the searches that were done were syntactic questions. The following links
helped us understand which function calls were appropriate for our desired
functionality. 

We have also included some CSS styling :)

https://www.w3schools.com/php/php_mysql_select.asp
http://php.net/manual/en/mysqli-result.fetch-assoc.php
https://stackoverflow.com/questions/14629636/mysql-field-name-to-the-new-mysqli
https://www.w3schools.com/sql/func_mysql_concat.asp