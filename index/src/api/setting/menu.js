import request from '@/utils/request'

// 获取菜单规则列表
export function getMenuList(params) {
  return request({
    url: '/setting/menu/index',
    method: 'post',
    params
  })
}
// 新增
export function addMenu(params) {
  return request({
    url: '/setting/menu/add',
    method: 'post',
    params
  })
}
// 修改
export function editMenu(params) {
  return request({
    url: '/setting/menu/edit',
    method: 'post',
    params
  })
}
// 删除
export function removeMenu(params) {
  return request({
    url: '/setting/menu/drop',
    method: 'post',
    params
  })
}
// 批量删除
export function allRemoveMenu(params) {
  return request({
    url: '/setting/menu/list_del',
    method: 'post',
    params
  })
}
//
export function getSelect(params) {
  return request({
    url: '/setting/menu/select',
    method: 'get',
    params
  })
}
//
export function disabledIsmenu(params) {
  return request({
    url: '/setting/menu/list_del',
    method: 'post',
    params
  })
}

