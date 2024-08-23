<?php
date_default_timezone_set('Africa/Casablanca');
if(!file_exists('config.json')){
	$token = readline('-Hi OMAR Enter Token: ');
	$id = readline('-Hi OMAR  Enter Id: ');
	file_put_contents('config.json', json_encode(['id'=>$id,'token'=>$token]));
	
} else {
		  $config = json_decode(file_get_contents('config.json'),1);
	$token = $config['token'];
	$id = $config['id'];
}

if(!file_exists('accounts.json')){
    file_put_contents('accounts.json',json_encode([]));
}

$fackfor = "fack For everyone who steals my file or changes rights\n";
    $fackfor2 = "HELLO TO MY TOOL";
    echo $fackfor;
    echo $fackfor2;

include 'index.php';
try {
	$callback = function ($update, $bot) {
		global $id;
		if($update != null){
		  $config = json_decode(file_get_contents('config.json'),1);
		  $config['filter'] = $config['filter'] != null ? $config['filter'] : 1;
          $accounts = json_decode(file_get_contents('accounts.json'),1);
			if(isset($update->message)){
				$message = $update->message;
				$chatId = $message->chat->id;
				$text = $message->text;
				if($chatId == $id){
					if($text == '/start'){
              $bot->sendphoto([ 'chat_id'=>$chatId,
              'photo'=>"https://t.me/omarzerhouni",
                   'caption'=>'التفعيل بواسطة المطور omarzerhouni@
~ @omarzerhouni ',
                  'inline_keyboard'=>true,
                  'reply_markup'=>json_encode([
                         'keyboard'=>[
                          [['text'=>'- English 🇺🇸']],
                          [['text'=>'- عربي🇮🇶']],
                          [['text'=>'- فيديو شرح للبوت 📷']],
                          [['text'=>'- طرق الصيد 📻']],
                          [['text'=>'-Zerhouni ̇༗']],
                          [['text'=>'️ما هوا بوت صيد والمتاحات 🔥']],
                          [['text'=>'️- فيديو تخطي حظر وهمي الانستقرام 🎥']],
                          [['text'=>'️- فيديو طريقه توصيل الريست 🗡️']],
                          [['text'=>'️تطبيق توصيل الريست✝️']],
                      ]
                  ])
              ]);   
            } if($text == '- English 🇺🇸'){ 
        	$config['filter'] = $text;
		    $bot->sendMessage([
		       'chat_id'=>$chatId,
                'message_id'=>$mid,
                'text'=>"Zerhouni ~ @omarzerhouni",
                'reply_markup'=>json_encode([
                      'inline_keyboard'=>[
                          [['text'=>'✨┇ Add Accounts🛗','callback_data'=>'login']],
                          [['text'=>'🕵️┇ Geting users','callback_data'=>'grabber']],
                          [['text'=>'📳┇ Start Checking','callback_data'=>'run'],['text'=>' 📴┇Stop Checking','callback_data'=>'stop']],
                          [['text'=>'🪐┇Accounts Status','callback_data'=>'status']],
                      ]
                  ])
               ]);
           } if($text == '- عربي🇮🇶'){
            $config['filter'] = $text;
		    $bot->sendMessage([
		       'chat_id'=>$chatId,
                'message_id'=>$mid,
                'text'=>"Zerhouni ~ @omarzerhouni",
                'reply_markup'=>json_encode([
                      'inline_keyboard'=>[
                          [['text'=>'تسجيل حساب ✨┇','callback_data'=>'login']],
                          [['text'=>'طرق الصيد 🕵️┇','callback_data'=>'grabber']],
                          [['text'=>'بدء الصيد 📳┇','callback_data'=>'run'],['text'=>'ايقاف الصيد 📴┇','callback_data'=>'stop']],
                          [['text'=>' حاله الحسابات 🪐┇','callback_data'=>'status']],
                      ]
                  ])
               ]);
               
               } if($text == '- فيديو شرح للبوت 📷'){
                  $bot->sendphoto([ 
                  'chat_id'=>$chatId,
                  'photo'=>"https://t.me/Zerhounicnal/50",

              ]);   
                
               } if($text == '- طرق الصيد 📻'){
                 $bot->sendvoice([
                  'chat_id'=>$chatId,
                  'voice'=>"https://t.me/FOLLOW_NAFSEA/147",
                  
              ]);   
                
              } if($text == '- طرق الصيد 📻'){
                $bot->sendvoice([ 
                 'chat_id'=>$chatId,
                 'voice'=>"https://t.me/FOLLOW_NAFSEA/160",

              ]);   
                
           } if($text == '-Zerhouni ̇༗'){ 
            $bot->sendMessage([
		       'chat_id'=>$chatId,
                'message_id'=>$mid,
                'text'=>".🔫.
𝐓𝐞𝐥𝐞 : @omarzerhouni |",

              ]);   
              
           } if($text == '️- ما هوا بوت صيد والمتاحات 🔥'){ 
            $bot->sendMessage([
         'chat_id'=>$chatId,
                'message_id'=>$mid,
                'text'=>"قبل ان اعلمك كيف تقوم بصيد المتاحات يجب عليك ان تعرف ما هي المتاحات
*
*
*
- المتاحات هي : حسابات انستقرام او فيس بوك او تويتر او .......  هذه الحسابات تكون مربوط بايميلات لكن هذه الايميلات غير موجود
*
*
*
كيف تربط الايميلات بهذه الحسابات اذ لم تكن موجودة اصلن . تربط هذه الايميلات بالحسابات لاكن عند ربطها لا تقوم بطلب كود للتحقق من البريد

*
*
*
- طريقة عمل بوت الصيد

طريقة عمل بوت الصيد هيه انك تقوم بجمع يوزرات من الانستقرام ووضعها بالبوت ويقوم بالتخمين على المتابعين او الذين يقوم بمتابعتهم حسب اختيار بالطبع هناك طرق اخرى للسحب عبر هاشتاق متلا او كلمات بالبحث
",

              ]);
              
           } if($text == '️- فيديو تخطي حظر وهمي الانستقرام 🎥'){
                  $bot->sendphoto([ 
                  'chat_id'=>$chatId,                  'photo'=>"https://t.me/SUPERX1/8",

              ]);
              
              } if($text == '️- فيديو طريقه توصيل الريست 🗡️'){
                  $bot->sendphoto([ 
                  'chat_id'=>$chatId,                  'photo'=>"https://t.me/HH7H9/7146",
				  'caption'=>'برنامج توصيل الريست 🌝
https://t.me/a011437/6',
              ]);
              
          } elseif($text != null){
          	if($config['mode'] != null){
          		$mode = $config['mode'];
          		if($mode == 'addL'){
          			$ig = new ig(['file'=>'','account'=>['useragent'=>'Instagram 27.0.0.7.97 Android (23/6.0.1; 640dpi; 1440x2392; LGE/lge; RS988; h1; h1; en_US)']]);
          			list($user,$pass) = explode(':',$text);
          			list($headers,$body) = $ig->login($user,$pass);
          			// echo $body;
          			$body = json_decode($body);
          			if(isset($body->message)){
          				if($body->message == 'challenge_required'){
          					$bot->sendMessage([
          							'chat_id'=>$chatId,
          							'parse_mode'=>'markdown',
          							'text'=>"*Error*. \n - Challenge Required"
          					]);
          				} else {
          					$bot->sendMessage([
          							'chat_id'=>$chatId,
          							'parse_mode'=>'markdown',
          							'text'=>"*Error*.\n - Incorrect Username Or Password"
          					]);
          				}
          			} elseif(isset($body->logged_in_user)) {
          				$body = $body->logged_in_user;
          				preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $headers, $matches);
								  $CookieStr = "";
								  foreach($matches[1] as $item) {
								      $CookieStr .= $item."; ";
								  }
          				$account = ['cookies'=>$CookieStr,'useragent'=>'Instagram 27.0.0.7.97 Android (23/6.0.1; 640dpi; 1440x2392; LGE/lge; RS988; h1; h1; en_US)'];
          				
          				$accounts[$text] = $account;
          				file_put_contents('accounts.json', json_encode($accounts));
          				$mid = $config['mid'];
          				$bot->sendMessage([
          				      'parse_mode'=>'markdown',
          							'chat_id'=>$chatId,
          							'text'=>"*Done Add New Accounts To Your Tool.*\n _Username_ : [$user])(instagram.com/$user)\n_Account Name_ : _{$body->full_name}_",
												'reply_to_message_id'=>$mid		
          					]);
          				$keyboard = ['inline_keyboard'=>[
										[['text'=>"Add New Accounts",'callback_data'=>'addL']]
									]];
		              foreach ($accounts as $account => $v) {
		                  $keyboard['inline_keyboard'][] = [['text'=>$account,'callback_data'=>'ddd'],['text'=>"Logout",'callback_data'=>'del&'.$account]];
		              }
		              $keyboard['inline_keyboard'][] = [['text'=>'🔙 رجوع','callback_data'=>'back']];
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
                'text'=>"Zerhouni ~ @omarzerhouni",
                'reply_markup'=>json_encode([
                      'inline_keyboard'=>[
                          [['text'=>'تسجيل حساب ✨┇','callback_data'=>'login']],
                          [['text'=>'طرق الصيد 🕵️┇','callback_data'=>'grabber']],
                          [['text'=>'بدء الصيد 📳┇','callback_data'=>'run'],['text'=>'ايقاف الصيد 📴┇','callback_data'=>'stop']],
                          [['text'=>' حاله الحسابات 🪐┇','callback_data'=>'status']],
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
					$bot->sendphoto([
       'chat_id'=>$chatId,
       'photo'=> "https://t.me/omarzerhouni",
        'caption'=>'البوت مدفوع 💲 و ليس مجاني 👁‍🗨
لشراء نسخه مراسلة المطور 👁‍🗨',
                  'reply_markup'=>json_encode([
                      'inline_keyboard'=>[
                          [['text'=>'▫️| مطور البوت','url'=>'t.me/omarzerhouni']],
                       [['text'=>"▪️| قناه صيد المشتركين", 'url'=>"t.me/Zerhounicnal"]],
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
										[['text'=>"Add New Accounts",'callback_data'=>'addL']]
									]];
		              foreach ($accounts as $account => $v) {
		                  $keyboard['inline_keyboard'][] = [['text'=>$account,'callback_data'=>'ddd'],['text'=>"Logout",'callback_data'=>'del&'.$account]];
		              }
		              $keyboard['inline_keyboard'][] = [['text'=>'🔙 رجوع','callback_data'=>'back']];
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
          			'text'=>"Send Account Like : \n `user:pass`",
          			'parse_mode'=>'markdown'
          	]);
          } elseif($data == 'grabber'){
            
            $for = $config['for'] != null ? $config['for'] : 'حدد الحساب';
            $count = count(explode("\n", file_get_contents($for)));
            $bot->editMessageText([
                'chat_id'=>$chatId,
                'message_id'=>$mid,
                'text'=>"Users collection page. \n - Users : $count \n - For Account : $for",
                'reply_markup'=>json_encode([
                    'inline_keyboard'=>[
                        [['text'=>'🔎 سحب من البحث','callback_data'=>'search']],
                        [['text'=>'#⃣ سحب من الهشتاكات','callback_data'=>'hashtag'],['text'=>'📮 سحب من الاكسبلور','callback_data'=>'explore']],
                        [['text'=>'👤 سحب من المتابعين','callback_data'=>'followers'],['text'=>"🚹 سحب من المتابعهم",'callback_data'=>'following']],
                        [['text'=>"تحديد حساب ♾ : $for",'callback_data'=>'for']],
                        [['text'=>'🔖 لسته جديده','callback_data'=>'newList'],['text'=>'🖇 لسته قديمة','callback_data'=>'append']],
                        [['text'=>'✅ رجوع','callback_data'=>'back']],
                    ]
                ])
            ]);
          } elseif($data == 'search'){
            $bot->sendMessage([
                'chat_id'=>$chatId,
                'text'=>"الان ارسل كلمات البحث لتم فحصهم \n يمكنك ارسال اكثر من كلمه من خلال وضع مسافه بينهم"
            ]);
            $config['mode'] = 'search';
            file_put_contents('config.json', json_encode($config));
          } elseif($data == 'followers'){
            $bot->sendMessage([
                'chat_id'=>$chatId,
                'text'=>"الان ارسل اليوزر لتم فحص اليتابعهم \n يمكنك ارسال اكثر من يوزر من خلال وضع مسافه بينهم"
            ]);
            $config['mode'] = 'followers';
            file_put_contents('config.json', json_encode($config));
          } elseif($data == 'following'){
            $bot->sendMessage([
                'chat_id'=>$chatId,
                'text'=>"الان ارسل اليوزر لتم فحص المتابعين \n يمكنك ارسال اكثر من يوزر من خلال وضع مسافه بينهم"
            ]);
            $config['mode'] = 'following';
            file_put_contents('config.json', json_encode($config));
          } elseif($data == 'hashtag'){
            $bot->sendMessage([
                'chat_id'=>$chatId,
                'text'=>"الان ارسل الهشتاكات بدون العلامه # \nيمكنك ارسال هشتاك واحد"
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
							'text'=>"Done Select Exsist list.",
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
							'text'=>"Done Stop Collecting.",
						// 	'show_alert'=>1
						]);
						$for = $config['for'] != null ? $config['for'] : 'Select Account';
                        $count = count(explode("\n", file_get_contents($for)));
						$bot->editMessageText([
                'chat_id'=>$chatId,
                'message_id'=>$mid,
                'text'=>"Users collection page. \n - Users : $count \n - For Account : $for",
                'reply_markup'=>json_encode([
                    'inline_keyboard'=>[
                        [['text'=>'🔎 سحب من البحث','callback_data'=>'search']],
                        [['text'=>'#⃣ سحب من الهشتاكات','callback_data'=>'hashtag'],['text'=>'📮 سحب من الاكسبلور','callback_data'=>'explore']],
                        [['text'=>'👤 سحب من المتابعين','callback_data'=>'followers'],['text'=>"🚹 سحب من المتابعهم",'callback_data'=>'following']],
                        [['text'=>"تحديد حساب ♾ : $for",'callback_data'=>'for']],
                        [['text'=>'🔖 لسته جديده','callback_data'=>'newList'],['text'=>'🖇 لسته قديمة','callback_data'=>'append']],
                        [['text'=>'✅ رجوع','callback_data'=>'back']],
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
							'text'=>"Accounts Status: \n\n $status",
							'parse_mode'=>'markdown'
						]);
				} elseif($data == 'back'){
          	$bot->editMessageText([
                      'chat_id'=>$chatId,
                'message_id'=>$mid,
                'text'=>"Zerhouni ~ @omarzerhouni",
                'reply_markup'=>json_encode([
                      'inline_keyboard'=>[
                          [['text'=>'تسجيل حساب ✨┇','callback_data'=>'login']],
                          [['text'=>'طرق الصيد 🕵️┇','callback_data'=>'grabber']],
                          [['text'=>'بدء الصيد 📳┇','callback_data'=>'run'],['text'=>'ايقاف الصيد 📴┇','callback_data'=>'stop']],
                          [['text'=>' حاله الحسابات 🪐┇','callback_data'=>'status']],
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
		              $keyboard['inline_keyboard'][] = [['text'=>'🔙 رجوع','callback_data'=>'back']];
		              $bot->editMessageText([
		                  'chat_id'=>$chatId,
		                  'message_id'=>$mid,
		                  'text'=>"Accounts Control Page.",
		                  'reply_markup'=>json_encode($keyboard)
		              ]);
		} elseif($data[0] == 'moveList'){
          	  file_put_contents('list', $data[1]);
          	  $keyboard = [];
          	  foreach ($accounts as $account => $v) {
                  $keyboard['inline_keyboard'][] = [['text'=>$account,'callback_data'=>'moveListTo&'.$account]];
              }
              $bot->editMessageText([
                  'chat_id'=>$chatId,
                  'message_id'=>$mid,
                  'text'=>"اختر الحساب الذي تريد نقل اللسته اليه",
                  'reply_markup'=>json_encode($keyboard)
	              ]);
          	} elseif($data[0] == 'moveListTo'){
          	  $keyboard = [];
          	  file_put_contents($data[1], file_get_contents(file_get_contents('list')));
          	  unlink(file_get_contents('list'));
          	  $keyboard['inline_keyboard'][] = [['text'=>'القائمه الرئيسية ✅','callback_data'=>'back']];
          	  $bot->editMessageText([
                  'chat_id'=>$chatId,
                  'message_id'=>$mid,
                  'text'=>"تم نقل اللسته الى الحساب  ✅".$data[1],
                  'reply_markup'=>json_encode($keyboard)
	              ]);
          	} elseif($data[0] == 'forg'){
          	  $config['for'] = $data[1];
          	  file_put_contents('config.json',json_encode($config));
              $for = $config['for'] != null ? $config['for'] : 'Select';
              $count = count(explode("\n", file_get_contents($for)));
              $bot->editMessageText([
                'chat_id'=>$chatId,
                'message_id'=>$mid,
                'text'=>"Users collection page. \n - Users : $count \n - For Account : $for",
                'reply_markup'=>json_encode([
                    'inline_keyboard'=>[
                        [['text'=>'🔎 سحب من البحث','callback_data'=>'search']],
                        [['text'=>'📌 سحب من الهشتاكات','callback_data'=>'hashtag'],['text'=>'📮 سحب من الاكسبلور','callback_data'=>'explore']],
                        [['text'=>'👤 سحب من المتابعين','callback_data'=>'followers'],['text'=>"🚹 سحب من المتابعهم",'callback_data'=>'following']],
                        [['text'=>"تحديد حساب ♾ : $for",'callback_data'=>'for']],
                        [['text'=>'🖇 لسته قديمة','callback_data'=>'newList'],['text'=>'🔖 لسته جديده','callback_data'=>'append']],
                        [['text'=>'✅ رجوع','callback_data'=>'back']],
                    ]
                ])
            ]);
          	} elseif($data[0] == 'start'){
          	  file_put_contents('screen', $data[1]);
          	  $bot->editMessageText([
                      'chat_id'=>$chatId,
                'message_id'=>$mid,
                'text'=>"Zerhouni ~ @omarzerhouni",
                'reply_markup'=>json_encode([
                      'inline_keyboard'=>[
                          [['text'=>'تسجيل حساب ✨┇','callback_data'=>'login']],
                          [['text'=>'طرق الصيد 🕵️┇','callback_data'=>'grabber']],
                          [['text'=>'بدء الصيد 📳┇','callback_data'=>'run'],['text'=>'ايقاف الصيد 📴┇','callback_data'=>'stop']],
                          [['text'=>' حاله الحسابات 🪐┇','callback_data'=>'status']],
                      ]
                  ])
               ]);
              exec('screen -dmS '.explode(':',$data[1])[0].' php start.php');
              $bot->sendMessage([
                'chat_id'=>$chatId,
                'text'=>"تم بدا الصيد 

الوقت ⏲️ : " . date('g:i') . "\n" . "
الحساب الوهمي 🦇 : ".explode(':',$data[1])[0].'
 ━━━━━━━━━━━━━━━━━━━━━',
                'parse_mode'=>'markdown'
              ]);
          	} elseif($data[0] == 'stop'){
          	  $bot->editMessageText([
                      'chat_id'=>$chatId,
                'message_id'=>$mid,
                'text'=>"Zerhouni ~ @omarzerhouni",
                'reply_markup'=>json_encode([
                      'inline_keyboard'=>[
                          [['text'=>'تسجيل حساب ✨┇','callback_data'=>'login']],
                          [['text'=>'طرق الصيد 🕵️┇','callback_data'=>'grabber']],
                          [['text'=>'بدء الصيد 📳┇','callback_data'=>'run'],['text'=>'ايقاف الصيد 📴┇','callback_data'=>'stop']],
                          [['text'=>' حاله الحسابات 🪐┇','callback_data'=>'status']],
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
