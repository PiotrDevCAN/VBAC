<?php

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
    $ssl_dsn  = $_ENV["ssldsn"]; // pick up from a secret

    # Build the connection string
    #
    $driver = "DRIVER={IBM DB2 ODBC DRIVER};";
    //    $conn_string = $driver . $dsn;     # Non-SSL
    $conn_string = $driver . $ssl_dsn; # SSL

    $conn = db2_connect( $conn_string, "", "" );
    if( $conn )
    {
        $_SESSION['conn'] = $conn;
        $schema = isset($GLOBALS['Db2Schema']) ? $GLOBALS['Db2Schema'] : 'REST';
        $Statement = "SET CURRENT SCHEMA='$schema';";
        $rs = db2_exec($conn, $Statement);

        if (! $rs) {
            echo "<br/>" . $Statement . "<br/>";

            echo "<pre>";
            print_r($_SESSION);
            echo "</pre>";


            echo "<BR>" . db2_stmt_errormsg() . "<BR>";
            echo "<BR>" . db2_stmt_error() . "<BR>";
            exit("Set current schema failed");
        }
        db2_autocommit($conn, TRUE); // This is how it was on the Wintel Box - so the code has no/few commit points.
    }
    else
    {
        error_log(__FILE__ . __LINE__ . " Connect to DB2 Failed");
        error_log(__FILE__ . __LINE__ . $conn_string);
        error_log(__FILE__ . __LINE__ . db2_conn_errormsg());
        error_log(__FILE__ . __LINE__ . db2_conn_error());

        echo "<p>Connection failed.</p>";
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
