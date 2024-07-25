<?php
require("../config/koneksi.php");

$response = array();

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $name = $_POST["name"];
    $username = $_POST["username"];
    $level = $_POST["level"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $nowa = $_POST["nowa"];

    $perintah = "INSERT INTO user (name, username, level, password, email, nowa) VALUES('$name','$username','$level','$password','$email','$nowa')";
    $eksekusi = mysqli_query($konek, $perintah);
    $cek      = mysqli_affected_rows($konek);

    if($cek > 0){
        $response["kode"] = 1;
        $response["pesan"] = "Simpan Data Berhasil";
    }
    else{
        $response["kode"] = 0;
        $response["pesan"] = "Gagal Menyimpan Data";
    }
}
else{
    $response["kode"] = 0;
    $response["pesan"] = "Tidak Ada Post Data";
}

echo json_encode($response);
mysqli_close($konek);