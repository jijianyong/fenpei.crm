import axios from 'axios'
import {
  MessageBox,
  Message
} from 'element-ui'
import store from '@/store'
import {
  getToken
} from '@/utils/auth'

import qs from 'qs'
import NProgress from 'nprogress'

// create an axios instance
const service = axios.create({
  headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' }, // set cross-domain requests headers
  // baseURL: process.env.VUE_APP_BASE_API, // url = base url + request url
  // baseURL: 'http://resources.tianqukj.com/', // url = base url + request url
  baseURL: 'http://interface.tianquji.com/', // url = base url + request url
  timeout: 5000, // request timeout,
  transformRequest: [function(data) { // Convert the Data to Form Data format before requesting
    if (Object.prototype.toString.call(data) !== '[object FormData]') { data = qs.stringify(data) }// Object data is not converted
    return data
  }]
})

// request interceptor
service.interceptors.request.use(
  config => {
    NProgress.start()
    // do something before request is sent
    if (store.getters.token) {
      // let each request carry token
      // ['Token'] is a custom headers key
      // please modify it according to the actual situation
      config.headers['Token'] = getToken()
    }
    return config
  },
  error => {
    // do something with request error
    console.log(error) // for debug
    return Promise.reject(error)
  }
)

// response interceptor
service.interceptors.response.use(

  /**
   * If you want to get http information such as headers or status
   * Please return  response => response
   */

  /**
   * Determine the request status by custom code
   * Here is just an example
   * You can also judge the status by HTTP Status Code
   */
  response => {
    NProgress.done()
    const res = response.data

    // if the custom code is not 0, it is judged as an error.
    if (res.code !== 0) {
      Message({
        message: res.msg || 'The request failed',
        type: 'error'
        // duration: 5 * 1000
      })

      // 101: Illegal token or Other clients logged in or Token expired;
      if (res.code === 101) {
        // to re-login
        MessageBox.confirm('You have been logged out, you can cancel to stay on this page, or log in again', 'Confirm logout', {
          confirmButtonText: 'Re-Login',
          cancelButtonText: 'Cancel',
          type: 'warning'
        }).then(() => {
          store.dispatch('user/resetToken').then(() => {
            location.reload()
          })
        })
      }

      return Promise.reject(new Error(res.msg || 'The request failed'))
    } else {
      return res
    }
  },

  error => {
    NProgress.done()
    console.log('' + error) // for debug
    Message({
      message: error.message,
      type: 'error',
      duration: 5 * 1000
    })
    return Promise.reject(error)
  }
)

export default service
