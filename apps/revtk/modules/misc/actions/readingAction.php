<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Sightreading page
 * 
 * @package    RevTK
 * @subpackage misc
 * @author     Fabrice Denis
 */

class readingAction extends coreActions
{
  public function execute($request)
  {
    $this->display_form = true;
    $this->display_kanji = false;
    $this->kanji_text = '';

    if ($request->getMethod() != coreRequest::POST)
    {
      // default text in utf-8 below
      $default_text = <<< EOD
むかし、むかし、ご存知のとおり、うさぎとかめは、山の上まで競争しました。誰もが、うさぎの方がかめよりも早くそこに着くと思いました。しかし迂闊にも、うさぎは途中で寝てしまいました。目が覚めた時は、もうあとのまつりでした。かめはすでに山のてっ辺に立っていました。
EOD;
      $request->setParameter('jtextarea', $default_text);

    }
    else
    {
      $validator = new coreValidator($this->getActionName());
      
      if ($validator->validate($request->getParameterHolder()->getAll()))
      {
        $this->display_form = false;
        $this->display_kanji = true;
        $this->kanji_text = $this->transformJapaneseText($request->getParameter('jtextarea'));
      }
    }
  }
  
  /**
   * Transform kanji in the input Japanese text into links to the Study area,
   * and add class for Javascript popup with the Heisig keywords.
   * 
   * @param  string  $j_text  Japanese text in utf-8 from validated post data.
   * @return string  Japanese text as HTML code.
   */
  protected function transformJapaneseText($j_text)
  {
    coreToolkit::loadHelpers('Tag');
    $j_text = escape_once(trim($j_text));

    // collect associative array of known kanji => kanji, framenum, keyword
    $kanjis = ReviewsPeer::getKnownKanji($this->getUser()->getUserId(), array('kanji','keyword'));
    $known = array();
    foreach($kanjis as $i => $kanjiData)
    {
      $known[$kanjiData['kanji']] = $kanjiData;
    }

    // wrap known kanji in text with links to Study area and hooks for javascript tooltip
    foreach ($known as $kanji => $info)
    {
      $url = '/study/?search='.$info['framenum'];
      $rep = '<a href="'.$url.'" class="j" title="'.$info['keyword'].'">'.$kanji.'</a>';
      $j_text = str_replace($kanji, $rep, $j_text);
    }
    
    // assumes lines end with \r\n
    $j_text = preg_replace('/[\r\n]+/', '<br/>', $j_text);
    
    return $j_text;
  }
}
