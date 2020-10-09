import request from '@/utils/request'

export function getList(params) {
  return request({
    url: '/setting/admin',
    method: 'get',
    params
  })
}

export function select(params) {
  return request({
    url: '/setting/admin/select',
    method: 'get',
    params
  })
}

export function addAdmin(params) {
  return request({
    url: '/setting/admin/add',
    method: 'post',
    params
  })
}

export function editAdmin(params) {
  return request({
    url: '/setting/admin/edit',
    method: 'post',
    params
  })
}

export function delAdmin(params) {
  return request({
    url: '/setting/admin/del',
    method: 'post',
    params
  })
}
