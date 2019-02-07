<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once APP_ROOT . '/Modelo/ModeloBD.php';
require_once APP_ROOT . '/lib/PHPMailer/src/PHPMailer.php';
require_once APP_ROOT . '/lib/PHPMailer/src/Exception.php';
require_once APP_ROOT . '/lib/PHPMailer/src/SMTP.php';

class Tool
{
    public static function adaptaFechaHora($date,$time){
        return $date . " " . $time;
    }
    
    public static function separaFechaHora($datetime,$fecha=true){
        $aux=substr($datetime,0,10);
        if(!$fecha)
        {
            $aux=substr($datetime,11,5);
        }
        
        return $aux;
    }
    
    public static function formatoFechaMysql($fecha){
        return date('Y-m-d H:i:s',strtotime($fecha));
    }
    
    /**
     * 
     * @return ModeloBD
     */
    public static function conectar(){
        return ModeloBD::getConexion();    
    }
    
    public static function desconectar(&$dbHandler){
        $dbHandler=null;
    }
    
    public static function getToken($longitud,$alfabeto=""){
        $token="";
        if ($alfabeto==""){
            $alfabeto="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            //$alfabeto.="abcdefghijklmnopqrstuvwxyz";
            $alfabeto.="0123456789";
        }
            
        $max=strlen($alfabeto);
            
        for($i=0;$i<$longitud;$i++){
            $token.=$alfabeto[random_int(0, $max-1)];
        }
        
        
        return $token;
    }
    
    public static function getBaseURL(){
        /*
        $aux=sprintf(
            "%s://%s%s",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
            $_SERVER['SERVER_NAME'],
            $_SERVER['REQUEST_URI']
            );
        
        
        return substr($aux,0, strrpos($aux, "/"));
        */
        return APP_URL;
    }
    
    
    public static function enviaEmail($direccion,$from,$fromNombre,$asunto,$mensaje,$cabeceras,$pdf=""){
        $mail=new PHPMailer(true);
        
        try{
            $mail->SMTPDebug=0;
            $mail->isSMTP();
            $mail->Host=EMAIL_HOST;
            $mail->SMTPAuth=true;
            $mail->Username=EMAIL_USER;
            $mail->Password=EMAIL_PASS;
            $mail->Port=EMAIL_PORT;
            $mail->SMTPSecure="tls";
            
            $mail->setFrom($from,$fromNombre);
            $mail->addAddress($direccion);
            
            $mail->isHTML(true);
            $mail->Subject=$asunto;
            $mail->Body=$mensaje;
            
            if($pdf!=""){
                $mail->addStringAttachment($pdf, "tickets.pdf","base64","application/pdf");
            }
            
            $mail->send();
            return true;
        }catch (Exception $e){
            return false;
        }
    }
}

