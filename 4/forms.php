<?php


if(!empty($_GET['action'])) {

    //Mysqli instead of PDO, because PDO on my local machine(5.9.11-3-MANJARO x86_64, Apache/2.4.46, PHP Version 7.2.33) is not initially enabled
    function db() {
        static $mysqli;
        if(empty($mysqli)) {
            $mysqli = new mysqli("localhost", "root", "root", "forms");
            if(!$mysqli)
                throw new Exception('Failed connection database');
        }
        return $mysqli;
    }

    function getKeys($secretKey) {
        return [
            'AES-256-CBC', //$encrypt_method
            hash('sha256', $secretKey), //$key
            substr(hash('sha256', 'secret'), 0, 16) //$iv
        ];
    }

    function iCrypt($str, $secretKey) {
        list($encrypt_method, $key, $iv) = getKeys($secretKey);
        return base64_encode(openssl_encrypt($str, $encrypt_method, $key, 0, $iv));
    }

    function iDecrypt($str, $secretKey) {
        list($encrypt_method, $key, $iv) = getKeys($secretKey);
        return openssl_decrypt(base64_decode($str), $encrypt_method, $key, 0, $iv);
    }


    class Handler {
        function add() {
            if(!empty($_POST['email']) && filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) {
                $email = $_POST['email'];
                $hashEmail = password_hash($email, PASSWORD_DEFAULT);

                //Users who entered a blank phone number will receive a e-mail message "You have not provided phone numbers."
                if(!empty($_POST['phone'])) {
                    $hashPhone = iCrypt($_POST['phone'], $email);
                    db()->query('INSERT INTO phones (email, phone) VALUES("'.$hashEmail.'", "'.$hashPhone.'")');
                }
            }
        }
        function retrieve() {
            if(!empty($_POST['email']) && filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) {
                $email = $_POST['email'];

                //Get phone numbers
                $phones = [];
                $res = db()->query('SELECT * FROM phones');
                while ($row = $res->fetch_assoc()) {
                    if(password_verify($email, $row['email'])) {
                        $phone = iDecrypt($row['phone'], $email);
                        if(!empty($phone))
                            $phones[] = '<li>'.$phone.'</li>';
                    }
                }

                //Send e-mail
                $message = empty($phones)
                    ? 'You have not provided phone numbers.'
                    : '<b>Your phone numbers:</b><ul>'.implode('',$phones).'</ul>';
                $to = $email;
                $subject = 'Your phone numbers';
                $headers = 'From: webmaster@example.com' . "\r\n" .
                    'Reply-To: webmaster@example.com' . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();
                mail($to, $subject, $message, $headers);
            }
        }
    }

    //Call action
    $handler = new Handler();
    $method = $_GET['action'];
    if(is_callable([$handler, $method]))
        $handler->$method();
}

include "forms.html";