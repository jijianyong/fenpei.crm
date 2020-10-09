<?php

/**
 * 分组模型（角色模型类）
 * jijianyong
 * 2020/9/7
 */

namespace app\admin\model\system;

use app\admin\model\system\Auth;
use think\Db;
use think\Model;

class Group extends Model
{
   // 开启自动写入/更新时间戳
   protected $autoWriteTimestamp = true;
   // 定义时间戳字段
   protected $createTime = 'createtime';
   protected $updateTime = 'updatetime';

   // 构造函数
   public function __construct()
   {
      // 步骤：4
      $this->auth = Auth::instance();
   }

   /**
    * 获取列表
    * @access public
    * @author csdeshang
    * @param type $condition
    * @param type $page
    * @param type $order
    * @return type
    */
   public function getGroupList($condition)
   {
      $result = db('auth_group')->where($condition)->select();
      return $result;
   }

   /**
    * 查询（查询单条数据）
    * @access public
    * @author csdeshang
    * @param int $condition
    * @return array 数组类型的返回结果
    */
   public function getOneGroup($condition)
   {
      $result = db('auth_group')->where($condition)->find();
      return $result;
   }

   /**
    * 新增（新增一条数据）
    * @access public
    * @author csdeshang
    * @param array $data 参数内容
    * @return bool 布尔类型的返回结果
    */
   public function addGroup($data)
   {
      if (empty($data)) {
         return false;
      }
      $data['createtime'] = time();
      $result = db('auth_group')->insertGetId($data);
      return $result;
   }

   /**
    * 更新信息
    * @access public
    * @author csdeshang
    * @param array $data 更新数据
    * @return bool 布尔类型的返回结果
    */
   public function editGroup($data, $id)
   {
      $data['updatetime'] = time();
      $result = db('auth_group')->where("id", $id)->update($data);
      return $result;
   }

   /**
    * 删除
    * @access public
    * @author csdeshang
    * @param int $id 记录ID
    * @return bool 布尔类型的返回结果
    */
   public function delGroup($id)
   {
      $this->delGroupField($id);
      $result = db('auth_group')->where("id=" . $id)->delete();
      return $result;
   }

   // 获取子节点id
   public function getMyChildIds()
   {
      // 步骤：3
      $groups = $this->auth->getGroups($this->auth->uid);
      $list = [];
      foreach ($groups as $v) {
         $list = array_merge($list, $this->getChildIds($v['id']));
      }
      return array_unique($list);
   }

   /**
    * 获取包括自己和所有子级的ID
    * @param int $id  角色组ID
    * @return array
    */
   public function getChildIds($myid)
   {
      $newarr = [];
      $item = db('auth_group')->field('id')->find($myid);
      if ($item) {
         $newarr[] = $item['id'];
         $child_db = db('auth_group')->field('id')->where('pid', $myid)->select();
         foreach ($child_db as $v) {
            $newarr = array_merge($newarr, $this->getChildIds($v['id']));
         }
         return $newarr;
      }
   }

   /**
    * 获取管理员有权限管理的所有角色组的树形列表
    * @return array
    */
   public function getMyGroupList()
   {
      // 步骤：2
      $groups = $this->auth->getGroups($this->auth->uid);
      $group_ids = $list = $groups_list = [];
      foreach ($groups as $v) {
         $group_ids[] = $v['id'];
      }
      foreach ($group_ids as $gid) {
         $list = array_merge($list, $this->getChildIds($gid));
      }
      if ($list) {
         $list = array_unique($list); // 去重
         $groups_list = $this->getGroupList(['id' => ['in', $list]]); // 获取所有管理员信息
      }
      return $groups_list;
   }

   // 获取树形列表数据
   public function getTreeList($list, $pid = 0)
   {
      $newarr = [];
      foreach ($list as $value) {
         if (!isset($value['id'])) {
            continue; // 停止
         }
         if ($value['pid'] == $pid) {
            $children = $this->getTreeList($list, $value['id']);
            if ($children) {
               $value['children'] = $children;
            }
            $newarr[] = $value;
            unset($value);
         }
      }
      return $newarr;
   }

   /**
    * 根据$list，生成树形列表的item
    * @param array $list   角色组的列表
    * @param int   $pid    父级id
    * @param int   $level  当前等级
    * @return array
    */
   public function getTreeItem($list, $pid = 0, $level = 0)
   {
      $newarr = [];
      foreach ($list as $value) {
         if (!isset($value['id'])) {
            continue;
         }
         if ($value['pid'] == $pid) {
            $value['spacer'] = $level > 0 ? str_repeat('│', $level) . '└' : '';
            $newarr[] = $value;
            $newarr = array_merge($newarr, $this->getTreeItem($list, $value['id'], $level + 1));
         }
      }
      return $newarr;
   }

   // 获取所有权限节点
   public function getAllRuleList()
   {
      return db('auth_rule')->order('weigh DESC,id ASC')->select();
   }

   // 获取权限节点的树形列表(layui-tree格式)
   public function getRuleTree($list, $pid = 0, $selected = [])
   {
      $newarr = [];
      foreach ($list as $value) {
         if (!isset($value['id']))
            continue;
         if ($value['pid'] == $pid) {
            $data = [];
            $data['id'] = $value['id'];
            $data['label'] = $value['title'];
            if (in_array($value['id'], $selected)) {
               $data['spread'] = true;
               $data['checked'] = true;
            }
            $children = $this->getRuleTree($list, $value['id']);
            if ($children)
               $data['children'] = $children;
            $newarr[] = $data;
         }
      }
      return $newarr;
   }

   // field
   public function getGroupField($param)
   {
      $db = Db::name('auth_group_field')->where('gid', 'in', $param)->select();
      $field_list = [];
      foreach ($db as $v)
         $field_list = array_merge($field_list,  json_decode($v['field'], true));
      $field_list = array_unique($field_list);
      return $field_list ? $field_list : [];
   }

   public function setGroupField($gid, $field)
   {
      $this->delGroupField($gid);
      Db::name('auth_group_field')->insert(['gid' => $gid, 'field' => $field]);
      return true;
   }

   public function delGroupField($gid)
   {
      Db::name('auth_group_field')->where(['gid' => $gid])->delete();
      return true;
   }
}
