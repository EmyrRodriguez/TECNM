<?php 

require_once('tcpdf.php');

class MYPDF extends TCPDF {

    //Page header
	public function Header() {
        // Logo
		//$image_file = K_PATH_IMAGES.'logo_example.jpg';
		//$this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
		$this->SetFont('helvetica', 'B', 20);
        // Title
		$this->Cell(0, 15, 'Factura', 0, false, 'C', 0, '', 0, false, 'M', 'M');
	}

    // Page footer
	public function Footer() {
        // Position at 15 mm from bottom
		$this->SetY(-15);
        // Set font
		$this->SetFont('helvetica', 'I', 8);
        // Page number
		$this->Cell(0, 10, 'Pagina '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
	}
}


$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 003');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

/**************************************************************/

// set font
$pdf->SetFont('times', 'BI', 12);

// add a page
$pdf->AddPage();


$var = "Aqui pueden ir todas las consultas";

$html = '.main{padding: 5%; font-size: 14px; font-family: regular}
   .header, .body-factura, .body-partidas, footer{text-transform: uppercase;color: #434958;}
   .folio{color: #77bbe2;} 
   .logoCliente{border: solid 2px #434958; height: 150px; margin-top: 30px;}
   .logoCliente > img{padding-top: 20%; padding-left: 2%; padding-right: 2%;}
   .factura{ color: #434958; font-family: bold; }
   .underline{border-bottom: solid 2px #434958; margin-top: 15px; margin-bottom: 15px;}
   .line-height{line-height: 30px;}
   .underline-alt{border-bottom: solid 2px #e8e8e7; margin-top: 15px; margin-bottom: 15px;}
   /*.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {border-top: 0px solid #DDD!important; padding: 17px;}*/
   thead > tr > th{background: #e8e8e7 !important; color: #434958!important; padding: 15px!important;}
   .body-partidas{text-align: center}
   .table > thead > tr > th {border-bottom: 0px solid #DDD!important;}
   .table > thead > tr > th, .table > tbody > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {border-top: 0px solid #DDD; padding-bottom: 13px; padding-top: 0px}
   .body-partidas > tbody > tr {background-color: #e8e8e7!important;}
   .table > tfoot > tr > td{padding: 4px; background: #FFF!important; color: #434958!important}
   .importeletra{background: #e8e8e7!important; padding: 12px!important;}
   .importetotal{padding: 12px!important;}
   .importetotal, .importetotal > strong{background: #434958!important; color: #FFF!important; text-align: center; font-family: semibold!important;}
   strong{font-family: semibold!important; color: #434958}
   .pago{background: #e8e8e7; padding: 3%; }
   .text-normal{text-transform: none!important}
   .gray{
      color: #666767!important; 
      white-space: pre;           /* CSS 2.0 */
      white-space: pre-wrap;      /* CSS 2.1 */
      white-space: pre-line;      /* CSS 3.0 */
      white-space: -pre-wrap;     /* Opera 4-6 */
      white-space: -o-pre-wrap;   /* Opera 7 */
      white-space: -moz-pre-wrap; /* Mozilla */
      white-space: -hp-pre-wrap;  /* HP Printers */
      word-wrap: break-word;      /* IE 5+ */ 
   }
   footer{text-align: center; font-family: semibold; margin-top: 3%;}
   .qr{position: relative; top: 25px; left: -6px;}
   .qr > img{max-width: 120%;}';

$html =$html . '<div role="main" class="main">
   <div class="row header">
      <div class="col-sm-9 col-md-9">
         <div>Rio Suchiate 431, S/N Lomas del Valle Garza García San Pedro Garza García, Nuevo León, México</div>
         <div><strong>C.P.</strong> <span id="cp">66256</span></div>
         <div><strong>RFC:</strong> <span id="rfc">LOU1307158L1</span></div>
         <div><strong>Régimen Fiscal: </strong> <span id="regimen">Régimen General</span></div><br />

         <div><strong>Fecha/Hora Certificación:</strong></div> 
         <div><span id="fechacertificacion">2015-09-09T12:38:15</span></div>
         <div><strong>Fecha de Emisión:</strong></div>
         <div><span id="fechaemision">2015-09-09T12:38:15</span></div><br />

         <div><strong>Folio fiscal:</strong> CAAD3C36-DF05-468D-B511-A74F69B714E7</div>
         <div><strong>No. Serie certificado SAT:</strong> 00001000000201455572</div>
      </div>
      <div class="col-sm-3 col-md-3">
         <div class="logoCliente">
            
         </div><br /><br />
         <h3 class="factura text-right">Factura <span class="folio"> A - 20</span></h3>
      </div>
      <div class="col-sm-12 col-md-12">
         <div class="underline"></div> 
      </div>
   </div>

   <div class="row body-factura">
      <div class="col-sm-12 col-md-12 line-height">
         <div class="line-height"><strong>Receptor del comprobante Fiscal</strong></div>
         <div class="line-height"><strong>Comprobante Global de operaciones con público en General</strong></div>
      </div>
      <div class="col-sm-6 col-md-6">
         <div>Rio Suchiate 431, S/N Lomas del Valle Garza García San Pedro Garza García, Nuevo León, México</div>
         <div><strong>C.P.</strong> <span id="cp">66256</span></div>
      </div>
      <div class="col-sm-6 col-md-6"></div>

      <div class="col-sm-12 col-md-12">
         <div class="underline-alt"></div> 
      </div>
   </div>

   <div class="row body-corridas">
      <div class="col-md-12">
         <table class="table body-partidas">
            <tbody>
               <thead>
                  <tr>
                     <th><strong>Cantidad</strong></th>
                     <th><strong>Unidad de Medida</strong></th>
                     <th><strong>Descripción</strong></th>
                     <th><strong>Precio Unitario</strong></th>
                     <th><strong>Importe</strong></th>
                  </tr>
               </thead>
               <tr>
                  <td>1.0</td> 
                  <td>Servicio</td>
                  <td>Servicio de Software Agosto 2014</td>
                  <td>$47.41</td>
                  <td>$47.41</td>
               </tr>
               <tr>
                  <td>1.0</td>
                  <td>Servicio</td>
                  <td>Servicio de Software Agosto 2014</td>
                  <td>$47.41</td>
                  <td>$47.41</td>
               </tr>
               <tr>
                  <td>1.0</td>
                  <td>Servicio</td>
                  <td>Servicio de Software Agosto 2014</td>
                  <td>$47.41</td>
                  <td>$47.41</td>
               </tr>
            </tbody>
            <tfoot>
               <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><strong>SUBTOTAL</strong></td>
                  <td>$ 47.41</td>
               </tr>
               <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><strong>IVA - 16.00%</strong></td>
                  <td>$ 7.59</td>
               </tr>
               <tr>
                  <th colspan="3" class="importeletra">cincuenta y cinco pesos MN</th>
                  <th class="importetotal"><strong>TOTAL</strong></th>
                  <th class="importetotal"><strong>$ 55.00</strong></th>
               </tr>
            </tfoot>
         </table>
      </div>
   </div>

   <div class="row body-sellos">
      <div class="col-sm-6 col-md-6">
         <div class="pago">
            <div><strong>Forma de Pago: </strong> <span id="formapago text-normal">Pago realizado en una sola exhibición</span></div>
            <div><strong>Método de Pago: </strong> <span id="metodopago text-normal">No identificado</span></div>
            <div><strong>Lugar de Expedición: </strong> <span id="lugarexpedicion text-normal">66256</span></div>
         </div>
      </div>
      <div class="col-sm-6 col-md-6"></div>

      <div class="col-sm-12 col-md-12">
         <br /><br />
         <div class="gray"><strong>Cadena original del complemento de certificación digital del SAT</strong></span></div>
         <div class="gray">||3.2|2015-09-09T12:39:07|ingreso|Pago hecho en una sola exhibición|47.41|MXP|55.00|No Identificado|Nuevo León|LOU1307158L1|LA LOUNGE, S.A. DE C.V.|Río Suchiate|431|S/N|Lomas del Valle Garza García|San Pedro Garza García|Nuevo León|México|66256|Nuevo León|México|Régimen General|XAXX010101000|COMPROBANTE GLOBAL DE OPERACIONES CON PÚBLICO EN GENERAL|Río Suchiate|431|S/N|Lomas del Valle Garza García|San Pedro Garza García|Nuevo León|Méxco|66256|1|Servicio|Servicio de Software Agosto</div>
         <br />
         <div class="col-sm-2 col-md-2">
            <div class="qr">
               
            </div>
         </div>
         <div class="col-sm-10 col-md-10">
            <div class="col-md-12">
               <br />
               <div class="gray"><strong>Sello digital del CFDI:</strong></div>
               <div class="gray">HCAOyC1aWLRwzneQhxwbNYrEYE7kdbax9BbbBW1VYi+ZNSPH+Dcit7tLsCfJ+mB8AJn8fDaidMj+DiZf13jql2CU0d1BMcAu1Lu XR4M1X0vvxzdi2D29BdMyOaIuIfgaXtoWitt47njWVeZlN+V5f2iiIlP/qcZm6vEhJEIdlM=
               </div>
            </div>
            <div class="col-sm-12 col-md-12">
               <br />
               <div class="gray"><strong>Sello digital del SAT:</strong></div>
               <div class="gray">jjfRY3pB0j1Y+S59akVUpMb/OH1JmRjSzKRZKBSS2rAOIxxcQpd1zCLLj4UvQHKUl6ueSo2f8wBXZM5UPAGAK+rQdiqf6FKzNV oUOfpaCFtRTIxNA4asKZMiLBVghezRrKO8tXkveB9jZ/SI3S7IOBmuZZhDu2qaOdKpK9GmI=</div>
            </div>
         </div>         
      </div>
   </div>

   <footer>
      Factura emitida por la mejor herramienta para administrar tus rentas:
      <div class="underline"></div> 
      
   </footer>
</div>';


//$html = '<style>'.file_get_contents(_BASE_PATH.'stylesheet.css').'</style>';

$style = array(
	'border' => 2,
	'vpadding' => 'auto',
	'hpadding' => 'auto',
	'fgcolor' => array(0,0,0),
	'bgcolor' => false, //array(255,255,255)
	'module_width' => 1, // width of a single module in points
	'module_height' => 1 // height of a single module in points
	);

$params = $pdf->serializeTCPDFtagParameters(array('www.tcpdf.org', 'QRCODE,H', 20, 150, 50, 50, $style, 'N'));

$filename = "test.php";
ob_start();
include($filename);
//$html = ob_get_contents();
ob_end_clean();

$pdf->writeHTML($html, true, false, true, false, 0);


$pdf->Output('ejemplo.pdf', 'I');
?>