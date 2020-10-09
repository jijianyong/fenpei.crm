import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

/* Layout */
import Layout from '@/layout'

// //当设置 true 的时候该路由不会再侧边栏出现 如401，login等页面，或者如一些编辑页面/edit/1
// hidden: true // (默认 false)

// //当设置 noRedirect 的时候该路由在面包屑导航中不可被点击
// redirect: 'noRedirect'

// //当你一个路由下面的 children 声明的路由大于1个时，自动会变成嵌套的模式--如组件页面
// //只有一个时，会将那个子路由当做根路由显示在侧边栏--如引导页面
// //若你想不管路由下面的 children 声明的个数都显示你的根路由
// //你可以设置 alwaysShow: true，这样它就会忽略之前定义的规则，一直显示根路由
// alwaysShow: true

// name: 'router-name' //设定路由的名字，一定要填写不然使用<keep-alive>时会出现各种问题
// meta: {
//   roles: ['admin', 'editor'] //设置该路由进入的权限，支持多个权限叠加
//   title: 'title' //设置该路由在侧边栏和面包屑中展示的名字
//   icon: 'svg-name' //设置该路由的图标
//   noCache: true //如果设置为true，则不会被 <keep-alive> 缓存(默认 false)
//   breadcrumb: false // 如果设置为false，则不会在breadcrumb面包屑中显示
// }

// 所有用户角色都通用的路由
export const constantRoutes = [{
  // 登录页
  path: '/redirect',
  component: Layout,
  hidden: true,
  children: [{
    path: '/redirect/:path*',
    component: () => import('@/views/redirect/index')
  }]
}, {
  path: '/login',
  component: () => import('@/views/login/index'),
  hidden: true
},
// 404页面
{
  path: '/404',
  component: () => import('@/views/404'),
  hidden: true
},
// 首页
{
  path: '/',
  component: Layout,
  redirect: '/home',
  children: [{
    path: 'home',
    name: 'Home',
    component: () => import('@/views/Home/index'),
    meta: {
      title: '首页',
      icon: 'home',
      affix: true
    }
  }]
},
// 资源
{
  path: '/sales',
  component: Layout,
  redirect: '/sales/order',
  name: 'Sales',
  alwaysShow: true,
  meta: {
    title: '资源设置',
    icon: 'sales'
  },
  children: [{
    path: 'order',
    name: 'Order',
    component: () => import('@/views/Sales/order'),
    meta: {
      title: '资源列表',
      icon: 'order'
    }
  },
  {
    path: 'userorder',
    name: 'Userorder',
    component: () => import('@/views/Sales/userorder'),
    meta: {
      title: '资源用户',
      icon: 'nested'
    }
  }
  ]
},
// 系统设置
{
  path: '/setting',
  component: Layout,
  redirect: '/setting/user',
  name: 'Setting',
  alwaysShow: true,
  meta: {
    title: '系统设置',
    icon: 'setting'
  },
  children: [{
    path: 'admin',
    name: 'Admin',
    component: () => import('@/views/Setting/admin'),
    meta: {
      title: '用户列表',
      icon: 'user'
    }
  },
  {
    path: 'group',
    name: 'Group',
    component: () => import('@/views/Setting/group'),
    meta: {
      title: '角色组',
      icon: 'peoples'
    }
  },
  {
    path: 'rule',
    name: 'Rule',
    component: () => import('@/views/Setting/rule'),
    meta: {
      title: '权限规则',
      icon: 'tree'
    }
  },
  {
    path: 'menu',
    name: 'Menu',
    component: () => import('@/views/Setting/menu'),
    meta: {
      title: '菜单规则',
      icon: 'tree-table'
    }
  },
  {
    path: 'resources',
    name: 'Resources',
    component: () => import('@/views/Setting/resources'),
    meta: {
      title: '资源分配规则',
      icon: 'renwufenpei'
    }
  }
  ]
},

// 404 page must be placed at the end !!!
{
  path: '*',
  redirect: '/404',
  hidden: true
}
]

const createRouter = () => new Router({
  mode: 'history', // require service support
  scrollBehavior: () => ({
    y: 0
  }),
  routes: constantRoutes
})

const router = createRouter()

// Detail see: https://github.com/vuejs/vue-router/issues/1234#issuecomment-357941465
export function resetRouter() {
  const newRouter = createRouter()
  router.matcher = newRouter.matcher // reset router
}

export default router
