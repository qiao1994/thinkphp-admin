<?php
namespace Api\Controller;
use Think\Controller;

/**
 * 微信支付接口
 * 使用方法:
 * 0.配置好微信登录
 * 1.需要微信支付直接post参数到Api/Wxpay/pay,其中body、out_trade_no、total_fee为必填项
 * 2.业务代码在bussiness中
appid=>wx33e3b0e193798e4f
bank_type=>CMB_DEBIT
cash_fee=>1
fee_type=>CNY
is_subscribe=>Y
mch_id=>1374027202
nonce_str=>l315cv1hjfhj29hk6r0tdl7gt8qsbv7x
openid=>oKRkHwXPd8hGrh0sf3mkSL4LyLpg
out_trade_no=>customizedTravel-1488803858
result_code=>SUCCESS
return_code=>SUCCESS
sign=>3FDD010040F79A45C9AAFC56DFC8A60C
time_end=>20170306204513
total_fee=>1
trade_type=>JSAPI
transaction_id=>4000002001201703062471883002
 */

class WxpayController extends Controller {

    protected function _initialize() {
        Vendor('WxPayPubHelper.WxPayPubHelper');
    } 

	public function pay(){
        //sui
        $this->assign('title', '支付');
        $sui = [ 
            'information' => 'active',
            'return' => false,
        ];  
        $this->assign('sui', $sui);        

        //获取必须参数
        $out_trade_no = $_GET['out_trade_no'];
        $body = $_GET['subject'];
        $total_fee = $_GET['total_fee']*100;

        $out_trade_no = session('wxpay_out_trade_no');
        $body = session('wxpay_subject');
        $total_fee = session('wxpay_total_fee')*100;
        //获取openid
	    $jsApi = new \JsApi_pub(C('WX_CONFIG'));
	    if (!session('WxpayOpenid')) {
		    session('WxpayRequired', true);
		    $this->redirect('Api/Wxlogin/index');
	    } else {
		    $openid = session('WxpayOpenid');
	    	session('WxpayRequired', false);
	    }
        //构造请求
	    $unifiedOrder = new \UnifiedOrder_pub(C('WX_CONFIG'));
	    $unifiedOrder->setParameter("openid", "$openid");//用户标识
	    $unifiedOrder->setParameter("body", $body);//商品描述
	    $unifiedOrder->setParameter("out_trade_no", $out_trade_no);//商户订单号
	    $unifiedOrder->setParameter("total_fee", $total_fee);//总金额
	    $unifiedOrder->setParameter("notify_url", C('WX_CONFIG.NOTIFY_URL'));//通知地址
	    $unifiedOrder->setParameter("trade_type", "JSAPI");//交易类型
	    $prepay_id = $unifiedOrder->getPrepayId();
	    $jsApi->setPrepayId($prepay_id);
	    $jsApiParameters = $jsApi->getParameters();
	    $wxconf = json_decode($jsApiParameters, true);
	    if ($wxconf['package'] == 'prepay_id=') {
	        $this->ajaxReturn(array('error_msg' => '当前订单存在异常，不能使用支付'));
	    }
	    if(IS_AJAX){
	        $this->ajaxReturn(array(
	            'status' => 'ok',
	            'wxconf' => $wxconf,
	        ));
	    }
	    $this->display('pay');
	}
	
	//异步通知url，商户根据实际开发过程设定
	public function notify() {
	    $notify = new \Notify_pub(C('WX_CONFIG'));
	    $xml = file_get_contents("php://input");
	    $notify->saveData($xml);
	    if($notify->checkSign() == FALSE){
	        $notify->setReturnParameter("return_code", "FAIL");//返回状态码
	        $notify->setReturnParameter("return_msg", "签名失败");//返回信息
	    }else{
	        $notify->setReturnParameter("return_code", "SUCCESS");//设置返回码
	    }
        $returnXml = $notify->returnXml();
        //==商户根据实际情况设置相应的处理流程，此处仅作举例=======
        //以log文件形式记录回调信息
        $log_name = ROOT_PATH."/notify_url.log";//log文件路径
        $parameter = $notify->xmlToArray($xml);
        if($notify->checkSign() == TRUE){
            if ($notify->data["return_code"] == "FAIL") {
                $notify->setReturnParameter("return_code","FAIL");
                $notify->setReturnParameter("return_msg","签名失败");
            } else if($notify->data["result_code"] == "FAIL"){
                $notify->setReturnParameter("return_code","FAIL");
                $notify->setReturnParameter("return_msg","签名失败");
            } else{
                $this->bussiness($notify->data);
                exit;
                $notify->setReturnParameter("return_code","SUCCESS");
            }
        } else {
            $notify->setReturnParameter("return_code","FAIL");
            $notify->setReturnParameter("return_msg","签名失败");
        }
        $returnXml = $notify->returnXml();
        echo $returnXml;
    }

    public function bussiness($data) {
        //业务代码
        $param = explode('-', $data['out_trade_no']);
        //$param[0]
        if ($param[0] == 'CustomizedGuider') {
        	$customizedGuider = D('CustomizedGuider')->where(['id' => $param[1]])->find();
        	if ($customizedGuider['state'] == '待支付') {
        		D('CustomizedGuider')->where(['id' => $param[1]])->data(['state' => '待评价'])->save();
                //增加资金明细记录
                unset($data);
                $data['user_id'] = $customizedGuider['user_id'];
                $data['pay_type'] = '微信';
                $data['amount'] = '-'.$customizedGuider['amount'];
                $data['remark'] = '微信支付-'.$customizedGuider['name'];
                $data['order_type'] = 'CustomizedGuider';
                $data['order_id'] = $customizedGuider['id'];
                D('Finance')->create($data);
                D('Finance')->add();
            }
            $this->redirect('/Home/User/CustomizedGuider');
        } elseif ($param[0] == 'customizedTravel') {
			$customizedTravel = D('CustomizedTravel')->find($param[1]);
			$funding = D('Funding')->find($customizedTravel['funding_id']);
			if ($customizedTravel['state'] == '待支付') {
				//更改当前订单状态
				D('CustomizedTravel')->where(['id'=>$customizedTravel['id']])->data(['state'=>'待评价'])->save();
				//更新筹单订单状态
				D('Funding')->where(['id'=>$customizedTravel['funding_id']])->data(['state'=>'待评价'])->save();
                //增加资金明细记录
                unset($data);
                $data['user_id'] = $customizedTravel['user_id'];
                $data['pay_type'] = '微信';
                $data['amount'] = '-'.$customizedTravel['amount'];
                $data['remark'] = '微信支付-'.$funding['name'];
                $data['order_type'] = 'CustomizedTravel';
                $data['order_id'] = $customizedTravel['id'];
                D('Finance')->create($data);
                D('Finance')->add();
            }
        } elseif ($param[0] == 'Funding') {
            $fundingOrder = D('FundingOrder')->where(['id' => $param[1]])->find();
            if ($fundingOrder['state'] == '待支付') {
                //更新订单状态
                D('FundingOrder')->where(['id' => $param[1]])->data(['state' => '待成功'])->save();
                //增加筹单人数
                $touristNum = count(explode(',', $fundingOrder['tourist_ids']));
                D('Funding')->where(['id' => $fundingOrder['funding_id']])->data(['already_num' => array('exp', 'already_num+'.$touristNum)])->save();
                //如果这个人加入后众筹成功
                $funding = D('Funding')->where(['id' => $fundingOrder['funding_id']])->find();
                if ($funding['already_num'] >= $funding['total_num']) {
                    //更新状态
                    D('Funding')->where(['id' => $fundingOrder['funding_id']])->data(['state' => '众筹成功'])->save();
                    //更新和这个筹单相关的所有订单
                    D('FundingOrder')->where(['funding_id' => $fundingOrder['funding_id']])->data(['state' => '待评价'])->save();
                }
                //增加资金明细记录
                unset($data);
                $data['user_id'] = $fundingOrder['user_id'];
                $data['pay_type'] = '微信';
                $data['amount'] = '-'.$funding['funding_price'];
                $data['remark'] = '微信支付-'.$funding['name'];
                $data['order_type'] = 'FundingOrder';
                $data['order_id'] = $fundingOrder['id'];
                D('Finance')->create($data);
                D('Finance')->add();
            }
            $this->redirect('/Home/User/FundingOrder');
        }
    }
}
?>
