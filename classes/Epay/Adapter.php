<?php


class Epay_Adapter {
    
    $api_base = Kohana::$config->load('epay-wrapper.settings.api_base');
    $appid = Kohana::$config->load('epay-wrapper.settings.appid');
    $secret = Kohana::$config->load('epay-wrapper.settings.secret');
    $deviceid = Kohana::$config->load('epay-wrapper.settings.deviceid');
    $token = '';
    $endpoint = '';
    $params = Array();
    $auth_params = Array();
    $key = '';
    
    public function __construct() {
          $this->key = md5(now() . $appid . $secret);
          $this->auth_params = Array(
                'APPID' => $this->appid,
                'DEVICEID' => $this->deviceid,
                'KEY' => $this->key,
          )          
    }
    
    private function get_result() {
          $url = $this->api_base . $this->endpoint . "?" . http_build_query($this->auth_params) . "&" . http_build_query($this->params);
          $result = file_get_contents($url);
          return json_deconde($result);
    }
    
    
    private function request_token() {
          $this->endpoint = 'api/start';
          $this->get_result();
    }
    
    
    private function get_token() {
          $this->endpoint = 'code/get';
          $result = $this->get_result();
          if($result['status'] == 'OK') {
              $this->endpoint = 'token/get';
              $result = $this->get_result();
              $this->token = $result['TOKEN'];
              unset($this->auth_params['key']);
              $this->auth_params['token'] = $this->token;
          
          } else {
          
              $this->error($result)
          }
    }
    
    public function error($result) {
    
          throw new Exception('Error. Please review result set:' . print_r($result, 1));
          
    }
    
    
    public function init() {
          $this->request_token();
          sleeep(10000); //We should delay the token request for some seconds;
          $this->get_token();
    }
    
    public function get_user_info() {
          $this->endpoint = 'user/info';
          return $this->get_result(); 
    
    }
    
    public function get_payment_options() {
          $this->endpoint = 'user/info/pins';
          return $this->get_result(); 
    }
    
    public function get_account_balance($account_id) {
          $return = Array();
          $this->params = Array("PINS" => $account_id);
          $this->endpoint = 'user/info/pins/balance';
          $result = $this->get_result();
          if($result['status'] == "CHECK") {
                  $this->params = Array(
                        "PINS" => $result['ID'];
                        "CHECKIDS" =>$result['checkid'];            
                        );
                  $new_result = $this->get_result();
                  $result = $new_result;      
          }
          
          return $result; 
    }    
    
    public function get_merchants($id = null) {
          $this->endpoint = 'bills/merchants';
          if($id) {
                $this->endpoint .= '/view';
                $this->params = Array("merchant" => $id);
          }
          
          return $this->get_result();
    }
    
}

