<?php
/**
 *---------------------------------------------------------------------------
 *
 *                  T E N C E N T   P R O P R I E T A R Y
 *
 *     COPYRIGHT (c)  2008 BY  TENCENT  CORPORATION.  ALL RIGHTS
 *     RESERVED.   NO  PART  OF THIS PROGRAM  OR  PUBLICATION  MAY
 *     BE  REPRODUCED,   TRANSMITTED,   TRANSCRIBED,   STORED  IN  A
 *     RETRIEVAL SYSTEM, OR TRANSLATED INTO ANY LANGUAGE OR COMPUTER
 *     LANGUAGE IN ANY FORM OR BY ANY MEANS, ELECTRONIC, MECHANICAL,
 *     MAGNETIC,  OPTICAL,  CHEMICAL, MANUAL, OR OTHERWISE,  WITHOUT
 *     THE PRIOR WRITTEN PERMISSION OF :
 *
 *                        TENCENT  CORPORATION
 *
 *       Advertising Platform R&D Team, Advertising Platform & Products
 *       Tencent Ltd.
 *---------------------------------------------------------------------------
 */

/**
 * 表示消息队列的Exception
 *
 * @package sdk.src.framework.exception
 * @author  ianzhang <ianzhang@tencent.com>
 * @version $Id: TMMQException.class.php 42 2016-11-07 03:23:24Z caseycheng $
 */
class TMMQException extends TMException
{

    const EXCEPTION_MQ_CODE = 40;

    /**
     * 构造函数
     *
     * @param string $message
     * @param int $code
     */
    public function __construct($message, $code = self::EXCEPTION_MQ_CODE)
    {
        parent::__construct($message, $code);
    }
}
