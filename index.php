<?php
function status($for){
    if($for == '1'){
        $x = exec('screen -S mails1 -Q select . ; echo $?');
    } elseif($for == '2'){
        $x = exec('screen -S mails2 -Q select . ; echo $?');
    }
    if($x == '0'){
        return 'Running.';
    } else {
        return 'Stopped.';
    }
    
}
function checkMail($mail){
    echo "[checkMail] Routing email: $mail\n";
    flush();
    
    if(stripos($mail, 'gmail.com') !== false){
        echo "[checkMail] -> Gmail detected\n";
        flush();
        return checkGmail($mail);
    } elseif(preg_match('/(hotmail|outlook|live)\./i', $mail)){
        echo "[checkMail] -> Hotmail detected\n";
        flush();
        $url = newURL();
        return checkHotmail($url, $mail);
    } elseif(stripos($mail, 'yahoo') !== false){
        echo "[checkMail] -> Yahoo detected\n";
        flush();
        return checkYahoo($mail);
    } elseif(preg_match('/(mail|bk|yandex|inbox|list)\.ru/i', $mail)){
        echo "[checkMail] -> Mail.ru detected\n";
        flush();
        return checkRU($mail);
    } else {
        echo "[checkMail] -> Unknown domain, assuming available\n";
        flush();
        return true;
    }
}
function bot($method,$datas=[]){
    global $token;
$url = "https://api.telegram.org/bot".$token."/".$method;
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
$res = curl_exec($ch);
if(curl_error($ch)){
var_dump(curl_error($ch));
}else{
return json_decode($res);
}
}
function inInsta($mail){
    $mail = strtolower($mail);
  $search = curl_init(); 
  $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
curl_setopt($search, CURLOPT_URL, "https://i.instagram.com/api/v1/users/lookup/"); 
curl_setopt($search, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($search, CURLOPT_ENCODING , "");
curl_setopt($search, CURLOPT_HTTPHEADER, explode("\n", 'Host: i.instagram.com
Connection: keep-alive
X-IG-Connection-Type: WIFI
X-IG-Capabilities: 3brTv10=
x-ig-app-id: 567067343352427
Accept-Language: en-US
Content-Type: application/x-www-form-urlencoded; charset=UTF-8
User-Agent: Instagram 316.0.0.38.120 Android (30/11; 420dpi; 1080x2400; samsung; SM-G991B; o1s; exynos2100; en_US; 541974943)
Accept-Encoding: gzip, deflate
t'));
curl_setopt($search,CURLOPT_POST, 1);
$fields = 'signed_body=SIGNATURE.'.urlencode(json_encode(['q'=>$mail,'_uid'=>rand(1000000000,9999999999),'guid'=>$uuid,'device_id'=>'android-'.$uuid])).'&ig_sig_key_version=4';
curl_setopt($search,CURLOPT_POSTFIELDS, $fields);
$search = curl_exec($search);
// echo $search;
$search = json_decode($search);
    if($search->status != 'fail'){
        if($search->can_email_reset == true){
            return ['fb'=>$search->fb_login_option,'ph'=>$search->has_valid_phone];
        } else {
            return false;
        }
    } else {
        return false;
    }
}
function getInfo($id,$cookies,$useragent){
$search = curl_init(); 
curl_setopt($search, CURLOPT_URL, "https://i.instagram.com/api/v1/users/".trim($id)."/usernameinfo/"); 
curl_setopt($search, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($search, CURLOPT_ENCODING , "");
curl_setopt($search, CURLOPT_TIMEOUT, 15);
$h = explode("\n", 'Host: i.instagram.com
Connection: keep-alive
X-IG-Connection-Type: WIFI
X-IG-Capabilities: 3brTv10=
x-ig-app-id: 567067343352427
Accept-Language: en-US
Cookie: '.$cookies.'
User-Agent: '.$useragent.'
Accept-Encoding: gzip, deflate');
curl_setopt($search, CURLOPT_HTTPHEADER, $h);
$search = curl_exec($search);
// echo $search;
$search = json_decode($search);

if(isset($search->user)){
    $user = $search->user;
$ret = ['f'=>$user->follower_count,'ff'=>$user->following_count,'m'=>$user->media_count,'user'=>$user->username];
if(isset($user->public_email)){
  if($user->public_email != ''){
      $mail = $user->public_email;
      $ret['mail'] = $mail;
  } else {
      $ret = false;
  }
} else {
  $ret = false;
}
} else {
    echo json_encode($search);
    $ret = false;
}
return $ret;
}
function newURL(){
  $url = 'https://login.live.com/';
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HEADER, 1);
  $get = curl_exec($ch);
  curl_close($ch);
  preg_match("/\:'https\:\/\/login.live.com\/GetCredentialType(.*)',/", $get,$m);
  $url = explode("',", $m[0])[0];
  $url = str_replace(':\'', '',$url);
  return $url;
}
function checkRU($mail){
    $mail = trim($mail);
    if(strpos($mail, ' ') !== false or strpos($mail, '+') !== false){
        return false;
    }
    
    echo "[Mail.ru] Checking: $mail\n";
    flush();
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://auth.mail.ru/api/v1/pushauth/info?login=' . urlencode($mail) . '&_=' . time() . '000');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: */*',
        'Accept-Encoding: gzip, deflate, br',
        'Accept-Language: en-US,en;q=0.9',
        'Origin: https://mail.ru',
        'Referer: https://mail.ru/',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    
    $res = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    
    if($err){
        echo "Mail.ru check error: $err\n";
        return false;
    }
    
    if(empty($res)){
        return false;
    }
    
    $decoded = json_decode($res);
    if(isset($decoded->body->exists)){
        if($decoded->body->exists == false){
            return true; // Doesn't exist = available
        }
    }
    
    return false;
}
function checkYahoo($mail){
    $mail = trim($mail);
    if(strpos($mail, ' ') !== false or strpos($mail, '+') !== false){
        return false;
    }
    
    // Simple validation check - Yahoo API is heavily protected
    // Use basic pattern matching as fallback
    $username = preg_replace('/@.*/', '', $mail);
    
    echo "[Yahoo] Checking: $username\n";
    flush();
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://login.yahoo.com/account/module/create?validateField=yid');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: */*',
        'Accept-Language: en-US,en;q=0.9',
        'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
        'Origin: https://login.yahoo.com',
        'Referer: https://login.yahoo.com/account/create',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
        'X-Requested-With: XMLHttpRequest'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    
    $fields = 'acrumb=&sessionIndex=&yid='.$username;
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    
    $res = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    
    if($err){
        echo "Yahoo check error: $err\n";
        return false;
    }
    
    if(empty($res)){
        return false;
    }
    
    $decoded = json_decode($res);
    // Yahoo returns error codes if username exists
    if(isset($decoded->errors) && is_array($decoded->errors) && count($decoded->errors) > 0){
        // Check for "username taken" type errors
        foreach($decoded->errors as $error){
            if(isset($error->name) && stripos($error->name, 'IDENTIFIER_EXISTS') !== false){
                return false; // Email exists
            }
        }
    }
    
    // If specific "invalid username" error
    if(isset($decoded->render->error) && $decoded->render->error == 'messages.ERROR_INVALID_USERNAME'){
        return true; // Available
    }
    
    return true; // Assume available if no clear "exists" signal
}
function checkGmail($mail){
    $mail = trim($mail);
    if(strpos($mail, ' ') !== false or strpos($mail, '+') !== false){
        return false;
    }
    $username = preg_replace('/@.*/', '', $mail);
    
    echo "[Gmail] Checking: $username\n";
    flush();
    
    // Try multiple endpoints for reliability
    $endpoints = [
        'https://accounts.google.com/accounts/CheckUsernameAvailability',
        'https://www.google.com/accounts/CheckUsername'
    ];
    
    foreach($endpoints as $url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: */*'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'username='.$username);
        
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);
        
        if(!$err && $httpCode < 400){
            echo "[Gmail] Got response from $url\\n";
            flush();
            
            // Check if available in response
            if(!empty($res) && (strpos($res, 'available') !== false || strpos($res, '"true"') !== false)){
                echo "[Gmail] ✓ Available\\n";
                flush();
                return true;
            }
        }
    }
    
    echo "[Gmail] ✗ Not available or check failed\\n";
    flush();
    return false;
}
function checkHotmail($url, $mail){
    $mail = trim($mail);
    if(strpos($mail, ' ') !== false or strpos($mail, '+') !== false){
        return false;
    }
    
    try {
        // Extract uaid from URL
        $parts = explode('uaid=', $url);
        if(count($parts) < 2){
            echo "Hotmail: Failed to parse uaid from URL\n";
            return false;
        }
        $uaid = $parts[1];
        
        echo "[Hotmail] Checking: $mail\n";
        flush();
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Accept-Encoding: gzip, deflate, br',
            'Accept-Language: en-US,en;q=0.9',
            'Content-Type: application/json; charset=UTF-8',
            'Origin: https://login.live.com',
            'Referer: https://login.live.com/',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        
        $payload = json_encode([
            'username' => $mail,
            'uaid' => $uaid,
            'isOtherIdpSupported' => true,
            'checkPhones' => false,
            'isRemoteNGCSupported' => true,
            'isFidoSupported' => false
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        
        $res = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        
        if($err){
            echo "Hotmail check error: $err\n";
            return false;
        }
        
        if(empty($res)){
            return false;
        }
        
        $decoded = json_decode($res);
        if(isset($decoded->IfExistsResult)){
            // IfExistsResult: 0 = doesn't exist (available), 1 = exists (taken), 5/6 = other
            if($decoded->IfExistsResult == 1){
                return true; // Available for signup = doesn't exist in system
            }
        }
        
        return false;
    } catch (Exception $e) {
        echo "Hotmail check exception: " . $e->getMessage() . "\n";
        return false;
    }
}
class EzTGException extends Exception
{
}
class EzTG
{
    private $settings;
    private $offset;
    private $json_payload;
    public function __construct($settings, $base = false)
    {
        $this->settings = array_merge(array(
      'endpoint' => 'https://api.telegram.org',
      'token' => '1234:abcd',
      'callback' => function ($update, $EzTG) {
          echo 'no callback' . PHP_EOL;
      },
      'objects' => true,
      'allow_only_telegram' => true,
      'throw_telegram_errors' => true,
      'magic_json_payload' => false
    ), $settings);
        if ($base !== false) {
            return true;
        }
        if (!is_callable($this->settings['callback'])) {
            $this->error('Invalid callback.', true);
        }
        if (php_sapi_name() === 'cli') {
            $this->settings['magic_json_payload'] = false;
            $this->offset = -1;
            $this->get_updates();
        } else {
            if ($this->settings['allow_only_telegram'] === true and $this->is_telegram() === false) {
                http_response_code(403);
                echo '403 - You are not Telegram,.,.';
                return 'Not Telegram';
            }
            if ($this->settings['magic_json_payload'] === true) {
                ob_start();
                $this->json_payload = false;
                register_shutdown_function(array($this, 'send_json_payload'));
            }
            if ($this->settings['objects'] === true) {
                $this->processUpdate(json_decode(file_get_contents('php://input')));
            } else {
                $this->processUpdate(json_decode(file_get_contents('php://input'), true));
            }
        }
    }
    private function is_telegram()
    {
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) { //preferisco non usare x-forwarded-for xk si può spoof
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if (($ip >= '149.154.160.0' && $ip <= '149.154.175.255') || ($ip >= '91.108.4.0' && $ip <= '91.108.7.255')) { //gram'''s ip : https://core.telegram.org/bots/webhooks
            return true;
        } else {
            return false;
        }
    }
    private function get_updates()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->settings['endpoint'] . '/bot' . $this->settings['token'] . '/getUpdates');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        while (true) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'offset=' . $this->offset . '&timeout=10');
            if ($this->settings['objects'] === true) {
                $result = json_decode(curl_exec($ch));
                if (isset($result->ok) and $result->ok === false) {
                    $this->error($result->description, false);
                } elseif (isset($result->result)) {
                    foreach ($result->result as $update) {
                        if (isset($update->update_id)) {
                            $this->offset = $update->update_id + 1;
                        }
                        $this->processUpdate($update);
                    }
                }
            } else {
                $result = json_decode(curl_exec($ch), true);
                if (isset($result['ok']) and $result['ok'] === false) {
                    $this->error($result['description'], false);
                } elseif (isset($result['result'])) {
                    foreach ($result['result'] as $update) {
                        if (isset($update['update_id'])) {
                            $this->offset = $update['update_id'] + 1;
                        }
                        $this->processUpdate($update);
                    }
                }
            }
        }
    }
    public function processUpdate($update)
    {
        $this->settings['callback']($update, $this);
    }
    protected function error($e, $throw = 'default')
    {
        if ($throw === 'default') {
            $throw = $this->settings['throw_telegram_errors'];
        }
        if ($throw === true) {
            throw new EzTGException($e);
        } else {
            echo 'Telegram error: ' . $e . PHP_EOL;
            return array(
        'ok' => false,
        'description' => $e
      );
        }
    }
    public function newKeyboard($type = 'keyboard', $rkm = array('resize_keyboard' => true, 'keyboard' => array()))
    {
        return new EzTGKeyboard($type, $rkm);
    }
    public function __call($name, $arguments)
    {
        if (!isset($arguments[0])) {
            $arguments[0] = array();
        }
        if (!isset($arguments[1])) {
            $arguments[1] = true;
        }
        if ($this->settings['magic_json_payload'] === true and $arguments[1] === true) {
            if ($this->json_payload === false) {
                $arguments[0]['method'] = $name;
                $this->json_payload = $arguments[0];
                return 'json_payloaded'; //xd
            } elseif (is_array($this->json_payload)) {
                $old_payload = $this->json_payload;
                $arguments[0]['method'] = $name;
                $this->json_payload = $arguments[0];
                $name = $old_payload['method'];
                $arguments[0] = $old_payload;
                unset($arguments[0]['method']);
                unset($old_payload);
            }
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->settings['endpoint'] . '/bot' . $this->settings['token'] . '/' . urlencode($name));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($arguments[0]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($this->settings['objects'] === true) {
            $result = json_decode(curl_exec($ch));
        } else {
            $result = json_decode(curl_exec($ch), true);
        }
        curl_close($ch);
        if ($this->settings['objects'] === true) {
            if (isset($result->ok) and $result->ok === false) {
                return $this->error($result->description);
            }
            if (isset($result->result)) {
                return $result->result;
            }
        } else {
            if (isset($result['ok']) and $result['ok'] === false) {
                return $this->error($result['description']);
            }
            if (isset($result['result'])) {
                return $result['result'];
            }
        }
        return $this->error('Unknown error', false);
    }
    public function send_json_payload()
    {
        if (is_array($this->json_payload)) {
            ob_end_clean();
            echo json_encode($this->json_payload);
            header('Content-Type: application/json');
            ob_end_flush();
            return true;
        }
    }
}
class EzTGKeyboard
{
    public $line;
    public $type;
    public $keyboard;
    public function __construct($type = 'keyboard', $rkm = array('resize_keyboard' => true, 'keyboard' => array()))
    {
        $this->line = 0;
        $this->type = $type;
        if ($type === 'inline') {
            $this->keyboard = array(
                'inline_keyboard' => array()
            );
        } else {
            $this->keyboard = $rkm;
        }
        return $this;
    }
    public function add($text, $callback_data = null, $type = 'auto')
    {
        if ($this->type === 'inline') {
            if ($callback_data === null) {
                $callback_data = trim($text);
            }
            if (!isset($this->keyboard['inline_keyboard'][$this->line])) {
                $this->keyboard['inline_keyboard'][$this->line] = array();
            }
            if ($type === 'auto') {
                if (filter_var($callback_data, FILTER_VALIDATE_URL)) {
                    $type = 'url';
                } else {
                    $type = 'callback_data';
                }
            }
            array_push($this->keyboard['inline_keyboard'][$this->line], array(
        'text' => $text,
        $type => $callback_data
      ));
        } else {
            if (!isset($this->keyboard['keyboard'][$this->line])) {
                $this->keyboard['keyboard'][$this->line] = array();
            }
            array_push($this->keyboard['keyboard'][$this->line], $text);
        }
        return $this;
    }
    public function newline()
    {
        $this->line++;
        return $this;
    }
    public function done()
    {
        if ($this->type === 'remove') {
            return '{"remove_keyboard": true}';
        } else {
            return json_encode($this->keyboard);
        }
    }
}
class ig {
	private $url = 'https://i.instagram.com/api/v1';
	private $account;
	private $ret = [];
	private $file;
	public function __construct($settings){
		$this->account = $settings['account'];
		// Updated to 2025 Instagram version
		$this->account['useragent'] = 'Instagram 316.0.0.38.120 Android (30/11; 420dpi; 1080x2400; samsung; SM-G991B; o1s; exynos2100; en_US; 541974943)';
		$this->file = $settings['file'];
		
	}
	public function login($user,$pass){
		$uuid = $this->UUID();
		$guid = $this->GUID();
		// Updated signature key for 2025
		return $this->request('accounts/login/',
			0,
			1,
			[
				'signed_body'=>'SIGNATURE.'.json_encode([
					'phone_id'=>$guid,
					'username'=>$user,
					'enc_password'=>'#PWD_INSTAGRAM:0:'.time().':'.$pass,
					'guid'=>$guid,
					'device_id'=>'android-'.$uuid,
					'login_attempt_count'=>'0',
					'adid'=>$this->UUID(),
					'google_tokens'=>'[]',
					'country_codes'=>json_encode([['country_code'=>'1','source'=>'default']]),
					'jazoest'=>'2'.rand(10000,99999),
				]),
				'ig_sig_key_version'=>'4'
			],
			1
		);
	}
	public function news(){
		return $this->request('news/inbox/',1);
	}
	
	public function getComments($mediaId){
    return $this->request("media/{$mediaId}/comments/",1,0,['can_support_threading'=>true]);
  }
  public function getLikers($mediaId){
	  return $this->request("media/{$mediaId}/likers/",1);
	}
	public function getPosts($userId){
		return $this->request("feed/user/{$userId}/",1);
	}
	public function getInfo($username){
		return $this->request("users/{$username}/usernameinfo/",1)->user;
	}
	public function comment($mediaId,$comment){
		$uuid = $this->UUID();
		$guid = $this->GUID();
		return $this->request("media/{$mediaId}/comment/",1,1,[
						'user_breadcrumb'=>$this->generateUserBreadcrumb(mb_strlen($comment)),
            'idempotence_token'=>$uuid,
            '_uuid'=>$uuid,
            '_uid'=>rand(1000000000,9999999999),
            'comment_text'=>$comment,
            'containermodule'=>'comments_feed_timeline',
            'radio_type'=>'wifi-none'
		]);
	}
	public function like($mediaId){
		$uuid = $this->UUID();
		$guid = $this->GUID();
    return $this->request("media/{$mediaId}/like/",1,1,[
        '_uuid'=>$uuid,
        '_uid'=>rand(1000000000,9999999999),
        'media_id'=>$mediaId,
        'radio_type'=>'wifi-none',
        'module_name'=>'feed_timeline'
    ]);
  }
	public function unfollow($id){
		$uuid = $this->UUID();
		$guid = $this->GUID();
		return $this->request("friendships/destroy/$id/",1,1,[
					'_uid'=>rand(1000000000,9999999999),
					'_uuid'=>$guid,
					'user_id'=>$id,
					'radio_type'=>'wifi-none'
		]);
	}
	public function follow($id){
		$uuid = $this->UUID();
		$guid = $this->GUID();
		return $this->request("friendships/create/$id/",1,1,[
					'_uid'=>rand(1000000000,9999999999),
					'_uuid'=>$guid,
					'user_id'=>$id,
					'radio_type'=>'wifi-none'
		]);
	}
	public function getFollowing($id,$mid,$uuu,$maxId = null){
	    $config = json_decode(file_get_contents('config.json'),1);
	    $from = 'Following.';
		$file = $this->file;
		$rank_token = $this->UUID();
		$datas['rank_token'] = $rank_token;
		if($maxId != null){
			$datas['max_id'] = $maxId;
		}
		$res = $this->request("friendships/$id/following/",1,0,$datas,0);
		if(isset($res->users)){
			$in = explode("\n",file_get_contents($file));
			foreach($res->users as $user){
				if(!in_array($user->username, $in)){
					$users[] = $user->username;
					file_put_contents($file, $user->username."\n",FILE_APPEND);
				}
			}
    	
    	bot('editmessageText',[
    		'chat_id'=>$config['id'],
    		'message_id'=>$mid,
    		'text'=>"*Collection From* ~ [ _ $from _ ]\n\n*Status* ~> _ Working _\n*Users* ~> _ ".count(explode("\n", file_get_contents($file)))."_",
	'parse_mode'=>'markdown',
	'reply_markup'=>json_encode(['inline_keyboard'=>[
			[['text'=>'Stop.','callback_data'=>'stopgr']]
		]])
    	]);
		}
		if($res->next_max_id != null){
			$this->getFollowing($id,$mid,$uuu,$res->next_max_id);
		} else {
			bot('editmessageText',[
    		'chat_id'=>$config['id'],
    		'message_id'=>$mid,
    		'text'=>"*Collection From* ~ [ _ $from _ ]\n\n*Status* ~> _ Working _\n*Users* ~> _ ".count(explode("\n", file_get_contents($file)))."_",
	'parse_mode'=>'markdown',
	'reply_markup'=>json_encode(['inline_keyboard'=>[
			[['text'=>'Stop.','callback_data'=>'stopgr']]
		]])
    	]);
    	bot('sendMessage',[
    		'chat_id'=>$config['id'],
    		'reply_to_message_id'=>$mid,
    		'text'=>"*Done Collection . * \n All : ".count(explode("\n", file_get_contents($file))),
    		'parse_mode'=>'markdown',
        ]);
		}
	}
	public function getFollowers($id,$mid,$uuu,$maxId = null){
	    $config = json_decode(file_get_contents('config.json'),1);
	    $from = 'Followers';
		$file = $this->file;
		$rank_token = $this->UUID();
		$datas['rank_token'] = $rank_token;
		if($maxId != null){
			$datas['max_id'] = $maxId;
		}
		$res = $this->request("friendships/$id/followers/",1,0,$datas,0);
		if(isset($res->users)){
			$in = explode("\n",file_get_contents($file));
			foreach($res->users as $user){
				if(!in_array($user->username, $in)){
					$users[] = $user->username;
					file_put_contents($file, $user->username."\n",FILE_APPEND);
				}
				}
    	
    	bot('editmessageText',[
    		'chat_id'=>$config['id'],
    		'message_id'=>$mid,
    		'text'=>"*Collection From* ~ [ _ $from _ ]\n\n*Status* ~> _ Working _\n*Users* ~> _ ".count(explode("\n", file_get_contents($file)))."_",
	'parse_mode'=>'markdown',
	'reply_markup'=>json_encode(['inline_keyboard'=>[
			[['text'=>'Stop.','callback_data'=>'stopgr']]
		]])
    	]);
		}
		if($res->next_max_id != null){
			$this->getFollowers($id,$mid,$uuu,$res->next_max_id);
		} else {
			bot('editmessageText',[
    		'chat_id'=>$config['id'],
    		'text'=>"*Collection From* ~ [ _ $from _ ]\n\n*Status* ~> _ Working _\n*Users* ~> _ ".count(explode("\n", file_get_contents($file)))."_",
	'parse_mode'=>'markdown',
	'reply_markup'=>json_encode(['inline_keyboard'=>[
			[['text'=>'Stop.','callback_data'=>'stopgr']]
		]])
    	]);
    	bot('sendMessage',[
    		'chat_id'=>$config['id'],
    		'reply_to_message_id'=>$mid,
    		'text'=>"*Done Collection . * \n All : ".count(explode("\n", file_get_contents($file))),
    		'parse_mode'=>'markdown',
        ]);
		}
	}
	
	
	public function bot($method,$datas=[]){
    $token = file_get_contents('token');
		$url = "https://api.telegram.org/bot".$token."/".$method;
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
		$res = curl_exec($ch);
		
		if(curl_error($ch)){
			return curl_error($ch);
		}else{
		    
			return json_decode($res);
		}
	}
	public function generateUserBreadcrumb($size){
      $key = 'iN4$aGr0m';
      $date = (int) (microtime(true) * 1000);
      $term = rand(2, 3) * 1000 + $size * rand(15, 20) * 100;
      $text_change_event_count = round($size / rand(2, 3));
      if ($text_change_event_count == 0) {
          $text_change_event_count = 1;
      }
      $data = $size.' '.$term.' '.$text_change_event_count.' '.$date;
      return base64_encode(hash_hmac('sha256', $data, $key, true))."\n".base64_encode($data)."\n";
  }
	private function GUID(){
    if (function_exists('com_create_guid') === true){
        return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
	}
	private function UUID(){
    $uuid = sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
    
    return $uuid;
	}
    private function request($path,$account = 0,$post = 0,$datas = array(),$returnHeaders = 0){
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, ''); // Enable automatic gzip/deflate decompression
        if($post == 1){
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        if(!empty($datas) && $post == 1){
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($datas));
            curl_setopt($ch, CURLOPT_URL, $this->url .'/'. $path); 
        } elseif(!empty($datas) && $post == 0){
            curl_setopt($ch, CURLOPT_URL, $this->url .'/'. $path.'?'.http_build_query($datas)); 
        } else {
            curl_setopt($ch, CURLOPT_URL, $this->url .'/'. $path); 
        }
	  if($account == 0){
	  	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		     'x-ig-capabilities: 3brTv10=',
		     'x-ig-app-id: 567067343352427',
		     'user-agent: Instagram 316.0.0.38.120 Android (30/11; 420dpi; 1080x2400; samsung; SM-G991B; o1s; exynos2100; en_US; 541974943)',
		     'host: i.instagram.com',
		     'accept-language: en-US',
		     'accept-encoding: gzip, deflate',
		     'X-IG-Connection-Type: WIFI',
		     'X-IG-Capabilities: 3brTv10=',
		     'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
		     "Cookie: mid=XUzLlQABAAH63ME45I6TG-i46cOi",
		     'Connection: keep-alive'
		  ));
	  } elseif($account == 1){
	  	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	      'x-ig-capabilities: 3brTv10=',
	      'x-ig-app-id: 567067343352427',
	      'user-agent: '.$this->account['useragent'],
	      'host: i.instagram.com',
	      'accept-language: en-US',
	      'accept-encoding: gzip, deflate',
	      'X-IG-Connection-Type: WIFI',
	      'X-IG-Capabilities: 3brTv10=',
	      'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
	      "Cookie: ".$this->account['cookies'],
	      'Connection: keep-alive'
	  ));
	  }
	  if($returnHeaders == 1){
		  curl_setopt($ch, CURLOPT_HEADER, 1);
		  $res = curl_exec($ch);
		  if(curl_errno($ch)){
			  error_log("Instagram API Error: " . curl_error($ch));
			  curl_close($ch);
			  return ['error' => curl_error($ch)];
		  }
		  // Split headers and body
		  $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		  $headers = substr($res, 0, $header_size);
		  $body = substr($res, $header_size);
		  curl_close($ch);
		  return [$headers, $body];
	  } else {
		  $res = curl_exec($ch);
		  if(curl_errno($ch)){
			  error_log("Instagram API Error: " . curl_error($ch));
			  curl_close($ch);
			  return json_decode(json_encode(['error' => curl_error($ch)]));
		  }
		  $decoded = json_decode($res);
		  // Better error handling for Instagram API responses
		  if(isset($decoded->status) && $decoded->status === 'fail'){
			  error_log("Instagram API returned fail status: " . $res);
		  }
		  if(isset($decoded->message) && strpos($decoded->message, 'challenge_required') !== false){
			  error_log("Instagram challenge required - account may need verification");
		  }
		  $res = $decoded;
	  }
	  curl_close($ch);
	  return $res;
	}
}