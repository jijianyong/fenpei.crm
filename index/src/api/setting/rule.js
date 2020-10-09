import request from '@/utils/request'

// 获取菜单节点列表
export function getRuleList(params) {
  return request({
    url: '/setting/rule/index',
    method: 'post',
    params
  })
}
// 新增
export function addRule(params) {
  return request({
    url: '/setting/rule/add',
    method: 'post',
    params
  })
}
// 编辑
export function editRule(params) {
  return request({
    url: '/setting/rule/edit',
    method: 'post',
    params
  })
}
// 删除
export function removeRule(params) {
  return request({
    url: '/setting/rule/drop',
    method: 'post',
    params
  })
}
// 批量删除
export function allRemoveRule(params) {
  return request({
    url: '/setting/rule/list_del',
    method: 'post',
    params
  })
}
// 修改菜单状态
export function disabledIsmenu(params) {
  return request({
    url: '/setting/rule/disabled',
    method: 'post',
    params
  })
}
// 查询
export function getSelect(params) {
  return request({
    url: '/setting/rule/select',
    method: 'post',
    params
  })
}
