<?php
// Set the encryption key
$encryption_key = 'yHwD0XKy1Zf3Id5AsIQCK+a79CN+cc8+3naCnj2Ffo0=';

// Define the base64-encoded encrypted configuration data
$encrypted_config_data = 'zp2+DEoVfvd4PoQSnJvGVEtobFNYZWF1NGhDalJFRnFkYjZzTWlPRUtZNHJNdlV3WVZrcm9NMkVCK0pORFhEVHpIZXBkWlV5NjZadWJUdTBYTmliZCt1MEU5RzNTSG1yUTdUUW5Kc1ZYM0pZaFZUck5FSDVWTnZrbXRjbHBwRUI5ZzhDY0g5eGp5T0NORVN6aHJidUxPamw4UFhUV0wzZmx6SXQ1Zz09';

// Function to decrypt and load database configuration
function getDbConfig() {
    global $encryption_key, $encrypted_config_data;

    $decoded_encryption_key = base64_decode($encryption_key);
    if (!$decoded_encryption_key) {
        throw new Exception("Encryption key not found or invalid");
    }

    // Decode the encrypted configuration data from base64
    $encrypted_data = base64_decode($encrypted_config_data);
    if ($encrypted_data === false) {
        throw new Exception("Failed to base64 decode the encrypted data");
    }

    // Define the IV length for AES-256-CBC
    $iv_length = openssl_cipher_iv_length('aes-256-cbc');

    // Extract the IV and the ciphertext
    $iv = substr($encrypted_data, 0, $iv_length);
    $ciphertext = substr($encrypted_data, $iv_length);

    // Decrypt the data
    $decrypted_data = openssl_decrypt($ciphertext, 'aes-256-cbc', $decoded_encryption_key, 0, $iv);
    if ($decrypted_data === false) {
        throw new Exception("Failed to decrypt configuration data");
    }

    // Decode and return the JSON data as an array
    return json_decode($decrypted_data, true);
}
