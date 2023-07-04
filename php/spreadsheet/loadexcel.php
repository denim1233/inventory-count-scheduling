<?php

		// require('php-excel-reader/excel_reader2.php');
 

		require('SpreadsheetReader.php');

		$Reader = new SpreadsheetReader('1k.xlsx');
		$Sheets = $Reader -> Sheets();
		$paramarray = array();


		foreach ($Sheets as $Index => $Name)
		{
			// echo 'Sheet #'.$Index.': '.$Name;
			// echo "<br>";

			$Reader -> ChangeSheet($Index);

			foreach ($Reader as $Row)
			{
				if($Row[0] != ''){

					// var_dump($Row);
					array_push($paramarray,$Row);
					// echo "<br>";
					// return;
				}
			}
		}

		$webname = 'apitinfo.ml';
        $dsn = 'mysql:host=localhost;dbname=producttable;charset=utf8';
        $username = 'root';
        $password = '';

        $db = new PDO($dsn,$username,$password);
        $db->beginTransaction();

        $insert_values = array();
        //create parameter question mark same with the excel values
        foreach($paramarray as $d){
        	print_r($insert_values);
		    $question_marks[] = '('  . placeholders('?', sizeof($d)) . ')';
		    $insert_values = array_merge($insert_values, array_values($d));
		     // $insert_values = array_push($insert_values, array_values($d));
		}

		$stmt = $db->prepare("
		INSERT INTO inv_product (barcode, description, inputdate, groups, systemonhand,sellout,receipts,invadj,sellingarea,whsearea,variance,remarks,remarks2)
		VALUES ".implode(',', $question_marks)."
		");

        if ($stmt->execute($insert_values))
        { 
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $JSON = json_encode($result);
            echo $JSON;
        } 
        else 
        {
        print_r($stmt->errorInfo());
        }

        $db->commit();

        function placeholders($text, $count=0, $separator=","){
		    $result = array();
		    if($count > 0){
		        for($x=0; $x<$count; $x++){
		            $result[] = $text;
		        }
		    }

		    return implode($separator, $result);
		}
?>