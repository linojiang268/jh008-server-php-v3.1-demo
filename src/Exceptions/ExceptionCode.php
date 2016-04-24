<?php
namespace Jihe\Exceptions;

/**
 * global exception codes
 */
final class ExceptionCode
{
    /**
     * Code for no exception.
     *
     * @var int
     */
    const NO_EXCEPTION            = 0;

    /**
     * Code for general exception. When a general exception is received, nothing more
     * can be done but show the error message along with it.
     * 
     * @var int
     */
    const GENERAL                 = 10000;
    
    /**
     *  throws when verification code is incorrect.
     *  
     * @var int
     */
    const VERIFICATION_INCORRECT   = 10001;

    /**
     * throws when request params signature is incorrect.
     *
     * @var int
     */
    const INCORRECT_REQUEST_SIGN    = 10002;

    //========================================
    //                User
    //========================================
    /**
     * Code for unauthorized user exception. When an authorized user is required but that's not
     * true, this exception will be thrown.
     *
     * @var int
     */
    const USER_UNAUTHORIZED        = 10101;
    
    /**
     * To access user-related API, user should have his/her information, such as gender, tags, 
     * be populated. If not, user will be requested to do that.
     * 
     * @var int
     */
    const USER_INFO_INCOMPLETE      = 10102;

    //========================================
    //                News
    //========================================

    /**
     * the news edited, deleted or getted is not exists.
     *
     * @var int
     */
    const NEWS_NOT_EXIST            = 10201;
}
