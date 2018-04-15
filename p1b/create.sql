create table Movie (
	id 		int,
	title 	varchar(100),
	year 	int,
	rating 	varchar(10),
	company varchar(50),
	primary key(id),
	check(id<=4750) #id must be equal to the max id number + 1 for 1c, but for now, less than 4750
);

create table Actor (
	id 		int,
	last 	varchar(20),
	first 	varchar(20),
	sex 	varchar(6),
	dob 	date,
	dod 	date,
	primary key(id), #id is the unique identifier for Actor table
	check(id<=69000) #id must be equal to the max id number + 1 for 1c, but for now, less than 69000
);

create table Director (
	id 		int,
	last 	varchar(20),
	first 	varchar(20),
	dob 	date,
	dod 	date,
	primary key(id), #id is the unique identifier for Director table
	check(id<=69000) #id must be equal to the max id number + 1 for 1c, but for now, less than 69000
);

create table MovieGenre (
	mid 	int,
	genre 	varchar(20),
	primary key(mid) #id is the unique identifier for Movie table
	foreign key (mid) references Movie(id) #movie id is primary key for Movie table
) ENGINE=INNODB;

create table MovieDirector (
	mid int,
	did int,
	primary key(mid, did) #mid and did combination is the unique identifier for MovieDirector table
	foreign key (mid) references Movie(id), #movie id is primary key for Movie table
	foreign key (did) references Director(id) #director id is primary key for Director table
) ENGINE=INNODB;

create table MovieActor (
	mid 	int,
	aid 	int,
	role 	varchar(50),
	primary key(mid, aid), #mid and aid combination is the unique identifier for MovieActor table
	foreign key (mid) references Movie(id), #movie id is primary key for Movie table
	foreign key (aid) references Actor(id) #actor id is primary key for Actor table
) ENGINE=INNODB;

create table Review (
	name 	varchar(20),
	time 	timestamp,
	mid 	int,
	rating 	int,
	comment varchar(500),
	primary key(name, time), #name and time combination is the unique identifier for Review table, assuming no review can be posted about the same movie at the same time
	foreign key (mid) references Movie(id) #movie id is primary key for Movie table
) ENGINE=INNODB;

create table MaxPersonID (
	id int
);

create table MaxMovieID (
	id int
);