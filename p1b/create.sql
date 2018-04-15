create table Movie (
	id 		int,
	title 	varchar(100),
	year 	int,
	rating 	varchar(10),
	company varchar(50),
	primary key(id)
);

create table Actor (
	id 		int,
	last 	varchar(20),
	first 	varchar(20),
	sex 	varchar(6),
	dob 	date,
	dod 	date default 'N/A',
	primary key(id)
);

create table Director (
	id 		int,
	last 	varchar(20),
	first 	varchar(20),
	dob 	date,
	dod 	date default 'N/A',
	primary key(id)
);

create table MovieGenre (
	mid 	int,
	genre 	varchar(20),
	primary key(mid)
);

create table MovieDirector (
	mid int,
	did int,
	primary key(mid, did)
);

create table MovieActor (
	mid 	int,
	aid 	int,
	role 	varchar(50),
	primary key(mid, aid)
);

create table Review (
	name 	varchar(20),
	time 	timestamp,
	mid 	int,
	rating 	int,
	comment varchar(500),
	primary key(name, time)
);

create table MaxPersonID (
	id int
);

create table MaxMovieID (
	id int
);