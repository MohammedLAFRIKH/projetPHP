<?php

function baseUrl() {
    // Get the protocol (http or https)
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";

    // Get the server name
    $serverName = $_SERVER['SERVER_NAME'];

    // Get the server port (if not standard)
    $port = $_SERVER['SERVER_PORT'] != '80' ? (":" . $_SERVER['SERVER_PORT']) : "";

    // Get the application path (if your project is in a subdirectory)
    $basePath = "/apogee_ens"; // Change this based on your project's structure

    // Combine all parts to form the base URL
    return $protocol . "://" . $serverName . $port . $basePath . "/";
}
