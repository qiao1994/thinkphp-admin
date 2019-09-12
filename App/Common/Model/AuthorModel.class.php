<?php
namespace Common\Model;
use Think\Model;
class AuthorModel extends FeasModel {
    public $modelName = '权限';
    public $modelIcon = 'icon-certificate';
    public $excel = false;
    public $update = true;
    public $detail = true;
    public $delete = true;
    public $sortStr = '';
    public $batchOperation = [];
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
        'role_id'=>[
            'type'=>'foreign',
            'title'=>'角色',

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

            'list_specify'=>'
                <if condition="$vo[\'role_id\'] eq 0">
                游客
                <else/>
                <a href="__MODULE__/Role/detail?id={$vo["role_id"]}"><php> echo D("Role")->find($vo["role_id"])["name"];</php></a>
                </if>
            ',
            'search_specify'=>'',
            'update_specify'=>'
                        <select name="role_id" id="role_id" class="width-100" >
                            <php> $role = D("Role")->select(); </php>
                            <option value="0">游客</option>
                            <volist name="role" id="vo">
                            <option value="{$vo[\'id\']}" <if condition ="$model[\'role_id\'] eq $vo[\'id\']"> selected </if>>{$vo[\'name\']}</option>
                            </volist>
                        </select>',
            'detail_specify'=>'',
            'export_specify'=>'',
        ],
        'module'=>[
            'type'=>'text',
            'title'=>'模块',

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
            'update_specify'=>'',
            'detail_specify'=>'',
            'export_specify'=>'',
        ],
        'controller'=>[
            'type'=>'text',
            'title'=>'控制器',

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
            'update_specify'=>'',
            'detail_specify'=>'',
            'export_specify'=>'',
        ],
        'action'=>[
            'type'=>'text',
            'title'=>'方法',

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
            'update_specify'=>'',
            'detail_specify'=>'',
            'export_specify'=>'',
        ],
        'rule'=>[
            'type'=>'textarea',
            'title'=>'规则',

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
}
