<?php
require 'vendor/autoload.php'; // Include PHPWord library
include 'db_connection.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

if (!isset($_GET['training_id']) || !isset($_GET['batch_id'])) {
    die("Training ID or Batch ID is missing.");
}

$training_id = intval($_GET['training_id']);
$batch_id = intval($_GET['batch_id']);

// Fetch participants
$query = "SELECT name, designation, office_address, contact FROM participants WHERE training_id = '$training_id' AND batch_id = '$batch_id'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching data: " . mysqli_error($conn));
}

// Function to convert numbers to Bangla
function convertToBanglaNumber($number) {
    $english_numbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $bangla_numbers = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
    return str_replace($english_numbers, $bangla_numbers, $number);
}

// Create new Word document
$phpWord = new PhpWord();
$section = $phpWord->addSection();

// Add title
$section->addText("প্রশিক্ষণার্থীদের তালিকা", ['bold' => true, 'size' => 16], ['alignment' => 'center']);
$section->addTextBreak(1);

// Add table
$table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);

// Table header (in Bangla)
$table->addRow();
$table->addCell(1000)->addText("ক্রমিক", ['bold' => true]);
$table->addCell(3000)->addText("নাম", ['bold' => true]);
$table->addCell(3000)->addText("পদবি", ['bold' => true]);
$table->addCell(3000)->addText("কার্যালয়", ['bold' => true]);
$table->addCell(2000)->addText("মোবাইল", ['bold' => true]);

// Populate table with data
$serial = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $table->addRow();
    $table->addCell(1000)->addText(convertToBanglaNumber($serial++)); // Convert serial to Bangla
    $table->addCell(3000)->addText($row['name']);
    $table->addCell(3000)->addText($row['designation']);
    $table->addCell(3000)->addText($row['office_address']);
    $table->addCell(2000)->addText($row['contact']);
}

// Save Word file
$filename = "Participants_List_Training_{$training_id}_Batch_{$batch_id}.docx";
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$objWriter = IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save("php://output");

exit;
?>
