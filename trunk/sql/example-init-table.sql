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

--
-- INSERT INTO user VALUES ('admin', MD5('admin'), '管理者', 'test@example.jp', 0x7fffffff, '2009-07-14 11:00:00', '2009-07-14 11:00:00');
-- INSERT INTO user VALUES ('test1', MD5('test1'), 'テスト表示のみ', 'test@example.com', 0x01, '2009-07-15 11:00:00', '2009-07-15 11:00:00');
-- INSERT INTO user VALUES ('test2', MD5('test2'), 'テスト作成者', 'test@example.com', 0x02, '2009-07-15 11:00:00', '2009-07-15 11:00:00');
-- INSERT INTO config VALUES ('template', 'テンプレート', 'default', '2009-07-14 11:00:00', '2009-07-14 11:00:00');
-- INSERT INTO config VALUES ('title', 'CMSタイトル', 'ようこそMinCMS', '2009-07-14 15:00:00', '2009-07-14 15:00:00');
-- INSERT INTO config VALUES ('subtitle', 'CMSサブタイトル', 'MinCMSがあなたの手助けになれば', '2009-07-14 15:00:00', '2009-07-14 15:00:00');
--

INSERT INTO site SET path='test0', name='テスト０', contact='addressテスト０のサイト', rtime='2009-07-31 15:00:00', utime='2009-07-31 15:00:00';
INSERT INTO siteuser VALUES (LAST_INSERT_ID(), 'test1');
INSERT INTO siteuser VALUES (LAST_INSERT_ID(), 'test2');
INSERT INTO site SET path='test1', name='テスト１', contact='addressテスト１のサイト', rtime='2009-07-31 15:01:00', utime='2009-07-31 15:01:00';
INSERT INTO siteuser VALUES (LAST_INSERT_ID(), 'test1');
INSERT INTO siteuser VALUES (LAST_INSERT_ID(), 'test2');
