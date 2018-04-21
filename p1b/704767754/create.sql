create table Movie (
	id 		int not null,
	title 	varchar(100) not null,
	year 	int not null,
	rating 	varchar(10),
	company varchar(50),
	primary key(id), #id is the unique identifier for Movie table
	check(year > 1800), #movie release year should definitely be at least after year 1800, considering that movies weren't invented till the late 19th century
	check(rating='G' OR rating='PG' OR rating='PG-13' OR rating='R' OR rating='NC-17' OR rating='NR' OR rating='UR' or rating is null) #the MPAA rating must be one of the standard possible ratings that MPAA gives out to movies
);

create table Actor (
	id 		int not null,
	last 	varchar(20),
	first 	varchar(20),
	sex 	varchar(6),
	dob 	date not null,
	dod 	date,
	primary key(id), #id is the unique identifier for Actor table
	check(dod is null or dod > dob) #either the Actor is not dead yet, or the date of death must be after they were born at the very least
);

create table Director (
	id 		int not null,
	last 	varchar(20),
	first 	varchar(20),
	dob 	date not null,
	dod 	date,
	primary key(id), #id is the unique identifier for Director table
	check(dod is null or dod > dob) #either the Director is not dead yet, or the date of death must be after they were born at the very least
);

create table MovieGenre (
	mid 	int not null,
	genre 	varchar(20),
	foreign key (mid) references Movie(id) #movie id is primary key for Movie table
) ENGINE=INNODB;

create table MovieDirector (
	mid int not null,
	did int not null,
	foreign key (mid) references Movie(id), #movie id is primary key for Movie table
	foreign key (did) references Director(id) #director id is primary key for Director table
) ENGINE=INNODB;

create table MovieActor (
	mid 	int not null,
	aid 	int not null,
	role 	varchar(50),
	foreign key (mid) references Movie(id), #movie id is primary key for Movie table
	foreign key (aid) references Actor(id) #actor id is primary key for Actor table
) ENGINE=INNODB;

create table Review (
	name 	varchar(20),
	time 	timestamp,
	mid 	int not null,
	rating 	int not null,
	comment varchar(500),
	foreign key (mid) references Movie(id), #movie id is primary key for Movie table
	check(rating >= 1 AND rating <= 5) #ratings are only out of 5, so the only possible scores are 1 through 5
) ENGINE=INNODB;

create table MaxPersonID (
	id int
);

create table MaxMovieID (
	id int
);