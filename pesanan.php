<?php

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'tokojahit';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$request_method = $_SERVER['REQUEST_METHOD'];

switch ($request_method) {
    case 'GET':
        get_pesanan($conn);
        break;
    case 'POST':
        post_pesanan($conn);
        break;
    case 'DELETE':
        delete_pesanan($conn);
        break;
    default:
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}

function get_pesanan($conn)
{
    $result = $conn->query("SELECT * FROM pesanan");
    $pesanan = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode(array("result" => $pesanan));
}

function post_pesanan($conn)
{
    $flag = $_POST['flag'];
    if ($flag == "INSERT") {
        insert_pesanan($conn);
    } elseif ($flag == "UPDATE") {
        update_pesanan($conn);
    }
}

function insert_pesanan($conn)
{
    $image = $_FILES['image']['name'];
    $new_filename = generate_random_filename($image);

    $upload_path = './assets/files/img/';
    $target_file = $upload_path . $new_filename;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $date = $_POST['date'];
        $baju = $_POST['baju'];
        $kain = $_POST['kain'];
        $desain = $_POST['desain'];
        $lingkar_badan = $_POST['lingkar_badan'];
        $lingkar_pinggang = $_POST['lingkar_pinggang'];

        $panjang_dada = $_POST['panjang_dada'];
        $lebar_dada = $_POST['lebar_dada'];
        $panjang_punggung = $_POST['panjang_punggung'];

        $lebar_punggung = $_POST['lebar_punggung'];
        $lebar_bahu = $_POST['lebar_bahu'];

        $lingkar_leher = $_POST['lingkar_leher'];
        $tinggi_dada = $_POST['tinggi_dada'];
        $jarak_dada = $_POST['jarak_dada'];

        $lingkar_pangkal_lengan = $_POST['lingkar_pangkal_lengan'];
        $panjang_lengan = $_POST['panjang_lengan'];
        $lingkar_siku = $_POST['lingkar_siku'];
        $lingkar_pergelangan_tangan = $_POST['lingkar_pergelangan_tangan'];
        $lingkar_kerung_lengan = $_POST['lingkar_kerung_lengan'];

        $lingkar_panggul_1 = $_POST['lingkar_panggul_1'];
        $lingkar_panggul_2 = $_POST['lingkar_panggul_2'];
        $lingkar_rok = $_POST['lingkar_rok'];

        $proses = 'proses';

        $stmt = $conn->prepare("INSERT INTO pesanan (id, name, price, date, image, baju, kain, desain, lingkar_badan, lingkar_pinggang, panjang_dada, lebar_dada, panjang_punggung, lebar_punggung, lebar_bahu, lingkar_leher, tinggi_dada, jarak_dada, lingkar_pangkal_lengan, panjang_lengan, lingkar_siku, lingkar_pergelangan_tangan, lingkar_kerung_lengan, lingkar_panggul_1, lingkar_panggul_2, lingkar_rok, proses) VALUES (?, ?, ?, ?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssdssssssssssssssssssssssss', $id, $name, $price, $date, $new_filename, $baju, $kain, $desain, $lingkar_badan, $lingkar_pinggang, $panjang_dada, $lebar_dada, $panjang_punggung, $lebar_punggung, $lebar_bahu, $lingkar_leher, $tinggi_dada, $jarak_dada, $lingkar_pangkal_lengan, $panjang_lengan, $lingkar_siku, $lingkar_pergelangan_tangan, $lingkar_kerung_lengan, $lingkar_panggul_1, $lingkar_panggul_2, $lingkar_rok, $proses);
        $stmt->execute();

        $data = array('id' => $id, 'name' => $name, 'price' => $price, 'date' => $date, 'image' => $new_filename, 'baju' => $baju, 'kain' => $kain, 'desain' => $desain, 'lingkar_badan' => $lingkar_badan, 'lingkar_pinggang' => $lingkar_pinggang, 'panjang_dada' => $panjang_dada, 'lebar_dada' => $lebar_dada, 'panjang_punggung' => $panjang_punggung, 'lebar_punggung' => $lebar_punggung, 'lebar_bahu' => $lebar_bahu, 'lingkar_leher' => $lingkar_leher, 'tinggi_dada' => $tinggi_dada, 'jarak_dada' => $jarak_dada, 'lingkar_pangkal_lengan' => $lingkar_pangkal_lengan, 'panjang_lengan' => $panjang_lengan, 'lingkar_siku' => $lingkar_siku, 'lingkar_pergelangan_tangan' => $lingkar_pergelangan_tangan, 'lingkar_kerung_lengan' => $lingkar_kerung_lengan, 'lingkar_panggul_1' => $lingkar_panggul_1, 'lingkar_panggul_2' => $lingkar_panggul_2, 'lingkar_rok' => $lingkar_rok, 'proses' => $proses);
        echo json_encode($data);
    } else {
        echo json_encode(array('status' => 'fail', 502));
    }
}

function update_pesanan($conn)
{
    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
        $image = $_FILES['image']['name'];
        $new_filename = generate_random_filename($image);

        $upload_path = './assets/files/img/';
        $target_file = $upload_path . $new_filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $price = $_POST['price'];
            $date = $_POST['date'];
            $proses = $_POST['proses'];

            // Hapus gambar lama
            $stmt = $conn->prepare("SELECT image FROM pesanan WHERE id = ?");
            $stmt->bind_param('i', $id); // 'i' untuk id yang merupakan int
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $picturepath = $upload_path . $row['image'];
            unlink($picturepath);

            // Update pesanan dengan gambar baru
            $stmt = $conn->prepare("UPDATE pesanan SET name = ?, price = ?, date = ?, image = ?, proses = ? WHERE id = ?");
            $stmt->bind_param('sssssi', $name, $price, $date, $new_filename, $proses, $id); // 'sssssi' sesuai tipe data
            $stmt->execute();

            $data = array('id' => $id, 'name' => $name, 'price' => $price, 'date' => $date, 'image' => $new_filename, 'proses' => $proses);
            echo json_encode($data);
        } else {
            echo json_encode(array('status' => 'fail', 502));
        }
    } else {
        $id = $_POST['id'];
        $proses = $_POST['proses'];

        // Update hanya kolom proses
        $stmt = $conn->prepare("UPDATE pesanan SET proses = ? WHERE id = ?");
        $stmt->bind_param('si', $proses, $id); // 'si' untuk proses yang varchar dan id yang int
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $data = array('id' => $id, 'proses' => $proses);
            echo json_encode($data);
        } else {
            echo json_encode(array('status' => 'fail', 'message' => 'Update failed'));
        }
    }
}


function delete_pesanan($conn)
{
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = $_DELETE['id'];

    $stmt = $conn->prepare("SELECT image FROM pesanan WHERE id = ?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $picturepath = './assets/files/img/' . $row['image'];
    unlink($picturepath);

    $stmt = $conn->prepare("DELETE FROM pesanan WHERE id = ?");
    $stmt->bind_param('s', $id);
    $stmt->execute();

    if ($stmt->affected_rows) {
        echo json_encode(array('status' => 'success'), 201);
    } else {
        echo json_encode(array('status' => 'fail', 502));
    }
}

function generate_random_filename($original_filename)
{
    $ext = pathinfo($original_filename, PATHINFO_EXTENSION);
    $random_filename = uniqid() . '.' . $ext;
    return $random_filename;
}
