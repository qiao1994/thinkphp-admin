<?php
namespace Api\Controller;
use Think\Controller;

/**
 * 微信登录接口
 * 使用方法:
 * 0.在微信公众号请求用户网页授权之前，开发者需要先到公众平台官网中的“开发 - 接口权限 - 网页服务 - 网页帐号 - 网页授权获取用户基本信息”的配置选项中，修改授权回调域名。请注意，这里填写的是域名（是一个字符串），而不是URL，因此请勿加 http:// 等协议头
 * 1.在/Api/Common/Conf/config.php 中配置WX_CONFIG下的APPID、APPSECRET、LOGINCALLBACK(配置为本控制器中的callback函数)
 * 2.需要微信登录的时候跳转到http://域名/Api/Wxlogin/index
 * 3.在callback中能获取到userinfo并传到bussiness,在bussiness中增加业务代码后跳转回需要用到微信用户信息的Controller
 * 4.userData的数据结构为
 ["openid"] => string(28) "oKRkHwXPd8hGrh0sf3mkSL4LyLpg"
 ["nickname"] => string(9) "满地可"
 ["sex"] => int(1)
 ["language"] => string(5) "zh_CN"
 ["city"] => string(0) ""
 ["province"] => string(0) ""
 ["country"] => string(6) "中国"
 ["headimgurl"] => string(119) "http://wx.qlogo.cn/mmopen/pk3v4yAjyIYJxaJENEW9FibDyHCPAAnK1nQck5eyVIZtDwDicXKYHNFDBxAm5UKkfkAjjMuJPoIxiaZ8OibVicbTNNw/0"
 ["privilege"] => array(0) {}
 **/
class WxloginController extends Controller {
    protected function _initialize() {
    }

    public function index() {
        $appId = C('WX_CONFIG.APPID');
        $callback = C('WX_CONFIG.LOGINCALLBACK');
        $scope = 'snsapi_userinfo';
        $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appId.'&redirect_uri='.urlencode($callback).'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
		header("Location:".$url);
	}

    public function bussiness($userData) {
    	//处理用户信息
    	$user = D('User')->where(['wechat_key'=>$userData['openid']])->find();
    	if ($user) {
    		session('用户', $user);
			//刚刚登录，跳转回之前的筹单
			if ($_SESSION['lastFunding']) {
                $lastFundingId = $_SESSION['lastFunding']['id'];
                $lastConsignmentId = $_SESSION['lastFunding']['consignment_id'];
                unset($_SESSION['lastFunding']);
				$this->redirect('/Home/Funding/detail/id/'.$lastFundingId.'/consignment_id/'.$lastConsignmentId);
			}
    		$this->redirect('/Home/User/index');
    	} else {
    		unset($data);
    		$data['wechat_key'] = $userData['openid'];
    		$data['name'] = $userData['nickname'];
    		$data['location'] = $userData['country'].$userData['province'].$userData['city'];
            $data['state'] = '正常';
    		D('User')->create($data);
    		D('User')->add();
    	    $user = D('User')->where(['wechat_key'=>$userData['openid']])->find();
    		session('用户', $user);
			//刚刚登录，跳转回之前的筹单
			if ($_SESSION['lastFunding']) {
                $lastFundingId = $_SESSION['lastFunding']['id'];
                $lastConsignmentId = $_SESSION['lastFunding']['consignment_id'];
                unset($_SESSION['lastFunding']);
				$this->redirect('/Home/Funding/detail/id/'.$lastFundingId.'/consignment_id/'.$lastConsignmentId);
			}
    		$this->redirect('/Home/User/index');
    	}
    }

	public function callback(){
		$appid = C('WX_CONFIG.APPID');
		$secret = C('WX_CONFIG.APPSECRET');
		$code = $_GET["code"];
		$get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$get_token_url);
		curl_setopt($ch,CURLOPT_HEADER,0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		$res = curl_exec($ch);
		curl_close($ch);
		$json_obj = json_decode($res,true);
		//根据openid和access_token查询用户信息 
		$access_token = $json_obj['access_token'];
		$openid = $json_obj['openid'];
		$get_user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$get_user_info_url);
		curl_setopt($ch,CURLOPT_HEADER,0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		$res = curl_exec($ch);
		curl_close($ch);

		//解析json 
		$user_obj = json_decode($res,true);
		//微信支付需要信息存入session
		if (session('WxpayRequired')) {
		    session('WxpayOpenid', $openid);
		    $this->redirect('Api/Wxpay/pay');	
		}
		//业务代码
        $this->bussiness($user_obj);
	}
}
