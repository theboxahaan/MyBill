<html>
<head>
  <meta charset="utf-8">
  <script src="ajax-jquery.min.js"></script>
  <script src="jquery-1.12.4.js"></script>
  <link rel="stylesheet" href="jquery-ui.css">
  <script src="jquery-ui.js"></script>
  <script src="jquery.json.js"></script>
  <link rel="stylesheet" href="bill.css">


<title>Shawarma Wala Billing</title>

</head>

<body>

	
	
  <div class="wrapper">
  
    <button onclick="accept_row()" class="btn" id="save">SAVE</button>
    <button onclick="send_table()" class="btn" id="send">PRINT</button>
   <!-- <button onclick="window.location.reload()" class="btn" id="clear">CLEAR</button>-->
  </div>

<h1>Shawarma Wala Billing</h1>

<?php 
session_start();
if(!isset($_SESSION['sumtotal']))
	$_SESSION['sumtotal'] = 0 ;

echo "<h1> Rs.".$_SESSION['sumtotal']. "</h1>";
?>

<section>
  <form method = "post" action = "" id = "order_table">
    <table id = "orders" cellpadding="0" cellspacing="0" border="0">
      <thead class="tbl-header">
        <tr>
          <th>ITEM</th>
          <th>QTY</th>
          <th>RATE</th>
          <th>PRICE</th>
          <th></th>
        </tr>
      </thead>
      <tbody class="tbl-content">
        <tr>
          <td>
            <input name = "name" id = "name" type="text" class="vertical">
          </td>
          <td>
            <input name= "qty" id = "qty" type="text" class="vertical">
          </td>
          <td></td>
          <td></td>
          <td class="last-child"></td>
        </tr>
        <tr>
          <td>Total:</td>
          <td id = "total" ></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
      </tbody>
    </table>
  </form>
</section>

<?php



$arr = array();
$db = new SQLite3('Bill.db');
$results = $db->query('SELECT ITEM_NAME FROM MENU');
while($row  = $results->fetchArray())
{
  $arr[] = $row['ITEM_NAME'];
  //var_dump($row);
}
$db->close();

$info = json_encode($arr);


 ?>

  <script type="text/javascript">
  var obj = JSON.parse('<?= $info; ?>');
  console.log(obj);
  $(function(){

    $( "#name" ).autocomplete({
      source:obj// availableTags
    });
  });

  function accept_row()
  {
      var x = document.getElementById("orders").rows.length;
      x--;
      var table = document.getElementById("orders");
      var row = table.insertRow(x);
      var cell0 = row.insertCell(0);
      var cell1 = row.insertCell(1);
      var cell2 = row.insertCell(2);
      var cell3 = row.insertCell(3);
      var cell4 = row.insertCell(4);
      var cell0val = document.getElementById('name').value;
      var cell1val = document.getElementById('qty').value;
      cell0.innerHTML = String(cell0val);
      cell1.innerHTML = parseInt(cell1val);
      cell1.style.textAlign = 'center';
      cell2.innerHTML = "0";
      cell2.style.textAlign = 'center';
      cell3.innerHTML = "0";
      cell3.style.textAlign = 'center';

      cell4.innerHTML = '<img src="Flat_cross_icon.svg.png" onclick="deleteRow(this)" style="width:60%;">';

      var query_data = new Array();
      query_data[0] = String(cell0val);
      query_data[1] = String(cell1val);

      query_data = $.toJSON(query_data);
      console.log(query_data);

      $.ajax({
      type: "POST",
      url: "query_data.php",
      data: "qdata=" + query_data,
      async: false,
      dataType: "json",
      success: function(ret_data)
      {
        console.log(ret_data);
        //$("#orders td:contains('0')").html(ret_data[0]);
        cell2.innerHTML = String(ret_data[0]);
        cell3.innerHTML = String(ret_data[1]);
      }
      });

      console.log("DBF");
      var sum = 0;

      while(x>1)
      {
        sum+=parseInt(document.getElementById("orders").rows[x].cells[3].innerHTML);

        x--;
      }
      document.getElementById('total').innerHTML = sum;

      document.getElementById("name").value = "";
      document.getElementById("qty").value = "";

  }

  function deleteRow(r) {
    var i = r.parentNode.parentNode.rowIndex;
      var j = parseInt(document.getElementById("orders").rows[i].cells[3].innerHTML);
    document.getElementById("orders").deleteRow(i);
       document.getElementById('total').innerHTML -= j;
  }

  function send_table()
  {
    var TableData;
    TableData = storeTblValues()
    TableData = $.toJSON(TableData);
    console.log(TableData);

    function storeTblValues()
    {
        var TableData = new Array();

        $('#orders tr').each(function(row, tr){
            TableData[row]={
                "name" : $(tr).find('td:eq(0)').text()
                , "qty" :$(tr).find('td:eq(1)').text()
                , "price" : $(tr).find('td:eq(3)').text()

            }
        });
        TableData.shift();  // first row will be empty - so remove
        TableData.shift();
		window.location.reload();
        return TableData;
    }

    $.ajax({
    type: "POST",
    url: "process.php",
    data: "pTableData=" + TableData,
    success: function(msg){
        // return value stored in msg variable
    }
    });

  }

      $(".vertical").keypress(function(event){
          if(event.keyCode == 13){
              textboxes = $("input.vertical");
              debugger;
              currentBoxNumber = textboxes.index(this);
              if(textboxes[currentBoxNumber+1]!=null){
                  nextBox = textboxes[currentBoxNumber+1]
                  nextBox.focus();
                  nextBox.select();
                  event.preventDefault();
                  return false
              }
              if(textboxes[currentBoxNumber+1] == null){
                  accept_row();
                  prevBox = textboxes[currentBoxNumber-1];
                  prevBox.focus();
                  prevBox.select();
                  event.preventDefault();
              }
          }
      });

  </script>


</body>
</html>
