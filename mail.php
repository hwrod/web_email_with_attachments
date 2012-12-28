<?php
$to = $_REQUEST['to'];
$subject = $_REQUEST['subject'];
/*body*/ foreach ($_REQUEST as $k=>$v) if (!empty($v)) $body .= str_replace("_"," ",$k).": $v\r\n";

$headers = "From: {$_REQUEST['fromname']} <{$_REQUEST['fromemail']}>\r\n";
$headers .= "Reply-To: {$_REQUEST['fromemail']}\r\n"; 
$headers .= "MIME-Version: 1.0\n";
$headers .= "Content-Type: multipart/related; type=\"multipart/alternative\"; boundary=\"----=MIME_BOUNDRY_main_message\"\n"; 
$headers .= "Return-Path: {$_REQUEST['fromemail']}\r\n"; 
$headers .= "This is a multi-part message in MIME format.\n";
$headers .= "------=MIME_BOUNDRY_main_message \n"; 
$headers .= "Content-Type: multipart/alternative; boundary=\"----=MIME_BOUNDRY_message_parts\"\n"; 

$message = "------=MIME_BOUNDRY_message_parts\n";
$message .= "Content-Type: text/plain; charset=\"iso-8859-1\"\n"; 
$message .= "Content-Transfer-Encoding: quoted-printable\n"; 
$message .= "\n"; 

$message .= "$body\n"; 
$message .= "\n";  
$message .= "------=MIME_BOUNDRY_message_parts--\n";  
$message .= "\n";  

/*deal with mulitple attachments */
foreach($_FILES as $file => $value) { 
	$_tmpname = $_FILES[$file]['tmp_name']; 
	$_filename = $_FILES[$file]['name']; 
	if (is_uploaded_file($_tmpname)) {
		$fp = fopen($_tmpname, "rb");
		$data = fread($fp, filesize($_tmpname));
		$data = chunk_split(base64_encode($data));
		$message .= "------=MIME_BOUNDRY_main_message\n";  
		$message .= "Content-Type: application/octet-stream;\n\tname=\"" . $_filename . "\"\n"; 
		$message .= "Content-Transfer-Encoding: base64\n"; 
		$message .= "Content-Disposition: attachment;\n\tfilename=\"" . $_filename . "\"\n\n"; 
		$message .= $data;
		$message .= "\n\n"; 
		fclose($fp); 
	} 
} 

$message .= "------=MIME_BOUNDRY_main_message--\n";  

$mail_sent = mail( $to, $subject, $message, $headers );
if ($mail_sent) {
	if ($_REQUEST['redirect']) header( "Location: {$_REQUEST['redirect']}" );	
	else echo "<br><br><br>Thank you, your email has been sent successfully.";
}
else {
	echo "<br><br><br>Sorry, there has been an error sending your e-mail.";
	//echo "<br><br><h2>Below are the message headers:</h2><hr><br><pre>$headers</pre><br><hr><h2>Message:</h2><br><pre>$message</pre>"; // debug
}
?>
