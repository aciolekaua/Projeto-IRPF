<?php 
    require __DIR__.'/vendor/autoload.php';
    /*var_dump($_FILES['files']['type']);
    exit();*/
    use Dompdf\Dompdf;
    use Dompdf\Options;
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    
    $options = new Options();
    $options->setChroot(__DIR__);
    $options->setIsRemoteEnabled(true);
    $options->set("isPhpEnabled", true);
    $dompdf = new Dompdf($options);
    
  
    $html = "
        <style>
            table {
              font-family: arial, sans-serif;
              border-collapse: collapse;
              width: 100%;
              margin-bottom:70px;
            }
            
            td, th {
              border: 1px solid #dddddd;
              text-align: left;
              padding: 8px;
            }
            
            tr:nth-child(even) {
              background-color: #dddddd;
              margin-bottom:40px;
            }
            
            h1 {
                font-size:30px;
                font-weight:400;
            }
            hr{
                margin-bottom:20px;
            }
            img {
                margin-bottom:30px;
                margin-left:160px;
            }
        </style>
        <h1>VMPE IRPF</h1>
        <hr>
        <table>
              <tr>
                <th>Email</th>
                <th>CPF</th>
                <th>Nome Contribuinte</th>
              </tr>
              <tr>
                <td>{$_POST['email']}</td>
                <td>{$_POST['cpf']}</td>
                <td>{$_POST['nomeC']}</td>
              </tr>
        </table>
        ";
    for($i=0; $i<=(count($_FILES['files']['type'])-1); $i++){
        $imgBase64=base64_encode(file_get_contents($_FILES['files']['tmp_name'][$i]));
        $typeImg=$_FILES['files']['type'][$i];
        //$html+="<span>type:{$typeImg} base64:{$imgBase64}</span>";
        $html.="<img style='width:400px; height:400px;' src='data:{$typeImg};base64,$imgBase64' />";
    }
    $dompdf->loadHtml($html);
    
    $dompdf->render();
    
    
    header('Content-type: application/pdf');
    $pdf = $dompdf->output();
    echo $dompdf->output();
    $mail = new PHPMailer(true);
    
    try{
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      
            $mail->isSMTP();                                       
            $mail->Host       = 'smtp.exemplo.com';                     
            $mail->SMTPAuth   = true;                                  
            $mail->Username   = 'exemplo@email.com';                   
            $mail->Password   = 'senhaexemplo';
            $mail->CharSet    = 'UTF-8';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            
            $mail->Port       = 465;  
            $mail->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    );
                    //SMTPOptions é só se vc usar a hospedagem da hostgator
            
            
            $mail->setFrom('exemplo@email.com', 'Nome de quem vai enviar');
            $mail->addAddress('emaildequemrecebe@email.com', 'Nome de quem vai receber'); 
            $mail->addStringAttachment($pdf,  'documento.pdf'); //Eu estava Enviando um pdf vc pode enviar qualquer arquivo
            
            
            $mail->isHTML(true);
            $mail->Subject = 'PDF';
            //$mail->Body = 'qualquer duvida,entre em contato com o suporte'; aqui pode conter tags html no altbody não
            $mail->AltBody = 'Email para exemploNome';
            
            $mail->send();
            if(!$mail->Send()){
                echo  'Message has been sent';
            } else{
                 echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
    
    } catch(Exception $e) {
         echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
    
    
?>