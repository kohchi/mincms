CREATE TABLE IF NOT EXISTS user (
	id varchar(32) NOT NULL,
	password char(40) NOT NULL,
	name varchar(192) NOT NULL,
	email varchar(192),
	authority int,
	rtime datetime NOT NULL,
	utime datetime NOT NULL,
	PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS config (
	id varchar(32) NOT NULL,
	name varchar(192) NOT NULL,
	value varchar(255) NOT NULL,
	rtime datetime NOT NULL,
	utime datetime NOT NULL,
	PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS site (
	id int NOT NULL auto_increment,
	path varchar(32) NOT NULL,
	name varchar(192) NOT NULL,
	contact text,
	rtime datetime NOT NULL,
	utime datetime NOT NULL,
	PRIMARY KEY (id),
	FULLTEXT (name,contact)
);

CREATE TABLE IF NOT EXISTS siteuser (
	siteid int NOT NULL,
	userid varchar(32) NOT NULL,
	KEY (siteid),
	KEY userid (userid)
);

CREATE TABLE IF NOT EXISTS page (
	id int NOT NULL auto_increment,
	path varchar(32) NOT NULL,
	title varchar(255) NOT NULL,
	description text,
	rtime datetime NOT NULL,
	utime datetime NOT NULL,
	PRIMARY KEY (id),
	FULLTEXT (title,description)
);
