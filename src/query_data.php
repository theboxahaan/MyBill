<?php
  $Data = stripcslashes($_POST['qdata']);

  // Decode the JSON array
  $Data = json_decode($Data,TRUE);

  $search_name = $Data[0];
  $qty = (int)$Data[1];

  $search_name = trim($search_name);

  if(!get_magic_quotes_gpc())
  {
    $search_name = addslashes($search_name);
  }

$db = new SQLite3('Bill.db');
$query = "select PRICE from menu where ITEM_NAME like '".$search_name."' ;";
$result = $db->query($query);
$row = $result->fetchArray();
//$results = $db->query('SELECT PRICE FROM MENU where ITEM_NAME like');
  $price = (int)$row['PRICE']*$qty;
  $arr = array($row['PRICE'],$price);
  $db->close();
  echo json_encode($arr);


 ?>
