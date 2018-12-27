<?php
namespace System\Datalayer;


class DLUserSession extends \System\Datalayers\Main
{

    public function insertUserSession( $id , $session , $last_action , $last_ip , $last_login_date ){
        $postData = array(
            "idus" => $id,
            "usss" => $session,
            "usla" => $last_action,
            "usli" => $last_ip,
            "uslld" => $last_login_date
        );
        $url = '/usss/insert';
        var_dump($postData);
        var_dump(json_encode($postData));
        $userSession = $this->curlAppsJson($url,$postData);

        if($userSession->ec == 0) return true ;
        return false ;
    }

    public function checkUserSession($id , $session){
        $url = '/usss/search' ;
        var_dump("<pre>");
        var_dump($session);

        $postData['conditions'][] = $this->curlConditions("=" , "user_id" , $id );
        $postData['conditions'][] = $this->curlConditions("=" , "user_session" , $session );
        $postData['orders'][] = $this->curlOrders("desc" , "user_last_action" );

        var_dump($postData);
        var_dump(json_encode($postData));
        $result = $this->curlAppsJson( $url , $postData);
        $result = $this->curlFindData($result);
        var_dump($result);

        if( isset($result[0]) ) return true ;
        return false ;

    }









}

