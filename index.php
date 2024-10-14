<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
$processedText = "";
// Define the file paths for keywords and cash tags
$keywordsFilePath = 'keywords.txt';
$cashTagsFilePath = 'cash_tags.txt';

// Load the keywords from the file
$keywordList = file($keywordsFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Load the cash tags from the file
$cashTagList = file($cashTagsFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Function to replace keywords with hashtagsfunction replaceKeywordsWithHashtags($text, $keywordList)
function replaceKeywordsWithHashtags($text, $keywordList)
{
    foreach ($keywordList as $keyword) {
        // Create a pattern that matches the keyword with optional surrounding punctuation and parentheses
        $pattern = '/(?<!\w)(?:[^\w\s]*$)?\s*'.preg_quote(strtolower($keyword), '/').'\s*(?:[^\w\s]*$)?(?!\w)/i';

        // Replace the keyword with the corresponding hashtag
        $text = preg_replace_callback('/(http|https):\/\/[^\s]*/', function($match) {
            return $match[0]; // Return the URL unchanged
        }, preg_replace($pattern, '#'.ucfirst($keyword), $text));
    }
    return $text;
}

// Function to replace cash tags with currency symbols
function replaceCashTagsWithCurrencySymbols($text, $cashTagList)
{
    foreach ($cashTagList as $cashTag) {
        // Create a pattern that matches the cash tag with optional surrounding punctuation and parentheses
        $pattern = '/(?<!\w)(?:[^\w\s]*$)?\s*'.preg_quote(strtolower($cashTag), '/').'\s*(?:[^\w\s]*$)?(?!\w)/i';

        // Replace the cash tag with the corresponding currency symbol
        $text = preg_replace_callback('/(http|https):\/\/[^\s]*/', function($match) {
            return $match[0]; // Return the URL unchanged
        }, preg_replace($pattern, strtoupper('$'.$cashTag), $text));
    }
    return $text;
}

// Function to clean the input text
function cleanInputText($text)
{
    $cleanedText = trim(htmlspecialchars(stripslashes($text)));
    return $cleanedText;
}

// Process the form submission
if (isset($_POST['textInput'])) {
    // Clean the input text
    $inputText = cleanInputText($_POST['textInput']);

    // Replace keywords with hashtags and cash tags with currency symbols
    $processedText = replaceKeywordsWithHashtags($inputText, $keywordList);
    $processedText = replaceCashTagsWithCurrencySymbols($processedText, $cashTagList);

} 
if (isset($_POST['urlInput'])) {
    // Clean the input text
    $urlInput = cleanInputText($_POST['urlInput']);

    if (!empty($urlInput)) {
        // Append the URL to the processed text
        $processedText .= "\r\n\r\n$urlInput";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keyword and Cash Tag Processor</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>

<div class="container my-5">
    <h2>Drop Text Here:</h2>
    <form method="POST">
    <div class="drop-zone">
        <textarea id="textInput" name="textInput" cols="80" rows="10" style="width:100%;height:80vh;" placeholder="Drag and drop text here or paste it in."><?php echo $processedText; ?></textarea>
    </div>
    <div class="form-group">
        <label for="urlInput">Enter a URL:</label>
        <input type="text" id="urlInput" name="urlInput" class="form-control" placeholder="Optional: Enter a URL to append to the post.">
    </div>    
    <button type="submit" formaction="" class="btn btn-primary my-3">Process Text</button>
    </form>
    <!-- Form to process the input text -->
    <form method="post">
        <input type="hidden" name="formSubmit" value="true">
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script>
    // Enable drag and drop functionality
    var $dropZone = $('.drop-zone');
    $dropZone.on('dragover', function(e) {
        e.preventDefault();
        $(this).css('background-color', '#ccc');
    });

    $dropZone.on('dragleave', function() {
        $(this).css('background-color', '');
    });

    $dropZone.on('drop', function(e) {
        e.preventDefault();
        var file = e.originalEvent.dataTransfer.files[0];
        if (file.type === 'text/plain') {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#textInput').val(e.target.result);
            };
            reader.readAsText(file);
        }
    });
</script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
