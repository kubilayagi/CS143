create table Movie (
	id 		int,
	title 	varchar(100),
	year 	int,
	rating 	varchar(10),
	company varchar(50),
	primary key(id),
	check(id<=4750)
);

create table Actor (
	id 		int,
	last 	varchar(20),
	first 	varchar(20),
	sex 	varchar(6),
	dob 	date,
	dod 	date,
	primary key(id),
	check(id<=69000)
);

create table Director (
	id 		int,
	last 	varchar(20),
	first 	varchar(20),
	dob 	date,
	dod 	date,
	primary key(id),
	check(id<=69000)
);

create table MovieGenre (
	mid 	int,
	genre 	varchar(20),
	primary key(mid)
	foreign key (mid) references Movie(id)
) ENGINE=INNODB;

create table MovieDirector (
	mid int,
	did int,
	primary key(mid, did)
	foreign key (mid) references Movie(id),
	foreign key (did) references Director(id)
) ENGINE=INNODB;

create table MovieActor (
	mid 	int,
	aid 	int,
	role 	varchar(50),
	primary key(mid, aid),
	foreign key (mid) references Movie(id),
	foreign key (aid) references Actor(id)
) ENGINE=INNODB;

create table Review (
	name 	varchar(20),
	time 	timestamp,
	mid 	int,
	rating 	int,
	comment varchar(500),
	primary key(name, time),
	foreign key (mid) references Movie(id)
) ENGINE=INNODB;

create table MaxPersonID (
	id int,
	check(id=69000)
);

create table MaxMovieID (
	id int,
	check(id=4750)
);