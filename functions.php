<?php

require_once "db-config.php";

set_exception_handler('handle_error');

// Custom error handling
function handle_error($exception) {

    // Log error details to a file for debugging purposes
    error_log($exception->getMessage() . "\n" . $exception->getTraceAsString(), 3, 'error_log.txt');

    // Redirect to error page
    header("Location: error.php");
    exit();
}

// Connect to mainport Database
function connect_db_mainport($license){
    try{
        // Fetch database configuration
        $db_config = getDbConfig();
        $external_db_host = $db_config['host'];
        $external_db_user = $db_config['user'];
        $external_db_pass = $db_config['pass'];
        $external_db_name = $db_config['name'];

        // Establish a connection with the external database
        $connection = new mysqli($external_db_host, $external_db_user, $external_db_pass, $external_db_name);

        // Check if connection has been established or not
        if ($connection->connect_error) {
            throw new Exception("Connection to retrieve the database failed.");
        }

        // Query to fetch customer database details 
        $sql = "SELECT Licence, ServerName, DatabaseName, UserName, UserPassword FROM MAIN_PORT WHERE Licence = '$license'";
        $result = $connection->query($sql);

        // Error for handling error with query
        if (!$result) {
            throw new Exception("Error executing query");
        }

        // Extract customer database details
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $customer_db_host = $row["ServerName"];
                $customer_db_user = $row["UserName"];
                $customer_db_pass = $row["UserPassword"];
                $customer_db_name = $row["DatabaseName"];
            }
        } else {
            throw new Exception("No results found for the provided license.");
        }

        $connection->close();

        return array(
            'customer_db_host' => $customer_db_host,
            'customer_db_user' => $customer_db_user,
            'customer_db_pass' => $customer_db_pass,
            'customer_db_name' => $customer_db_name,
        );
    } catch(Exception $e){
        throw new Exception("An error has occured while trying to connect to Quickbooks");
    }
}

function connect_db_customer($qb_access_token, $qb_access_exp, $qb_refresh_token, $qb_refresh_exp){
    try {
        // echo "Starting connect_db_customer function"; 
        // Get the license from the URL parameter
        $license = isset($_GET['license']) ? ($_GET['license']) : '';
        
        // Retrieve customer database details using the main port connection
        $db_customer = connect_db_mainport($license);

        // Extract customer database connection details
        $customer_db_host = $db_customer['customer_db_host'] ?? '';
        $customer_db_user = $db_customer['customer_db_user'] ?? '';
        $customer_db_pass = $db_customer['customer_db_pass'] ?? '';
        $customer_db_name = $db_customer['customer_db_name'] ?? '';

         // Validate if customer database details are complete
         if (!$customer_db_host || !$customer_db_user || !$customer_db_pass || !$customer_db_name) {
            throw new Exception("Incomplete customer database information retrieved.");
        }

        // Establish a connection with the external database
        $connection = new mysqli($customer_db_host, $customer_db_user, $customer_db_pass, $customer_db_name);
        
        // Check if connection has been established or not
        if ($connection->connect_error) {
            throw new Exception("Connection to customer database failed.");
        }

        $sql = "UPDATE SYSTEM_SETUP SET QB_ACCESS_TOKEN = '$qb_access_token', QB_ACCESS_EXPIRY = '$qb_access_exp', QB_REFRESH_TOKEN = '$qb_refresh_token', QB_REFRESH_EXPIRY = '$qb_refresh_exp' WHERE COMPANYNO=1";

        // Execute the query and check for success
        if ($connection->query($sql) !== TRUE) {
           throw new Exception("Error updating record: " . $connection->error);
        }

        $connection->close();

    } catch(Exception $e){
        throw new Exception("An error has occured while trying to connect to Quickbooks");
    }
}


?>