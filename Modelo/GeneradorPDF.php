<?php
require_once APP_ROOT . '/Modelo/Entrada.php';

require_once APP_ROOT . '/lib/tfpdf/tfpdf.php';
require_once APP_ROOT . '/lib/fpdf/fpdf.php';
require_once APP_ROOT . '/lib/qrcode/qrcode.class.php';

const POS_INFO_EVENTO_X=0;
const POS_INFO_EVENTO_Y=0;

const POS_QR_X=170;
const POS_QR_Y=10;

const POS_CODE_X=167;
const POS_CODE_Y=50;

const POS_CONDICIONES_X=10;
const POS_CONDICIONES_Y=260;

const POS_IMAGEN_X=10;
const POS_IMAGEN_Y=60;

class GeneradorPDF{
    
    public static function generaPDF($arrayEntradas){
        $pdf=new tFPDF();

        foreach($arrayEntradas as $entrada){
            $pdf->AddPage();
            
            // Add a Unicode font (uses UTF-8)
            $pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
            $pdf->SetFont('DejaVu','',12);
            
            //$pdf->SetFont('Arial','',12);
            
            self::escribeEntradaEnDPF($pdf,$entrada);                       
        }
        
        
        $pdf->Output();
    }
    
    /**
     * 
     * @param tFPDF $pdf
     * @param Entrada $entrada
     */
    private static function escribeEntradaEnDPF(&$pdf,$entrada){
        
        self::seccionInfoEvento($pdf, $entrada);

        $pdf->Text(POS_CODE_X, POS_CODE_Y, $entrada->codigo);        
        $qrcode=new QRcode($entrada->codigo,"H");
        $qrcode->displayFPDF($pdf,  POS_QR_X, POS_QR_Y, 30);
        
        self::seccionArtwork($pdf);
        
        self::seccionCondiciones($pdf);
    }
    
    /**
     * 
     * @param tFPDF $pdf
     * @param Entrada $entrada
     */
    private static function seccionInfoEvento(&$pdf,$entrada){
        //$pdf->SetXY(POS_INFO_EVENTO_X, POS_INFO_EVENTO_Y);
        $evento=$entrada->getEvento();
        $tipoentrada=$entrada->getTipoEntrada();
        
        $out=$evento->nombre . "\n";
        $out.=$tipoentrada->nombre . " " . $tipoentrada->precio . " €\n";
        $out.=$evento->local . ", " . $evento->direccion . ", " . $evento->ciudad . ", " . $evento->pais . "\n";
        $out.=$evento->fecha_inicio;
        
        $pdf->MultiCell(150, 10, $out,1,"L");
    }
    
    /**
     * 
     * @param tFPDF $pdf
     */
    private static function seccionArtwork(&$pdf){
        $pdf->Image(APP_ROOT . "/imagen.png",POS_IMAGEN_X,POS_IMAGEN_Y,0,100);
    }
    
    /**
     * 
     * @param tFPDF $pdf
     */
    private static function seccionCondiciones(&$pdf){
        $pdf->SetXY(POS_CONDICIONES_X, POS_CONDICIONES_Y);
        
        
        $out="The return or refund of this ticket is not permitted. In the case of ticket falsification, only the first ticket presented will be considered valid." . 
        "The Organisation will be able to verify the validity of this ticket by use of guest lists and ticket scanners." .
        "MYTickets distributes tickets on behalf of the event organiser, remaining exempt from any responsibility related to the event.";
        
        $pdf->SetFont('DejaVu','',8);
        $pdf->MultiCell(0, 5, $out,1,"L");
    }
    
    
}

?>