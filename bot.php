<?php
date_default_timezone_set('Africa/Casablanca');
if(!file_exists('config.json')){
	$token = readline('Enter Token: ');
	$id = readline('Enter Id: ');
	file_put_contents('config.json', json_encode(['id'=>$id,'token'=>$token]));
	
} else {
		  $config = json_decode(file_get_contents('config.json'),1);
	$token = $config['token'];
	$id = $config['id'];
}

if(!file_exists('accounts.json')){
    file_put_contents('accounts.json',json_encode([]));
}
include 'index.php';
try {
	$callback = function ($update, $bot) {
		global $id;
		if($update != null){
		  $config = json_decode(file_get_contents('config.json'),1);
		  if (!isset($config['filter']) || $config['filter'] == null) {
			  $config['filter'] = 1;
		  }
		  if (!isset($config['for']) || $config['for'] == null) {
			  $config['for'] = 'حدد الحساب';
		  }
		  $accounts = json_decode(file_get_contents('accounts.json'),1);
		  // Always define $mid from context if possible
		  $mid = null;
		  if (isset($update->callback_query) && isset($update->callback_query->message->message_id)) {
			  $mid = $update->callback_query->message->message_id;
		  } elseif (isset($update->message) && isset($update->message->message_id)) {
			  $mid = $update->message->message_id;
		  }
		  if(isset($update->message)){
				$message = $update->message;
				$chatId = $message->chat->id;
				$text = $message->text;
				if($chatId == $id){
					if($text == '/start'){
              $bot->sendMessage([
                  'chat_id'=>$chatId,
                  'text'=>"اهلا بك عزيزي المشترك . \n - في اداة صيد المتاحات الخاصه بك. \n\n By ~ @zerhounicnal",
                  'reply_markup'=>json_encode([
                      'inline_keyboard'=>[
                          [['text'=>' اضافة حسابات','callback_data'=>'login']],
                          [['text'=>'ادارة اليوزرات والبحث','callback_data'=>'grabber']],
                          [['text'=>'بدأ الفحص','callback_data'=>'run'],['text'=>'ايقاف الفحص','callback_data'=>'stop']],
                          [['text'=>'حالة الاداة','callback_data'=>'status']],
                      ]
                  ])
              ]);   
          } elseif($text != null){
          	if($config['mode'] != null){
          		$mode = $config['mode'];
          		if($mode == 'addL'){
          			$ig = new ig(['file'=>'','account'=>['useragent'=>'Instagram 316.0.0.38.120 Android (30/11; 420dpi; 1080x2400; samsung; SM-G991B; o1s; exynos2100; en_US; 541974943)']]);
          			list($user,$pass) = explode(':',$text);
          			list($headers,$body) = $ig->login($user,$pass);
          			
          			// Debug: Log the response
          			file_put_contents('debug_login.txt', "Headers:\n" . print_r($headers, true) . "\n\nBody:\n" . $body . "\n\n", FILE_APPEND);
          			
          			$body = json_decode($body);
          			
          			// Debug: Log parsed body
          			file_put_contents('debug_login.txt', "Parsed Body:\n" . print_r($body, true) . "\n\n", FILE_APPEND);
          			
          			if(isset($body->message)){
          				if($body->message == 'challenge_required'){
							   $bot->sendMessage([
								   'chat_id'=>$chatId,
								   'parse_mode'=>'markdown',
								   'text'=>"*Error*. \n - Challenge Required"
							   ]);
						       } else {
							   $errorMsg = isset($body->message) ? $body->message : 'اليوزر او الرمز خاطئ';
							   $bot->sendMessage([
								   'chat_id'=>$chatId,
								   'parse_mode'=>'markdown',
								   'text'=>"*Error*.\n - ".$errorMsg
							   ]);
						       }
          			} elseif(isset($body->logged_in_user)) {
          				file_put_contents('debug_login.txt', "SUCCESS: logged_in_user found!\n", FILE_APPEND);
          				
          				$body = $body->logged_in_user;
          				
          				// Instagram now uses ig-set-* headers instead of Set-Cookie
          				$CookieStr = "";
          				
          				// Extract ig-set-x-mid
          				if(preg_match('/ig-set-x-mid:\s*([^\r\n]+)/i', $headers, $match)){
          					$CookieStr .= "mid=" . trim($match[1]) . "; ";
          				}
          				
          				// Extract ig-set-ig-u-ds-user-id
          				if(preg_match('/ig-set-ig-u-ds-user-id:\s*([^\r\n]+)/i', $headers, $match)){
          					$CookieStr .= "ds_user_id=" . trim($match[1]) . "; ";
          				}
          				
          				// Extract sessionid from ig-set-authorization
          				if(preg_match('/ig-set-authorization:\s*Bearer\s+IGT:2:([^\r\n]+)/i', $headers, $match)){
          					$token = trim($match[1]);
          					$decoded = json_decode(base64_decode($token));
          					if($decoded && isset($decoded->sessionid)){
          						$CookieStr .= "sessionid=" . $decoded->sessionid . "; ";
          					}
          				}
          				
          				// Extract ig-set-ig-u-rur (if present)
          				if(preg_match('/ig-set-ig-u-rur:\s*([^\r\n]+)/i', $headers, $match)){
          					$rur = trim($match[1]);
          					if(!empty($rur)){
          						$CookieStr .= "rur=" . $rur . "; ";
          					}
          				}
          				
          				// Extract csrftoken from ig-set-www-claim
          				if(preg_match('/x-ig-set-www-claim:\s*hmac\.([^\r\n]+)/i', $headers, $match)){
          					$CookieStr .= "csrftoken=" . trim($match[1]) . "; ";
          				}
          				
          				file_put_contents('debug_login.txt', "Cookies: " . $CookieStr . "\n", FILE_APPEND);
								  
          				$account = ['cookies'=>$CookieStr,'useragent'=>'Instagram 316.0.0.38.120 Android (30/11; 420dpi; 1080x2400; samsung; SM-G991B; o1s; exynos2100; en_US; 541974943)'];
          				
          				// Re-read accounts.json to get the latest data
          				$accounts = json_decode(file_get_contents('accounts.json'),1);
          				if(!is_array($accounts)){
          					$accounts = [];
          				}
          				
          				file_put_contents('debug_login.txt', "Before save - accounts count: " . count($accounts) . "\n", FILE_APPEND);
          				
          				$accounts[$user] = $account;
          				
          				file_put_contents('debug_login.txt', "After adding - accounts count: " . count($accounts) . "\n", FILE_APPEND);
          				file_put_contents('debug_login.txt', "Saving account with key: " . $user . "\n", FILE_APPEND);
          				
          				$saveResult = file_put_contents('accounts.json', json_encode($accounts, JSON_PRETTY_PRINT));
          				
          				file_put_contents('debug_login.txt', "Save result: " . $saveResult . " bytes written\n", FILE_APPEND);
          				file_put_contents('debug_login.txt', "Accounts.json content: " . file_get_contents('accounts.json') . "\n\n", FILE_APPEND);
          				
          				$mid = $config['mid'];
          				$bot->sendMessage([
          				      'parse_mode'=>'markdown',
          							'chat_id'=>$chatId,
          							'text'=>"*تم اضافة الحساب الى الاداة.*\n _Username_ : [$user](https://instagram.com/$user)\n_Account Name_ : _{$body->full_name}_",
												'reply_to_message_id'=>$mid		
          					]);
						  $keyboard = ['inline_keyboard'=>[
											[['text'=>"اضافة حساب جديد",'callback_data'=>'addL']]
													]];
							  foreach ($accounts as $account => $v) {
								  $keyboard['inline_keyboard'][] = [['text'=>$account,'callback_data'=>'ddd'],['text'=>"Logout",'callback_data'=>'del&'.$account]];
							  }
							  $keyboard['inline_keyboard'][] = [['text'=>'Main Page','callback_data'=>'back']];
							  if (!isset($mid)) {
								  if (isset($update->callback_query) && isset($update->callback_query->message->message_id)) {
									  $mid = $update->callback_query->message->message_id;
								  } elseif (isset($message->message_id)) {
									  $mid = $message->message_id;
								  } else {
									  $mid = 1;
								  }
							  }
							  // Ensure $mid is set for all usages below
							  if (!isset($mid) || !$mid) {
								  if (isset($update->callback_query) && isset($update->callback_query->message->message_id)) {
									  $mid = $update->callback_query->message->message_id;
								  } elseif (isset($message->message_id)) {
									  $mid = $message->message_id;
								  } else {
									  $mid = 1;
								  }
							  }
							  $bot->editMessageText([
								  'chat_id'=>$chatId,
								  'message_id'=>$mid,
								  'text'=>"Accounts Control Page.",
								  'reply_markup'=>json_encode($keyboard)
						  ]);
		              $config['mode'] = null;
		              $config['mid'] = null;
		              file_put_contents('config.json', json_encode($config));
          			}
          		}  elseif($mode == 'selectFollowers'){
          		  if(is_numeric($text)){
          		    bot('sendMessage',[
          		        'chat_id'=>$chatId,
          		        'text'=>"تم التعديل.",
          		        'reply_to_message_id'=>$config['mid']
          		    ]);
          		    $config['filter'] = $text;
          		    $bot->editMessageText([
                      'chat_id'=>$chatId,
                      'message_id'=>$mid,
                      'text'=>"اهلا بك عزيزي المشترك . \n - في اداة صيد المتاحات الخاصه بك. \n\n By ~ @omarzerhouni",
                  'reply_markup'=>json_encode([
                      'inline_keyboard'=>[
                          [['text'=>'اضافة حساب','callback_data'=>'login']],
                          [['text'=>'ادارة اليوزرات','callback_data'=>'grabber']],
                          [['text'=>'بدأ الفحص','callback_data'=>'run'],['text'=>'ايقاف الفحص','callback_data'=>'stop']],
                          [['text'=>'حالة الاداة','callback_data'=>'status']],
                      ]
                  ])
                  ]);
          		    $config['mode'] = null;
		              $config['mid'] = null;
		              file_put_contents('config.json', json_encode($config));
          		  } else {
          		    bot('sendMessage',[
          		        'chat_id'=>$chatId,
          		        'text'=>'- يرجى ارسال رقم فقط .'
          		    ]);
          		  }
          		} else {
          		  switch($config['mode']){
          		    case 'search': 
          		      $config['mode'] = null; 
          		      $config['words'] = $text;
          		      file_put_contents('config.json', json_encode($config));
          		      exec('screen -dmS gr php search.php');
          		      break;
          		      case 'followers': 
          		      $config['mode'] = null; 
          		      $config['words'] = $text;
          		      file_put_contents('config.json', json_encode($config));
          		      exec('screen -dmS gr php followers.php');
          		      break;
          		      case 'following': 
          		      $config['mode'] = null; 
          		      $config['words'] = $text;
          		      file_put_contents('config.json', json_encode($config));
          		      exec('screen -dmS gr php following.php');
          		      break;
          		      case 'hashtag': 
          		      $config['mode'] = null; 
          		      $config['words'] = $text;
          		      file_put_contents('config.json', json_encode($config));
          		      exec('screen -dmS gr php hashtag.php');
          		      break;
          		  }
          		}
          	}
          }
				} else {
					$bot->sendMessage([
							'chat_id'=>$chatId,
							'text'=>"🕸️|| نسخه صيد متاحات انستكرام خاصه
لشراء ملفات النسخه او تنصيب راسلنا . ",
							'reply_markup'=>json_encode([
                  'inline_keyboard'=>[
                      [['text'=>'🕸️|| المطور ','url'=>'t.me/omarzerhouni']]
                  ]
							])
					]);
				}
			} elseif(isset($update->callback_query)) {
          $chatId = $update->callback_query->message->chat->id;
          $mid = $update->callback_query->message->message_id;
          $data = $update->callback_query->data;
          echo $data;
          if($data == 'login'){
              
        		$keyboard = ['inline_keyboard'=>[
										[['text'=>"اضافة حساب جديد",'callback_data'=>'addL']]
									]];
		              foreach ($accounts as $account => $v) {
		                  $keyboard['inline_keyboard'][] = [['text'=>$account,'callback_data'=>'ddd'],['text'=>"تسجيل خروج",'callback_data'=>'del&'.$account]];
		              }
		              $keyboard['inline_keyboard'][] = [['text'=>'Main Page','callback_data'=>'back']];
		              $bot->editMessageText([
		                  'chat_id'=>$chatId,
		                  'message_id'=>$mid,
		                  'text'=>"Accounts Control Page.",
		                  'reply_markup'=>json_encode($keyboard)
		              ]);
          } elseif($data == 'addL'){
          	
          	$config['mode'] = 'addL';
          	$config['mid'] = $mid;
          	file_put_contents('config.json', json_encode($config));
          	$bot->sendMessage([
          			'chat_id'=>$chatId,
          			'text'=>"ارسل الحساب الوهمي : \n `يوزر:باسوورد`",
          			'parse_mode'=>'markdown'
          	]);
          } elseif($data == 'grabber'){
            
			$for = (isset($config['for']) && $config['for'] != null) ? $config['for'] : 'حدد الحساب';
			$count = file_exists($for) ? count(explode("\n", file_get_contents($for))) : 0;
            $bot->editMessageText([
                'chat_id'=>$chatId,
                'message_id'=>$mid,
                'text'=>"هذه ادارة اليوزرات والبحث. \n - اختر نوع الصيد : $count \n - For Account : $for",
                'reply_markup'=>json_encode([
                    'inline_keyboard'=>[
                        [['text'=>'من البحث','callback_data'=>'search']],
                        [['text'=>'من الهاشتاكات #','callback_data'=>'hashtag'],['text'=>'Explore .','callback_data'=>'explore']],
                        [['text'=>'من متابعين','callback_data'=>'followers'],['text'=>"من متابعهم",'callback_data'=>'following']],
                        [['text'=>"For Accounts : $for",'callback_data'=>'for']],
                        [['text'=>'لستة يوزرات جديده','callback_data'=>'newList'],['text'=>'بدأ لسته جديده','callback_data'=>'append']],
                        [['text'=>'خروج من الصفحه','callback_data'=>'back']],
                    ]
                ])
            ]);
          } elseif($data == 'search'){
            $bot->sendMessage([
                'chat_id'=>$chatId,
                'text'=>"ارسل الكلمات للبحث \n - مثال doe hitham name city pro photo"
            ]);
            $config['mode'] = 'search';
            file_put_contents('config.json', json_encode($config));
          } elseif($data == 'followers'){
            $bot->sendMessage([
                'chat_id'=>$chatId,
                'text'=>"Now Send Users to check out followers \n - You can send more than one user by putting a space between them"
            ]);
            $config['mode'] = 'followers';
            file_put_contents('config.json', json_encode($config));
          } elseif($data == 'following'){
            $bot->sendMessage([
                'chat_id'=>$chatId,
                'text'=>"اليك السحب من متابعهم \n - You ارسل اليوزرات للفحص"
            ]);
            $config['mode'] = 'following';
            file_put_contents('config.json', json_encode($config));
          } elseif($data == 'hashtag'){
            $bot->sendMessage([
                'chat_id'=>$chatId,
                'text'=>"ارسل هاشتاك واحد للبحث # \n 0 You can only send one."
            ]);
            $config['mode'] = 'hashtag';
            file_put_contents('config.json', json_encode($config));
          } elseif($data == 'newList'){
            file_put_contents('a','new');
            $bot->answerCallbackQuery([
							'callback_query_id'=>$update->callback_query->id,
							'text'=>"Done Select New List.",
							'show_alert'=>1
						]);
          } elseif($data == 'append'){ 
            file_put_contents('a', 'ap');
            $bot->answerCallbackQuery([
							'callback_query_id'=>$update->callback_query->id,
							'text'=>"تم تحديد اللسته جديده.",
							'show_alert'=>1
						]);
						
          } elseif($data == 'for'){
            if(!empty($accounts)){
            $keyboard = [];
             foreach ($accounts as $account => $v) {
                $keyboard['inline_keyboard'][] = [['text'=>$account,'callback_data'=>'forg&'.$account]];
              }
              $bot->editMessageText([
                  'chat_id'=>$chatId,
                  'message_id'=>$mid,
                  'text'=>"Select Account.",
                  'reply_markup'=>json_encode($keyboard)
              ]);
            } else {
              $bot->answerCallbackQuery([
							'callback_query_id'=>$update->callback_query->id,
							'text'=>"Add Account First.",
							'show_alert'=>1
						]);
            }
          } elseif($data == 'selectFollowers'){
            bot('sendMessage',[
                'chat_id'=>$chatId,
                'text'=>'قم بأرسال عدد متابعين .'  
            ]);
            $config['mode'] = 'selectFollowers';
          	$config['mid'] = $mid;
          	file_put_contents('config.json', json_encode($config));
          } elseif($data == 'run'){
            if(!empty($accounts)){
            $keyboard = [];
             foreach ($accounts as $account => $v) {
                $keyboard['inline_keyboard'][] = [['text'=>$account,'callback_data'=>'start&'.$account]];
              }
              $bot->editMessageText([
                  'chat_id'=>$chatId,
                  'message_id'=>$mid,
                  'text'=>"Select Account.",
                  'reply_markup'=>json_encode($keyboard)
              ]);
            } else {
              $bot->answerCallbackQuery([
							'callback_query_id'=>$update->callback_query->id,
							'text'=>"Add Account First.",
							'show_alert'=>1
						]);
            }
          }elseif($data == 'stop'){
            if(!empty($accounts)){
            $keyboard = [];
             foreach ($accounts as $account => $v) {
                $keyboard['inline_keyboard'][] = [['text'=>$account,'callback_data'=>'stop&'.$account]];
              }
              $bot->editMessageText([
                  'chat_id'=>$chatId,
                  'message_id'=>$mid,
                  'text'=>"Select Account.",
                  'reply_markup'=>json_encode($keyboard)
              ]);
            } else {
              $bot->answerCallbackQuery([
							'callback_query_id'=>$update->callback_query->id,
							'text'=>"Add Account First.",
							'show_alert'=>1
						]);
            }
          }elseif($data == 'stopgr'){
            shell_exec('screen -S gr -X quit');
            $bot->answerCallbackQuery([
							'callback_query_id'=>$update->callback_query->id,
							'text'=>"انتهى تخمين اليوزرات اذهب للفحص.",
						// 	'show_alert'=>1
						]);
						  $for = (isset($config['for']) && $config['for'] != null) ? $config['for'] : 'Select Account';
						  $count = file_exists($for) ? count(explode("\n", file_get_contents($for))) : 0;
						$bot->editMessageText([
                'chat_id'=>$chatId,
                'message_id'=>$mid,
                'text'=>"لستة اليوزرات المخمنه. \n - اليوزرات : $count \n - For Account : $for",
                'reply_markup'=>json_encode([
                    'inline_keyboard'=>[
                        [['text'=>'من البحث','callback_data'=>'search']],
                        [['text'=>'من الهاشتاك #','callback_data'=>'hashtag'],['text'=>'Explore .','callback_data'=>'explore']],
                        [['text'=>'من متابعين','callback_data'=>'followers'],['text'=>"من المتابعهم",'callback_data'=>'following']],
                        [['text'=>"For Accounts : $for",'callback_data'=>'for']],
                        [['text'=>'لستة جديده','callback_data'=>'newList'],['text'=>'ضبط اللسته جديده','callback_data'=>'append']],
                        [['text'=>'الخروج من الصفحه','callback_data'=>'back']],
                    ]
                ])
            ]);
          } elseif($data == 'explore'){
            exec('screen -dmS gr php explore.php');
          } elseif($data == 'status'){
					$status = '';
					foreach($accounts as $account => $ac){
						$c = explode(':', $account)[0];
						$x = exec('screen -S '.$c.' -Q select . ; echo $?');
						if($x == '0'){
				        $status .= "*$account* ~> _Working_\n";
				    } else {
				        $status .= "*$account* ~> _Stop_\n";
				    }
					}
					$bot->sendMessage([
							'chat_id'=>$chatId,
							'text'=>"حالة الحساب: \n\n $status",
							'parse_mode'=>'markdown'
						]);
				} elseif($data == 'back'){
          	$bot->editMessageText([
                      'chat_id'=>$chatId,
                      'message_id'=>$mid,
                      'text'=>"اهلا بك عزيزي المشترك . \n - في اداة صيد المتاحات الخاصه بك. \n\n By ~ @omarzerhouni",
                  'reply_markup'=>json_encode([
                      'inline_keyboard'=>[
                          [['text'=>'اضافة حسابات','callback_data'=>'login']],
                          [['text'=>'ادارة اليوزرات','callback_data'=>'grabber']],
                          [['text'=>'بدأ الفحص','callback_data'=>'run'],['text'=>'ايقاف الفحص','callback_data'=>'stop']],
                          [['text'=>'حالة الاداة','callback_data'=>'status']],
                      ]
                  ])
                  ]);
          } else {
          	$data = explode('&',$data);
          	if($data[0] == 'del'){
          		
          		unset($accounts[$data[1]]);
          		file_put_contents('accounts.json', json_encode($accounts));
              $keyboard = ['inline_keyboard'=>[
							[['text'=>"Add New Accounts",'callback_data'=>'addL']]
									]];
		              foreach ($accounts as $account => $v) {
		                  $keyboard['inline_keyboard'][] = [['text'=>$account,'callback_data'=>'ddd'],['text'=>"Logout",'callback_data'=>'del&'.$account]];
		              }
		              $keyboard['inline_keyboard'][] = [['text'=>'Main Page','callback_data'=>'back']];
		              $bot->editMessageText([
		                  'chat_id'=>$chatId,
		                  'message_id'=>$mid,
		                  'text'=>"صفحة ادارة الحسابات.",
		                  'reply_markup'=>json_encode($keyboard)
	              ]);
          	} elseif($data[0] == 'forg'){
          	  $config['for'] = $data[1];
          	  file_put_contents('config.json',json_encode($config));
              $for = $config['for'] != null ? $config['for'] : 'Select';
              // Check if file exists before trying to read it
              if(file_exists($for) && is_file($for)){
                  $count = count(explode("\n", file_get_contents($for)));
              } else {
                  $count = 0;
              }
              $bot->editMessageText([
                'chat_id'=>$chatId,
                'message_id'=>$mid,
                'text'=>"عدد اليوزرات المخمنه. \n - اليوزرات : $count \n - في الحساب : $for",
                'reply_markup'=>json_encode([
                    'inline_keyboard'=>[
                        [['text'=>'من البحث','callback_data'=>'search']],
                        [['text'=>'From Hashtag #','callback_data'=>'hashtag'],['text'=>'Explore .','callback_data'=>'explore']],
                        [['text'=>'من متابعين','callback_data'=>'followers'],['text'=>"من المتابعهم",'callback_data'=>'following']],
                        [['text'=>"الحساب المحدد : $for",'callback_data'=>'for']],
                        [['text'=>'لسته جديده','callback_data'=>'newList'],['text'=>'تأكيد اللسته الجديده','callback_data'=>'append']],
                        [['text'=>'الخروج من الصفحه','callback_data'=>'back']],
                    ]
                ])
            ]);
          	} elseif($data[0] == 'start'){
          	  file_put_contents('screen', $data[1]);
          	  $bot->editMessageText([
                      'chat_id'=>$chatId,
                      'message_id'=>$mid,
                      'text'=>"Welcome . \n - To Your IG Bussines Tool. \n\n By ~ @omarzerhouni",
                  'reply_markup'=>json_encode([
                      'inline_keyboard'=>[
                          [['text'=>'اضافة الحسابات','callback_data'=>'login']],
                          [['text'=>'ادارة اليوزرات','callback_data'=>'grabber']],
                          [['text'=>'بدأ الفحص','callback_data'=>'run'],['text'=>'ايقاف الفحص','callback_data'=>'stop']],
                          [['text'=>'حالة الاداة','callback_data'=>'status']],
                      ]
                  ])
                  ]);
              // Windows compatible background execution
              if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                  pclose(popen("start /B php start.php", "r"));
              } else {
                  exec('screen -dmS '.explode(':',$data[1])[0].' php start.php');
              }
              $bot->sendMessage([
                'chat_id'=>$chatId,
                'text'=>"*جار بدأ الفحص.*\n Account: `".explode(':',$data[1])[0].'`',
                'parse_mode'=>'markdown'
              ]);
          	} elseif($data[0] == 'stop'){
          	  $bot->editMessageText([
                      'chat_id'=>$chatId,
                      'message_id'=>$mid,
                      'text'=>"اهلا بك عزيزي المشترك . \n - في اداة صيد المتاحات الخاصه بك. \n\n By ~ @omarzerhouni",
                      'reply_markup'=>json_encode([
                      'inline_keyboard'=>[
                          [['text'=>'اضافة الحسابات','callback_data'=>'login']],
                          [['text'=>'ادارة اليوزرات','callback_data'=>'grabber']],
                          [['text'=>'بدأ الفحص','callback_data'=>'run'],['text'=>'ايقاف الفحص','callback_data'=>'stop']],
                          [['text'=>'حالة الاداة','callback_data'=>'status']],
                       ]
                  ])
               ]);
              exec('screen -S '.explode(':',$data[1])[0].' -X quit');
          	}
          }
			}
		}
	};
	$bot = new EzTG(array('throw_telegram_errors'=>false,'token' => $token, 'callback' => $callback));
} catch(Exception $e){
	echo $e->getMessage().PHP_EOL;
	sleep(1);
}
