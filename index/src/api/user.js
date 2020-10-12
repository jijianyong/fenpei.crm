import request from '@/utils/request'
// 登陆页面
export function login(data) {
  return request({
    url: '/index/login',
    method: 'post',
    data
  })
}
// 用户信息初始化
export function getInfo() {
  return request({
    url: '/index/info',
    method: 'get'
  })
}
// 菜单信息初始化
export function getMenu() {
  return request({
    url: '/index/menu',
    method: 'get'
  })
}
// 退出登陆
export function logout() {
  return request({
    url: '/index/logout',
    method: 'post'
  })
}

