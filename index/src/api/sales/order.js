import request from '@/utils/request'

export function getList(params) {
  return request({
    url: '/sales/order',
    method: 'get',
    params
  })
}
// 资源用户页面查询
export function getUserList(params) {
  return request({
    url: '/sales/order/selUserList',
    method: 'get',
    params
  })
}

export function addOrder(params) {
  return request({
    url: '/sales/order/add',
    method: 'post',
    params
  })
}

export function editOrder(params) {
  return request({
    url: '/sales/order/edit',
    method: 'post',
    params
  })
}

export function delOrder(params) {
  return request({
    url: '/sales/order/del',
    method: 'post',
    params
  })
}

export function copyOrder(params) {
  return request({
    url: '/sales/order/copys',
    method: 'get',
    params
  })
}

