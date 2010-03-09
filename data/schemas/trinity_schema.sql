--
-- Trinity (alpha) - schema
--
-- This is the schema that was used in the "Trinity (alpha)" application
-- which was usable for some time on the Reviewing the Kanji website, to
-- a limited number of users.
-- 
-- You may find this data useful for implementing new features, the
-- tables allow to search Jim Breen's JMDICT with the standard flags
-- "pri" "pos" etc, but also allow to determine the reading for
-- each character within compounds:
--
--  . find all compounds using a given kanji/kana
--  . find all compounds using a given reading
--  . find all compounds using any combination of character & reading
--  . find all compounds by priority, miscellaneous flags...
--  . find all compounds using a given character as a prefix, suffix,
--    or at any given position
-- 
-- The dictionary flags are stored as bits and require corresponding 
-- constants to be defined in the application:
--
--   (to document)
-- 
-- @author  Fabrice Denis 
--

-- ----------------------------------------------------------------------------
-- Set database character set & collation
-- (the web host may not allow to use CREATE DATABASE directly)
-- ----------------------------------------------------------------------------

ALTER DATABASE DEFAULT CHARACTER SET 'utf8';

-- ----------------------------------------------------------------------------
-- Do this if LOAD DATA creates multiple ascii characters instead of kanji
-- (the web host may not allow to use CREATE DATABASE directly)
-- ----------------------------------------------------------------------------

SET NAMES 'utf8';


-- ----------------------------------------------------------------------------
-- jdict
-- ----------------------------------------------------------------------------
-- 
-- This is a simple implementation of JMDICT, which uses newly generated ent_seq
-- id's (dictid) for multiple compound/reading combinations that belong to the
-- same "gloss".
-- 
-- The key goal for Trinity was to make sure that the compound ids
-- do not change if feeding the database with a new version of JMDICT.
-- To handle the newly generated ids, there would be a remapping of old ids to
-- new ids, by using the unique compound/reading combinations as the link
-- (never tested/implemented)
-- 
-- Script:  table_dict.pl
-- 
--  dictid     Unique id derived from ent_seq (but not necessarily sequential)
--  pri        Priority (news1, news2, ichi1, ... as a bitmask)
--  pos        Bitmask (see "dictpos" constant in the script)
--  verb       Bitmask (see "dictverb" constant in the script)
--  misc       Bitmask (see "dictmisc" constant in the script)
--  field      Bitmask (see "dictfield" constant in the script)
--  compound   
--  reading
--  glossary   Contains all the glosses separated by ;
--  
-- ----------------------------------------------------------------------------

DROP TABLE IF EXISTS jdict;

CREATE TABLE jdict (
  dictid        MEDIUMINT UNSIGNED NOT NULL,
  pri           TINYINT UNSIGNED NOT NULL,
  pos           INT UNSIGNED NOT NULL,
  verb          INT UNSIGNED NOT NULL,
  misc          INT UNSIGNED NOT NULL,
  field         INT UNSIGNED NOT NULL,
  compound      TINYTEXT NOT NULL,
  reading       TINYTEXT NOT NULL,
  glossary      TEXT(1023) NOT NULL,
  PRIMARY KEY (dictid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES jdict WRITE;
LOAD DATA LOW_PRIORITY LOCAL INFILE "data/generated/table_jdict.utf8" INTO TABLE jdict LINES TERMINATED BY '\r\n';
UNLOCK TABLES;


-- ----------------------------------------------------------------------------
-- dictgloss
-- ----------------------------------------------------------------------------
-- 
-- This table was never completed in Trinity, it was not essential for simple
-- vocabulary review. The scripts need to be updated to be able to split the
-- glosses. I think there was a problem with generating new unique ent_seq
-- ids for the multiple glosses, and making sure these ids remain permanent
-- when reimporting a new version of JMDICT.
-- 
-- Summary: I have no idea if the table below worked at some point.
--
-- ----------------------------------------------------------------------------
-- DROP TABLE IF EXISTS dictgloss;
-- CREATE TABLE dictgloss (
--   glossid    INT NOT NULL,
--   glosstext  TINYTEXT NOT NULL
-- ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- LOCK TABLES dictgloss WRITE;
-- LOAD DATA LOW_PRIORITY LOCAL INFILE "data/generated/table_dictgloss.utf8" INTO TABLE dictgloss;
-- UNLOCK TABLES;


-- ----------------------------------------------------------------------------
-- dictsplit
-- ----------------------------------------------------------------------------
-- 
-- This table contains the okurigana information.
--
-- Script:  table_dict.pl
-- 
--  dictid         => jdict.dictid
--  kanji          Unicode code point (16bit value)
--                 Use CHAR(kanji USING "ucs2") to get the utf8 character in MySQL
--  pronid         => dictprons.pronid
--  type           0 = kana, 1 = ON, 2 = KUN
--  position       Zero-based index in compound
-- 
-- ----------------------------------------------------------------------------

DROP TABLE IF EXISTS dictsplit;

CREATE TABLE dictsplit (
  dictid        MEDIUMINT UNSIGNED NOT NULL,
  kanji         SMALLINT UNSIGNED NOT NULL,
  pronid        SMALLINT UNSIGNED NOT NULL,
  type          TINYINT NOT NULL,
  position      TINYINT UNSIGNED NOT NULL,
  INDEX (dictid),
  INDEX (kanji),
  INDEX (pronid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES dictsplit WRITE;
LOAD DATA LOW_PRIORITY LOCAL INFILE "data/generated/table_dictsplit.utf8" INTO TABLE dictsplit LINES TERMINATED BY '\r\n';
UNLOCK TABLES;


-- ----------------------------------------------------------------------------
-- dictprons
-- ----------------------------------------------------------------------------
-- 
-- This table contains all possible okurigana as found in JMDICT entries.
-- 
-- Script:  table_dict.pl
-- 
--  pronid         Unique key from existing okurigana found in JMDICT entries,
--                 and ordered by a hiragana_sort
--  pron           Kana string
-- 
-- ----------------------------------------------------------------------------

DROP TABLE IF EXISTS dictprons;

CREATE TABLE dictprons (
  pronid        SMALLINT UNSIGNED NOT NULL,
  pron          CHAR(5) NOT NULL,
  PRIMARY KEY (pronid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES dictprons WRITE;
LOAD DATA LOW_PRIORITY LOCAL INFILE "data/generated/table_dictprons.utf8" INTO TABLE dictprons LINES TERMINATED BY '\r\n';
UNLOCK TABLES;


-- ----------------------------------------------------------------------------
-- v_kanjipron_to_dict
-- ----------------------------------------------------------------------------
-- 
-- Lookup table for speed, quickly find all dict entries that match a kanji and
-- reading combination. Speeds up a jdict+dictsplit JOIN.
-- 
-- Can also lookup kanji and type (KANA/ON/KUN).
-- 
-- Script:  vocab/_misc/make-v_kanjipron_to_dict.php (old Trinity codebase)
-- 
-- ----------------------------------------------------------------------------
-- 
-- DROP TABLE IF EXISTS v_kanjipron_to_dict;
-- 
-- CREATE TABLE `v_kanjipron_to_dict` (
--   `kanji`       SMALLINT UNSIGNED NOT NULL,
--   `type`        TINYINT UNSIGNED NOT NULL,
--   `pronid`      SMALLINT UNSIGNED NOT NULL,
--   `dictid`      MEDIUMINT UNSIGNED NOT NULL,
--   `pri`          TINYINT UNSIGNED NOT NULL,
--    KEY `kanjipron` (`kanji`,`pronid`)
-- ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- 
-- LOCK TABLES v_kanjipron_to_dict WRITE;
-- LOAD DATA LOW_PRIORITY LOCAL INFILE "data/generated/table_v_kanjipron_to_dict.utf8" INTO TABLE v_kanjipron_to_dict LINES TERMINATED BY '\n';
-- UNLOCK TABLES;


-- ----------------------------------------------------------------------------
-- v_kanjipronstats
-- ----------------------------------------------------------------------------
-- 
-- Table with info for onyomi groups and kanji chains.
-- 
-- Contains an entry for every unique combination of character and reading that
-- was found in JMDICT.
-- 
-- Script:  vocab/_misc/make-v_kanjipronstats.php (old Trinity codebase)
--
--  kanjichar      The character in utf8 format (3 bytes)
--  kanji          Unicode code point (16bit value)
--                 Use CHAR(kanji USING "ucs2") to get the utf8 character in MySQL
--  pronid         => dictprons.pronid
--  allcompounds   Count of matches in EDICT
--  pricompounds   Count of matches of priority entries only (ichi1,news1,spec1,gai1)
--  rtk1           Set to 1 if character part of Remembering the Kanji Vol.1
--  mainreading    Set to 1 if this reading is chosen as the main reading for the
--                 kanji (it has the highest "pricompounds")
-- 
-- ----------------------------------------------------------------------------

DROP TABLE IF EXISTS v_kanjipronstats;

CREATE TABLE `v_kanjipronstats` (
  `kanjichar`   char(1) NOT NULL default '',
  `kanji`       SMALLINT UNSIGNED NOT NULL,
  `pronid`      SMALLINT UNSIGNED NOT NULL default '0',
  `allcompounds` smallint(5) unsigned NOT NULL default '0',
  `pricompounds` smallint(5) unsigned NOT NULL default '0',
  `rtk1`        tinyint(3) unsigned NOT NULL default '0',
  `mainreading` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY (`kanji`,`pronid`),
  KEY (`kanjichar`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES v_kanjipronstats WRITE;
LOAD DATA LOW_PRIORITY LOCAL INFILE "data/generated/table_v_kanjipronstats.utf8" INTO TABLE v_kanjipronstats LINES TERMINATED BY '\n';
UNLOCK TABLES;


-- ----------------------------------------------------------------------------
-- v_ongroups
-- ----------------------------------------------------------------------------
-- 
-- Onyomi groups.
--
-- Note: that was probably too specific to Trinity. It was useful to quickly
-- display the number of kanji pertaining to each "onyomi" group, for use
-- with the "kanji chain" study method.
-- 
-- Summary table currently used by onyomi-groups page, onyomi useful to join with onchains.
-- 
-- ----------------------------------------------------------------------------
-- 
-- DROP TABLE IF EXISTS v_ongroups;
-- 
-- CREATE TABLE `v_ongroups` (
--   `pronid`     SMALLINT UNSIGNED NOT NULL default '0',
--   `onyomi`     CHAR(3) NOT NULL,
--   `kanjicount`   tinyint(3) unsigned NOT NULL default '0',
--   PRIMARY KEY  (`pronid`)
-- ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
--
-- SELECT v_kanjipronstats.pronid,pron,COUNT(*) AS count INTO OUTFILE 'D:/dev/koohii.com/_databases/onyomi/onyomi-groups.utf8' FROM v_kanjipronstats LEFT JOIN dictprons USING (pronid) WHERE rtk1=1 AND mainreading=1 GROUP BY pronid ORDER BY pronid ASC;
-- 
-- LOCK TABLES v_ongroups WRITE;
-- LOAD DATA LOW_PRIORITY LOCAL INFILE 'data/generated/onyomi-groups.utf8' INTO TABLE v_ongroups;
-- UNLOCK TABLES;
