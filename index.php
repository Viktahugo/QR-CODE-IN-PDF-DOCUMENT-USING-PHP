<?php
session_start();
require('fpdf.php');
include 'qrcode.php';


$data='Trial';
$options='qr-l'; 

$generator = new QRCode($data, $options);
$image = $generator->render_image();

$tempFilePath = tempnam('codes', 'qr_') . '.png';
imagepng($image, $tempFilePath);
imagedestroy($image);



class PDF extends FPDF
{
protected $B = 0;
protected $I = 0;
protected $U = 0;
protected $HREF = '';

function WriteHTML($html) 
{
	$html = str_replace("\n",' ',$html);
	$a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
	foreach($a as $i=>$e)
	{
		if($i%2==0)
		{
			if($this->HREF)
				$this->PutLink($this->HREF,$e);
			else
				$this->Write(5,$e);
		}
		else
		{
			if($e[0]=='/')
				$this->CloseTag(strtoupper(substr($e,1)));
			else
			{
				$a2 = explode(' ',$e);
				$tag = strtoupper(array_shift($a2));
				$attr = array();
				foreach($a2 as $v)
				{
					if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
						$attr[strtoupper($a3[1])] = $a3[2];
				}
				$this->OpenTag($tag,$attr);
			}
		}
	}
}

function OpenTag($tag, $attr)
{
   
    if($tag=='B' || $tag=='I' || $tag=='U')
        $this->SetStyle($tag,true);
    if($tag=='A')
        $this->HREF = $attr['HREF'];
    if($tag=='BR')
        $this->Ln(5);
    if($tag == 'H1') {
        $this->SetFont('Arial', 'B', 14);
        $this->Ln(10); 
    }
    if($tag == 'H2') {
        $this->SetFont('Arial', 'B', 12);
        $this->Ln(8); 
    }
    if($tag == 'H3') {
        $this->SetFont('Arial', 'B', 12);
        $this->Ln(6); 
    }
    if($tag == 'P') {
        
        $this->SetLeftMargin(10); 
        $this->SetX(10); 
        $this->Ln(6); 
    }
	if ($tag == 'H1') {
        
        $this->Ln(30); 
        
        
        $this->SetFont('Arial', 'B', 24);
        
        
        $this->SetX($this->GetX() + 10); 
    }
	if ($tag == 'DIV' && isset($attr['CLASS'])) {
        if ($attr['CLASS'] == 'col-xs-6') {
           
        }
        if ($attr['CLASS'] == 'text-right') {
           
            $this->SetX($this->w - $this->GetStringWidth($this->GetPageWidth()) - 45); 
        } else {
           
            $this->SetX(10);
        }
    }
	
}

function CloseTag($tag)
{
   
    if($tag=='B' || $tag=='I' || $tag=='U')
        $this->SetStyle($tag,false);
    if($tag=='A')
        $this->HREF = '';
    if($tag == 'H1' || $tag == 'H2' || $tag == 'H3') {
        $this->Ln(5); 
    }
    
	if ($tag == 'H1') {
       
        $this->Ln(10); 
        $this->SetX($this->GetX() - 10);
        $this->SetFont('Arial', '', 14);
    }
}

function SetStyle($tag, $enable)
{
	$this->$tag += ($enable ? 1 : -1);
	$style = '';
	foreach(array('B', 'I', 'U') as $s)
	{
		if($this->$s>0)
			$style .= $s;
	}
	$this->SetFont('',$style);
}

function PutLink($URL, $txt)
{
	$this->SetTextColor(0,0,255);
	$this->SetStyle('U',true);
	$this->Write(5,$txt,$URL);
	$this->SetStyle('U',false);
	$this->SetTextColor(0);
}
}



$html = "<html lang='en'>
<body>
<div class='container license-container'>
    <div class='section'>
        <p> This is the trial Version of the License Document</p>
    </div>
</div> 
</body>
</html>";

$pdf = new PDF();
$pdf->AddPage();
$pageWidth = $pdf->GetPageWidth(); 
$imageWidth = 30;
$centerX = ($pageWidth - $imageWidth) / 2;
$pdf->Image('ferwafa.jpg', $centerX, 25, $imageWidth, 0, '', 'http://www.google.com');
$pdf->SetLeftMargin(45);
$pdf->SetFontSize(14);
$pdf->SetFont('Arial', 'B', 24);
$pdf->Write(5,"LICENSE");
$pdf->SetFont('','U');
$pdf->ln(28);
$pdf->SetFont('Arial','',12);
$pdf->SetY(40);  
$pdf->SetX(10);  
$pdf->MultiCell(90, 10, "Trail\nWorldWideWeb\nPO BOX 10", 0, 'L');   
$pdf->SetY(40);  
$pdf->SetX(110);  
$pdf->MultiCell(90, 10, "License Category:". "\n Granted on: " . date('l, F j, Y') . "\nValid For: One year from date of Issue\nAuthorizing Party:CHECK THIS OUT!", 0, 'R');  
$pdf->Image($tempFilePath, 170, 115, $imageWidth, 0, '', 'http://www.google.com');   

$pdf->WriteHTML($html);  
$pdf->Output();
?>
