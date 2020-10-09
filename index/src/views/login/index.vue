<template>
  <div class="login-wrap">
    <div class="login-container">
      <el-card class="box-card" style="margin-top:20px;border: 1px solid #ffffff63;background-color: #ffffff63;">
        <p>天渠商盈系统</p>
        <el-form
          :model="loginForm"
          :rules="loginRules"
          ref="loginForm"
          class="login_form"
          label-width="70px"
        >
          <el-form-item
            label="用户名"
            prop="username"
            ><el-input
              v-model="loginForm.username"
              suffix-icon="el-icon-s-custom"
            ></el-input
          ></el-form-item>
          <el-form-item
            label="密码"
            prop="password"
            ><el-input
              v-model="loginForm.password"
              suffix-icon="el-icon-unlock"
              type="password"
            ></el-input
          ></el-form-item>
          <el-form-item label="验证码" prop="captcha">
            <div id="Captcha"></div>
            <input type="hidden" v-model="loginForm.captcha">
          </el-form-item>
          <el-button
            type="primary"
            @click="loginSubmit()"
            :loading="logining"
            class="loginsubmit"
            >登录</el-button
          >
        </el-form>
      </el-card>
    </div>
    <el-row :gutter="20" style="margin-top:18px;">
      <el-col :span="6" :offset="4">
        <div class="login-logo">
          <img src="@/assets/images/login-logo.png" alt="" />
        </div>
      </el-col>
      <el-col :span="6" :offset="4">
        <div class="login-phone">
          <img src="@/assets/images/phone.png" alt="" />
          <el-divider direction="vertical"></el-divider>
          <span>{{ phone }}</span>
        </div>
      </el-col>
    </el-row>

    <remote-js src="https://www.yunpian.com/static/official/js/libs/riddler-sdk-0.2.2.js" :callback="initCaptcha"></remote-js>

  </div>
</template>
<script>

import RemoteJs from '@/components/Common/RemoteJs'

export default {
  components: {
    RemoteJs
  },
  data() {
    return {
      phone: '400-086-1117',
      loginForm: {
        username: null,
        password: null,
        captcha: null,
        captcha_token: null
      },
      loginRules: {
        username: [{ required: true, message: '请输入用户名', trigger: 'blur' }],
        password: [{ required: true, message: '请输入密码', trigger: 'blur' }],
        captcha: [{ required: true, message: '请点击验证', trigger: 'blur' }]
      },
      logining: false,
      redirect: false
    }
  },
  watch: {
    $route: { // 路由参数
      handler: function(route) {
        const query = route.query

        if (query) {
          this.redirect = query.redirect
        }
      },
      immediate: true
    }
  },
  methods: {
    loginSubmit() {
      this.$refs.loginForm.validate(valid => {
        if (valid) {
          this.loginForm.username = this.loginForm.username.trim()
          this.$store.dispatch('user/login', this.loginForm).then((res) => {
            this.$router.push({
              path: this.redirect || '/'
            })
            this.loading = false
          }).catch(() => {
            this.initCaptcha()
            this.loading = false
          })
        }
      })
    },
    // 实例化验证码
    initCaptcha() {
      const _this = this

      // eslint-disable-next-line no-undef
      new YpRiddler({
        expired: 10,
        mode: 'float',
        winWidth: 300,
        lang: 'zh-cn', // 界面语言, 目前支持: 中文简体 zh-cn, 英语 en
        container: document.getElementById('Captcha'),
        appId: '7737e7fbf6e34e338da078992e15f9f8',
        version: 'v1',
        onError: function(param) {
          if (!param.code) {
            console.error('错误请求')
          } else if (parseInt(param.code / 100) === 5) {
            // 服务不可用时，开发者可采取替代方案
            console.error('验证服务暂不可用')
          } else if (param.code === 429) {
            console.warn('请求过于频繁，请稍后再试')
          } else if (param.code === 403) {
            console.warn('请求受限，请稍后再试')
          } else if (param.code === 400) {
            console.warn('非法请求，请检查参数')
          }
          // 异常回调
          console.error('验证服务异常')
        },
        onSuccess: function(validInfo, close, useDefaultSuccess) {
          // 成功回调
          _this.loginForm.captcha_token = validInfo.token
          _this.loginForm.captcha = validInfo.authenticate
          // 验证成功默认样式
          useDefaultSuccess(true)
          close()
        },
        onFail: function(code, msg, retry) {
          // 失败回调
          retry()
        },
        beforeStart: function(next) {
          // console.log('验证马上开始')
          next()
        },
        onExit: function() {
          // 退出验证 （仅限dialog模式有效）
          // console.log('退出验证')
        }
      })
    }
  },
  mounted() {

  }
}
</script>

<style lang="scss" scoped>
.box-card{overflow: inherit;}

.login-wrap {
  background: url(../../assets/images/login-bg.png) no-repeat center center;
  width: 100%;
  height: 100%;
  background-size: cover;
  overflow: hidden;
  .login-container {
    position: absolute;
    top: 30%;
    right: 45%;
    text-align: center;
    width: 450px;
    h1 {
      font-size: 116px;
      margin:0;
    }
    p {
      font-size: 22px;
      font-family: "SimSun";
      margin-bottom: 15px;
      em {
        color: #47a7fb;
      }
    }
  }
  .login-phone {
    margin-top: 30px;
    img {
      vertical-align: middle;
    }
    .el-divider--vertical {
      background: #999;
    }
    span {
      font-size: 24px;
      vertical-align: middle;
    }
  }
}
</style>

<style>
.login-container .el-input__icon {
  font-size: 20px;
}
.checkCode {
  width: 100px;
  height: 40px;
  margin-left: 80px;
  letter-spacing: 10px;
}
.loginsubmit {
  width: 338px;
  margin-left: 70px !important;
}
</style>
