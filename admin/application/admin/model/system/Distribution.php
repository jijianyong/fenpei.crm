<?php

namespace app\admin\model\system;

use think\Model;
use think\Db;

class Distribution extends Model
{
    public $table = 'tq_auth_distribution';

    /**
     * 获取全部用户信息
     * @access public
     * @author jijianyong
     * @param int $condition
     * @return array 数组类型的返回结果
     */
    public function getAdmin($condition)
    {
        $result = db('admin')->where($condition)->value('name');
        return $result;
    }

    /**
     * 获取全部用户信息
     * @access public
     * @author jijianyong
     * @param int $condition
     * @return array 数组类型的返回结果
     */
    public function getOneAdmin($condition)
    {
        $result = db('admin')->where($condition)->find();
        return $result;
    }

    /**
     * 获取全部资源分配规则信息
     * @access public
     * @author jijianyong
     * @param int $condition
     * @return array 数组类型的返回结果
     */
    public function getList($page = '', $where = [], $order = '')
    {
        if (!$order)
            $order = 'id ASC';
        // 记录总数
        $count = Db::name('auth_distribution')->where($where)->count();
        // 查询
        $db = Db::name('auth_distribution')->where($where)->field('id')->order($order)->page($page)->select();
        $list = [];
        foreach ($db as $v) {
            $list[] = $v['id'];
        }
        return ['count' => $count, 'list' => $list];
    }

    /**
     * 新增
     * @access public
     * @author jijianyong
     * @param array $data 参数内容
     * @return bool 布尔类型的返回结果
     */
    public function addDistribution($data)
    {
        if (empty($data)) {
            return false;
        }
        $data['createtime'] = time();
        $result = db('auth_distribution')->insertGetId($data);
        return $result;
    }

    /**
     * 更新信息
     * @access public
     * @author jijianyong
     * @param array $data 更新数据
     * @return bool 布尔类型的返回结果
     */
    public function editDistribution($data, $id)
    {
        $data['updatetime'] = time();
        $result = db('auth_distribution')->where("id=" . $id)->update($data);
        return $result;
    }

    /**
     * 删除信息
     * @access public
     * @author jijianyong
     * @param array $data 更新数据
     * @return bool 布尔类型的返回结果
     */
    public function delDistribution($id)
    {
        Db::startTrans();
        $result = db('auth_distribution')->where("id=" . $id)->delete();
        Db::commit();
        return $result;
    }
}
