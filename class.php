<?php
include 'index.php';
class Zerhouni{
public function Gmail($UseName){
    $UseName = $UseName."@gmail.com";
    $ch = curl_init("https://mail.google.com/mail/gxlu?email=$UseName"); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $response = curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    curl_close($ch);
    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $header, $matches);
    $cookies = array();
    foreach($matches[1] as $item) {
        parse_str($item, $cookie);
        $cookies = array_merge($cookies, $cookie);
    }
    if(empty($cookies)){
        return true;
    }else{
        return false;
    }
}
    public function Yahoo($UseName)
    {
        $mail = 0;
        $user = $mail;
        @mkdir("Info");
        $c = curl_init("https://login.ya.com/"); 
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, true); 
        curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36"); 
        curl_setopt($c, CURLOPT_REFERER, 'https://www.google.com'); 
        curl_setopt($c, CURLOPT_ENCODING, 'gzip, deflate, br');  
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($c, CURLOPT_HEADER, true); 
        curl_setopt($c, CURLOPT_COOKIEJAR, "Info/cookie.txt"); 
        curl_setopt($c, CURLOPT_COOKIEFILE, "Info/cookie.txt"); 
        $response = curl_exec($c); 
        $httpcode = curl_getinfo($c); 
        $header = substr($response, 0, curl_getinfo($c, CURLINFO_HEADER_SIZE)); 
        $body = substr($response, curl_getinfo($c, CURLINFO_HEADER_SIZE)); 
        preg_match_all('#name="crumb" value="(.*?)" />#', $response, $crumb); 
        preg_match_all('#name="acrumb" value="(.*?)" />#', $response, $acrumb); 
        preg_match_all('#name="config" value="(.*?)" />#', $response, $config); 
        preg_match_all('#name="sessionIndex" value="(.*?)" />#', $response, $sesindex); 
        $data['status'] = "ok"; 
        $data['crumb'] = isset($crumb[1][0]) ? $crumb[1][0] : ""; 
        $data['acrumb'] = $acrumb[1][0]; 
        $data['config'] = isset($config[1][0]) ? $config[1][0] : ""; 
        $data['sesindex'] = $sesindex[1][0]; 
        $crumb = trim($data['crumb']); 
        $acrumb = trim($data['acrumb']); 
        $config = trim($data['config']); 
        $sesindex = trim($data['sesindex']); 
        $header = array(); 
        $header[] = "Host: login.yahoo.com"; 
        $header[] = "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:56.0) Gecko/20100101 Firefox/56.0"; 
        $header[] = "Accept: */*"; 
        $header[] = "Accept-Language: en-US,en;q=0.5"; 
        $header[] = "content-type: application/x-www-form-urlencoded; charset=UTF-8"; 
        $header[] = "X-Requested-With: XMLHttpRequest"; 
        $header[] = "Referer: https://login.yahoo.com/"; 
        $header[] = "Connection: keep-alive"; 
        $data = "acrumb=$acrumb&sessionIndex=$sesindex&username=".urlencode($user)."&passwd=&signin=Next"; 
        $c = curl_init("https://login.yahoo.com/"); 
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, true); 
        curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36"); 
        curl_setopt($c, CURLOPT_REFERER, 'https://login.yahoo.com/'); 
        curl_setopt($c, CURLOPT_ENCODING, 'gzip, deflate, br');  
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($c, CURLOPT_HTTPHEADER, $header); 
        curl_setopt($c, CURLOPT_COOKIEJAR, "Info/cookie.txt"); 
        curl_setopt($c, CURLOPT_COOKIEFILE, "Info/cookie.txt"); 
        curl_setopt($c, CURLOPT_POSTFIELDS, $data); 
        curl_setopt($c, CURLOPT_POST, 1); 
        $b = curl_exec($c); 
        if(strstr($b,"INVALID_USERNAME")){
            return true;
        }else{
            return false;
        }
}
    public function verifyEmail($mail)
    {
        $fromemail = 'yasali517@gmail.com';
        $details = '';

        // Remove all illegal characters from email
        $email = filter_var($mail, FILTER_SANITIZE_EMAIL);
        // Validate e-mail
        if (filter_var($mail, FILTER_VALIDATE_EMAIL))
        {
            // Get the domain of the email recipient
            $email_arr = explode('@', $mail);
            if ($email_arr[1] != 'yahoo.com')
            {
                $domain = array_slice($email_arr, -1);
                $domain = $domain[0];

                // Trim [ and ] from beginning and end of domain string, respectively
                $domain = ltrim($domain, '[');
                $domain = rtrim($domain, ']');

                if ('IPv6:' == substr($domain, 0, strlen('IPv6:')))
                {
                    $domain = substr($domain, strlen('IPv6') + 1);
                }

                $mxhosts = array();
                // Check if the domain has an IP address assigned to it
                if (filter_var($domain, FILTER_VALIDATE_IP))
                {
                    $mx_ip = $domain;
                }
                else
                {
                    // If no IP assigned, get the MX records for the host name
                    getmxrr($domain, $mxhosts, $mxweight);
                }

                if (!empty($mxhosts))
                {
                    $mx_ip = $mxhosts[array_search(min($mxweight) , $mxhosts) ];
                }
                else
                {
                    // If MX records not found, get the A DNS records for the host
                    if (filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
                    {
                        $record_a = dns_get_record($domain, DNS_A);
                        // else get the AAAA IPv6 address record
                        
                    }
                    elseif (filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
                    {
                        $record_a = dns_get_record($domain, DNS_AAAA);
                    }

                    if (!empty($record_a))
                    {
                        $mx_ip = $record_a[0]['ip'];
                    }
                    else
                    {
                        $mxhosts = array();
                        // Check if the domain has an IP address assigned to it
                        $domain = 'mail.' . $domain;
                        if (filter_var($domain, FILTER_VALIDATE_IP))
                        {
                            $mx_ip = $domain;
                        }
                        else
                        {
                            // If no IP assigned, get the MX records for the host name
                            getmxrr($domain, $mxhosts, $mxweight);
                        }

                        if (!empty($mxhosts))
                        {
                            $mx_ip = $mxhosts[array_search(min($mxweight) , $mxhosts) ];
                        }
                        else
                        {
                            // If MX records not found, get the A DNS records for the host
                            if (filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
                            {
                                $record_a = dns_get_record($domain, DNS_A);
                                // else get the AAAA IPv6 address record
                                
                            }
                            elseif (filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
                            {
                                $record_a = dns_get_record($domain, DNS_AAAA);
                            }

                            if (!empty($record_a))
                            {
                                $mx_ip = $record_a[0]['ip'];
                            }
                            else
                            {
                                $domain = 'email.' . $domain;
                                $mxhosts = array();
                                // Check if the domain has an IP address assigned to it
                                if (filter_var($domain, FILTER_VALIDATE_IP))
                                {
                                    $mx_ip = $domain;
                                }
                                else
                                {
                                    // If no IP assigned, get the MX records for the host name
                                    getmxrr($domain, $mxhosts, $mxweight);
                                }

                                if (!empty($mxhosts))
                                {
                                    $mx_ip = $mxhosts[array_search(min($mxweight) , $mxhosts) ];
                                }
                                else
                                {
                                    // If MX records not found, get the A DNS records for the host
                                    if (filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
                                    {
                                        $record_a = dns_get_record($domain, DNS_A);
                                        // else get the AAAA IPv6 address record
                                        
                                    }
                                    elseif (filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
                                    {
                                        $record_a = dns_get_record($domain, DNS_AAAA);
                                    }

                                    if (!empty($record_a))
                                    {
                                        $mx_ip = $record_a[0]['ip'];
                                    }
                                    else
                                    {
                                        // Exit the program if no MX records are found for the domain host
                                        $result = 'fail';
                                        $details .= 'No suitable MX records found';

                                        return false;
                                    }
                                }

                            }
                        }

                    }
                }

                // Open a socket connection with the hostname, smtp port 25
                $connect = @fsockopen($mx_ip, 25);

                if ($connect)
                {

                    // Initiate the Mail Sending SMTP transaction
                    if (preg_match('/^220/i', $out = fgets($connect, 1024)))
                    {

                        // Send the HELO command to the SMTP server
                        fputs($connect, "HELO $mx_ip\r\n");
                        $out = fgets($connect, 1024);

                        // Send an SMTP Mail command from the sender's email address
                        fputs($connect, "MAIL FROM: <$fromemail>\r\n");
                        $from = fgets($connect, 1024);

                        // Send the SCPT command with the recepient's email address
                        fputs($connect, "RCPT TO: <$mail>\r\n");
                        $to = fgets($connect, 1024);
                        $details .= $to . "\n";
                        $test = $to;

                        // Close the socket connection with QUIT command to the SMTP server
                        fputs($connect, 'QUIT');
                        fclose($connect);
                    }
                }
                if (!strpos($details, 'OK'))
                {
                    if($email_arr[1] == "gmail.com"){
                        return $this->Gmail($email_arr[0]);
                    }else{
                        return true;
                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return $this->Yahoo($email_arr[0]);
            }
        }

    }
}
?>