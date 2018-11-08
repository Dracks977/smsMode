# smsMode
SmsMode composer

Init the class : 
  `$sms = new SmsMode(<"api key">, <Need SSL default true>, <"sender name default use default sender">);`
  
Send a sms :
  ` $sms->send(<"phonenumber,...">, <"sms text">, <stop sms option default false>)`
  return :
    ```
      object(stdClass)#2 (4) {
        ["code"]=>
          string(1) "0"
        ["msg"]=>
          string(84) "Accepté - le message a été accepté par le système et est en cours de traitement"
        ["desc"]=>
          string(7) "Accept"
        ["smsID"]=>
          string(13) "Vz4lHUWycCKb"
       }
    ```
  
Get sms sold : 
  `$sms->getSolde()`
  return :
    ```
      object(stdClass)#2 (2) {
        ["code"]=>
          string(1) "0"
        ["msg"]=>
        string(5) "32.0"
      }
    ```
