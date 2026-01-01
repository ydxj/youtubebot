<?php
date_default_timezone_set('Africa/Casablanca');
$config = json_decode(file_get_contents('config.json'),1);
$id = $config['id'];
$token = $config['token'];
$config['filter'] = $config['filter'] != null ? $config['filter'] : 1;
$screen = file_get_contents('screen');

// Kill previous process (Windows compatible)
if(file_exists($screen . 'pid')){
    $oldPid = file_get_contents($screen . 'pid');
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        exec("taskkill /F /PID $oldPid 2>nul");
    } else {
        exec('kill -9 ' . $oldPid);
    }
}
file_put_contents($screen . 'pid', getmypid());
include 'index.php';
$accounts = json_decode(file_get_contents('accounts.json') , 1);

// Check if account exists
if(!isset($accounts[$screen])){
    bot('sendMessage',[
        'chat_id'=>$id,
        'text'=>"*Error:* Account not found in accounts.json",
        'parse_mode'=>'markdown'
    ]);
    exit("Account $screen not found\n");
}

// Check if username file exists
if(!file_exists($screen)){
    bot('sendMessage',[
        'chat_id'=>$id,
        'text'=>"*Error:* No usernames to check. Please collect usernames first using the grabber menu.",
        'parse_mode'=>'markdown'
    ]);
    exit("Username file $screen not found. Collect usernames first.\n");
}

$cookies = $accounts[$screen]['cookies'];
$useragent = $accounts[$screen]['useragent'];
$users = explode("\n", file_get_contents($screen));
$users = array_filter(array_map('trim', $users)); // Remove empty lines

// Debug to file
$debugLog = 'start_debug.log';
file_put_contents($debugLog, "=== Starting check ===\n", FILE_APPEND);
file_put_contents($debugLog, "Account: $screen\n", FILE_APPEND);
file_put_contents($debugLog, "Total users: " . count($users) . "\n", FILE_APPEND);
file_put_contents($debugLog, "Cookies length: " . strlen($cookies) . "\n", FILE_APPEND);

echo "Account: $screen\n";
echo "Total users to check: " . count($users) . "\n";
echo "Cookies length: " . strlen($cookies) . "\n";

$uu = explode(':', $screen) [0];
$se = 100;
$i = 0;
$gmail = 0;
$hotmail = 0;
$yahoo = 0;
$mailru = 0;
$true = 0;
$false = 0;
$edit = bot('sendMessage',[
    'chat_id'=>$id,
    'text'=>"- *Status:*\nTotal users: " . count($users),
    'parse_mode'=>'markdown',
    'reply_markup'=>json_encode([
            'inline_keyboard'=>[
                [['text'=>'Checked: '.$i,'callback_data'=>'fgf']],
                [['text'=>'On User: Starting...','callback_data'=>'fgdfg']],
                [['text'=>"Gmail: $gmail",'callback_data'=>'dfgfd'],['text'=>"Yahoo: $yahoo",'callback_data'=>'gdfgfd']],
                [['text'=>'MailRu: '.$mailru,'callback_data'=>'fgd'],['text'=>'Hotmail: '.$hotmail,'callback_data'=>'ghj']],
                [['text'=>'True: '.$true,'callback_data'=>'gj']],
                [['text'=>'False: '.$false,'callback_data'=>'dghkf']]
            ]
        ])
]);
$se = 100;
$editAfter = 5; // update UI every few users

file_put_contents($debugLog, "Starting foreach loop...\n", FILE_APPEND);

foreach ($users as $user) {
    $user = trim($user);
    if(empty($user)) continue; // Skip empty usernames
    
    file_put_contents($debugLog, "Checking user: $user\n", FILE_APPEND);
    echo "Checking user: $user\n";
    
    try {
        $info = getInfo($user, $cookies, $useragent);
        
        file_put_contents($debugLog, "Info result: " . ($info ? json_encode($info) : "False") . "\n", FILE_APPEND);
        echo "Info result: " . ($info ? "Found" : "False") . "\n";
        
        if($info && isset($info['mail'])){
            file_put_contents($debugLog, "Email found: " . $info['mail'] . "\n", FILE_APPEND);
            echo "Email found: " . $info['mail'] . "\n";
        }
    } catch (Exception $e) {
        file_put_contents($debugLog, "Error: " . $e->getMessage() . "\n", FILE_APPEND);
    }
    
    $i++; // Increment counter for each user checked

    if ($info != false && isset($info['mail'])) {
        $mail = trim($info['mail']);
        $usern = $info['user'];
        $e = explode('@', $mail);
        
        echo "Processing email: $mail\n";
        file_put_contents($debugLog, "Processing email: $mail\n", FILE_APPEND);
        
        if (preg_match('/(live|hotmail|outlook|yahoo)\.(.*)|(gmail)\.(com)|(mail|bk|yandex|inbox|list)\.(ru)/i', $mail,$m)) {
            echo "Email matches valid domain, checking availability...\n";
            file_put_contents($debugLog, "Email domain valid, calling checkMail...\n", FILE_APPEND);
            
            $mailCheckStart = microtime(true);
            $mailIsAvailable = checkMail($mail);
            $mailCheckTime = round((microtime(true) - $mailCheckStart) * 1000);
            
            echo "Mail check completed in {$mailCheckTime}ms - Result: " . ($mailIsAvailable ? 'Available' : 'Taken') . "\n";
            file_put_contents($debugLog, "checkMail returned: " . ($mailIsAvailable ? 'true' : 'false') . " (took {$mailCheckTime}ms)\n", FILE_APPEND);
            
            if($mailIsAvailable){
                echo "Checking if email is on Instagram...\n";
                file_put_contents($debugLog, "Calling inInsta...\n", FILE_APPEND);
                
                $inInstaStart = microtime(true);
                $inInsta = inInsta($mail);
                $inInstaTime = round((microtime(true) - $inInstaStart) * 1000);
                
                echo "inInsta check completed in {$inInstaTime}ms - Result: " . ($inInsta !== false ? 'Found' : 'Not found') . "\n";
                file_put_contents($debugLog, "inInsta returned: " . ($inInsta !== false ? json_encode($inInsta) : 'false') . " (took {$inInstaTime}ms)\n", FILE_APPEND);
                
                if ($inInsta !== false) {
                    // if($config['filter'] <= $follow){
                        echo "True - $user - " . $mail . "\n";
                        file_put_contents($debugLog, "HIT FOUND: $user - $mail\n", FILE_APPEND);
                        
                        if(strpos($mail, 'gmail.com')){
                            $gmail += 1;
                        } elseif(strpos($mail, 'hotmail.') or strpos($mail,'outlook.') or strpos($mail,'live.com')){
                            $hotmail += 1;
                        } elseif(strpos($mail, 'yahoo')){
                            $yahoo += 1;
                        } elseif(preg_match('/(mail|bk|yandex|inbox|list)\.(ru)/i', $mail)){
                            $mailru += 1;
                        }
                        $follow = $info['f'];
                        $following = $info['ff'];
                        $media = $info['m'];
                        $true += 1;
                                bot('sendMessage', ['disable_web_page_preview' => true, 'chat_id' => $id, 'text' => "تم صيد حساب جديد  ✅\n━━━━━━━━━━━━\n.❖. اليوزر : [$usern](instagram.com/$usern)\n.❖.  الايميل : [$mail]\n. عدد المتابعين : $follow\n.❖. عدد المتابعهم : $following\n.❖. عدد المنشورات : $media\n━━━━━━━━━━━━\nCH :- [@av_vva]",
                                
                                'parse_mode'=>'markdown']);
                                
                                bot('editMessageReplyMarkup',[
                                    'chat_id'=>$id,
                                    'message_id'=>$edit->result->message_id,
                                    'reply_markup'=>json_encode([
                                        'inline_keyboard'=>[
                                            [['text'=>'Checked: '.$i,'callback_data'=>'fgf']],
                                            [['text'=>'On User: '.$user,'callback_data'=>'fgdfg']],
                                            [['text'=>"Gmail: $gmail",'callback_data'=>'dfgfd'],['text'=>"Yahoo: $yahoo",'callback_data'=>'gdfgfd']],
                                            [['text'=>'MailRu: '.$mailru,'callback_data'=>'fgd'],['text'=>'Hotmail: '.$hotmail,'callback_data'=>'ghj']],
                                            [['text'=>'True: '.$true,'callback_data'=>'gj']],
                                            [['text'=>'False: '.$false,'callback_data'=>'dghkf']]
                                        ]
                                    ])
                                ]);
                            // } else {
                            //     echo "Filter , ".$mail.PHP_EOL;
                            // }
                            
                        } else {
                          echo "No Rest $mail\n";
                          file_put_contents($debugLog, "inInsta returned false - not on Instagram\n", FILE_APPEND);
                          $false += 1;
                        }
                    } else {
                        echo "Not Vaild 2 - $mail\n";
                        file_put_contents($debugLog, "checkMail returned false - email exists/taken\n", FILE_APPEND);
                        $false += 1;
                    }
        } else {
          echo "BlackList - $mail\n";
          file_put_contents($debugLog, "Email domain blacklisted: $mail\n", FILE_APPEND);
          $false += 1;
        }
    } else {
        echo "Not Bussines - $user\n";
        file_put_contents($debugLog, "No email found for user\n", FILE_APPEND);
        $false += 1;
    }
    usleep(750000);
    if($i % $editAfter == 0){
        bot('editMessageReplyMarkup',[
            'chat_id'=>$id,
            'message_id'=>$edit->result->message_id,
            'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                    [['text'=>'Checked: '.$i,'callback_data'=>'fgf']],
                    [['text'=>'On User: '.$user,'callback_data'=>'fgdfg']],
                    [['text'=>"Gmail: $gmail",'callback_data'=>'dfgfd'],['text'=>"Yahoo: $yahoo",'callback_data'=>'gdfgfd']],
                    [['text'=>'MailRu: '.$mailru,'callback_data'=>'fgd'],['text'=>'Hotmail: '.$hotmail,'callback_data'=>'ghj']],
                    [['text'=>'True: '.$true,'callback_data'=>'gj']],
                    [['text'=>'False: '.$false,'callback_data'=>'dghkf']]
                ]
            ])
        ]);
        $editAfter += 1;
    }




}


