import request from '@/utils/request'

export function getList(params) {
  return request({
    url: '/setting/distribution',
    method: 'get',
    params
  })
}

export function addDistribution(params) {
  return request({
    url: '/setting/distribution/add',
    method: 'post',
    params
  })
}

export function editDistribution(params) {
  return request({
    url: '/setting/distribution/edit',
    method: 'post',
    params
  })
}

export function delDistribution(params) {
  return request({
    url: '/setting/distribution/del',
    method: 'post',
    params
  })
}
