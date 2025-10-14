<?php
/**
 * Test endpoint to debug form submissions
 * Shows exactly what data is being received
 */

header('Content-Type: text/html; charset=UTF-8');

echo "<h1>Form Data Received:</h1>";
echo "<pre>";
echo "POST Data:\n";
print_r($_POST);
echo "\n\n";
echo "Audience_Type: " . (isset($_POST['Audience_Type']) ? $_POST['Audience_Type'] : 'NOT SET') . "\n";
echo "Email: " . (isset($_POST['Email']) ? $_POST['Email'] : 'NOT SET') . "\n";
echo "Therapy_Interests: " . (isset($_POST['Therapy_Interests']) ? $_POST['Therapy_Interests'] : 'NOT SET') . "\n";
echo "</pre>";

echo "<br><a href='/'>Back to form</a>";
?>


