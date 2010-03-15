<?php
/**
 * CJK contains helpers for conversion between katakana/hiragana, hankakau,
 * and working with kanji, all in utf8 strings.
 * 
 * @author  Fabrice Denis
 */

class CJK
{
  /**
   * Regular expression range for valid kanji.
   */
  const
    PREG_KANJI = '\x{4e00}-\x{9fa5}';

  /**
   * Returns true if the string contains any kanji character
   * 
   * @param string  $u8String  String in utf8 or ascii
   *
   */
  static public function hasKanji($u8String)
  {
    return preg_match('/['.self::PREG_KANJI.']/u', $u8String);
  }

  /**
   * Returns an array containing all kanji characters found in a string.
   * 
   * All non-kanji characters are ignored.
   * 
   * @param  string $u8String  Utf8 or ascii string
   * 
   * @return array  Array of kanji characters, empty array if non found
   */
  static public function getKanji($u8String)
  {
    $result = preg_match_all('/['.self::PREG_KANJI.']/u', $u8String, $matches);
    return $result ? $matches[0] : array();
  }

  /**
   * 
   * OLD CODE TO REFACTOR WITH  mbstring  AND USE CONSTANTS
   * 
   */
  static function isHiragana($ucs)
  {
    return ($ucs>=0x3040 && $ucs<=0x309f) ? 1 : 0;
  }

  static function isKatakana($ucs)
  {
    return ($ucs>=0x30a0 && $ucs<=0x30ff) ? 1 : 0;
  }
  
  static function isKana($ucs)
  {
    return ($ucs>=0x3040 && $ucs<=0x30ff) ? 1 : 0;
  }

  static function isKanji($ucs)
  {
    //CJK unifed ideographs - Common and uncommon kanji (4e00 - 9faf)
    return ($ucs>=0x4e00 && $ucs<=0x9faf) ? 1 : 0;
  }

  // convert Katakana in string to Hiragana
  static function toHiragana($u8s)
  {
    $ua_text = utf8::toUnicode($u8s);
    // convert katakana in unicode array to hiragana
    // Hiragana ( 3040 - 309f)
    // Katakana ( 30a0 - 30ff)
    for ($i=0; $i<count($ua_text); $i++)
    {
      if ($ua_text[$i]>=0x30a0 && $ua_text[$i]<=0x30ff)
        $ua_text[$i] -= 0x0060;
    }
    return utf8::fromUnicode($ua_text);
  }

  // convert Hiragana in string to Katakana
  static function toKatakana($u8s)
  {
    $ua_text = utf8::toUnicode($u8s);
    // Hiragana ( 3040 - 309f)
    // Katakana ( 30a0 - 30ff)
    for ($i=0; $i<count($ua_text); $i++)
    {
      if ($ua_text[$i]>=0x3040 && $ua_text[$i]<=0x309f)
        $ua_text[$i] += 0x0060;
    }
    return utf8::fromUnicode($ua_text);
  }
  
  /**
   * Convert full-width Japanese Roman characters to ASCII roman characters.
   * 
   * This helps the user not having to shift out of the Japanese input mode to write ascii stuff.
   * 
   * Note: not thoroughly tested beyond the digits (0-9)
   * 
   * @param  string  Utf8 string
   * 
   * @return string  Utf8 string
   */
  static function normalizeFullWidthRomanCharacters($u8s)
  {
    $aUCS = utf8::toUnicode($u8s);
    for ($i=0; $i < count($aUCS); $i++)
    {
      if ($aUCS[$i]>=0xff10 && $aUCS[$i]<=0xff5a)
      {
        $aUCS[$i] = $aUCS[$i]-0xff00+32;
      }
    }
    return utf8::fromUnicode($aUCS);
  }
}
