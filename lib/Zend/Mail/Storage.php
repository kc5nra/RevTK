<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE.txt, and
 * is available through the world-wide-web at the following URL:
 * http://framework.zend.com/license/new-bsd. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @package    Zend_Mail
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Mail_Storage
{
    // maildir and IMAP flags, using IMAP names, where possible to be able to distinguish between IMAP
    // system flags and other flags
    const FLAG_PASSED   = 'Passed';
    const FLAG_SEEN     = '\Seen';
    const FLAG_ANSWERED = '\Answered';
    const FLAG_FLAGGED  = '\Flagged';
    const FLAG_DELETED  = '\Deleted';
    const FLAG_DRAFT    = '\Draft';
    const FLAG_RECENT   = '\Recent';
}
