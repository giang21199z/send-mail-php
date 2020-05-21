<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token");
header("Access-Control-Allow-Credentials: true");
header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(array("success" => 0, "message" => "Từ chối truy cập."));
    exit();
}

$body = json_decode(file_get_contents('php://input'), true);
$nameOrg = $body['nameOrg'];
$phone = $body['phone'];
$email = $body['email'];
$attachment = $body['attachment'];
$destinationEmail = $body['destinationEmail'];
date_default_timezone_set("Asia/Bangkok");
$currentNow = date("h:i:sa d-m-Y");
if(
    $nameOrg == null || $phone == null || $email == null || $attachment == null || $destinationEmail) {
    echo json_encode(array("success" => 0, "message" => "Sai dữ liệu truyền vào"));
    exit();
}

$content = "<h3>Thông tin đăng ký nhanh</h3>";
$content.= "<div><b>Tên đơn vị:</b> $nameOrg</div>";
$content.= "<div><b>Số điện thoại:</b> $phone</div>";
$content.= "<div><b>Email:</b> $email</div>";
$content.= "<div><b>File đính kèm:</b> $attachment</div>";
$content.= "<div><b>Thời gian gửi:</b> $currentNow </div>";
$content.= "<br>";
$content.= "<i style='color: red'>Đây là hệ thống thư tự động được gửi từ website vrqc.org.vn. Vui lòng kiểm tra thông tin đơn đăng ký.</i>";


use PHPMailer\PHPMailer\PHPMailer;
require './vendor/autoload.php';
$mail = new PHPMailer;
$mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 0;
$mail->Host = 'smtp.gmail.com';

$mail->Port = 587;
$mail->SMTPSecure = 'tls';
$mail->SMTPAuth = true;
$mail->Username = "vrqc.email@gmail.com";
$mail->Password = "Gi@ng1993";
$mail->setFrom('vrqc.email@gmail.com', 'vrqc.org.vn');
$mail->addAddress($destinationEmail, 'Administrator');
$mail->Subject = '[Thu tu dong] Thong tin dang ky';

$mail->msgHTML($content);
if (!$mail->send()) {
    echo json_encode(array("success" => 0, "message" => "Đường truyền không ổn định vui lòng thử lại."));
} else {
    echo json_encode(array("success" => 1, "message" => "Gửi email thành công đến quản trị."));
}