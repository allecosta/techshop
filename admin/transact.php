<?php

require 'includes/session.php';

$id = $_POST['id'];

$conn = $pdo->open();

$output = ['list' => ''];
$sql = "SELECT 
			* 
		FROM 
			details 
		LEFT JOIN 
			products ON products.id = details.product_id 
		LEFT JOIN 
			sales ON sales.id=details.sales_id 
		WHERE 
			details.sales_id = :id";

$stmt = $conn->prepare($sql);
$stmt->execute(['id' => $id]);
$total = 0;

foreach ($stmt as $row) {
	$output['transaction'] = $row['pay_id'];
	$output['date'] = date('d M, Y', strtotime($row['sales_date']));
	$subtotal = $row['price'] * $row['quantity'];
	$total += $subtotal;
	$output['list'] .= "
		<tr class='prepend_items'>
			<td>" . $row['name'] . "</td>
			<td>&#82;&#36; " . number_format($row['price'], 2, ',', '.') . "</td>
			<td>" . $row['quantity'] . "</td>
			<td>&#82;&#36; " . number_format($subtotal, 2, ',', '.') . "</td>
		</tr>
	";
}

$output['total'] = '<strong>&#82;&#36; ' . number_format($total, 2, ',', '.') . '<strong>';

$pdo->close();

echo json_encode($output);
