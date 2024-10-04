<?php
$uploadDir = 'uploads/';
$action = $_GET['action'] ?? '';

// Create the uploads directory if it doesn't exist
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

switch ($action) {
    case 'upload':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $file = $_FILES['pdfFile'];
            $fileName = basename($file['name']);
            $targetFile = $uploadDir . $fileName;

            // Move the uploaded file to the uploads directory
            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                echo 'File uploaded successfully: ' . htmlspecialchars($fileName);
            } else {
                echo 'Error uploading file.';
            }
        }
        break;

    case 'list':
        // List uploaded PDFs
        $pdfs = [];
        if (is_dir($uploadDir)) {
            $files = array_diff(scandir($uploadDir), array('.', '..'));
            foreach ($files as $index => $filename) {
                $pdfs[] = ['id' => $index, 'filename' => $filename];
            }
        }
        echo json_encode($pdfs);
        break;

    case 'view':
        // Serve the PDF for viewing
        $id = $_GET['id'] ?? '';
        $files = array_diff(scandir($uploadDir), array('.', '..'));
        if (isset($files[$id])) {
            $filePath = $uploadDir . $files[$id];
            if (file_exists($filePath)) {
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
                readfile($filePath);
                exit;
            } else {
                echo 'File does not exist.';
            }
        } else {
            echo 'Invalid file ID.';
        }
        break;

    case 'download':
        // Serve the PDF for download
        $id = $_GET['id'] ?? '';
        $files = array_diff(scandir($uploadDir), array('.', '..'));
        if (isset($files[$id])) {
            $filePath = $uploadDir . $files[$id];
            if (file_exists($filePath)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filePath));
                readfile($filePath);
                exit;
            } else {
                echo 'File does not exist.';
            }
        } else {
            echo 'Invalid file ID.';
        }
        break;

    default:
        echo 'Invalid action.';
        break;
}
?>
