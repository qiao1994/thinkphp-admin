<?php
namespace Admin\Controller;
use Think\Controller;

class IndexController extends Controller {
	protected function _initialize() {
		//===系统信息======
		$system = D('System')->find(1);
		$this->assign('system', $system);
	}
	public function index() {
		if (IS_POST) {
            //验证码
            $verify = new \Think\Verify();
			if (!$verify->check(I('post.identifyCode'))) {
			    $this->error('验证码错误!');
			    return false;
			}
			//登录
			$userModel = D('User');
			if (!($userModel->create($_POST, 4) && $userModel->login(1))) {
				$this->error($userModel->getError());
			}
			$this->redirect('System/index');
			return true;
		}
		if (session(MODULE_NAME.'-user')) {
			$this->redirect('System/index');
		} else {
			//登录页面
			$this->display('login');
		}
	}

	public function identifyCode() {
	     $verify = new \Think\Verify(['length'=>4, 'fontSize'=>30]);
	     echo $verify->entry();
	}

	public function logout() {
		session(MODULE_NAME.'-user', null);
		$this->success('注销成功!', U('Index/index'));
	}

}
