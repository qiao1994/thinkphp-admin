<?php
namespace Api\Controller;
use Think\Controller;

/**
 * 支付宝接口
 * 使用说明：
 * 1.修改Api模块的config中ALIPAY_CONFIG相关信息(设置partner、seller_id、key、service)
 * 2.pay接口需要POST参数(out_trade_no、subject、total_fee三个必选参数)
 * 3.在bussiness中增加业务代码
 * 4.bussiness中$data的数据结构为
 ["is_success"] => string(1) "T"
 ["notify_id"] => string(70) "RqPnCoPT3K9%2Fvwbh3InZezfNTnJl4d2Jhgo76ziro8e1maCoAkFWvMzx4WcM4g4lZJtr"
 ["notify_time"] => string(19) "2017-02-26 09:41:47"
 ["notify_type"] => string(17) "trade_status_sync"
 ["out_trade_no"] => string(10) "1488072869"
 ["payment_type"] => string(1) "1"
 ["seller_id"] => string(16) "2088421597394473"
 ["service"] => string(36) "alipay.wap.create.direct.pay.by.user"
 ["subject"] => string(7) "subject"
 ["total_fee"] => string(4) "0.01"
 ["trade_no"] => string(28) "2017022621001004720275034354"
 ["trade_status"] => string(13) "TRADE_SUCCESS"
 ["sign"] => string(32) "daa342eb812a427f74c17b9d0efaeab1"
 ["sign_type"] => string(3) "MD5"
 **/
class AlipayController extends Controller {
    protected function _initialize() {
        //引入支付宝类库
        vendor('Alipay.Corefunction');
        vendor('Alipay.Md5function');
		vendor('Alipay.Notify');
		vendor('Alipay.Submit');
	}

	public function pay($out_trade_no='', $subject='', $total_fee='') {
        $_POST['out_trade_no'] =  $out_trade_no;
        $_POST['subject'] = $subject;
        $_POST['total_fee'] = $total_fee;
        if ((!$_POST['out_trade_no'])||(!$_POST['subject'])||(!$_POST['total_fee'])){
            $this->error('请完善订单信息!');
        }
		//构造要请求的参数数组，无需改动
		$parameter = array(
			"service" => C('ALIPAY_CONFIG')['service'],
			"partner" => C('ALIPAY_CONFIG')['partner'],
			"seller_id" => C('ALIPAY_CONFIG')['seller_id'],
			"payment_type" => C('ALIPAY_CONFIG')['payment_type'],
			"notify_url" => C('ALIPAY_CONFIG')['notify_url'],
			"return_url" => C('ALIPAY_CONFIG')['return_url'],
			"_input_charset" => trim(strtolower(C('ALIPAY_CONFIG')['input_charset'])),
			"out_trade_no" => $_POST['out_trade_no'], //订单号
			"subject" => $_POST['subject'], //订单主题
			"total_fee" => $_POST['total_fee'],
		    "app_pay" => "Y", //启用此参数能唤起钱包APP支付宝
		);
		$alipaySubmit = new \AlipaySubmit(C('ALIPAY_CONFIG'));
		$htmlText = $alipaySubmit->buildRequestForm($parameter, "get", "确认");
		echo $htmlText;
	}
    public function bussiness($data, $type) {
        //业务代码
        $param = explode('-', $data['out_trade_no']);
        //$param[0]
        if ($param[0] == 'CustomizedGuider') {
        	$customizedGuider = D('CustomizedGuider')->where(['id' => $param[1]])->find();
        	if (($customizedGuider['amount'] == $data['total_fee'])&&($customizedGuider['state'] == '待支付')) {
        		D('CustomizedGuider')->where(['id' => $param[1]])->data(['state' => '待评价'])->save();
                //增加资金明细记录
                unset($data);
                $data['user_id'] = $customizedGuider['user_id'];
                $data['pay_type'] = '支付宝';
                $data['amount'] = '-'.$customizedGuider['amount'];
                $data['remark'] = '支付宝支付-'.$customizedGuider['name'];
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
                $data['pay_type'] = '支付宝';
                $data['amount'] = '-'.$customizedTravel['amount'];
                $data['remark'] = '支付宝支付-'.$funding['name'];
                $data['order_type'] = 'CustomizedTravel';
                $data['order_id'] = $customizedTravel['id'];
                D('Finance')->create($data);
                D('Finance')->add();
			}

            $this->redirect('/Home/User/CustomizedTravel');
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
                $data['pay_type'] = '支付宝';
                $data['amount'] = '-'.$funding['funding_price'];
                $data['remark'] = '支付宝支付-'.$funding['name'];
                $data['order_type'] = 'FundingOrder';
                $data['order_id'] = $fundingOrder['id'];
                D('Finance')->create($data);
                D('Finance')->add();
            }
            $this->redirect('/Home/User/FundingOrder');
        }
    }
    //异步url
    public function notifyUrl() {
		$alipayNotify = new \AlipayNotify($alipay_config);
		$verifyResult = $alipayNotify->verifyNotify();
        $this->bussiness($_POST, 'notify');
		if ($verifyResult) {
		} else {
		}
	}
	// 返回url
	public function returnUrl() {
		$alipayNotify = new \AlipayNotify($alipay_config);
		$verifyResult = $alipayNotify->verifyReturn();
        $this->bussiness($_GET, 'return');
		if ($verifyResult) {
		} else {
		}
	}
}
