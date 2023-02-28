#!/usr/bin/php
<?php

    // Define command line options: file, create_table, dry_run and help.
    $options = getopt("u:p:h:", ["file:", "create_table", "dry_run", "help"]);

    // Print help if requested.
    if (isset($options['help'])) {
        echo "This script processes a CSV file and inserts the data into a MySQL database.\n";
        echo "Usage: php script.php [options]\n";
        echo "Options:\n";
        echo "--file [csv file name]\tThe name of the CSV to be parsed.\n";
        echo "--create_table\t\tBuild the MySQL users table and exit.\n";
        echo "--dry_run\t\tRun the script without inserting into the DB.\n";
        echo "-u\t\t\tMySQL username.\n";
        echo "-p\t\t\tMySQL password.\n";
        echo "-h\t\t\tMySQL host.\n";
        echo "--help\t\t\tDisplay this help message.\n";
        exit();
    }

    // Create users table if requested.
    if (isset($options['create_table'])) {
        $db->query("CREATE DATABASE IF NOT EXISTS users_db");
        $db->query("USE users_db");
        $db->query("DROP TABLE IF EXISTS users");
        $db->query("CREATE TABLE users (
            name VARCHAR(255),
            surname VARCHAR(255),
            email VARCHAR(255) UNIQUE
        )");

        echo "User table is created successfully.\n";
        exit();
    }

    // Ensure required options are set
    if (!isset($options['file'])) {
        die("Error: --file option is required.\n");
    }

    // Check if file is found.
    if (!file_exists($options['file'])) {
        die("Error: File not found: " . $options['file'] . "\n");
    }

    // Print error is -u is not in the options.
    if (!isset($options['u'])) {
        die("Error: -u option is required.\n");
    }

    // Print error is -p is not in the options.
    if (!isset($options['p'])) {
        die("Error: -p option is required.\n");
    }

    // Print error is -h is not in the options.
    if (!isset($options['h'])) {
        die("Error: -h option is required.\n");
    }

    // Connecting to the MySQL database.
    $db = new mysqli($options['h'], $options['u'], $options['p']);

    if ($db->connect_error) {
        die("Error: Connection failed: " . $db->connect_error . "\n");
    }

    // Iterate through CSV and insert into database
    if (($handle = fopen($options['file'], "r")) !== false) {
        $db->query("CREATE DATABASE IF NOT EXISTS users_db");
        $db->query("USE users_db");
        $db->query("DROP TABLE IF EXISTS users");
        $db->query("CREATE TABLE users (
            name VARCHAR(255),
            surname VARCHAR(255),
            email VARCHAR(255) UNIQUE
        )");

        $numRows = 0;
        $numInserts = 0;
        $numErrors = 0;

        while (($data = fgetcsv($handle, 1024, ",")) !== false) {
            $numRows++;

            // Validate data.
            if (!filter_var($data[2], FILTER_VALIDATE_EMAIL)) {
                $numErrors++;
                echo "Error: Invalid email on row $numRows. Skipping.\n";
                continue;
            }

            // Preparing the data to be inserted.
            $name = ucwords(strtolower(trim($data[0])));
            $surname = ucwords(strtolower(trim($data[1])));
            $email = strtolower(trim($data[2]));

            // Insert the row.
            if (!isset($options['dry_run'])) {
                if (!$db->query("INSERT INTO users (name, surname, email) VALUES ('$name', '$surname', '$email')")) {
                    $numErrors++;
                    echo "Error: Failed to insert data on row $numRows.\n";
                } else {
                    $numInserts++;
                }
            } else {
                $numInserts++;
            }
        }

        fclose($handle);

        echo "Processed $numRows rows. ";
        
        if (isset($options['dry_run'])) {
            echo "No data was inserted into the database.\n";
        } else {
            echo "$numInserts rows inserted, $numErrors errors.\n";
        }
    }

    $db->close();

?>
