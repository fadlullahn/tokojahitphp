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
        get_kain($conn);
        break;
    case 'POST':
        post_kain($conn);
        break;
    case 'DELETE':
        delete_kain($conn);
        break;
    default:
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}

function get_kain($conn)
{
    $result = $conn->query("SELECT * FROM kain");
    $kain = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode(array("result" => $kain));
}

function post_kain($conn)
{
    $flag = $_POST['flag'];
    if ($flag == "INSERT") {
        insert_kain($conn);
    } elseif ($flag == "UPDATE") {
        update_kain($conn);
    }
}

function insert_kain($conn)
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

        $stmt = $conn->prepare("INSERT INTO kain (id, name, price, date, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('ssdss', $id, $name, $price, $date, $new_filename);
        $stmt->execute();

        $data = array('id' => $id, 'name' => $name, 'price' => $price, 'date' => $date, 'image' => $new_filename);
        echo json_encode($data);
    } else {
        echo json_encode(array('status' => 'fail', 502));
    }
}

function update_kain($conn)
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

        // Delete old image
        $stmt = $conn->prepare("SELECT image FROM kain WHERE id = ?");
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $picturepath = $upload_path . $row['image'];
        unlink($picturepath);

        $stmt = $conn->prepare("UPDATE kain SET name = ?, price = ?, date = ?, image = ? WHERE id = ?");
        $stmt->bind_param('sdsss', $name, $price, $date, $new_filename, $id);
        $stmt->execute();

        $data = array('id' => $id, 'name' => $name, 'price' => $price, 'date' => $date, 'image' => $new_filename);
        echo json_encode($data);
    } else {
        echo json_encode(array('status' => 'fail', 502));
    }
}

function delete_kain($conn)
{
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = $_DELETE['id'];

    $stmt = $conn->prepare("SELECT image FROM kain WHERE id = ?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $picturepath = './assets/files/img/' . $row['image'];
    unlink($picturepath);

    $stmt = $conn->prepare("DELETE FROM kain WHERE id = ?");
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
