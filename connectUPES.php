<?php

if(!function_exists('tryConnectToUPES')){
    function tryConnectToUPES($conn_string){
        error_log("Attempting Pconnect to DB2 from Pod:" . $_ENV['HOSTNAME'] . ":" . $conn_string);
        $preConnect = microtime(true);
        $connection =  db2_pconnect( $conn_string, "", "" );
        $postConnect = microtime(true);
        error_log("Db2 Pconnect took:" . (float)($postConnect-$preConnect));
        return $connection;
    }
}

if( isset($_ENV['ssldsn']) )
{
    # Get database details from the VCAP_SERVICES environment variable
    #
    # *This can only work if you have used the Bluemix dashboard to
    # create a connection from your dashDB service to your PHP App.
    #
    //     $details  = json_decode( getenv( "VCAP_SERVICES" ), true );
    //     $dsn      = $details [ "user-provided" ][0][ "credentials" ][ "dsn" ];
    //     $ssl_dsn  = $details [ "user-provided" ][0][ "credentials" ][ "ssldsn" ];
    //     $ssl_dsn  = $_ENV["ssldsn"]; // pick up from a secret
    $ssl_dsn  = 'DATABASE=BLUDB;HOSTNAME=541923aa-a2a2-40a4-9e67-94eb6e88d5f5.bs2io90l08kqb1od8lcg.databases.appdomain.cloud;PORT=30725;PROTOCOL=TCPIP;UID=iae2chzk;PWD=voJchMHqPNqo3mVk;Security=SSL;';

    # Build the connection string
    #
    $driver = "DRIVER={IBM DB2 ODBC DRIVER};";
    //    $conn_string = $driver . $dsn;     # Non-SSL
    $conn_string = $driver . $ssl_dsn; # SSL

    $conn=false;
    $attempts = 0;

    while(!$conn && ++$attempts < 3){
        // since Cirrus - we have the occasional problem connecting, so sleep and try again a couple of times 
        $conn = tryConnectToUPES($conn_string);
        if(!$conn){
            error_log("Failed attempt $attempts to connect to DB2");
            error_log("Msg:" . db2_conn_errormsg());
            error_log("Err:" . db2_conn_error());
            sleep(3);
        }
    }
    
    if( $conn )
    {
        $GLOBALS['connUPES'] = $conn;
        // $schema = isset($GLOBALS['Db2Schema']) ? $GLOBALS['Db2Schema'] : 'REST';
        // $schema = isset($GLOBALS['Db2Schema']) ? $GLOBALS['Db2Schema'] : 'UPES_DEV';
        $schemaUPES = 'UPES_DEV';
        $StatementUPES = "SET CURRENT SCHEMA='$schemaUPES';";

        $GLOBALS['Db2SchemaUPES'] = $schemaUPES;

        $rs = sqlsrv_query($conn, $StatementUPES);

        if (! $rs) {
            echo "<br/>" . $StatementUPES    . "<br/>";

            echo "<pre>";
            print_r($_SESSION);
            echo "</pre>";

            echo "<BR>" . db2_stmt_errormsg() . "<BR>";
            echo "<BR>" . db2_stmt_error() . "<BR>";
            exit("Set current schema failed");
        }
        sqlsrv_commit($conn, TRUE); // This is how it was on the Wintel Box - so the code has no/few commit points.
    }
    else
    {
        error_log(__FILE__ . __LINE__ . " Connect to DB2 Failed");
        error_log(__FILE__ . __LINE__ . $conn_string);
        error_log(__FILE__ . __LINE__ . db2_conn_errormsg());
        error_log(__FILE__ . __LINE__ . db2_conn_error());
        throw new Exception('Failed to connect to DB2');
    }
}
else
{
    echo "<pre>";
    print_r($_ENV);
    echo "</pre>";
    echo "<p>No credentials.</p>";
}

?>
