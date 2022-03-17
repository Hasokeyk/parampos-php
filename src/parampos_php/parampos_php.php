<?php
    
    namespace Hasokeyk\parampos_php;
    
    class parampos_php{
        
        public $test_url = 'https://test-dmz.param.com.tr:4443/turkpos.ws/service_turkpos_test.asmx?WSDL';
        public $live_url = 'https://posws.param.com.tr/turkpos.ws/service_turkpos_prod.asmx?WSDL';
        
        public $client_username = 'Test';
        public $client_code     = 10738;
        public $client_password = 'Test';
        public $guid            = '0c13d406-873b-403b-9c09-a5766840d98c';
        public $client;
        public $installment     = 1;
        
        public $ip      = null;
        public $ref_url = null;
        
        public $error_callback_url   = '/?action=error';
        public $success_callback_url = '/?action=success';
        
        public function __construct($test_mode = true, $client_username = null, $client_code = null, $client_password = null, $guid = null, $error_callback_url = null, $success_callback_url = null){
            
            $this->ip                   = $_SERVER['REMOTE_ADDR'];
            $this->ref_url              = $this->ref_url();
            $this->error_callback_url   = $error_callback_url ?? $this->ref_url().'/?action=error';
            $this->success_callback_url = $success_callback_url ?? $this->ref_url().'/?action=success';
            
            if($test_mode == false){
                
                $this->client_username      = $client_username;
                $this->client_code          = $client_code;
                $this->client_password      = $client_password;
                $this->guid                 = $guid;
                $this->error_callback_url   = $error_callback_url;
                $this->success_callback_url = $success_callback_url;
                
                $this->client = new \SoapClient($this->live_url);
                
            }
            else{
                $this->client = new \SoapClient($this->test_url);
            }
            
        }
        
        public function pay_3d($holder_name = 'Test', $card_number = null, $card_month = null, $card_year = null, $card_cvc = null, $phone_number = null, $order_desc = '', $total = 1, $order_id = 1){
            
            $total                                 = number_format($total, 2, ',', '');
            $auth                                  = $this->auth($order_id, $total);
            $total_payment_transaction             = new Pos_Odeme($this->client_code, $this->client_username, $this->client_password, $this->guid, $holder_name, $card_number, $card_month, $card_year, $card_cvc, $phone_number, $this->error_callback_url, $this->success_callback_url, $order_id, $order_desc, $this->installment, $total, $total, '', $this->ip, $this->ref_url);
            $total_payment_transaction->Islem_Hash = $this->client->SHA2B64($auth)->SHA2B64Result;
            $response                              = $this->client->Pos_Odeme($total_payment_transaction);
            return $response;
            
        }
        
        function auth($order_id = null, $total = null){
            
            $auth_data = [
                $this->client_code,
                $this->guid,
                $this->installment,
                $total,
                $total,
                $order_id,
                $this->error_callback_url,
                $this->success_callback_url,
            ];
            $auth_data = implode('', $auth_data);
            
            $obj                     = new \stdClass();
            $obj->Data               = $auth_data;
            $obj->G                  = new \stdClass();
            $obj->G->CLIENT_CODE     = $this->client_code;
            $obj->G->CLIENT_USERNAME = $this->client_username;
            $obj->G->CLIENT_PASSWORD = $this->client_password;
            return $obj;
            
        }
        
        public function test_card($card_type = 'visa'){
            
            if($card_type == 'visa'){
                $card = [
                    'card_number'   => '4546711234567894',
                    'month'         => '12',
                    'year'          => '26',
                    'security_code' => '000',
                    '3d_pass'       => 'a',
                ];
            }
            else{
                $card = [
                    'card_number'   => '5401341234567891',
                    'month'         => '12',
                    'year'          => '26',
                    'security_code' => '000',
                    '3d_pass'       => 'a',
                ];
            }
            
            return $card;
            
        }
        
        public function ref_url(){
            $server = $_SERVER;
            return $server['REQUEST_SCHEME'].'://'.$server['SERVER_NAME'].$server['REQUEST_URI'];
        }
    }
    
    class Pos_Odeme{
        public $G;
        public $SanalPOS_ID;
        public $GUID;
        public $KK_Sahibi;
        public $KK_No;
        public $KK_SK_Ay;
        public $KK_SK_Yil;
        public $KK_CVC;
        public $KK_Sahibi_GSM;
        public $Hata_URL;
        public $Basarili_URL;
        public $Siparis_ID;
        public $Siparis_Aciklama;
        public $Taksit;
        public $Islem_Tutar;
        public $Toplam_Tutar;
        public $Islem_Hash;
        public $Islem_ID;
        public $IPAdr;
        public $Ref_URL;
        public $Data1;
        public $Data2;
        public $Data3;
        public $Data4;
        public $Data5;
        public $Islem_Guvenlik_Tip;
        
        public function __construct($CLIENT_CODE, $CLIENT_USERNAME, $CLIENT_PASSWORD, $guid, $ccSahibi, $ccNo, $ccSkAy, $ccSkYil, $ccCvc, $ccSahibiGsm, $hataUrl, $basariliUrl, $siparisId, $siparisAciklama, $taksit, $islemtutar, $toplamTutar, $islemId, $ipAdr, $RefUrl, $dataBir = null, $dataIki = null, $dataUc = null, $dataDort = null, $dataBes = null){
            $this->G                  = new \stdClass();
            $this->G->CLIENT_CODE     = $CLIENT_CODE;
            $this->G->CLIENT_USERNAME = $CLIENT_USERNAME;
            $this->G->CLIENT_PASSWORD = $CLIENT_PASSWORD;
            $this->GUID               = $guid;
            $this->KK_Sahibi          = $ccSahibi;
            $this->KK_No              = $ccNo;
            $this->KK_SK_Ay           = $ccSkAy;
            $this->KK_SK_Yil          = $ccSkYil;
            $this->KK_CVC             = $ccCvc;
            $this->KK_Sahibi_GSM      = $ccSahibiGsm;
            $this->Hata_URL           = $hataUrl;
            $this->Basarili_URL       = $basariliUrl;
            $this->Siparis_ID         = $siparisId;
            $this->Siparis_Aciklama   = $siparisAciklama;
            $this->Taksit             = $taksit;
            $this->Islem_Tutar        = $islemtutar;
            $this->Toplam_Tutar       = $toplamTutar;
            $this->Islem_Guvenlik_Tip = '3D';
            $this->Islem_Hash         = null;
            $this->Islem_ID           = $islemId;
            $this->IPAdr              = $ipAdr;
            $this->Ref_URL            = $RefUrl;
            $this->Data1              = $dataBir;
            $this->Data2              = $dataIki;
            $this->Data3              = $dataUc;
            $this->Data4              = $dataDort;
            $this->Data5              = $dataBes;
        }
    }