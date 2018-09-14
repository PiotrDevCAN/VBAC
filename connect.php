<?php

if( getenv( "VCAP_SERVICES" ) )
{
    # Get database details from the VCAP_SERVICES environment variable
    #
    # *This can only work if you have used the Bluemix dashboard to
    # create a connection from your dashDB service to your PHP App.
    #
    $details  = json_decode( getenv( "VCAP_SERVICES" ), true );
    $dsn      = $details [ "dashDB For Transactions" ][0][ "credentials" ][ "dsn" ];
    $ssl_dsn  = $details [ "dashDB For Transactions" ][0][ "credentials" ][ "ssldsn" ];

    # Build the connection string
    #
    $driver = "DRIVER={IBM DB2 ODBC DRIVER};";
    $conn_string = $driver . $dsn;     # Non-SSL
    $conn_string = $driver . $ssl_dsn; # SSL

    $conn = db2_connect( $conn_string, "", "" );

//   $_SESSION['ssoEmail'] = 'dummyUser';
    if( $conn )
    {
        $_SESSION['conn'] = $conn;
//         $schema = isset($_SESSION['Db2Schema']) ? $_SESSION['Db2Schema'] : 'VBAC';
//         $schema = 'VBAC';
//         $Statement = "SET CURRENT SCHEMA='$schema';";
//         $rs = db2_exec($conn, $Statement);

//         if (! $rs) {
//             echo "<br/>" . $Statement . "<br/>";
//             echo "<BR>" . db2_stmt_errormsg() . "<BR>";
//             echo "<BR>" . db2_stmt_error() . "<BR>";
//             exit("Set current schema failed");
//         }
        db2_autocommit($conn, TRUE); // This is how it was on the Wintel Box - so the code has no/few commit points.
    }
    else
    {
        echo "<p>Connection failed.</p>";
        echo db2_conn_error();
        echo db2_conn_errormsg();
    }
}
else
{
    echo "<p>No credentials.</p>";
}




?>
