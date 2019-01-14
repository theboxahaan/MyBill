<?php
session_start();
$session = & $_SESSION['sumtotal'];

function intLowHigh($input, $length){

    // Function to encode a number as two bytes. This is straight out of Mike42\Escpos\Printer
    $outp = "";
    for ($i = 0; $i < $length; $i++) {
        $outp .= chr($input % 256);
        $input = (int)($input / 256);
    }
    return $outp;
}


function check_file($file_name , $def_val){

	if(!file_exists($file_name))
	{
		$file = fopen($file_name , "w");
		fwrite($file , $def_val);
		fclose($file);
	}
}


function incre_bill_no(){
	$file = fopen("bill_no.txt" , "r");
	$bill_no = fgets($file);
	$GLOBALS['BILL_NO']=$bill_no;
	$bill_no = (int)$bill_no;
	$bill_no++;
	fclose($file);
	$file = fopen("bill_no.txt" , "w");
	fwrite($file , (string)$bill_no);
	fclose($file);
}



$BILL_NO="1";	//global variable #1

check_file("bill_no.txt" , "1");

incre_bill_no();



  require __DIR__ . '/autoload.php';
  use Mike42\Escpos\Printer;
  use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
  try{
    

  // Unescape the string values in the JSON array
  $tableData = stripcslashes($_POST['pTableData']);

  // Decode the JSON array
  $tableData = json_decode($tableData,TRUE);

  // now $tableData can be accessed like a PHP associative array
  $session += (int)$tableData[count($tableData)-1]['qty'];
  chdir('records');
  $file = fopen(date("d-m-Y").".txt" , 'a+');
  fwrite($file ,"\n============================================\r\n"."Order No: ".$GLOBALS['BILL_NO']."\r\n");
  fwrite($file , "\n".date("d-m-Y")."\r\n");
 
  foreach($tableData as $x)
	  fwrite($file , str_pad($x['name'],30)."\t".str_pad($x['qty'] , 4)."\t".$x['price']."\r\n");
  fwrite($file ,"============================================\r\n");
  fwrite($file , "Cumulative Total: Rs".$session."\r\n");
  fclose($file);
  chdir('.');
 

    $connector = new WindowsPrintConnector("Morty");
    $printer = new Printer($connector);

    $printer -> setJustification(Printer::JUSTIFY_CENTER);
    $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
    $printer -> text("SHAWARMA WALA\n");
    $printer -> selectPrintMode();
    $printer -> setFont(Printer::FONT_B);
    $printer -> text("Sant Kawar Ram Chowk, Katora Talab\n0771-4000808\n\n");
    $printer -> setJustification(Printer::JUSTIFY_LEFT);
    $printer -> text("Date: ".date("d-m-Y"));

    $printer -> setJustification(Printer::JUSTIFY_RIGHT);
    $printer -> text(" , ".date("h:i:sa")."\n");
    $printer -> setJustification();
    $printer -> text("Order No.# : ".$GLOBALS['BILL_NO']."\n");

    $printer -> setFont(Printer::FONT_A);

  $printer -> setFont(Printer::FONT_A);
  $printer -> text("--------------------------------\n");

  $printer -> setFont(Printer::FONT_B);
  
    $printer -> text("S.N        NAME             QTY   PRICE\n");
    $printer -> setFont(Printer::FONT_A);
  $printer -> text("--------------------------------\n");

  $printer -> setFont(Printer::FONT_B);

    for($i=0; $i < count($tableData)-1 ; $i++){
  
  $stream = "";
  if($i<9)$stream .=" ";

  $stream .= (string)$i+1;
  $stream .=".  ";
  $len = strlen($tableData[$i]['name']);
  $temp_name = $tableData[$i]['name'];
  if($len > 20)
    {
      $last_space = 19;
      for($j = 19 ; $temp_name[$j] != ' ' ; $j--)$last_space = $j;
      $first = substr( $temp_name , 0 , $last_space-1);
      $rest = substr($temp_name , $last_space);
      $stream .= $first;
      for($j = strlen($first) ; $j < 20 ; $j++ )$stream .= " ";
      $stream.="   ";
      $qty = (int)($tableData[$i]['qty']);
        if($qty<10)$stream.="  ";
        elseif($qty>9 && $qty<100)$stream.=" ";
        $stream .= $tableData[$i]['qty'];
        $stream .= "   ";
        $stream .= $tableData[$i]['price'];
            $stream .="/-";
      $printer -> text($stream);
      $printer -> text("\n");
      $stream = "";
      $stream .="  ";
      $stream .="   ";
      $stream  .= $rest;
      $printer -> text($stream);
      $printer -> text("\n");

    }
    else {
  $stream .=$tableData[$i]['name'];
  $len = 20 - $len;
    for($j=0;$j<$len;$j++)$stream.=" ";
  $stream.="   ";
  $qty = (int)($tableData[$i]['qty']);
    if($qty<10)$stream.="  ";
    elseif($qty>9 && $qty<100)$stream.=" ";
    $stream .= $tableData[$i]['qty'];
    $stream .= "   ";
    $stream .= $tableData[$i]['price'];
        $stream .="/-";
  $printer -> text($stream);
  $printer -> text("\n");
}

}
    $printer -> setFont(Printer::FONT_A);
    $printer -> text("--------------------------------\n");
    $printer -> text("         TOTAL:     ");
    $printer -> text($tableData[count($tableData)-1]['qty']."/-");
    $printer -> text("\n");
    $printer -> text("--------------------------------\n");

    $printer -> text("--------------------------------\n");
    $printer -> feed(2);
  $printer -> close();

}

    catch(Exception $e)
    {
        echo "Could not print to prnter: ".$e->getMessage()."\n";
    }





?>
