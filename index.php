<!DOCTYPE html>
<html>
<head>
	<title>PHP MySQL Connection App</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	
	<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
	
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	
	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
	
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

	
	<?php
		//get the Bluemix environment credentials to connect to MySQL ClearDB service
		$vcap_services = json_decode($_ENV["VCAP_SERVICES" ]);
		$db = $vcap_services->{'cleardb'}[0]->credentials;
	
		$dbname = $db->name;
	    $server_name =$db->hostname . ':' . $db->port;
	    $username = $db->username; 
	    $password = $db->password;
	
		//create new connection to MySQL
		$mysqli = new mysqli($server_name, $username, $password, $dbname);
		if ($mysqli->connect_errno) {
		    echo "Problems connecting to the MySQL Service (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		    die();
		}

		
		//Case POST request, data will be inserted
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
		    $insert_statement = "INSERT INTO test1 VALUES (".$_POST["cod"].",'".$_POST["username"]."');"; //query to insert new message
		    if ($mysqli->query($insert_statement)) {
		        //"Insert success!";
		    } else {
		        echo "Problems inserting into the table ";
		    }
		}
		
		//Case GET request with namedel variable provided to delete a specific record
		if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["namedel"])) {
		    $del_statement = "DELETE FROM test1 WHERE name = '".$_GET["namedel"]."';"; //query to delete a record
		    if ($mysqli->query($del_statement)) {
		        //echo "Delete success!";
		    } else {
		        echo "Problems deleting from table ";
		    }
		}
		
		//check is table test1 already exists, otherwise creates it
		$check_table_query = "SHOW TABLES LIKE 'test1'";
		if ($table_check = $mysqli->query($check_table_query)) {
		   if($table_check->num_rows == 0){
			   $create_table = "create table test1 (cod integer,name varchar(50))";
			   if ($table_result = $mysqli->query($create_table)) {
				   // create table statement ran ok
			   } else {
			        echo "Problems creating the table";
			   }
		   }

		}

		//Query the DB for messages
		$select_query = "select * from test1";
		if ($result = $mysqli->query($select_query)) {
		   // select statement ran ok
		} else {
	        //Problems viewing the table
	    }
	?>
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-md-2"></div>
			<div class="col-md-8">
				<div class="table-responsive">
			  		<table class="table table-striped">
						<thead>
							<tr>
								<th>
									Code
								</th>
								<th>
									Name
								</th>
							</tr>
						</thead>
						<tbody>
						<?php
							if ($result->num_rows > 0){ //Check if the table is empty to show Empty warning in the table
			                	$name_link = "";
					            while ( $row = mysqli_fetch_row ( $result ) ) { //scan through the select results to generate data table
					            	echo "<tr>";
					                for($i = 0; $i < mysqli_num_fields ( $result ); $i ++) { //sacan through each table row
					                    echo "<td>$row[$i]</td>";
					                    if($i == 1){
					                    	$name_link = $row[$i];
					                    }
					                }
					                	echo "<td align='center' width='20px'><a href='index.php?namedel=$name_link'>delete</a></td>";
					                echo "</tr>";
					            }
				            }
				            else{
				            	echo "<tr><td colspan='3' align='center'> Table is empty insert something</td></tr>";
				            }

				            $result->close(); //close cursor
				            mysqli_close($mysqli); //close database connection
				        ?>
						</tbody>
			  		</table>
				</div>
			</div>
			<div class="col-md-2"></div>
		</div>
	
	 
    <hr>    
    <form method="POST" action="index.php">
	  <div class="form-group">
	    <label for="Code">Code</label>
	    <input type="text" name="cod" class="form-control" id="Code">
	  </div>
	  <div class="form-group">
	    <label for="Name">Name</label>
	    <input type="text" name="username" class="form-control" id="Name" placeholder="Your name">
	  </div>
	  <button type="submit" class="btn btn-default">Submit</button>
	</form>
    </div>
</body>
</html>
