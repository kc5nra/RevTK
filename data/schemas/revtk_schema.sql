--
-- Reviewing the Kanji - database schema
-- 
-- This file is part of the Reviewing the Kanji package.
-- Copyright (c) 2005-2010  Fabrice Denis
--
-- For the full copyright and license information, please view the LICENSE
-- file that was distributed with this source code.
--

-- ----------------------------------------------------------------------------
-- Set database character set & collation
-- (the web host may not allow to use CREATE DATABASE directly)
-- ----------------------------------------------------------------------------

ALTER DATABASE DEFAULT CHARACTER SET 'utf8';

-- ----------------------------------------------------------------------------
-- active_guests
-- ----------------------------------------------------------------------------
-- Simple table that keeps track of recent visitors with unique ips, who are
-- not signed in. The old site footer used to display "30 members, 5 guests" ..
-- ----------------------------------------------------------------------------

DROP TABLE IF EXISTS active_guests;

CREATE TABLE `active_guests` (
  `ip`           char(15) NOT NULL default '',
  `timestamp`    int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- ----------------------------------------------------------------------------
-- active_users
-- ----------------------------------------------------------------------------
-- Simple table that keeps track of recently signed in users. It used to show
-- in the footer of the old site, not usre if it is still used..
-- ----------------------------------------------------------------------------

DROP TABLE IF EXISTS active_users;

CREATE TABLE `active_users` (
  `username`     char(32) NOT NULL default '',
  `timestamp`    int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- ----------------------------------------------------------------------------
-- active_members
-- ----------------------------------------------------------------------------
--
-- This table stores information from an active user that can be looked up
-- easily later and save querries, for example on the active members list.
--
-- Not all data is updated at once, and not always in the same order,
-- because of this, the default values are what the logic needs to identify
-- information that was not already set for the user.
-- 
-- The main use for this table for now is to allow to revist the flashcard
-- review summary without a POST request. So long as another review was not
-- started, the last review (lastrs_*) info can be used. There is code somewhere
-- that clears this data after a while.
-- 
--  userid
--  fc_count      Flashcard count
--  last_review   Most recent single flashcard review
--  lastrs_start  Last review session start time (timestamp value) (Review Summary)
--                This integer value must match the lastreview timestamp of the flashcard review table
--  lastrs_pass   Last review session pass count (Review Summary)
--  lastrs_fail   Last review session fail count (Review Summary)
--
-- ----------------------------------------------------------------------------

DROP TABLE IF EXISTS active_members;

CREATE TABLE `active_members` (
  `userid`       mediumint(4) unsigned NOT NULL default '0',
  `fc_count`     smallint NOT NULL default 0,
  `last_review`  datetime NOT NULL default '0000-00-00 00:00:00',
  `lastrs_start` int(10) unsigned NOT NULL default '0',
  `lastrs_pass`  smallint(5) unsigned NOT NULL default '0',
  `lastrs_fail`  smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`userid`),
  KEY `last_review` (`last_review`),
  KEY `fc_count` (`fc_count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- ----------------------------------------------------------------------------
-- kanjis
-- ----------------------------------------------------------------------------
-- Perl script not in repo yet, load data from the data/table_kanjis.utf8
-- ----------------------------------------------------------------------------

DROP TABLE IF EXISTS kanjis;

CREATE TABLE `kanjis` (
  `keyword`      char(32) NOT NULL default '',
  `kanji`        char(1) NOT NULL default '',
  `onyomi`       char(3) default NULL,
  `framenum`     int(11) NOT NULL default '0',
  `lessonnum`    int(11) NOT NULL default '0',
  `strokecount`  int(11) NOT NULL default '0',
  PRIMARY KEY  (`framenum`),
  UNIQUE KEY `kanji` (`kanji`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- ----------------------------------------------------------------------------
-- learnedkanji
-- ----------------------------------------------------------------------------
-- This table maintains the list of kanji that the user has (re)"learned" by
-- clicking the "learn" button in the Study page. The selection then allows to
-- review just those learned kanji. The kanji are cleared during succesful review
-- or when the user chooses "clear" in the Study page to empty the list.
-- ----------------------------------------------------------------------------

DROP TABLE IF EXISTS learnedkanji;

CREATE TABLE `learnedkanji` (
  `userid`       mediumint(4) unsigned NOT NULL default '0',
  `framenum`     smallint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`userid`,`framenum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- ----------------------------------------------------------------------------
-- reviews
-- ----------------------------------------------------------------------------
--  userid		    : from users table
--  framenum	    : Heisig frame number
--  			          could later store a 3-byte UTF8 character (kanji) ?
--  lastreview	  : timestamp of last review date for this kanji
--  expiredate	  : scheduled date for review of this kanji
--  totalreviews	: total number of reviews for the kanji
--  leitnerbox 	  : Leitner (flashcard system) box slot number 1-8
--  failurecount	: total number of times answered 'no'
--  successcount	: total number of times answered 'yes'
--  leitnerbox = 1 && totalreviews = 0 : untested cards
--  leitnerbox = 1 && totalreviews > 0 : failed cards
-- ----------------------------------------------------------------------------

DROP TABLE IF EXISTS reviews;

CREATE TABLE `reviews` (
  `userid`       mediumint(4) unsigned NOT NULL default '0',
  `framenum`     mediumint(4) unsigned NOT NULL default '0',
  `lastreview`   timestamp NOT NULL default '0000-00-00 00:00:00',
  `expiredate`   date NOT NULL default '0000-00-00',
  `totalreviews` smallint(4) unsigned NOT NULL default '0',
  `leitnerbox`   tinyint(1) unsigned NOT NULL default '0',
  `failurecount` smallint(4) unsigned NOT NULL default '0',
  `successcount` smallint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`userid`,`framenum`),
  KEY `lastreview` (`lastreview`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- ----------------------------------------------------------------------------
-- sitenews
-- ----------------------------------------------------------------------------
-- Simplistic news posts displayed on the site's home page.	Should really
-- replace this with a lightweight blog so people can post comments...
-- Note: news posts can be edited in the administration area (nothing fancy),
-- this admin area (the "backend" app) is not included.
-- ----------------------------------------------------------------------------

DROP TABLE IF EXISTS sitenews;

CREATE TABLE `sitenews` (
  `id`           int(11) NOT NULL auto_increment,
  `subject`      varchar(64) default NULL,
  `text`         text NOT NULL,
  `created_on`   date default NULL,
  `updated_on`   timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- ----------------------------------------------------------------------------
-- stories
-- ----------------------------------------------------------------------------
--	userid		: from users table
--	framenum	: Heisig frame number
-- ----------------------------------------------------------------------------

DROP TABLE IF EXISTS stories;

CREATE TABLE `stories` (
  `framenum`     int(4) NOT NULL default '0',
  `userid`       int(4) NOT NULL default '0',
  `updated_on`   timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `score`        tinyint(4) NOT NULL default '0',
  `public`       tinyint(4) NOT NULL default '0',
  `text`         text NOT NULL,
  PRIMARY KEY (`framenum`,`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- ----------------------------------------------------------------------------
-- storyvotes
-- ----------------------------------------------------------------------------

DROP TABLE IF EXISTS storyvotes;

CREATE TABLE `storyvotes` (
  `authorid`     mediumint(9) unsigned NOT NULL,
  `framenum`     mediumint(9) unsigned NOT NULL,
  `userid`       mediumint(9) unsigned NOT NULL,
  `vote`         tinyint(1) NOT NULL,
  PRIMARY KEY  (`authorid`,`framenum`,`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- ----------------------------------------------------------------------------
-- storiesscores
-- ----------------------------------------------------------------------------

DROP TABLE IF EXISTS storiesscores;

CREATE TABLE `storiesscores` (
  `framenum`     mediumint(8) unsigned NOT NULL default '0',
  `authorid`     mediumint(8) unsigned NOT NULL default '0',
  `stars`        smallint(5) unsigned NOT NULL default '0',
  `kicks`        smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`framenum`,`authorid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- ----------------------------------------------------------------------------
-- users
-- ----------------------------------------------------------------------------

DROP TABLE IF EXISTS users;

CREATE TABLE `users` (
  `userid`       int(10) NOT NULL auto_increment,
  `username`     varchar(32) NOT NULL default '',
  `password`     varchar(40) NOT NULL default '',
  `userlevel`    int(11) NOT NULL default '1',
  `joindate`     datetime default NULL,
  `lastlogin`    datetime default NULL,
  `email`        varchar(100) default NULL,
  `location`     varchar(32) default NULL,
  `timezone`     float NOT NULL default '-6',
  PRIMARY KEY  (`userid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- ----------------------------------------------------------------------------
-- users_trinity
-- ----------------------------------------------------------------------------
-- This table used to keep track of users registered with Trinity alpha,
-- still required in production (if user listed, displays message telling user
-- how to export their data).
-- ----------------------------------------------------------------------------

DROP TABLE IF EXISTS users_trinity;

CREATE TABLE `users_trinity` (
  `userid`       mediumint(8) unsigned NOT NULL default '0',
  `username`     varchar(32) NOT NULL default '',
  `allowed`      tinyint(3) unsigned NOT NULL default '0',
  `created_at`   timestamp NOT NULL default '0000-00-00 00:00:00' on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

