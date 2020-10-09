<?php

namespace app\admin\model\system;

use think\Model;
use think\Db;

class Admin extends Model
{
    // 开启自动写入/更新时间戳
    // protected $autoWriteTimestamp = true;
    // 定义时间戳字段
    // protected $createTime = 'createtime';
    // protected $updateTime = 'updatetime';

    /**
     * 取单个内容
     * @access public
     * @author csdeshang
     * @param int $condition
     * @return array 数组类型的返回结果
     */
    public function getOneAdmin($condition)
    {
        $result = db('admin')->where($condition)->find();
        return $result;
    }

    /**
     * 新增
     * @access public
     * @author csdeshang
     * @param array $data 参数内容
     * @return bool 布尔类型的返回结果
     */
    public function addAdmin($data) 
    {
        if (empty($data)) {
            return false;
        }
        $data['createtime'] = time();
        $result = db('admin')->insertGetId($data);
        return $result;
    }

    /**
     * 更新信息
     * @access public
     * @author csdeshang
     * @param array $data 更新数据
     * @return bool 布尔类型的返回结果
     */
    public function editAdmin($data,$id)
    {
        $data['updatetime'] = time();
        $result = db('admin')->where("id=".$id)->update($data);
        return $result;
    }

    // 删除
    public function delAdmin($id){
        Db::startTrans();
        Db::name('admin')->where('id',$id)->delete();
        Db::name('auth_group_access')->where('uid',$id)->delete();
        Db::commit();   
        return true;
    }
    

    /**
     * 重置用户密码
     * @author baiyouwen
     */
    public function resetPassword($uid, $NewPassword)
    {
        $passwd = $this->encryptPassword($NewPassword);
        $ret = $this->where(['id' => $uid])->update(['password' => $passwd]);
        return $ret;
    }

    // 密码加密
    public function encryptPassword($password, $salt = '')
    {
        return md5( md5($password) . $salt);
    }

    // 获取id列表
    public function getList( $page='', $where=[], $order='')
    {
        if(!$order)
            $order = 'id ASC';
        // 记录总数
        $count = Db::name('admin')->where($where)->count();
        // 查询
        $db = Db::name('admin')->where($where)->field('id')->order($order)->page($page)->select();
        $list = [];
        foreach($db as $v){
            $list[] = $v['id'];
        }
        return ['count'=>$count, 'list'=>$list];
    }

    // 关联角色组
    public function accessGroup($uid, $param=[])
    {
        Db::startTrans(); // 启动事务
        Db::name('auth_group_access')->where(['uid'=>$uid])->delete(); // 先删除关联角色组数据，再后面遍历添加
        foreach($param as $gid){
            Db::name('auth_group_access')->insert(['uid'=>$uid, 'group_id'=>$gid]);
        }
        Db::commit(); // 关闭事务
        return true;
    }

    // 删除关联角色组
    public function delGroup($uid)
    {
        Db::name('auth_group_access')->where(['uid'=>$uid])->delete();
        return true;
    }

    // 查询登录次数
    public function getHomeLoginLog($uid)
    {
        $data['count'] = Db::name('admin_login_log')->where('uid',$uid)->count();

        $db_ip = Db::name('admin_login_log')->field('id,ip,date')->where('uid',$uid)->limit(2)->order('date DESC, id DESC')->select();

        $data['this'] = isset($db_ip[0]) ? $db_ip[0] : [];
        $data['last'] = isset($db_ip[1]) ? $db_ip[1] : [];

        return $data;
    }

    // 查询登录记录
    public function getHomeBrowserLog($uid)
    {
        $db = Db::name('admin_login_log')->field('browser,count(browser) as count')->where('uid',$uid)->group('browser')->select();
        return $db;
    }

    public function checkMobile($id)
    {
        $admin = $this->getOneAdmin($id);
        dump($admin);
    }
}
