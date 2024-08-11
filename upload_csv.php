<head>
<style>
.search_panel {
    width:20%;
    border-right:1px solid grey;
    height:100%;
    overflow-y:scroll;
    text-align:left;
    /* padding-left:20%; */
    /* padding-right:20%; */
    float:left;
    background-color:#f5f7f6;
}
body {
    margin:0;
    padding:0;
    font-family:verdana;
}
.container {
    width:79%;
    float:left;
   
    height:95%;
    overflow:scroll;
}
.images img {
    width:100px;
    height:100px;
    float:left;
    margin:5px;
    border:1px solid black;
}
label, input, textarea,select { float:left; margin-top:8px; width:95%; }
textarea { height:150px; white-space:pre-wrap; }
td {padding:5px;border:1px solid grey;}
.status { width:79%; float:left;  text-align:left; border:0px solid grey;background-color: yellowgreen;
    padding: 6px;}
td.images {width:650px;}
table {border-spacing:0;margin:10px;}
.btn {background-color:#4caf50; width:70px; border-radius:4px;padding:5px;}
</style>

<style type="text/css">
@font-face {
  font-weight: 400;
  font-style:  normal;
  font-family: 'Circular-Loom';

  src: url('https://cdn.loom.com/assets/fonts/circular/CircularXXWeb-Book-cd7d2bcec649b1243839a15d5eb8f0a3.woff2') format('woff2');
}

@font-face {
  font-weight: 500;
  font-style:  normal;
  font-family: 'Circular-Loom';

  src: url('https://cdn.loom.com/assets/fonts/circular/CircularXXWeb-Medium-d74eac43c78bd5852478998ce63dceb3.woff2') format('woff2');
}

@font-face {
  font-weight: 700;
  font-style:  normal;
  font-family: 'Circular-Loom';

  src: url('https://cdn.loom.com/assets/fonts/circular/CircularXXWeb-Bold-83b8ceaf77f49c7cffa44107561909e4.woff2') format('woff2');
}

@font-face {
  font-weight: 900;
  font-style:  normal;
  font-family: 'Circular-Loom';

  src: url('https://cdn.loom.com/assets/fonts/circular/CircularXXWeb-Black-bf067ecb8aa777ceb6df7d72226febca.woff2') format('woff2');
}
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

</head>

<body data-new-gr-c-s-check-loaded="14.1063.0" data-gr-ext-installed="">

    <div class="search_panel">    

		<a href="index.php" > 
			Search Category </a> </br></br>			 
		
			<a href="upload_csv.php" >
				Upload CSV </a>

		

	</div>

<!--Form-->
    <div class="table-responsive" align="center">
		<h4 align="center" style="color:maroon;"> Upload Bigcommerce CSV</h4>
    	
          <table align="center" style="width:400px; height:200px;" class="table table-bordered" >
            <tr>
              <td><div align="center"><strong>Upload CSV File</strong></div></td>
            </tr>
            <tr>
              <td><input type="file" name="uploadfile" id="uploadfile"> .csv file</td>
            </tr>
            <tr>
              <td><button type="submit" name="submit_csv" id="upload" > Upload </button></td>
            </tr>
          </table>
        
    </div>
    
   <div id="loading" align="center" style="display:none;"> <i> Processing... </i> </div>
   <div id="op_data" align="center">  </div>

<script>

	function auto_downloadCSV(fileName) {
		window.location.href = 'download_csv.php?fname='+fileName;
	};
	
	$('#upload').on('click', function() {
			
		$('#loading').attr("style", "display:block");
		
		var file_data = $("#uploadfile").prop("files")[0]; 
		var form_data = new FormData();                 
			form_data.append('file', file_data);
			//form_data.append('oid', oid);   
								
		$.ajax({
			url: 'ajax_csv_upload.php', // point to server-side PHP script 
			dataType: 'text',  // what to expect back from the PHP script, if anything
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,                         
			type: 'post',
			success: function(response){
				// alert(response); // display response from the PHP script, if any
				$('#loading').attr("style", "display:none");
				$('#op_data').html(response);

				// setTimeout(function() {
				// 	//var csvLink = $('#download_csv_link').attr('href');   
				// 	auto_downloadCSV(response);                    
				// }, 100);
			}
		});
	});

</script>

</body>