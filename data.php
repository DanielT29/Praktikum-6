<?php
echo $action = $_POST['action'];

parse_str($_POST['dataform'], $hasil);


$gambar = $_FILES["foto"];

$ccnumber = trim($hasil['ccnumber']);

if (!empty($gambar["name"]) and !empty($ccnumber)){
	$filename = $gambar["name"];		//nama filenya
	preg_match("/([^\.]+$)/", $filename, $ext);		//Regex: mencari string sesudah titik terakhir, saved in array ext
	$file_ext = strtolower($ext[1]);
	$namafilebaru = $hasil['ccnumber'].".".$ext[1];	//nama file barunya [ccnumber].png
    $file = $gambar["tmp_name"];						//source filenya
    //perform the upload operation
	$extensions= array("jpeg","jpg","png");				//extensi file yang diijinkan
	//Kirim pesan error jika extensi file yang diunggah tidak termasuk dalam extensions
	$errors = array();
	if(in_array($file_ext,$extensions) === false)
	 $errors[] = "format salah";

	//Kirim pesan error jika ukuran file > 500kB
	$fsize = $gambar['size'];
	if($size > 2097152)
	 $errors[] = "Ukuran file harus lebih kecil dari 2MB.";

	//Upload file
	if(empty($errors)){
		if(move_uploaded_file($file, "uploads/" . $namafilebaru))
			echo "Uploaded dengan nama $namafilebaru";
	}
}else echo $errors[] = "isi nomor telepon ";
echo "<br/>";

if(!empty($errors)){
	echo "Error : ";
	foreach ($errors as $val)
		echo $val;
}

if($action == 'create')
{
	$sql= "INSERT INTO `user` VALUES ('$hasil[nama_lengkap]','$hasil[panggilan]','$hasil[Username]','$hasil[inputEmail3]','$hasil[Password]',
	'$hasil[inputAddress]','$hasil[ccnumber]','$hasil[UnitKegiatan]','$hasil[Jurusan]','{$namafilebaru}')";
}
elseif ($action == 'update')
{
	$sql = "UPDATE user SET nama_depan =  '$hasil[nama_lengkap]', panggilan = '$hasil[panggilan]', foto = '$namafilebaru' where nama_depan = '$hasil[nama_lengkap]'";
}
elseif($action == 'delete')
{
	$sql = "DELETE from user where nama_depan = '$hasil[nama_lengkap]'";
}
elseif($action == 'read')
{
	$sql = "SELECT * from `user`";
}

else {
	echo "ERROR ACTION";
	exit();
}
$conn = new mysqli("localhost","root","","ukm");
if ($conn->connect_errno) {
  echo "Failed to connect to MySQL: " . $conn -> connect_error;
  exit();
}else{
  echo "Database connected. ";
}

if ($conn->query($sql) === TRUE) {
	echo "Query $action with syntax $sql suceeded !";

}
elseif ($conn->query($sql) === FALSE){
	echo "Error: $sql" .$conn -> error;
}
else
{
	$result = $conn->query($sql);
	if($result->num_rows > 0)

	{
		echo "<table id='' class='table table-striped table-bordered'>";
		echo "<thead><th>Nama Lengkap</th><th>Panggilan</th></th><th>Username</th></th><th>Email</th></th><th>Password</th>
		<th>Alamat</th><th>No Telp</th><th>Unit Kegiatan</th><th>Jurusan</th><th>Foto</th></thead>";
		while($row = $result->fetch_assoc())
		{
			echo "<tr>
			<td>".$row['nama_lengkap']."</td>
			<td>".$row['panggilan']."</td>
			<td>".$row['Username']."</td>
			<td>".$row['email']."</td>
			<td>".$row['inputEmail3']."</td>
			<td>".$row['Password']."</td>
			<td>".$row['inputAddress']."</td>
			<td>".$row['ccnumber']."</td>
			<td>".$row['UnitKegiatan']."</td>
			<td>".$row['Jurusan']."</td>
			</tr>";
		}
		echo "</tbody>";
		echo "</table>";
	}
}
$conn->close();
?>
