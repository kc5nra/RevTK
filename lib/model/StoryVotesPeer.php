<?php
/**
 * StoryVotes Peer.
 *
 * @package RevTK
 * @author  Fabrice Denis
 */

class StoryVotesPeer extends coreDatabaseTable
{
  protected
    $tableName = 'storyvotes',
    $columns = array
    (
      'authorid',
      'framenum',
      'userid',
      'vote'
    );

  /**
   * This function must be copied in each peer class.
   */
  public static function getInstance()
  {
    return coreDatabaseTable::_getInstance(__CLASS__);
  }

  /**
   * OLD CODE NOT REFACTORED BECAUSE NEW VOTING SYSTEM TO-DO.
   *
   * @param  int  $userId    User id
   * @param  int  $authorId  Id of the author of the story
   * @param  int  $storyId
   *
   * @return object  Object for JSON response (OLD CODE!!)
   */
  public static function starStory($userId, $authorId, $storyId)
  {
    $R = new stdClass();

    // cannot vote for self (GreaseMonkey may bypass client-side testing for this?)
    if ($userId == intval($authorId))
    {
      $R = new stdClass();
      $R->uid = $authorId;
      $R->sid = $storyId;
      $R->vote = -1;
      return $R;
    }

    // already voted?
    $q = sprintf('SELECT vote FROM storyvotes WHERE authorid=%d AND framenum=%d AND userid=%d',
        intval($authorId),
        intval($storyId),
        $userId
        );
    $result = self::$db->fetchOne($q);
    $lastvote = is_null($result) ? 0 : $result;

    // new vote or toggle vote
    $cur_vote = ($lastvote==1) ? 0 : 1;
    $UPD_STARS = array('+1','-1','+1');
    $UPD_KICKS = array('+0','+0','-1');
    $stars_inc = $UPD_STARS[$lastvote];
    $kicks_inc = $UPD_KICKS[$lastvote];

    // one vote per story per person
    $q = sprintf('REPLACE INTO storyvotes (authorid,framenum,userid,vote) VALUES (%d,%d,%d,%d)',
        intval($authorId),
        intval($storyId),
        $userId,
        $cur_vote);
    self::$db->query($q);

    // count total stars and reports in another table... (>_<) !!
    $q = sprintf('SELECT COUNT(*) FROM storiesscores WHERE framenum=%d AND authorid=%d LIMIT 1',
        intval($storyId), intval($authorId));
    $count = self::$db->fetchOne($q);
    if (!$count)
    {
      $q = sprintf('INSERT INTO storiesscores (framenum,authorid,stars,kicks) VALUES (%d,%d,0,0)',
          intval($storyId),
          intval($authorId));
      self::$db->query($q);
    }

    $q = sprintf('UPDATE storiesscores SET stars=stars%s,kicks=kicks%s WHERE framenum=%d AND authorid=%d',
        $stars_inc, $kicks_inc, intval($storyId), intval($authorId) );
    self::$db->query($q);

    $R->vote = $cur_vote;
    $R->lastvote = $lastvote;
    $R->stars = $stars_inc;
    $R->kicks = $kicks_inc;
    $R->uid = $authorId;
    $R->sid = $storyId;
    return $R;
  }

  /**
   * OLD CODE NOT REFACTORED BECAUSE NEW VOTING SYSTEM TO-DO.
   *
   * @param  int  $userId    User id
   * @param  int  $authorId  Id of the author of the story
   * @param  int  $storyId
   *
   * @return object
   */
  public static function reportStory($userId, $authorId, $storyId)
  {
    // cannot report self (duh)
    if ($userId==intval($authorId))
    {
      $R = new stdClass();
      $R->uid = $authorId;
      $R->sid = $storyId;
      $R->vote = -1;
      return $R;
    }

    // already voted?
    $q = sprintf('SELECT vote FROM storyvotes WHERE authorid=%d AND framenum=%d AND userid=%d',
        intval($authorId),
        intval($storyId),
        $userId
        );
    $result = self::$db->fetchOne($q);
    $lastvote = is_null($result) ? 0 : $result;

    // new vote or toggle vote
    $cur_vote = ($lastvote==2) ? 0 : 2;
    $UPD_STARS = array('+0','-1','+0');
    $UPD_KICKS = array('+1','+1','-1');
    $stars_inc = $UPD_STARS[$lastvote];
    $kicks_inc = $UPD_KICKS[$lastvote];

    // one vote per story per person
    $q = sprintf('REPLACE INTO storyvotes (authorid,framenum,userid,vote) VALUES (%d,%d,%d,%d)',
        intval($authorId),
        intval($storyId),
        $userId,
        $cur_vote);
    self::$db->query($q);

    // count total stars and reports in another table... (>_<) !!
    $q = sprintf('SELECT COUNT(*) FROM storiesscores WHERE framenum=%d AND authorid=%d LIMIT 1',
        intval($storyId), intval($authorId));
    $count = self::$db->fetchOne($q);
    if (!$count) {
      $q = sprintf('INSERT INTO storiesscores (framenum,authorid,stars,kicks) VALUES (%d,%d,0,0)',
          intval($storyId),
          intval($authorId));
      self::$db->query($q);
    }

    $q = sprintf('UPDATE storiesscores SET stars=stars%s,kicks=kicks%s WHERE framenum=%d AND authorid=%d',
        $stars_inc, $kicks_inc, intval($storyId), intval($authorId) );
    self::$db->query($q);

    $R = new stdClass();
    $R->vote = $cur_vote;
    $R->lastvote = $lastvote;
    $R->stars = $stars_inc;
    $R->kicks = $kicks_inc;
    $R->uid = $authorId;
    $R->sid = $storyId;
    return $R;
  }

}
