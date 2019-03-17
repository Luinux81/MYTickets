<?php
/**
 * Clase Tool | Modelo/Tool.php
 *
 * @author      Luis Breña Calvo <luinux81@gmail.com>
 * @version     v.0.1
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once APP_ROOT . '/Modelo/ModeloBD.php';
require_once APP_ROOT . '/lib/PHPMailer/src/PHPMailer.php';
require_once APP_ROOT . '/lib/PHPMailer/src/Exception.php';
require_once APP_ROOT . '/lib/PHPMailer/src/SMTP.php';


/**
 * Clase con funciones de utilidad para las demas clases.
 *
 */
class Tool
{
    
    /**
     * Concatena los dos parametros con un espacio blanco en medio.
     * 
     * @param string $date Fecha.
     * @param string $time Hora.
     * 
     * @return string
     */
    public static function adaptaFechaHora($date,$time){
        return $date . " " . $time;
    }
    
    /**
     * Obtiene la fecha o la hora de una fecha en formato dd-MM-YYYY HH:mm:ss pasada como parametro de entrada.
     * 
     * @param string $datetime Fecha
     * @param boolean $fecha Determina si devuelve la fecha(true, valor por defecto) o la hora(false).
     * 
     * @return string
     */
    public static function separaFechaHora($datetime,$fecha=true){
        $aux=substr($datetime,0,10);
        if(!$fecha)
        {
            $aux=substr($datetime,11,5);
        }
        
        return $aux;
    }
    
    /**
     * Transforma una fecha al formato de fechas de MySQL
     * @param string $fecha
     * @return string
     */
    public static function formatoFechaMysql($fecha){
        return date('Y-m-d H:i:s',strtotime($fecha));
    }
    
    /**
     * Obtiene un handler de la conexion con la base  de datos.
     * 
     * @return ModeloBD
     */
    public static function conectar(){
        return ModeloBD::getConexion();    
    }
    
    /**
     * Asigna null al valor pasado como parametro.
     * 
     * @param ModeloBD $dbHandler
     */
    public static function desconectar(&$dbHandler){
        $dbHandler=null;
    }
    
    /**
     * Obtiene una cadena de caracteres aleatorios con la longitud y el alfabeto pasados como parametros.
     * 
     * @param int $longitud Longitud de la cadena de salida.
     * @param string $alfabeto Alfabeto del que obtener los caracteres aleatorios
     * 
     * @return string
     */
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
    
    /**
     * Obtiene la url base de la aplicacion.
     * 
     * @return string
     */
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
    
    /**
     * Envia un email utilizando la libreria PHPMailer.
     * 
     * @param string $direccion Direccion de email del destinatario.
     * @param string $from Direccion de email del remitente.
     * @param string $fromNombre Nombre del remitente.
     * @param string $asunto Asunto del email.
     * @param string $mensaje Cuerpo del email.
     * @param string $cabeceras Cabeceras.
     * @param string $pdf String para adjuntar al email.
     * 
     * @return boolean True si el envio es correcto, false en caso contrario.
     */
    public static function enviaEmail($direccion,$from,$fromNombre,$asunto,$mensaje,$cabeceras,$pdf=""){
        $mail=new PHPMailer(true);
        
        try{
            $mail->SMTPDebug=0;
            
            //1and1 hack
            if(substr_count(APP_URL, "localhost")>0){
                $mail->isSMTP();
            }
            else{
                $mail->isSendmail();
            }
            //
            
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
            self::log("Mensaje enviado a " . $direccion);
            return true;
        }catch (Exception $e){
            self::log("Error enviado mensaje a " . $direccion);
            self::log("Error: [" . $e->getCode() . "] " . $e->errorMessage());
            return false;
        }
    }
    
    
    /**
     * Inserta un mensaje en el archivo de log con marca de tiempo.
     * 
     * @param string $mensaje Mensaje para insertar en el archivo de log.
     */
    public static function log($mensaje){
        error_log(date('[Y-m-d H:i] '). " " . $mensaje . PHP_EOL, 3, LOG_FILE);
    }
}

