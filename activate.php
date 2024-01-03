<?php 

require_once 'includes/session.php';

$output = '';

if (!isset($_GET['code']) OR !isset($_GET['user'])) {
	$output .= '
		<div class="alert alert-danger">
			<h4><i class="icon fa fa-warning"></i> Erro!</h4> Código para conta ativa não encontrado.
		</div>
		<h4>Você pode <a href="signup.php">inscreve-se</a> ou voltar para <a href="index.php">página inicial</a>.</h4>
	'; 
} else {
	$conn = $pdo->open();
	$sql = "SELECT *, COUNT(*) AS numrows FROM users WHERE activate_code=:code AND id=:id";
	$stmt = $conn->prepare($sql);
	$stmt->execute(['code'=>$_GET['code'], 'id'=>$_GET['user']]);
	$row = $stmt->fetch();

	if ($row['numrows'] > 0) {
		if ($row['status']) {
			$output .= '
				<div class="alert alert-danger">
					<h4><i class="icon fa fa-warning"></i> Erro!</h4> Conta já ativada.
				</div>
				<h4>
					Você pode fazer <a href="login.php">Login</a> ou voltar para <a href="index.php">página inicial</a>.
				</h4>
			';
		} else {
			try {
				$sql = "UPDATE users SET status=:status WHERE id=:id";
				$stmt = $conn->prepare($sql);
				$stmt->execute(['status'=>1, 'id'=>$row['id']]);
				$output .= '
					<div class="alert alert-success">
						<h4><i class="icon fa fa-check"></i> Sucesso!</h4>
						Conta ativada - Email: <strong>'.$row['email'].'</strong>.
					</div>
					<h4>
						Você pode fazer <a href="login.php">Login</a> ou voltar para <a href="index.php">página inicial</a>.
					</h4>
				';
			} catch(PDOException $e) {
				$output .= '
					<div class="alert alert-danger">
						<h4><i class="icon fa fa-warning"></i> Erro!</h4>
						'.$e->getMessage().'
					</div>
					<h4>
						Você pode <a href="signup.php">inscreve-se</a> ou voltar para <a href="index.php">página inicial</a>.
					</h4>
				';
			}
		}
	} else {
		$output .= '
			<div class="alert alert-danger">
				<h4><i class="icon fa fa-warning"></i> Erro!</h4> Não é possivel ativar a conta. Código errado.
			</div>
			<h4>
				Você pode <a href="signup.php">inscreve-se</a> ou voltar para <a href="index.php">página inicial</a>.
			</h4>
		';
	}

	$pdo->close();
}

require 'includes/header.php'; 

?>
<body class="hold-transition skin-blue layout-top-nav">
	<div class="wrapper">
		<?php require 'includes/navbar.php'; ?>
		<div class="content-wrapper">
			<div class="container">
				<section class="content">
					<div class="row">
						<div class="col-sm-9">
							<?= $output; ?>
						</div>
						<div class="col-sm-3">
							<?php require 'includes/sidebar.php'; ?>
						</div>
					</div>
				</section>
			</div>
		</div>
		
		<?php require 'includes/footer.php'; ?>

	</div>

	<?php require_once 'includes/scripts.php'; ?>

</body>
</html>