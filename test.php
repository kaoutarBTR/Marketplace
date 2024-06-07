<?php 
// API credentials
$apiKey = 'JB468K7K57ZWJQ9AQLTZHWARRQVV8ELH';
$productId = 1042;
$imageFilePath = ('http://localhost/prestashop_/img/p/3/3/33.jpg');
$apiUrl = "http://localhost/prestashop_/api/images/products/".$productId;

// Prepare the cURL session
$ch = curl_init();

// Set the cURL options
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $apiKey.':');
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

// Prepare the file for upload
$file = new CURLFile($imageFilePath);
$postFields = ['image' => $file];

curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
} else {
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo $httpCode;
    if ($httpCode == 200) {
        echo 'Image uploaded successfully!';

        $xml = simplexml_load_string($response);

        if ($xml !== false) {
            // Extract the image ID from the response
            $imageId = (string) $xml->image->id;
            echo 'Image ID: ' . $imageId . PHP_EOL;
        } else {
            echo 'Failed to parse XML response.' . PHP_EOL;
            echo 'Response: ' . $response . PHP_EOL;
        }
        
    } else {
        echo "Failed to upload image. HTTP Status Code: $httpCode\n";
        echo "Response: $response\n";
    }
}

// Close the cURL session
curl_close($ch);
?>
