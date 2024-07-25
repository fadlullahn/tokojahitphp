<?php

// Konfigurasi koneksi database
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'tokojahit';

// Membuat koneksi ke database
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Mendapatkan data baju
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM baju";
    $result = $conn->query($sql);

    $baju = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $baju[] = $row;
        }
    }

    header('Content-Type: application/json');
    echo json_encode(array("result" => $baju));
}

// Menambah data baju baru
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['flag'] == "INSERT") {
        $image = upload_image($_FILES['image']);

        $name = $_POST['name'];
        $price = $_POST['price'];
        $date = $_POST['date'];

        $sql = "INSERT INTO baju (name, price, date, image) VALUES ('$name', '$price', '$date', '$image')";

        if ($conn->query($sql) === TRUE) {
            http_response_code(200);
            echo json_encode(array("status" => "success"));
        } else {
            http_response_code(502);
            echo json_encode(array("status" => "fail"));
        }
    }
}

// Mengupdate data baju
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['flag'] == "UPDATE") {
        $id = $_POST['id'];
        $image = upload_image($_FILES['image']);

        $name = $_POST['name'];
        $price = $_POST['price'];
        $date = $_POST['date'];

        // Hapus Image Lama
        $queryimg = $conn->query("SELECT image FROM baju WHERE id='$id'");
        $row = $queryimg->fetch_assoc();
        $picturepath = "./assets/files/img/" . $row['image'];
        if (file_exists($picturepath)) {
            unlink($picturepath);
        }

        $sql = "UPDATE baju SET name='$name', price='$price', date='$date', image='$image' WHERE id='$id'";

        if ($conn->query($sql) === TRUE) {
            http_response_code(200);
            echo json_encode(array("status" => "success"));
        } else {
            http_response_code(502);
            echo json_encode(array("status" => "fail"));
        }
    }
}

// Menghapus data baju
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = $_DELETE['id'];

    // Hapus Image Lama
    $queryimg = $conn->query("SELECT image FROM baju WHERE id='$id'");
    $row = $queryimg->fetch_assoc();
    $picturepath = "./assets/files/img/" . $row['image'];
    if (file_exists($picturepath)) {
        unlink($picturepath);
    }

    $sql = "DELETE FROM baju WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        http_response_code(200);
        echo json_encode(array("status" => "success"));
    } else {
        http_response_code(502);
        echo json_encode(array("status" => "fail"));
    }
}

// Fungsi untuk upload gambar
function upload_image($image_file)
{
    $target_dir = "./assets/files/img/";
    $target_file = $target_dir . basename($image_file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check file size
    if ($image_file["size"] > 20480000) {
        http_response_code(502);
        echo json_encode(array("status" => "fail", "message" => "File size is too large."));
        exit();
    }

    // Generate random file name
    $new_filename = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $new_filename;

    // Upload file
    if (move_uploaded_file($image_file["tmp_name"], $target_file)) {
        return $new_filename;
    } else {
        http_response_code(502);
        echo json_encode(array("status" => "fail", "message" => "Failed to upload file."));
        exit();
    }
}
