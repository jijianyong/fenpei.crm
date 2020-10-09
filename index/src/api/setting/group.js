import request from '@/utils/request'

// 获取留言列表
export function getGroup(params) {
  return request({
    url: '/setting/group/index',
    method: 'post',
    params
  })
}
// 获取权限节点树
export function getRule(params) {
  return request({
    url: '/setting/group/ruletree',
    method: 'get',
    params
  })
}
// 获取角色组下拉列表
export function getSelect(params) {
  return request({
    url: '/setting/group/select',
    method: 'get',
    params
  })
}
// 新增
export function addGroup(params) {
  return request({
    url: '/setting/group/add',
    method: 'post',
    params
  })
}
// 编辑
export function editGroup(params) {
  return request({
    url: '/setting/group/edit',
    method: 'post',
    params
  })
}
// 删除
export function delGroup(params) {
  return request({
    url: '/setting/group/del',
    method: 'post',
    params
  })
}

