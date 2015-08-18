<?php
/**
 * The AuthorizeNet PHP SDK. Include this file in your project.
 *
 * @package AuthorizeNet
 */
require dirname(__FILE__) . '/library/shared/AuthorizeNetRequest.php';
require dirname(__FILE__) . '/library/shared/AuthorizeNetTypes.php';
require dirname(__FILE__) . '/library/shared/AuthorizeNetXMLResponse.php';
require dirname(__FILE__) . '/library/shared/AuthorizeNetResponse.php';
require dirname(__FILE__) . '/library/AuthorizeNetAIM.php';
require dirname(__FILE__) . '/library/AuthorizeNetARB.php';
require dirname(__FILE__) . '/library/AuthorizeNetCIM.php';
require dirname(__FILE__) . '/library/AuthorizeNetSIM.php';
require dirname(__FILE__) . '/library/AuthorizeNetDPM.php';
require dirname(__FILE__) . '/library/AuthorizeNetTD.php';
require dirname(__FILE__) . '/library/AuthorizeNetCP.php';

if (class_exists("SoapClient")) {
    require dirname(__FILE__) . '/library/AuthorizeNetSOAP.php';
}
/**
 * Exception class for AuthorizeNet PHP SDK.
 *
 * @package AuthorizeNet
 */
class AuthorizeNetException extends Exception
{
}