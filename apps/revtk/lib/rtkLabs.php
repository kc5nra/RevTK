<?php
/**
 * rtkLabs
 *
 * Methods methods to access JMDICT data and furigana for the Japanese words.
 *
 * TODO: peer classes, bundled up into an experimental class for now.
 *
 * This is the schema that was used in the "Trinity (alpha)" application
 * which was usable for some time on the Reviewing the Kanji website, to
 * a limited number of users.
 * 
 * The tables allow to search Jim Breen's JMDICT with the standard flags
 * "pri" "pos" etc, but also allow to determine the furigana for 99% of
 * JMDICT entries.
 *
 * Example of possible querries:
 *
 *  . find all compounds using a given kanji/kana
 *  . find all compounds using a given reading
 *  . find all compounds using any combination of character & reading
 *  . find all compounds by priority, miscellaneous flags...
 *  . find all compounds using a given character as a prefix, suffix,
 *    or at any given position
 *
 *
 * @see     data/schemas/trinity_schema.sql
 * 
 * @package RevTK
 * @author  Fabrice Denis
 * @date    21 March 2010
 */

class rtkLabs
{
  /*
   * This is a simple implementation of JMDICT, which uses newly generated ent_seq
   * id's (dictid) for multiple compound/reading combinations that belong to the
   * same "gloss".
   *
   *  dictid     Unique id derived from ent_seq (but not necessarily sequential)
   *  pri        Priority (news1, news2, ichi1, ... as a bitmask)
   *  pos        Bitmask (see "dictpos" constant in the script)
   *  verb       Bitmask (see "dictverb" constant in the script)
   *  misc       Bitmask (see "dictmisc" constant in the script)
   *  field      Bitmask (see "dictfield" constant in the script)
   *  compound   
   *  reading
   *  glossary   Contains all the glosses separated by ";"
   *
   * @link    http://www.csse.monash.edu.au/~jwb/jmdict_dtd_h.html
   */
  const TABLE_JDICT = 'jdict';

  /*
   * This table contains the okurigana information.
   *
   *  dictid         => jdict.dictid
   *  kanji          Unicode code point (16bit value)
   *                 Use CHAR(kanji USING "ucs2") to get the utf8 character in MySQL
   *  pronid         => dictprons.pronid
   *  type           0 = kana, 1 = ON, 2 = KUN
   *  position       Zero-based index in compound
   */
  const TABLE_DICTSPLIT = 'dictsplit';

  /*
   * This table contains all possible okurigana as found in JMDICT entries.
   * 
   *  pronid         Unique key from existing okurigana found in JMDICT entries,
   *                 and ordered by a hiragana_sort
   *  pron           Kana string
   */
  const TABLE_DICTPRONS = 'dictprons';

  /*
   * Lookup table for speed, quickly find all dict entries that match a kanji and
   * reading combination. Speeds up a jdict+dictsplit JOIN.
   *
   * Key: (kanji,pron)
   * 
   * Can also lookup kanji and type (KANA/ON/KUN).
   *
   *  kanji         SMALLINT UNSIGNED NOT NULL,
   *  type          TINYINT UNSIGNED NOT NULL,
   *  pronid        SMALLINT UNSIGNED NOT NULL,
   *  dictid        MEDIUMINT UNSIGNED NOT NULL,
   *  pri           TINYINT UNSIGNED NOT NULL,
   */
  const TABLE_KANJIPRON_TO_DICT = 'v_kanjipron_to_dict';

  /*
   * Table with info for onyomi groups and kanji chains.
   * 
   * Contains an entry for every unique combination of character and reading that
   * was found in JMDICT.
   * 
   * Script:  vocab/_misc/make-v_kanjipronstats.php (old Trinity codebase)
   *
   *  kanjichar      The character in utf8 format (3 bytes)
   *  kanji          Unicode code point (16bit value)
   *                 Use CHAR(kanji USING "ucs2") to get the utf8 character in MySQL
   *  pronid         => dictprons.pronid
   *  allcompounds   Count of matches in EDICT
   *  pricompounds   Count of matches of priority entries only (ichi1,news1,spec1,gai1)
   *  rtk1           Set to 1 if character part of Remembering the Kanji Vol.1
   *  mainreading    Set to 1 if this reading is chosen as the main reading for the
   *                 kanji (it has the highest "pricompounds")
   */
  const TABLE_KANJIPRONSTATS = 'v_kanjipronstats';


  /* jdict.pri: relative priority of entries as a bitfield, higher priority = higher bit value (TINYINT UNSIGNED 8bits)
   *
   * - news1/2: appears in the "wordfreq" file compiled by Alexandre Girardi
   * from the Mainichi Shimbun. (See the Monash ftp archive for a copy.)
   * Words in the first 12,000 in that file are marked "news1" and words 
   * in the second 12,000 are marked "news2".
   * - ichi1/2: appears in the "Ichimango goi bunruishuu", Senmon Kyouiku 
   * Publishing, Tokyo, 1998.  (The entries marked "ichi2" were
   * demoted from ichi1 because they were observed to have low
   * frequencies in the WWW and newspapers.)
   * - spec1 and spec2: a small number of words use this marker when they 
   * are detected as being common, but are not included in other lists.
   * - gai1/2: common loanwords, based on the wordfreq file.
   *
   * For reference, as of JMDICT Rev 1.06
   *   count of total entries:  130930
   *   count of (P) entries:     16062
   *   count of ichi1,news1:     15751
   *   count of ichi1:            9267
   */
  public static 
    $pricodes = array(
      'ichi1' => 0x80,
      'news1' => 0x40,
      'news2' => 0x20,
      'ichi2' => 0x10,
      'spec1' => 8,
      'spec2' => 4,
      'gai1'  => 2,
      'gai2'  => 1
    );

  // jdict.pri: Entries marked (P) in EDICT and EDICT2 (priority entries) (ichi1, news1, spec1, gai1)
  const EDICT_PRIORITY = 0xCA;
  
  // jdict.pos (INT UNSIGNED 32bits)
  public static
    $dictpos = array(
    // <pos>
    'adj'     => 0x00000001,  # adjective (keiyoushi)
    'adj-na'  => 0x00000002,  # adjectival nouns or quasi-adjectives (keiyodoshi)
    'adj-no'  => 0x00000004,  # nouns which may take the genitive case particle `no'
    'adj-pn'  => 0x00000008,  # pre-noun adjectival (rentaishi)
    'adj-t'   => 0x00000010,  # `taru' adjective
    'adv'     => 0x00000020,  # adverb (fukushi)
    'adv-n'   => 0x00000040,  # adverbial noun
    'adv-to'  => 0x00000080,  # adverb taking the `to' particle
    'aux'     => 0x00000100,  # auxiliary
    'aux-v'   => 0x00000200,  # auxiliary verb
    'aux-adj' => 0x00000400,  # auxiliary adjective
    'conj'    => 0x00000800,  # conjunction
    'ctr'     => 0x00001000,  # counter
    'exp'     => 0x00002000,  # Expressions (phrases, clauses, etc.)
    'int'     => 0x00004000,  # interjection (kandoushi)
    'iv'      => 0x00010000,  # irregular verb
    'n'       => 0x00020000,  # noun (common) (futsuumeishi)
    'n-adv'   => 0x00040000,  # adverbial noun (fukushitekimeishi)
    'n-pref'  => 0x00080000,  # noun, used as a prefix
    'n-suf'   => 0x00100000,  # noun, used as a suffix
    'n-t'     => 0x00200000,  # noun (temporal) (jisoumeishi)
    'neg'     => 0x00400000,  # negative (in a negative sentence, or with negative verb)
    'neg-v'   => 0x00800000,  # negative verb (when used with)
    'num'     => 0x01000000,  # numeric
    'pref'    => 0x02000000,  # prefix
    'prt'     => 0x04000000,  # particle
    'suf'     => 0x08000000   # suffix
    );

  // jdict.verb (INT UNSIGNED 32bits)  
  public static
    $dictverb = array(
    // <pos>
    'v1'    => 0x00000001,  # Ichidan verb
    'v5'    => 0x00000002,  # Godan verb (not completely classified)
    'v5aru' => 0x00000004,  # Godan verb - -aru special class
    'v5b'   => 0x00000008,  # Godan verb with `bu' ending
    'v5g'   => 0x00000010,  # Godan verb with `gu' ending
    'v5k'   => 0x00000020,  # Godan verb with `ku' ending
    'v5k-s' => 0x00000040,  # Godan verb - iku/yuku special class
    'v5m'   => 0x00000080,  # Godan verb with `mu' ending
    'v5n'   => 0x00000100,  # Godan verb with `nu' ending
    'v5r'   => 0x00000200,  # Godan verb with `ru' ending
    'v5r-i' => 0x00000400,  # Godan verb with `ru' ending (irregular verb)
    'v5s'   => 0x00000800,  # Godan verb with `su' ending
    'v5t'   => 0x00001000,  # Godan verb with `tsu' ending
    'v5u'   => 0x00002000,  # Godan verb with `u' ending
    'v5u-s' => 0x00004000,  # Godan verb with `u' ending (special class)
    'v5uru' => 0x00008000,  # Godan verb - uru old class verb (old form of Eru)
    'vi'    => 0x00010000,  # intransitive verb
    'vk'    => 0x00020000,  # kuru verb - special class
    'vs'    => 0x00040000,  # noun or participle which takes the aux. verb suru
    'vs-i'  => 0x00080000,  # suru verb - irregular
    'vs-s'  => 0x00100000,  # suru verb - special class
    'vt'    => 0x00200000,  # transitive verb
    'vz'    => 0x00400000   # zuru verb - (alternative form of -jiru verbs)
    );

  // jdict.field (INT UNSIGNED 32bits)
  public static
    $dictfield = array(
    // <field>
    'Buddh'   => 0x00000001,  # Buddhist term
    'MA'      => 0x00000002,  # martial arts term
    'comp'    => 0x00000004,  # computer terminology
    'food'    => 0x00000008,  # food term
    'geom'    => 0x00000010,  # geometry term
    'gram'    => 0x00000020,  # grammatical term
    'ling'    => 0x00000040,  # linguistics terminology
    'math'    => 0x00000080,  # mathematics
    'mil'     => 0x00000100,  # military
    'physics' => 0x00000200   # physics terminology
    );

  // jdict.misc (INT UNSIGNED 32bits)
  public static
    $dictmisc = array(
    // <misc>
    'X'        => 0x00000001,  # rude or X-rated term
    'abbr'     => 0x00000002,  # abbreviation
    'arch'     => 0x00000004,  # archaism
    'ateji'    => 0x00000008,  # ateji (phonetic) reading
    'chn'      => 0x00000010,  # children's language
    'col'      => 0x00000020,  # colloquialism
    'derog'    => 0x00000040,  # derogatory
    'eK'       => 0x00000080,  # exclusively kanji
    'fam'      => 0x00000100,  # familiar language
    'fem'      => 0x00000200,  # female term or language
    'gikun'    => 0x00000400,  # gikun (meaning) reading
    'hon'      => 0x00000800,  # honorific or respectful (sonkeigo) language
    'hum'      => 0x00001000,  # humble (kenjougo) language
    'id'       => 0x00002000,  # idiomatic expression
    'm-sl'     => 0x00004000,  # manga slang
    'male'     => 0x00008000,  # male term or language
    'male-sl'  => 0x00010000,  # male slang
    'ng'       => 0x00020000,  # neuter gender
    'obs'      => 0x00040000,  # obsolete term
    'obsc'     => 0x00080000,  # obscure term
    'pol'      => 0x00100000,  # polite (teineigo) language
    'rare'     => 0x00200000,  # rare
    'sens'     => 0x00400000,  # sensitive
    'sl'       => 0x00800000,  # slang
    'uK'       => 0x01000000,  # word usually written using kanji alone
    'uk'       => 0x02000000,  # word usually written using kana alone
    'vulg'     => 0x04000000,  # vulgar expression or word
    // <re_inf>
    'ik'       => 0x08000000,  # word containing irregular kana usage
    'ok'       => 0x10000000,  # out-dated or obsolete kana usage
    // <ke_inf>
    'iK'       => 0x20000000,  # word containing irregular kanji usage
    'io'       => 0x40000000,  # irregular okurigana usage
    'oK'       => 0x80000000  # word containing out-dated kanji
    );

  /**
   * Name of the user variable that stores pre-selected flashcard data.
   *
   */
  const FLASHCARDREVIEW_SESS = 'uifr_data';

  protected
    $db      = null;

  /**
   * __construct 
   * 
   * @return void
   */
  public function __construct()
  {
    $this->db = coreContext::getInstance()->getDatabase();
  }

  /**
   * iVocabShuffleBegin 
   * 
   * @return array  Array of flashcard ids for uiFlashcardReview frontend
   */
  public static function iVocabShuffleBegin()
  {
    $db = coreContext::getInstance()->getDatabase();
    $select = $db->select(array(
      'compound',
      'reading',
      'glossary'))
      ->from(self::TABLE_JDICT)->where('pri & ?', self::$pricodes['ichi1'] + self::$pricodes['spec1'])
      ->order('rand()')
      ->limit(20);

    $data = array();
    $items = array();
    $fcId = 1; // can not be zero

    //DBG::out($select);exit;

    $select->query();
    while ($row = $db->fetch()) {
        $data[] = $row;
        $items[] = $fcId++;
    }

    //DBG::printr($data);exit;

    self::setFlashcardSession($data);

    return $items;
  }

  // unused yet
  public static function readingtest()
  {
    // pick from kanji list
    $cjk = '存知山上競争誰方早着'; 
    //くと思いました。しかし迂闊にも、うさぎは途中で寝てしまいました。目が覚めた時は、もうあとのまつりでした。かめはすでに山のてっ辺に立っていました。
    
    $data = array();
    //DBG::out(print_r($cjkc, true));exit;
    foreach ($cjkc as $k)
    {
      //rtkLabs->select('dictid')->from('v_kanjipron_to_dict')->where('kanji = ?', $ucs)
      $Q = "SELECT v_kanjipron_to_dict.dictid,v_kanjipron_to_dict.pri,compound,reading,glossary"
         . " FROM v_kanjipron_to_dict LEFT JOIN jdict USING (dictid)"
         . " WHERE kanji = ? AND type > 0 AND (v_kanjipron_to_dict.pri & 0xca)"
         . " ORDER BY RAND()";
      $db->query($Q, array(utf8::toCodePoint($k)));
      $r = $db->fetch();
      if ($r !== false) {
        $sess[] = $r;
      }
    }

    self::setFlashcardSession($data);
  }

  /**
   * Sets array data into the session for retrieving later with the
   * uiFlashcardReview callback.
   *
   * See getFlashcardData()
   * 
   * @param array $data 
   * @return void
   */
  public static function setFlashcardSession(array $data)
  {
    coreContext::getInstance()->getUser()->setAttribute(self::FLASHCARDREVIEW_SESS, $data);
  }

  /**
   * Static function callback for uiFlashcardReview 'fn_get_flashcard'.
   * 
   * Returns flashcard data that was already loaded into the session,
   * given a unique flashcard id, which was used as the key for the
   * session array 'uifr'.
   * 
   * @return object   Object to be returned as JSON data
   */
  public static function getFlashcardData($id)
  {
    $sess = coreContext::getInstance()->getUser()->getAttribute(self::FLASHCARDREVIEW_SESS);
    if (!$sess) {
      throw new rtkAjaxException('getFlashcardData(): No session?');
    }
    if (isset($sess[$id-1]))
    {
      // grab the flashcard data from the session
      $card = (object)$sess[$id-1];   //new stdClass();
      
      // uiFlashcardReview frontend requires this to match JSON response with its selection
      $card->id = $id;
      
      $card->dispword = self::getKeywordizedCompound($card->compound);

      return $card;
    }
    return null;
  }

  /**
   * Takes a string of Japanese text and returns the kanji with links to
   * the Study page, and the title attribute contains the RTK keyword.
   * 
   * @param string $compound 
   * 
   * @return string  Html markup
   */
  public static function getKeywordizedCompound($compound)
  {
    $chars = CJK::splitU($compound);
    
    //DBG::out(print_r($chars, true));

    $s = '';

    coreToolkit::loadHelpers(array('Tag', 'Url'));

    foreach($chars as $c)
    {
      if (false !== ($framenum = rtkBook::getIndexForKanji($c)))
      {
        $keyword = KanjisPeer::getKeyword($framenum);
        $url = link_to($c, '@study_edit?id='.$c, array('title' => $keyword));
        $s = $s . $url;
      }
      else
      {
        $s = $s . $c;
      }
    }

    //DBG::out($s);exit;
    return $s;
  }

}
