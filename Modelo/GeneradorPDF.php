<?php
require_once APP_ROOT . '/Modelo/Entrada.php';

require_once APP_ROOT . '/lib/tfpdf/tfpdf.php';
require_once APP_ROOT . '/lib/tcpdf/tcpdf.php';
require_once APP_ROOT . '/lib/fpdf/fpdf.php';
require_once APP_ROOT . '/lib/qrcode/qrcode.class.php';

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

class GeneradorPDF{
    
    public static function generaPDF($arrayEntradas,$modo="normal"){
        $pdf=new TCPDF();

        foreach($arrayEntradas as $entrada){
            $pdf->AddPage();
            
            $pdf->SetFont('dejavusans', '', 12, '', true);
            
            self::escribeEntradaEnDPF($pdf,$entrada);                       
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
     * 
     * @param TCPDF $pdf
     * @param Entrada $entrada
     */
    private static function escribeEntradaEnDPF(&$pdf,$entrada){
        
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
    
    
}

?>