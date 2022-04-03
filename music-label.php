<html>
    <head>
        <title>Music Label Database</title>
    </head>

    <body>
        <h1>Music Label Database</h1>
        <p>This database consists of two tables, artists and albums</p>
        <form method="GET" action="music-label.php"> <!--refresh page when submitted-->
            <input type="submit" value="View Artists" name="viewArtistsRequest"></p>
            <input type="submit" value="View Albums" name="viewAlbumsRequest"></p>
        </form>
        <hr />

        <h2>Selection</h2>
        <form method="GET" action="music-label.php"> <!--refresh page when submitted-->
            <input type="hidden" id="selectQueryRequest" name="selectQueryRequest">
            Table Name: <input type="text" name="selTableName"> <br /><br />
            Column to Display: <input type="text" name="selCol1"> <br /><br />
            Where Column: <input type="text" name="selCol2"> >= 0 <br /><br />

            <input type="submit" value="Fetch" name="selectQueryRequest"></p>
        </form>

        <hr />

        <h2>Projection</h2>
        <form method="GET" action="music-label.php"> <!--refresh page when submitted-->
            <input type="hidden" id="selectQueryRequest" name="selectQueryRequest">
            Table Name: <input type="text" name="selTableName"> <br /><br />
            Column to Display: <input type="text" name="selCol1"> <br /><br />

            <input type="submit" value="Fetch" name="selectQueryRequest"></p>
        </form>

        <hr />

        <h2>Join</h2>
        <p>This query joins the artists and albums tables, finding the names of all artists who has more than x sales on any album (x being user specified)</p>
        <form method="GET" action="music-label.php"> <!--refresh page when submitted-->
            <input type="hidden" id="joinQueryRequest" name="joinQueryRequest">
            Minimum Sales: <input type="text" name="minSales"> <br /><br />

            <input type="submit" value="Fetch" name="joinQueryRequest"></p>
        </form>

        <hr />

        
        <h2>Delete Artists</h2>
        <form method="POST" action="music-label.php"> <!--refresh page when submitted-->
            <input type="hidden" id="deleteQueryRequest" name="deleteQueryRequest">
            ArtistID: <input type="text" name="deleteNo"> <br /><br />

            <input type="submit" value="Delete" name="deleteSubmit"></p>
        </form>

        <hr /> 

        
        <h2>Nested Aggregation with Group-by</h2>
        <p>This query outputs the average number of sales made per artist</p>
        <form method="GET" action="music-label.php"> <!--refresh page when submitted-->
            <input type="hidden" id="nestedAggregationQueryRequest" name="nestedAggregationQueryRequest">

            <input type="submit" value="Fetch" name="nestedAggregationQueryRequest"></p>
        </form>

        <hr /> 

        <h2>Count Tuples</h2>
        <form method="GET" action="music-label.php"> <!--refresh page when submitted-->
            <input type="hidden" id="countTupleRequest" name="countTupleRequest">
            <input type="submit" value="Count Artists" name="countArtistTuples"></p>
        </form>
        <form method="GET" action="music-label.php"> <!--refresh page when submitted-->
            <input type="hidden" id="countTupleRequest" name="countTupleRequest">
            <input type="submit" value="Count Albums" name="countAlbumTuples"></p>
        </form>

        <hr />
        <h2>Results</h2>

        <?php
        $success = True; 
        $db_conn = NULL; 
        $show_debug_alert_messages = False; 

        function debugAlertMessage($message) {
            global $show_debug_alert_messages;

            if ($show_debug_alert_messages) {
                echo "<script type='text/javascript'>alert('" . $message . "');</script>";
            }
        }
        function executePlainSQL($cmdstr) {
            global $db_conn, $success;

            $statement = OCIParse($db_conn, $cmdstr);

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
                echo htmlentities($e['message']);
                $success = False;
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = oci_error($statement); 
                echo htmlentities($e['message']);
                $success = False;
            } 

			return $statement;
		}

        function printResult($statement, $tableName, $displayCol ) {
            //echo "<br>Retrieved data from table {$tableName}:<br>";
            echo "<table>";

            echo "<tr><th>{$displayCol}</th></tr>";
            while ($row = OCI_Fetch_Array($statement, OCI_BOTH)) {
                echo "<tr><td>{$row[0]}</td></tr>";
            }

            echo "</table>";
        }

        function printTable($statement, $tableName, $displayCols ) {
            echo "<br>Retrieved data from table {$tableName}:<br>";
            echo "<table>";
            echo "<tr>";
            for ($x = 0; $x < count($displayCols); $x++) {
                echo "<th>{$displayCols[$x]}</th>";
            }
            echo "</tr>";
            while ($row = OCI_Fetch_Array($statement, OCI_BOTH)) {
                echo "<tr>";
                for ($i = 0; $i < count($displayCols); $i++) {
                    echo "<td>{$row[$i]}</td>";
                }
                echo "</tr>";
            }

            echo "</table>";
        }

        function connectToDB() {
            global $db_conn;

            $db_conn = OCILogon("ora_carrollq", "a63615926", "dbhost.students.cs.ubc.ca:1522/stu");

            if ($db_conn) {
                debugAlertMessage("Database is Connected");
                return true;
            } else {
                debugAlertMessage("Cannot connect to Database");
                $e = OCI_Error(); // For OCILogon errors pass no handle
                echo htmlentities($e['message']);
                return false;
            }
        }

        function disconnectFromDB() {
            global $db_conn;

            debugAlertMessage("Disconnect from Database");
            OCILogoff($db_conn);
        }

        function handleSelectRequest() {
            global $db_conn;

            $tableName=$_GET['selTableName'];
            $displayCol=$_GET['selCol1'];
            $compCol=$_GET['selCol2'];
            if ($compCol) {
                $statement = executePlainSQL("select {$displayCol} from {$tableName} where {$compCol} >= 0");
            } else {
                $statement = executePlainSQL("select {$displayCol} from {$tableName}");
            }
            
            OCIExecute($statement, OCI_DEFAULT);
            printResult($statement, $tableName, $displayCol);
            OCICommit($db_conn);
        }

        function handleJoinRequest() {
            global $db_conn;

            $minSales=$_GET['minSales'];

            $statement = executePlainSQL("select distinct name from artists, albums where artists.artistid = albums.artistid and sales >= {$minSales}");

            
            OCIExecute($statement, OCI_DEFAULT);
            printResult($statement, "(artists x albums)", "name");
            OCICommit($db_conn);
        }

        function handleNestedAggregationQueryRequest() {
            global $db_conn;

            $statement = executePlainSQL("select name, AVG(sales) from artists, albums where artists.artistID = albums.artistid group by name");

            OCIExecute($statement, OCI_DEFAULT);
            printTable($statement, "(artists x albums)", ["name", "sales"]);
            OCICommit($db_conn);
        }

        function handleDeleteRequest() {
            global $db_conn;

            $artistID=$_POST['deleteNo'];
            executePlainSQL("delete from ARTISTS where artistID={$artistID}");
            OCICommit($db_conn);
        }

        function handleCountRequest( $table ) {
            global $db_conn;

            $result = executePlainSQL("SELECT Count(*) FROM {$table}");

            if (($row = oci_fetch_row($result)) != false) {
                echo "<br> The number of tuples in {$table}: " . $row[0] . "<br>";
            }
        }

        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('resetTablesRequest', $_POST)) {
                    handleResetRequest();
                } else if (array_key_exists('updateQueryRequest', $_POST)) {
                    handleUpdateRequest();
                } else if (array_key_exists('insertQueryRequest', $_POST)) {
                    handleInsertRequest();
                } else if (array_key_exists('deleteQueryRequest', $_POST)) {
                    handleDeleteRequest();
                }

                disconnectFromDB();
            }
        }

        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('countArtistTuples', $_GET)) {
                    handleCountRequest("artists");
                } elseif (array_key_exists('countAlbumTuples', $_GET)) {
                    handleCountRequest("albums");
                } elseif (array_key_exists('selectQueryRequest', $_GET)) {
                    handleSelectRequest();
                } elseif (array_key_exists('joinQueryRequest', $_GET)) {
                    handleJoinRequest();
                } elseif (array_key_exists('nestedAggregationQueryRequest', $_GET)) {
                    handleNestedAggregationQueryRequest();
                }

                disconnectFromDB();
            }
        }

		if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit']) || isset($_POST['deleteSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_GET['countTupleRequest']) || isset($_GET['selectQueryRequest']) || isset($_GET['joinQueryRequest']) || isset($_GET['nestedAggregationQueryRequest'])) {
            handleGETRequest();
        }
		?>
	</body>
</html>

