<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * CJKHelper includes helpers related to CJK (Chinese/Japanese/Korean) language specification
 * in html documents, and font selection.
 * 
 * @package RevTK
 * @author  Fabrice Denis
 */

/**
 * Wraps given html with a span with language attributes for Japanese characters.
 * 
 * @param  string   $html   Content to wrap with the language tag, is not escaped!
 */
function cjk_lang_ja($html)
{
	$html = '<span lang="ja" xml:lang="ja">'.$html.'</span>';
	return $html;
}
