<?php
require 'vendor/autoload.php';

use FbMediaDownloader\Downloader;

function downloadVideoFb($videoURLFb) {
    $downloader = new Downloader();
    //set idurl
    $downloader->set_url(trim($videoURLFb));
    $datas = $downloader->generate_data();
     
    return $datas;
}


function downloadVideo($videoURL, $savePath) {
    // Initialize a cURL session
    $ch = curl_init();

    // Set the cURL options
    curl_setopt($ch, CURLOPT_URL, $videoURL);       // The URL of the video
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the output as a string
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow any redirects

    // Execute the cURL request and get the video data
    $videoData = curl_exec($ch);

    // Close the cURL session
    curl_close($ch);

    // Check if the video data was retrieved successfully
    if ($videoData !== false) {
        // Save the video data to the specified path using file_put_contents
        if (file_put_contents($savePath, $videoData)) {
            echo "Video saved to " . $savePath;
        } else {
            echo "Failed to save video.";
        }
    } else {
        echo "Failed to retrieve video data.";
    }
}
 
function addRowToFileOutput($filePath, $rowData) {
    // Add a newline to the row data to ensure it starts on a new line in the file
    $rowDataWithNewline = $rowData . PHP_EOL;

    // Use file_put_contents with the FILE_APPEND flag to add the row to the file
    if (file_put_contents($filePath, $rowDataWithNewline, FILE_APPEND | LOCK_EX)) {
       // echo "Row added to file successfully.";
    } else {
       // echo "Failed to add row to file.";
    }
}



$numberPost = file_get_contents('configGet.txt');

$fh = fopen('listUrlReels.txt','r');
$i = 1;
while ($line = fgets($fh)) {
  // <... Do your work with the line ...>
   if ($line && $i == $numberPost) {
    $datas = downloadVideoFb($line);
    // var_dump($datas);
    $data_output = trim($line);
    if ($datas) {
        $title = explode('|', $datas["title"]);
        $titlePost = explode('#', $title[1]);
        // var_dump($titlePost[0]);
        // echo '<hr/>';
        // var_dump($datas["id"]);
        // echo '<hr/>';
        // var_dump($datas["dl_urls"]["high"]);

        $savePath = 'videos/'.$i.'-video-'.$datas["id"].'.mp4';
        $data_output =  $data_output.'-----'.$savePath.'-----'.$titlePost[0];

        
        downloadVideo($datas["dl_urls"]["high"], $savePath) ;
    }
   
     echo $data_output ;
     file_put_contents('configGet.txt', $i+1);
     addRowToFileOutput('listUrlReelsOutPut.txt', $data_output);
   }
   $i++;
}
fclose($fh);
?>
