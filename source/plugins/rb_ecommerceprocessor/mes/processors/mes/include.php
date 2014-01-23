<?php

/**
* @copyright	Copyright (C) 2009 - 2014 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.Mes
* @contact		suport+payinvoice@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TpgTransaction
{
  var $Post          =   true;
  var $ApiHost       =   "https://cert.merchante-solutions.com/mes-api/tridentApi"; 
  var $ProxyHost     =   "";
  var $ProfileId;
  var $ProfileKey;
  var $TranType      =   "A";
  var $ApiResponse;
  var $ErrorMessage;
  var $ResponseRaw;
  var $ResponseFields;
  var $RequestFields;
  var $RequestFieldNames = array( "avs_data", "cardholder_street_address", "cardholder_zip", "cvv2", "transaction_amount", "card_number", "card_exp_date", "transaction_id", "card_present", "reference_number", 
                                  "merchant_name", "merchant_city", "merchant_state", "merchant_zip", "merchant_category_code", "merchant_phone", 
                                  "invoice_number", "tax_amount", "ship_to_zip", "moto_ecommerce_ind", "industry_code", "auth_code", "card_id", "country_code", 
                                  "fx_amount", "fx_rate_id", "currency_code", "rctl_product_level", "echo_customfield", 
                                  "3d_payload", "3d_transaction_id", "client_reference_number", 
                                  "bml_request", "promo_code", "order_num", "order_desc", "amount", "ship_amount", "ip_address", "bill_first_name", "bill_middle_name", "bill_last_name", "bill_addr1", "bill_addr2", "bill_city", "bill_state", "bill_zip", "bill_phone1", "bill_phone2", "bill_email", "ship_first_name", "ship_middle_name", "ship_last_name", "ship_addr1", "ship_addr2", "ship_city", "ship_state", "ship_zip", "ship_phone1", "ship_phone2", "ship_email" );
  var $url;

  function TpgTransaction( $profileId = '', $profileKey = '' ) 
  { 
    $this->setProfile($profileId,$profileKey);
  }  

  function execute() 
  { 
    if ( $this->isValid() ) 
    {
      $url .= "profile_id=" . $this->ProfileId; 
      $url .= "&profile_key=" . $this->ProfileKey; 

      $url .= "&transaction_type=" . $this->TranType; 
      foreach( $this->RequestFieldNames as $fname ) 
      { 
        if ( isset( $this->RequestFields[$fname] ) ) 
        {
          $url .= "&" . $fname . "=" . $this->RequestFields[$fname]; 
        }
      }
      $this->url = $url;

      $this->processRequest();
    } 
  } 

  function getResponseField($fieldName) 
  { 
    $retVal = ''; 
    if ( isset( $this->ResponseFields[$fieldName] ) ) 
    { 
      $retVal = $this->ResponseFields[$fieldName]; 
    } 
    return( $retVal ); 
  } 

  function isApproved() 
  { 
    $errorCode = $this->getResponseField('error_code'); 
    $retVal = FALSE; 
    if ( $errorCode == '000' ) 
    { 
      $retVal = TRUE; 
    } 
    else if ( $errorCode == '085' && $this->TranType == 'A' ) 
    { 
      $retVal = TRUE; 
    } 
    return( $retVal ); 
  } 

  function isBlank( $value ) 
  { 
    return( $value == "" ); 
  } 

  function isValid() 
  { 
    $retVal = TRUE; // assume true 
    $this->ErrorMessage = ""; 
    if ( $this->isBlank($this->ProfileId) || $this->isBlank($this->ProfileKey) ) 
    { 
      $this->ErrorMessage = "Missing profile data"; 
    } 
    else if ( isset( $this->RequestFields['transaction_amount'] ) && !is_numeric($this->RequestFields['transaction_amount']) ) 
    { 
      $this->ErrorMessage = "Amount must be a number"; 
    } 
    return( $this->isBlank($this->ErrorMessage) ); 
  } 

  function parseResponse( $response ) 
  {
    $this->ResponseRaw = $response; 
    $responseFields = explode("&",$response); 

    foreach($responseFields as $field) 
    { 
      $nameValue = explode("=",$field); 
      $this->ResponseFields[$nameValue[0]] = $nameValue[1]; 
    } 
  } 

  function processRequest() 
  {
    $ch = curl_init();
	
    if($this->Post) //Use POST
    {
      curl_setopt($ch, CURLOPT_POST, TRUE); 
      curl_setopt($ch, CURLOPT_POSTFIELDS, $this->url);
      curl_setopt($ch, CURLOPT_URL, $this->ApiHost);
    }
    else //Use GET(cURL default)
    {
      curl_setopt($ch,CURLOPT_URL, $url = $this->ApiHost . "?" . $this->url);
    }
   
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
    curl_setopt($ch, CURLOPT_HEADER, 0); 

    if ( !$this->isBlank($this->ProxyHost) ) 
    { 
      curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); 
      curl_setopt ($ch, CURLOPT_PROXY, $this->ProxyHost); 
    }

    curl_setopt ($ch, CURLOPT_TIMEOUT, 120); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 

    $this->parseResponse( curl_exec($ch) ); 
  } 

  function setAvsRequest( $cardholderStreetAddr, $cardholderZip ) 
  { 
    $this->setRequestField("cardholder_street_address", $cardholderStreetAddr); 
    $this->setRequestField("cardholder_zip", $cardholderZip); 
  } 

  function setHost( $host ) 
  { 
    $this->ApiHost = $host; 
  } 

  function setProfile( $profileId, $profileKey ) 
  { 
    $this->ProfileId = $profileId; 
    $this->ProfileKey = $profileKey; 
  } 

  function setProxyHost( $proxyHost ) 
  { 
    $this->ProxyHost = $proxyHost; 
  } 

  function setRequestField( $fieldName, $fieldValue )
  { 
    $this->RequestFields[$fieldName] = urlencode($fieldValue); 
  } 

  function setTransactionData( $cardNumber, $expDate, $tranAmount = 0.0 ) 
  { 
    $this->RequestFields['card_number'] = $cardNumber; 
    $this->RequestFields['card_exp_date'] = $expDate; 
    $this->RequestFields['transaction_amount'] = $tranAmount; 
  }

  function setPost( $bool )
  { 
    $this->Post = $bool; 
  }
  
  function setDynamicData( $name, $city, $state, $zip, $mcc, $phone )
  {
    $this->RequestFields['merchant_name'] = $name;
    $this->RequestFields['merchant_city'] = $city;
    $this->RequestFields['merchant_state'] = $state;
    $this->RequestFields['merchant_zip'] = $zip;
    $this->RequestFields['merchant_category_code'] = $mcc;
    $this->RequestFields['merchant_phone'] = $phone;
  }
}

class TpgPreAuth extends TpgTransaction 
{ 
  function TpgPreAuth( $profileId = '', $profileKey = '' ) 
  { 
    $this->TpgTransaction( $profileId, $profileKey ); 
    $this->TranType = "P"; // pre-auth 
  }

  function setStoredData( $cardId, $amount ) 
  { 
    $this->RequestFields['card_id'] = $cardId; 
    $this->RequestFields['transaction_amount'] = $amount;
  }

  function setFXData( $amt, $rid, $curr)
  {
    $this->RequestFields['fx_amount'] = $amt;
    $this->RequestFields['fx_rate_id'] = $rid;
    $this->RequestFields['currency_code'] = $curr;
  }

  function setEcommInd( $ind )
  {
    $this->RequestFields['moto_ecommerce_ind'] = $ind;
  }
}

class TpgSale extends TpgTransaction 
{ 
  function TpgSale( $profileId, $profileKey ) 
  { 
    $this->TpgTransaction($profileId, $profileKey); 
    $this->TranType = "D"; 
  }

  function setStoredData( $cardId, $amount ) 
  { 
    $this->RequestFields['card_id'] = $cardId; 
    $this->RequestFields['transaction_amount'] = $amount;
  }

  function setFXData( $amt, $rid, $curr)
  {
    $this->RequestFields['fx_amount'] = $amt;
    $this->RequestFields['fx_rate_id'] = $rid;
    $this->RequestFields['currency_code'] = $curr;
  }

  function setEcommInd( $ind )
  {
    $this->RequestFields['moto_ecommerce_ind'] = $ind;
  }
}

class TpgCredit extends TpgTransaction 
{ 
  function TpgCredit( $profileId, $profileKey ) 
  { 
    $this->TpgTransaction($profileId, $profileKey); 
    $this->TranType = "C";
  }

  function setStoredData( $cardId, $amount ) 
  { 
    $this->RequestFields['card_id'] = $cardId; 
    $this->RequestFields['transaction_amount'] = $amount;
  }
} 

class TpgSettle extends TpgTransaction 
{ 
  function TpgSettle( $profileId, $profileKey, $tranId, $settleAmount = 0 ) 
  { 
    $this->TpgTransaction($profileId,$profileKey); 
    $this->RequestFields['transaction_id'] = $tranId; 
    $this->RequestFields['transaction_amount'] = $settleAmount; 
    $this->TranType = "S"; 
  } 

  function setSettlementAmount( $settleAmount ) 
  { 
    $this->RequestFields['transaction_amount'] = $settleAmount; 
  }
} 

class TpgRefund extends TpgTransaction 
{ 
  function TpgRefund( $profileId, $profileKey, $tranId ) 
  { 
    $this->TpgTransaction($profileId, $profileKey); 
    $this->RequestFields['transaction_id'] = $tranId; 
    $this->TranType = "U"; 
  } 

  function setStoredData( $cardId, $amount ) 
  { 
    $this->RequestFields['card_id'] = $cardId; 
    $this->RequestFields['transaction_amount'] = $amount;
  }
}

class TpgVoid extends TpgTransaction 
{ 
  function TpgVoid( $profileId, $profileKey, $tranId ) 
  { 
    $this->TpgTransaction($profileId, $profileKey); 
    $this->RequestFields['transaction_id'] = $tranId; 
    $this->TranType = "V"; 
  }

  function setStoredData( $cardId, $amount ) 
  { 
    $this->RequestFields['card_id'] = $cardId; 
    $this->RequestFields['transaction_amount'] = $amount;
  } 
} 

class TpgOffline extends TpgTransaction 
{ 
  function TpgOffline( $profileId, $profileKey, $authCode ) 
  { 
    $this->TpgTransaction($profileId, $profileKey); 
    $this->RequestFields['auth_code'] = $authCode; 
    $this->TranType = "O"; 
  }

  function setStoredData( $cardId, $amount ) 
  { 
    $this->RequestFields['card_id'] = $cardId; 
    $this->RequestFields['transaction_amount'] = $amount;
  }
}

class TpgStoreData extends TpgTransaction 
{ 
  function TpgStoreData( $profileId, $profileKey ) 
  { 
    $this->TpgTransaction($profileId, $profileKey); 
    $this->TranType = "T"; 
  } 
}

class TpgRemoveData extends TpgTransaction 
{ 
  function TpgRemoveData( $profileId, $profileKey, $cardId) 
  { 
    $this->TpgTransaction($profileId, $profileKey);
    $this->RequestFields['card_id'] = $cardId;
    $this->TranType = "X"; 
  } 
}

?>