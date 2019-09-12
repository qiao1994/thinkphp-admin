<?php
namespace Common\Model;
use Think\Model;
class UserModel extends FeasModel {
    public $modelName = '用户';
    public $modelIcon = 'icon-user';
    public $excel = false;
    public $update = true;
    public $detail = true;
    public $delete = true;
    public $sortStr = '';
    public $batchOperation = [];
    protected $_auto = [
        ['password', 'passwordHash', 1, 'callback'], //新增用户时hash密码
        ['password', 'passwordHash', 2, 'callback'], //更新用户时hash密码

    ];
    protected $_validate = [
        ['username', '', '用户名已经存在！', 0, 'unique', 1], // 在新增的时候验证账号是否唯一
        ['repassword','password','两次密码输入不一致!', 0, 'confirm'],
    ];
    protected function _before_insert(&$data,$options) {}
    protected function _after_insert($data,$options) {}
    protected function _before_delete($options) {}
    protected function _after_delete($data,$options) {}
    protected function _before_update(&$data,$options) {}
    protected function _after_update($data,$options) {}
    protected function _after_select(&$resultSet,$options) {}
    public $fieldMap = [
        'id'=>[
            'type'=>'text',
            'title'=>'ID',

            'list'=>true,
            'detail'=>true,
            'input'=>false,
            'excel'=>false,

            'search'=>true,
            'sort'=>false,
            'required'=>true,
            'list_update'=>false,
            'params'=>[
            ],

            'list_specify'=>'',
            'search_specify'=>'',
            'update_specify'=>'',
            'detail_specify'=>'',
            'export_specify'=>'',
        ],
        'username'=>[
            'type'=>'text',
            'title'=>'用户名',

            'list'=>true,
            'detail'=>true,
            'input'=>true,
            'excel'=>false,

            'search'=>false,
            'sort'=>false,
            'required'=>true,
            'list_update'=>false,
            'params'=>[
            ],

            'list_specify'=>'',
            'search_specify'=>'',
            'update_specify'=>'
            <input type="text" class="width-100" placeholder="用户名" id="username" name="username"  value="{$model[\'username\']|default=\'\'}" validate-required="required:请填写用户名" <if condition="ACTION_NAME eq update">readonly</if>/>
            ',
            'detail_specify'=>'',
            'export_specify'=>'',
        ],
        'name'=>[
            'type'=>'text',
            'title'=>'姓名',

            'list'=>true,
            'detail'=>true,
            'input'=>true,
            'excel'=>false,

            'search'=>true,
            'sort'=>false,
            'required'=>true,
            'list_update'=>false,
            'params'=>[
            ],

            'list_specify'=>'',
            'search_specify'=>'',
            'update_specify'=>'',
            'detail_specify'=>'',
            'export_specify'=>'',
        ],
        'password'=>[
            'type'=>'password',
            'title'=>'密码',

            'list'=>false,
            'detail'=>false,
            'input'=>true,
            'excel'=>false,

            'search'=>false,
            'sort'=>false,
            'required'=>false,
            'list_update'=>false,
            'params'=>[
            ],

            'list_specify'=>'',
            'search_specify'=>'',
            'update_specify'=>'
            <input type="password" class="width-100" placeholder="无需修改密码则不填写" id="password" name="password"  value=""  />
            ',
            'detail_specify'=>'',
            'export_specify'=>'',
        ],
        'repassword'=>[
            'type'=>'password',
            'title'=>'重复密码',

            'list'=>false,
            'detail'=>false,
            'input'=>true,
            'excel'=>false,

            'search'=>false,
            'sort'=>false,
            'required'=>false,
            'list_update'=>false,
            'params'=>[
            ],

            'list_specify'=>'',
            'search_specify'=>'',
            'update_specify'=>'
            <input type="password" class="width-100" placeholder="无需修改密码则不填写" id="repassword" name="repassword"  value=""  />
            ',
            'detail_specify'=>'',
            'export_specify'=>'',
        ],
        'role_id'=>[
            'type'=>'foreign',
            'title'=>'用户类型',

            'list'=>true,
            'detail'=>true,
            'input'=>true,
            'excel'=>false,

            'search'=>false,
            'sort'=>false,
            'required'=>true,
            'list_update'=>false,
            'params'=>[
                'foreign_table' => 'role',
                'foreign_table_key' => 'id',
                'foreign_show' => 'name',
                'search_type' => 'select',
                'foreign_href' => false,
            ],

            'list_specify'=>'',
            'search_specify'=>'',
            'update_specify'=>'',
            'detail_specify'=>'',
            'export_specify'=>'',
        ],
        'state'=>[
            'type'=>'select',
            'title'=>'状态',

            'list'=>true,
            'detail'=>true,
            'input'=>true,
            'excel'=>false,

            'search'=>false,
            'sort'=>false,
            'required'=>true,
            'list_update'=>false,
            'params'=>[
                'options' => '正常,禁用',
            ],

            'list_specify'=>'',
            'search_specify'=>'',
            'update_specify'=>'',
            'detail_specify'=>'',
            'export_specify'=>'',
        ],
        'create_time'=>[
            'type'=>'datetime',
            'title'=>'创建时间',

            'list'=>false,
            'detail'=>true,
            'input'=>false,
            'excel'=>false,

            'search'=>false,
            'sort'=>false,
            'required'=>true,
            'list_update'=>false,
            'params'=>[
            ],

            'list_specify'=>'',
            'search_specify'=>'',
            'update_specify'=>'',
            'detail_specify'=>'',
            'export_specify'=>'',
        ],
        'update_time'=>[
            'type'=>'datetime',
            'title'=>'更新时间',

            'list'=>false,
            'detail'=>true,
            'input'=>false,
            'excel'=>false,

            'search'=>false,
            'sort'=>false,
            'required'=>true,
            'list_update'=>false,
            'params'=>[
            ],

            'list_specify'=>'',
            'search_specify'=>'',
            'update_specify'=>'',
            'detail_specify'=>'',
            'export_specify'=>'',
        ],
    ];

    //密码hash
    protected function passwordHash($data = '') {
        if ($data != '') {
            return password_hash($data, PASSWORD_BCRYPT);
        } else {
            return $this->find($_GET['id'])['password'];
        }
    }
    //登录
    public function login($role_id = 1) {
        //验证账号
        $user = M('User')->getByUsername($this->username);
        if (!$user) {
            $this->error = '用户名不存在!';
            return false;
        }
        //验证密码
        if (!password_verify($this->password, $user['password'])) {
            $this->error = '密码错误!';
            return false;
        }
        //验证用户类型
        if ($user['role_id'] != $role_id) {
            $this->error = '请求不合法!';
            return false;
        }
        //验证用户状态
        if ($user['state'] != '正常') {
            $this->error = '用户被禁用!';
            return false;
        }
        //登录成功
        session(MODULE_NAME.'-user', $user);
        return true;
    }
}
