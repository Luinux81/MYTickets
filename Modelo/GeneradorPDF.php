<?php
/**
 * Clase GeneradorPDF | Modelo/GeneradorPDF.php
 *
 * @author      Luis Bre�a Calvo <luinux81@gmail.com>
 * @version     v.0.1
 */

require_once APP_ROOT . '/Modelo/Entrada.php';
require_once APP_ROOT . '/Modelo/Venta.php';
require_once APP_ROOT . '/Modelo/Usuario.php';

require APP_ROOT . '/vendor/autoload.php';

//require_once APP_ROOT . '/lib/tcpdf/tcpdf.php';
require_once APP_ROOT . '/lib/qrcode/qrcode.class.php';


/* Modelo 1*/
const POS_INFO_EVENTO_X=10;
const POS_INFO_EVENTO_Y=10;

const POS_QR_X=165;
const POS_QR_Y=15;

const POS_CODE_X=165;
const POS_CODE_Y=45;

const POS_CONDICIONES_X=10;
const POS_CONDICIONES_Y=265;

const POS_IMAGEN_X=25;
const POS_IMAGEN_Y=60;

const POS_BARCODE_X=50;
const POS_BARCODE_Y=240;


/*Modelo 2 y 3*/
const HEAD_X=10;
const HEAD_Y=10;
const GLOBAL_WIDTH=190;
const HEAD_LINE_HEIGHT=15;

const HEAD2_LINE_HEIGHT=8;
const HEAD2_Y=30;

const BODY_LINE_HEIGHT=5;
const OFFSETY_CENTRAL=10;

const POSY_PRECIO=170;
const POSY_DATA=220;


/*Modelo 2*/

/**
 * Clase para generar tickets de entradas y listados de eventos en formato PDF.
 *
 */
class GeneradorPDF{
    private static $modelo2Info=array(
        "top1"=>"BIENVENIDO",
        "top2"=>"WELCOME",
        "head_i"=>"",
        "head_i_1"=>"IMPRIME Y PRESENTA ESTA",
        "head_i_2"=>"PAGINA EL DIA DEL EVENTO",
        "head_d"=>"",
        "head_d_1"=>"PRINT AND SHOW THIS PAGE",
        "head_d_2"=>"THE DAY OF THE EVENT",
        "info_t_1"=>"",
        "info_t_2"=>"",
        "info_b_1"=>"",
        "info_b_2"=>"",
        "ticket"=>"T\nI\nC\nK\nE\nT",
        "imagen"=>"",
        "important_i_1"=>"IMPORTANTE!",
        "important_i_2"=>"RECUERDA:",
        "important_i_3"=>"\nDEBES PRESENTAR \n COPIA DEL DNI O PASAPORTE DEL COMPRADOR JUNTO CON ESTA ENTRADA \n 1 CODIGO = 1 ACCESO 1 PERSONA",
        "important_d_1"=>"IMPORTANT!",
        "important_d_2"=>"REMEMBER",
        "important_d_3"=>"\nYOU MUST SHOW  \n A COPY OF THE PASSPORT/ID CARD OF THE BUYER WITH THIS TICKET \n 1 CODE = 1 ACCESS 1 PERSON"
    );
    
    /**
     * Devuelve las entradas pasadas como parametro en formato PDF.
     * 
     * @param Entrada[] $arrayEntradas Array de entradas para generar en formato PDF.
     * @param string $modo Los valores posibles son "normal"(por defecto)(para visualizar en navegador/descargar) para o "cadena"(para adjuntar a email).
     * 
     * @return string
     */
    public static function generaPDF($arrayEntradas,$modo="normal"){
        $pdf=new TCPDF();
        
        $modelo="modelo1";
        
        foreach($arrayEntradas as $entrada){
            $pdf->AddPage();
            
            $pdf->SetFont('dejavusans', '', 12, '', true);
            
            switch($entrada->idEvento){
                case "9": //Market Transition 2019
                    self::$modelo2Info['head_i']="MARKET TRANSITION 2019";
                    self::$modelo2Info['head_d']="MARKET TRANSITION 2019";
                    self::$modelo2Info['info_t_1']="MARTES 14 MAYO 2019";
                    self::$modelo2Info['info_t_2']="PINAR JURADO\n(ALMONTE-SPAIN)\n\n CEREMONIA DE APERTURA: 22:22 H\n\n WWW.TRANSITIONFESTIVAL.ORG\n\n\n";
                    self::$modelo2Info['info_b_1']="TUESDAY 14th MAY 2019";
                    self::$modelo2Info['info_b_2']="PINAR JURADO\n(ALMONTE-SPAIN)\n\n OPENING CEREMONY: 22:22 H\n\n WWW.TRANSITIONFESTIVAL.ORG";
                    self::$modelo2Info['imagen']="http://market.transitionfestival.org/logo.jpg";
                    $modelo="modelo2";
                    break;
                case 10: //Connection Festival 2019
                    self::$modelo2Info['head_i']="ESTA ES TU ENTRADA 2019";
                    self::$modelo2Info['head_d']="THIS IS YOUR TICKET 2019";
                    self::$modelo2Info['info_t_1']="MARTES 17 SEPTIEMBRE 2019";
                    self::$modelo2Info['info_t_2']="PISCINAS NATURALES LA CODOSERA\n(BADAJOZ-SPAIN)\n\n WWW.CONNECTIONFESTIVAL.ES\n\n\n";
                    self::$modelo2Info['info_b_1']="TUESDAY 17th SEPTEMBER 2019";
                    self::$modelo2Info['info_b_2']="NATURAL POOLS LA CODOSERA\n(BADAJOZ-SPAIN)\n\n WWW.CONNECTIONFESTIVAL.ES";
                    self::$modelo2Info['imagen']="http://connection.transitionfestival.org/connection2016.jpg";
                    $modelo="modelo2";
                    break;
            }
            
            self::escribeEntradaEnDPF($pdf,$entrada,$modelo);                       
        }
        
        switch($modo){
            case "normal":
                $pdfdoc=$pdf->Output();
                break;
            case "cadena":
                $pdfdoc=$pdf->Output("","S");
                break;
        }
        
        return $pdfdoc;
    }
    
    /**
     * Devuelve un listado de acceso con todas las entradas de un evento en formato PDF. 
     * 
     * Cada linea del listado contiene el codigo de la entrada, el email y el nombre del usuario. Listado de acceso al evento.
     * 
     * @param int $eid Identificador del evento.
     */
    public static function generaPDFListadoEvento($eid){
        $aux="u.Nombre,u.Email,e.Codigo";
        
        $sql="SELECT ". $aux . " FROM entradas AS e "
            . "INNER JOIN ventas AS v ON e.Id_Venta=v.Id "
            . "INNER JOIN usuarios AS u ON v.Id_Usuario=u.Id "
            . " WHERE e.Id_Evento=? ORDER BY u.Nombre ASC";
                    
        $dbh=Tool::conectar();
        
        $query=$dbh->prepare($sql);
        $query->bindParam(1, $eid,PDO::PARAM_INT);
        $query->execute();
        
        $res=$query->fetchAll(PDO::FETCH_ASSOC);
        
        Tool::desconectar($dbh);
        
        $pdf=new TCPDF();
        
        $pdf->AddPage();
        $pdf->SetFont('dejavusans', 'B', 14, '', true);
        $pdf->Cell(0,10,'TICKETS PREVENTA',"B",1,"C");
        
        $pdf->SetFont('dejavusans', '', 12, '', true);
        
        $pdf->Cell(10,10,"#","B");
        $pdf->Cell(100,10,"Nombre","B");        
        $pdf->Cell(40,10,"Codigo","B",1);
        
        $i=0;
        foreach($res as $l){
            $i++;
        
            $pdf->SetFont('dejavusans', '', 10, '', true);
            $pdf->Cell(10,8,$i);
            //$pdf->Cell(60,8,$l['Apellidos']);
            $pdf->Cell(100,8,$l['Nombre']);
            $pdf->Cell(45,8,$l['Codigo']);
            $pdf->SetFont('dejavusans', '', 7, '', true);
            $pdf->Cell(20,8,$l['Email'],0,1);
        }
        
        return $pdf->Output('Listado.pdf');
    }
    
    /**
     * 
     * @param TCPDF $pdf
     * @param Entrada $entrada
     * @param String $modelo
     */
    private static function escribeEntradaEnDPF(&$pdf,$entrada,$modelo){
        switch($modelo){
            case "modelo1":
                self::generaModelo1($pdf, $entrada);
                break;
            case "modelo2":
                self::generaModelo2($pdf, $entrada);
                break;
        }
    }
        
    
    
    /**
     *
     * @param TCPDF $pdf
     * @param Entrada $entrada
     */
    private static function generaModelo1(&$pdf,$entrada){
        self::seccionInfoEvento($pdf, $entrada);
        
        self::seccionQR($pdf, $entrada->codigo);
        
        $imagen=$entrada->getEvento()->imagen;
        if($imagen!=null){
            self::seccionArtwork($pdf,$imagen);
        }
        
        self::seccionBarcode($pdf, $entrada->codigo);
        
        self::seccionCondiciones($pdf);
    }
    
    /**
     * 
     * @param TCPDF $pdf
     * @param Entrada $entrada
     */
    private static function seccionInfoEvento(&$pdf,$entrada){
        $pdf->SetXY(POS_INFO_EVENTO_X, POS_INFO_EVENTO_Y);
        $evento=$entrada->getEvento();
        $tipoentrada=$entrada->getTipoEntrada();
        
        $out=$evento->nombre;
        $pdf->SetFont('dejavusans', 'B', 18, '', true);
        $pdf->MultiCell(150, 10, $out, 0, 'L');
        
        $out=$tipoentrada->nombre . " " . $tipoentrada->precio . " &euro;" ;
        $pdf->SetFont('dejavusans', 'B', 14, '', true);
        //$pdf->MultiCell(150, 10, $out, 0, 'L');
        $pdf->SetX(POS_INFO_EVENTO_X);
        $pdf->writeHTML($out);
        
        $out=$evento->local . ", " . $evento->direccion . ", " . $evento->ciudad . ", " . $evento->pais;
        $pdf->SetFont('dejavusans', '', 10, '', true);
        $pdf->MultiCell(150, 5, $out , 0, 'L');
        
        $out=$evento->fecha_inicio;
        $pdf->SetFont('dejavusans', '', 12, '', true);
        $pdf->MultiCell(150, 5, $out, 0, 'L');
        
        $pdf->SetXY(POS_INFO_EVENTO_X, POS_INFO_EVENTO_Y);
        $pdf->MultiCell(190, 45, "", 1, 'L');
    }
    
    /**
     * 
     * @param TCPDF $pdf
     * @param string $codigo
     */
    private static function seccionQR(&$pdf,$codigo){
        $pdf->SetFont('dejavusans', '', 8, '', true);
        $pdf->Text(POS_CODE_X, POS_CODE_Y, $codigo);
        
        $qrcode=new QRcode($codigo,"H");
        $qrcode->displayFPDF($pdf,  POS_QR_X, POS_QR_Y, 30);
    }
    
    /**
     * 
     * @param TCPDF $pdf
     */
    private static function seccionArtwork(&$pdf, &$imagen){
        $pdf->Image('@' . stripslashes($imagen), POS_IMAGEN_X,POS_IMAGEN_Y, 160, 80, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    }
        
    /**
     * 
     * @param TCPDF $pdf
     * @param string $codigo
     */
    private static function seccionBarcode(&$pdf,$codigo){
        $pdf->SetXY(POS_BARCODE_X, POS_BARCODE_Y);
        
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => true,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 6,
            'stretchtext' => 4
        );
        
        $pdf->write1DBarcode($codigo, 'C39', '', '', '', 18, 0.4, $style, 'N');
    }
    
    /**
     * 
     * @param TCPDF $pdf
     */
    private static function seccionCondiciones(&$pdf){
        $pdf->SetXY(POS_CONDICIONES_X, POS_CONDICIONES_Y);
        
        
        $out="The return or refund of this ticket is not permitted. In the case of ticket falsification, only the first ticket presented will be considered valid." . 
        "The Organisation will be able to verify the validity of this ticket by use of guest lists and ticket scanners." .
        "MYTickets distributes tickets on behalf of the event organiser, remaining exempt from any responsibility related to the event.";
        
        $pdf->SetFont('dejavusans', '', 8, '', true);
        $pdf->MultiCell(0, 5, $out,1,"L");
    }
    
    
    
    
    
    /**
     *
     * @param TCPDF $pdf
     * @param Entrada $entrada
     */
    private static function generaModelo2(&$pdf,$entrada){
        
        
        
        self::modelo2Cabecera($pdf);
        
        ///////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////
        
        self::modelo2Centro($pdf);
  
        
        ///////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////
        
        self::modelo2Footer($pdf,$entrada);
        
    }    
  
    /**
     *
     * @param TCPDF $pdf
     */
    private static function modelo2Cabecera(&$pdf){        
        $posX=HEAD_X;
        $posY=HEAD_Y+2.5*HEAD_LINE_HEIGHT;
        
        //Cabecera bienvenidos
        $pdf->SetXY(HEAD_X,HEAD_Y);
        //$pdf->SetFont('Arial','B',48);
        $pdf->SetFont('dejavusans', 'B', 44, '', true);
        $pdf->Cell(GLOBAL_WIDTH,HEAD_LINE_HEIGHT,self::$modelo2Info['top1'],0,2,"C",0);
        
        //$pdf->SetFont('Arial','B',32);
        $pdf->SetFont('dejavusans', 'B', 28, '', true);
        $pdf->SetTextColor(150,150,150);
        $pdf->Cell(GLOBAL_WIDTH,HEAD_LINE_HEIGHT,self::$modelo2Info['top2'],0,1,"C",0);
        
        ///////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////
        
        //Seccion <<This is your ticket>> ESP
        
        $pdf->SetXY($posX,$posY);
        
        //$pdf->SetFont('Arial','B',16);
        $pdf->SetFont('dejavusans', 'B', 16, '', true);
        $pdf->SetTextColor(255,255,255);
        $pdf->SetFillColor(0,0,0);
        $pdf->Cell(GLOBAL_WIDTH/2,HEAD2_LINE_HEIGHT,self::$modelo2Info['head_i'],1,2,'C',1);
        //$pdf->SetFont('Arial','',12);
        $pdf->SetFont('dejavusans', '', 12, '', true);
        $pdf->Cell(GLOBAL_WIDTH/2,HEAD2_LINE_HEIGHT,self::$modelo2Info['head_i_1'],1,2,'C',1);
        $pdf->Cell(GLOBAL_WIDTH/2,HEAD2_LINE_HEIGHT,self::$modelo2Info['head_i_2'],1,1,'C',1);
        
        //Seccion <<This is your ticket>> ING
        $posX=GLOBAL_WIDTH/2+HEAD_X;
        $pdf->SetXY($posX,$posY);
        
        //$pdf->SetFont('Arial','B',16);
        $pdf->SetFont('dejavusans', 'B', 16, '', true);
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(GLOBAL_WIDTH/2,HEAD2_LINE_HEIGHT,self::$modelo2Info['head_i'],"LTR",2,'C',0);
        //$pdf->SetFont('Arial','',12);
        $pdf->SetFont('dejavusans', '', 12, '', true);
        $pdf->Cell(GLOBAL_WIDTH/2,HEAD2_LINE_HEIGHT,self::$modelo2Info['head_d_1'],"LR",2,'C',0);
        $pdf->Cell(GLOBAL_WIDTH/2,HEAD2_LINE_HEIGHT,self::$modelo2Info['head_d_2'],"LRB",1,'C',0);
        
    }

    /**
     *
     * @param TCPDF $pdf
     */
    private static function modelo2Centro(&$pdf){
        $posX=HEAD_X;
        $posY=HEAD_Y+2.5*HEAD_LINE_HEIGHT;
        $posYCentro=$posY+(HEAD2_LINE_HEIGHT*3)+OFFSETY_CENTRAL;
        
        //Seccion central
        $posX=HEAD_X;
        $posY=$posYCentro;
        $pdf->SetXY($posX,$posY);
        //Celda que contendra a 3 celdas más
        $pdf->Cell(0,HEAD2_LINE_HEIGHT*2+BODY_LINE_HEIGHT*14,"",1,2,'L',false);
        
        
        //Seccion central izquierda
        $pdf->SetXY($posX,$posY);
        //        $pdf->SetFont('Arial','B',12);
        $pdf->SetFont('dejavusans', 'B', 12, '', true);
        $pdf->Cell(GLOBAL_WIDTH/2-20,HEAD2_LINE_HEIGHT,self::$modelo2Info['info_t_1'],0,2,"",0);
        //$pdf->SetFont('Arial','',10);
        $pdf->SetFont('dejavusans', '', 10, '', true);
        $pdf->MultiCell(GLOBAL_WIDTH/2-20,BODY_LINE_HEIGHT,self::$modelo2Info['info_t_2'],0,2,"",0);
        
        
        $posX=HEAD_X;
        $posY=$posYCentro+40;
        $pdf->SetXY($posX, $posY);
        //$pdf->SetFont('Arial','B',12);
        $pdf->SetFont('dejavusans', 'B', 10, '', true);
        $pdf->SetTextColor(150,150,150);
        $pdf->Cell(GLOBAL_WIDTH/2-20,HEAD2_LINE_HEIGHT,self::$modelo2Info['info_b_1'],0,2,"",0);
        //$pdf->SetFont('Arial','',10);
        $pdf->SetFont('dejavusans', '', 10, '', true);
        $pdf->MultiCell(GLOBAL_WIDTH/2-20,BODY_LINE_HEIGHT,self::$modelo2Info['info_b_2'],0,2,"",0);
        
        
        //Seccion central centro
        $posX=GLOBAL_WIDTH/2-10;
        $posY=$posYCentro+0.3;
        $pdf->SetXY($posX,$posY);
        
        $pdf->SetFont('dejavusans', 'B', 32, '', true);
        $pdf->SetTextColor(255,255,255);
        $pdf->SetFillColor(0,0,0);
        
        $pdf->MultiCell(40,14.25,self::$modelo2Info['ticket'],0,'C',true);        
        
        
        //Seccion central derecha
        $posX=GLOBAL_WIDTH/2-10;
        $posY=$posYCentro+0.3;
        
        $posX=$posX+40;
        $pdf->SetXY($posX,$posY);
        $pdf->Image(self::$modelo2Info['imagen'],$posX,$posY,GLOBAL_WIDTH/2-20.5,85.8);
        
        self::modelo2CentroBajo($pdf);

        
    }
    
    /**
     *
     * @param TCPDF $pdf
     */
    private static function modelo2CentroBajo(&$pdf){
        //Seccion REMEMBER izquierda
        $posX=HEAD_X;
        $posY=POSY_PRECIO;
        $pdf->SetXY($posX,$posY);
        
        //$pdf->SetFont('Arial','B',14);
        $pdf->SetFont('dejavusans', 'B', 14, '', true);
        $pdf->SetTextColor(255,255,255);
        $pdf->SetFillColor(0,0,0);
        $pdf->Cell(GLOBAL_WIDTH/2,BODY_LINE_HEIGHT,self::$modelo2Info['important_i_1'],1,2,'C',1);
        
        //$pdf->SetFont('Arial','B',11);
        $pdf->SetFont('dejavusans', 'B', 11, '', true);
        $pdf->Cell(GLOBAL_WIDTH/2,BODY_LINE_HEIGHT,self::$modelo2Info['important_i_2'],1,2,'C',1);
        
        //$pdf->SetFont('Arial','',10);
        $pdf->SetFont('dejavusans', '', 10, '', true);
        $pdf->MultiCell(GLOBAL_WIDTH/2,BODY_LINE_HEIGHT,self::$modelo2Info['important_i_3'],1,'C',1);
        
        //Seccion REMEMBER derecha
        $posX=GLOBAL_WIDTH/2+HEAD_X;
        $posY=POSY_PRECIO;
        $pdf->SetXY($posX,$posY);
        
        
        //$pdf->SetFont('Arial','B',14);
        $pdf->SetFont('dejavusans', 'B', 14, '', true);
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(GLOBAL_WIDTH/2,BODY_LINE_HEIGHT,self::$modelo2Info['important_d_1'],"LTR",2,'C',1);
        
        //$pdf->SetFont('Arial','B',11);
        $pdf->SetFont('dejavusans', 'B', 11, '', true);
        $pdf->Cell(GLOBAL_WIDTH/2,BODY_LINE_HEIGHT,self::$modelo2Info['important_d_2'],"LR",2,'C',1);
        
        //$pdf->SetFont('Arial','',10);
        $pdf->SetFont('dejavusans', '', 10, '', true);
        $pdf->MultiCell(GLOBAL_WIDTH/2,BODY_LINE_HEIGHT,self::$modelo2Info['important_d_3'],"LBR",'C',1);
    }
    
    /**
     *
     * @param TCPDF $pdf
     * @param Entrada $entrada
     */
    private static function modelo2Footer(&$pdf,$entrada){
        //Seccion DATOS $ CODES
        $posX=HEAD_X+10;
        $posY=POSY_DATA;
        
        $pdf->SetXY($posX,$posY);
        
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(0,45,"",1,0,'L',1);
        $pdf->SetXY($posX,$posY);
        
        $v=Venta::getVenta($entrada->idVenta);
        $u=Usuario::getUsuario($v->idUsuario);
        
        $aux="DATOS DEL COMPRADOR / BUYER INFO:\n" 
                . "\nNombre:  " . $u->nombre
                . "\nEmail:   " . $u->email
                . "\nCOD:     " . $entrada->codigo;
        
        $pdf->MultiCell(0,BODY_LINE_HEIGHT,$aux,1,'L',0);
        
        //Primer código de barras
        $posX=GLOBAL_WIDTH/4;
        $posY=POSY_DATA+4*BODY_LINE_HEIGHT+5;
        
        
        //$pdf->Code39($posX,$posY,$this->codigo . $num,1,10);
        $pdf->SetXY($posX,$posY);
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255),
            'text' => false,
            'font' => 'helvetica',
            'fontsize' => 6,
            'stretchtext' => 4
        );
        
        $pdf->write1DBarcode($entrada->codigo, 'C39', '', '', '', 18, 0.4, $style, 'N');
    }

}

?>