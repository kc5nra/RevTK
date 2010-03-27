<?php
/**
 * Stories Peer.
 * 
 * @package RevTK
 * @author  Fabrice Denis
 */

class StoriesPeer extends coreDatabaseTable
{
  protected
    $tableName = 'stories',
    $columns = array
    (
      'userid',
      'framenum',
      'updated_on',
      'text',
      'public'
    );

  /**
   * This function must be copied in each peer class.
   */
  public static function getInstance()
  {
    return coreDatabaseTable::_getInstance(__CLASS__);
  }

  /**
   * Returns story and story settings for given user.
   * 
   * @param
   * @return object  Object row data or false
   */
  public static function getStory($user_id, $framenum)
  {
    $select = self::getInstance()
      ->select()
      ->where('userid = ? AND framenum = ?', array($user_id, $framenum))
      ->query();
    return self::$db->fetchObject();
  }

  /**
   * Create/Update story and story settings for user.
   * 
   * @param  int    $user_id   User id
   * @param  int    $framenum  Kanji id
   * @param  array  $options   Table data
   * @return
   */
  public static function updateStory($user_id, $framenum, $data)
  {
    if (self::getInstance()->count('userid = ? AND framenum = ?', array($user_id, $framenum)))
    {
      return self::getInstance()->update($data, 'userid = ? AND framenum = ?', array($user_id, $framenum));
    }
    
    $data['userid'] = $user_id;
    $data['framenum'] = $framenum;
    return self::getInstance()->insert($data);
  }
  
  /**
   * Delete a story.
   * 
   * @param  int    $user_id   User id
   * @param  int    $framenum  Kanji id
   * @return
   */
  public static function deleteStory($user_id, $framenum)
  {
    return self::getInstance()->delete('userid = ? AND framenum = ?', array($user_id, $framenum));
  }

  /**
   * Return a story formatted for display.
   * 
   * The input story is ESCAPED before html tags are inserted for the formatting.
   * It is assumed strip_tags() was used previously. The returned string should not be escaped
   * again in the view template.
   * 
   * @param  String   $story
   * @param  String   $keyword
   * @param  Boolean  $bSubstituteLinks    True to show frame number references as links otherwise plain text.
   * @return String
   */
  public static function getFormattedStory($story, $keyword, $bSubstituteLinks = true)
  {
    // minimal punctuation : upper case first beginning of text
    $s = phpToolkit::mb_ucfirst($story);

//echo error_reporting();exit;

    // minimal punctuation : end sentence with dot.
    if (preg_match ('/[^.!?]$/', $s))
    {
      $s = $s . '.';
    }

    // remove extra spaces
    $s = preg_replace('/\s\s+/u', ' ', $s);

    // format mnemonic keyword if keyword is found within text
    $keywords = explode(rtkBook::EDITION_SEPARATOR, $keyword);
    if (count($keywords) > 1)
    {
      // use 4th edition keyword if multiple edition keyword
      $keyword = $keywords[1];
    }
    
    // remove trailing '?' or '...'
    $keyword = preg_replace('/\s*\.\.\.$|\s*\?$/', '', $keyword);
    // fixes highlighting keywords like "lead (metal)" or "abyss [old]"
    if (strstr($keyword,'(')) { $keyword = preg_replace('/\s+\([^\)]+\)/', '', $keyword); }
    if (strstr($keyword,'[')) { $keyword = preg_replace('/\s+\[[^\]]+\]/', '', $keyword); }

    if (strlen($keyword)==1)
    {
      $keyword = $keyword . '($|\s+)';
    }

    // escape text before adding html tags, replace the single quotes with another
    // special character because the escaping uses htmlspecialchars() inserts &#039;
    // and then the '#' character is matched by another regexp as the #keyword# marker
    coreToolkit::loadHelpers('Tag');
    $s = str_replace('\'', '`', $s);
    $s = escape_once($s);

    $s = preg_replace('/(^|\s+)('.$keyword.')/i', '<strong>$1$2</strong>', $s);

    // format mnemonic #keyword#
    $s = preg_replace('/#([^#]+)#/ui', '<strong>$1</strong>', $s);
    // format mnemonic *primitives*
    $s = preg_replace('/\*([^\*]+)\*/ui', '<em>$1</em>', $s);
    
//    $s = preg_replace("/{([0-9]+)}/", "<a href=\"?framenum=$1\">frame $1</a>", $s);
    if ($bSubstituteLinks)
    {
      $s = preg_replace_callback('/{([0-9]+)}/', array('StoriesPeer', 'getFormattedKanjiLink'), $s);
    }
    else {
          $s = preg_replace_callback(
                 '/{([0-9]+)}/',
                 create_function(
                     // single quotes are essential here, or alternative escape all $ as \$
                     '$matches',
                     'return sprintf("<em>%s</em> (FRAME %d)", KanjisPeer::getKeyword($matches[1]), $matches[1]);'
                 ), $s
             );
    }

    // Now restore the single quotes (as escaped single quotes)
    $s = str_replace('`', '&#039;', $s);

    return $s;
  }

  /**
   * Returns a frame number link as used in stories, for RTK index number.
   * 
   * @param  array    $matches  Reg exp matches, $matches[1] is the kanji id
   * 
   * @return string
   */
  public static function getFormattedKanjiLink($matches)
  {
    $id = $matches[1];
    $keyword = KanjisPeer::getKeyword($id);
    $link = link_to($keyword, 'study/edit?id='.$id);
    return sprintf('%s <span class="index">(#%d)</span>', $link, $id);
  }
  

  /**
   * Return array of public stories for SharedStories component.
   * 
   * Third parameter indicates which part of the shared stories selection to return:
   * - newest
   * - old (sorted by stars)
   * 
   * @see    study/SharedStoriesComponent
   * 
   * @return array<array>
   */
  public static function getPublicStories($framenum, $keyword, $bNewest)
  {
    coreToolkit::loadHelpers(array('Tag', 'Url', 'Links'));

    $select = self::getInstance()
      ->select(array(
        'stories.userid', 'username', 'stories.framenum',
        'lastmodified' => 'DATE_FORMAT(updated_on,\'%e-%c-%Y\')',
        'stories.text', 'stars', 'kicks'))
      ->joinLeft('storiesscores', 'stories.framenum=storiesscores.framenum AND stories.userid=storiesscores.authorid')
      ->join('users', 'users.userid=stories.userid')
      ->where('stories.framenum=? AND public!=0', $framenum);

    if ($bNewest) {
      $select->where('updated_on >= DATE_ADD(CURDATE(),INTERVAL -1 MONTH)');
      $select->order('updated_on DESC');
    }
    else {
      $select->where('updated_on < DATE_ADD(CURDATE(),INTERVAL -1 MONTH)');
      $select->order(array('stars DESC', 'updated_on DESC'));
    }
//if (!$bNewest) { 
//  echo $select;exit;
//}


    $rows = self::$db->fetchAll($select);

    foreach ($rows as &$R)
    {
      // do not show 0's
      if (!$R['stars']) {  $R['stars']=''; }
      if (!$R['kicks']) { $R['kicks']=''; }

      $R['text']   = StoriesPeer::getFormattedStory($R['text'], $keyword);
      $R['author'] = link_to_member($R['username']);
    }
    
    return $rows;
  }

  /**
   * Returns count of shared and private stories for given user.
   * 
   * @param  int  $user_id   User's id.
   * @return object          Object with properties 'private' 'public' and 'total'
   */
  public static function getStoriesCounts($user_id)
  {
    $num_stories = new stdClass;
    $num_stories->private = 0;
    $num_stories->public  = 0;
    
    self::getInstance()->select(array('public', 'count' => 'COUNT(*)'))
      ->where('userid = ?', $user_id)
      ->group('public')
      ->query();
    while ($R = self::$db->fetchObject())
    {
      if ($R->public==0){
        $num_stories->private = $R->count;
      }
      else {
        $num_stories->public = $R->count;
      }
    }
    $num_stories->total = $num_stories->private + $num_stories->public;

    return $num_stories;
  }

  /**
   * Returns Select object for My Stories component.
   * 
   * @param
   * @return
   */
  public static function getMyStoriesSelect($user_id)
  {
    return self::getInstance()->select(array(
        'stories.framenum', 'kanji', 'keyword', 'story' => 'text',
        'stars', 'kicks', 'updated_on', 'dispdate' => 'DATE_FORMAT(updated_on, \'%b. %e, %Y\')'))
      ->joinUsing(KanjisPeer::getInstance()->getName(), 'framenum')
      ->joinLeft(StoryScoresPeer::TABLE, sprintf('authorid=%d AND stories.framenum=storiesscores.framenum', $user_id))
      ->where('userid = ?', $user_id);
  }

  /**
   * Returns select for export to CSV.
   * 
   * @return coreDatabaseSelect
   */
  public static function getSelectForExport($user_id)
  {
    $select = self::getInstance()->select(array(
      'framenum' => 'stories.framenum',
      'kanji',
      'keyword',
      'public',
      'last_edited' => 'updated_on',
      'story' => 'text'))
      ->joinLeftUsing('kanjis', 'framenum')
      ->where('userid = ?', $user_id);
    return $select;
  }
}
