<?php
require("../config/koneksi.php");

$response = array();

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $id = $_POST["id"];
    $name = $_POST["name"];
    $username = $_POST["username"];
    $level = $_POST["level"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $nowa = $_POST["nowa"];
    
    $perintah = "UPDATE user SET name = '$name', username = '$username', level = '$level', password = '$password', email = '$email', nowa = '$nowa' WHERE id = '$id'";
    $eksekusi = mysqli_query($konek, $perintah);
    $cek      = mysqli_affected_rows($konek);

    if($cek > 0){
        $response["kode"] = 1;
        $response["pesan"] = "Data Berhasil Diubah";
    }
    else{
        $response["kode"] = 0;
        $response["pesan"] = "Data Gagal Diubah";
    }
}
else{
    $response["kode"] = 0;
    $response["pesan"] = "Tidak Ada Post Data";
}

echo json_encode($response);
mysqli_close($konek);