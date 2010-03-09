# Data files

This folder contains data files which are used to generate the content for some tables of the database.

The scripts may reference one or more of these files (in data/datafiles/):

data/
  datafiles/
    
    rtk1_data.utf8
      "Heisig Kanji Index" contributed by Ziggr  (http://ziggr.com/heisig/).

    rtk3_keywords.txt
      Compiled and contributed by John 'kurojohn' Vold.


# Downloaded files

Files that downloaded from various sources and saved in the data/datafiles/download/ folder. These files tend to be very large (JMDICT, KANJIDIC, etc) so they are not included in the public repository.

If you need any of these files to run a script from the data/scripts/ folder, then download the file from the URL listed below, and name it as in the list below.
  
data/
  datafiles/
    download/

      jmdict.xml.utf8
        Download JMdict_e.gz ("only English glosses") from  http://www.csse.monash.edu.au/~jwb/edict_doc.html

      kanjidic2.xml.utf8
        Download the current version of kanjidic2.xml (.gz) from  http://www.csse.monash.edu.au/~jwb/kanjidic2/

