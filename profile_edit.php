<?php

require_once 'includes/session.php';

$conn = $pdo->open();

if (isset($_POST['edit'])) {
	$currPassword = $_POST['curr_password'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$contact = $_POST['contact'];
	$address = $_POST['address'];
	$photo = $_FILES['photo']['name'];

	if (password_verify($currPassword, $user['password'])) {
		if (!empty($photo)) {
			move_uploaded_file($_FILES['photo']['tmp_name'], 'images/'.$photo);
			$filename = $photo;

		} else {
			$filename = $user['photo'];
		}

		if ($password == $user['password']) {
			$password = $user['password'];
		} else {
			$password = password_hash($password, PASSWORD_DEFAULT);
		}

		try {
			$sql = "UPDATE 
						users 
					SET 
						email=:email, 
						password=:password, 
						firstname=:firstname, 
						lastname=:lastname, 
						contact_info=:contact, 
						address=:address, 
						photo=:photo 
					WHERE 
						id=:id"
					;

			$stmt = $conn->prepare($sql);
			$stmt->execute([
					'email' => $email, 
					'password' => $password, 
					'firstname' => $firstname, 
					'lastname' => $lastname, 
					'contact' => $contact, 
					'address' => $address, 
					'photo' => $filename, 'id' => $user['id']
				]);

			$_SESSION['success'] = 'Conta atualizada com sucesso';

		} catch(PDOException $e) {
			$_SESSION['error'] = $e->getMessage();
		}

	} else {
		$_SESSION['error'] = 'Senha incorreta';
	}

} else {
	$_SESSION['error'] = 'Preencha o formulário de edição primeiro';
}

$pdo->close();

header('location: profile.php');
exit();
