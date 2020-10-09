<template>
  <div class="navbar">
    <hamburger
      id="hamburger-container"
      :is-active="sidebar.opened"
      class="hamburger-container"
      @toggleClick="toggleSideBar"
    />
    <breadcrumb id="breadcrumb-container" class="breadcrumb-container" />

    <div class="right-menu">
      <el-dropdown class="avatar-container right-menu-item hover-effect" trigger="click">
        <div class="avatar-wrapper">
          <img src="@/assets/images/favicon.png" class="user-avatar" />
          <i class="el-icon-caret-bottom" />
        </div>
        <el-dropdown-menu slot="dropdown">
          <el-dropdown-item>
            <el-button type="text" @click="editPassword = true" style="color:#333333">修改密码</el-button>
            <!-- 信息设置弹出框 -->
            <el-dialog title="修改密码" :visible.sync="editPassword" :append-to-body="true">
              <el-form :model="userform" :rules="userformRules" ref="userform">
                <el-form-item label="原密码" prop="pass">
                  <el-input v-model="userform.pass" autocomplete="off" show-password></el-input>
                </el-form-item>
                <el-form-item label="新密码" prop="newpass">
                  <el-input v-model="userform.newpass" autocomplete="off" show-password></el-input>
                </el-form-item>
                <el-form-item label="确认新密码" prop="repass">
                  <el-input v-model="userform.repass" autocomplete="off" show-password></el-input>
                </el-form-item>
              </el-form>
              <div slot="footer" class="dialog-footer">
                <el-button @click="editPassword = false">取 消</el-button>
                <el-button type="primary" @click="editSubmit()" :loading="editLoading">确 定</el-button>
              </div>
            </el-dialog>
          </el-dropdown-item>
          <el-dropdown-item divided>
            <span style="display:block;" @click="logout">退出登录</span>
          </el-dropdown-item>
        </el-dropdown-menu>
      </el-dropdown>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Breadcrumb from '@/components/Breadcrumb'
import Hamburger from '@/components/Hamburger'

export default {
  components: {
    Breadcrumb,
    Hamburger
  },
  data() {
    return {
      userform: {
        pass: '',
        newpass: '',
        repass: ''
      },
      userformRules: {
        pass: [{ required: true, message: '请输入原密码', trigger: 'blur' }],
        newpass: [{ required: true, message: '请输入新密码', trigger: 'blur' }],
        repass: [{ required: true, message: '请确认新密码', trigger: 'blur' }]
      },
      editLoading: false,
      editPassword: false
    }
  },
  computed: {
    ...mapGetters(['sidebar', 'avatar', 'device'])
  },
  methods: {
    toggleSideBar() {
      this.$store.dispatch('app/toggleSideBar')
    },
    async logout() {
      await this.$store.dispatch('user/logout')
      this.$router.push(`/login?redirect=${this.$route.fullPath}`)
      window.addEventListener('beforeunload', () => {
        this.$store.commit('setToken', '')
      })
    },
    editSubmit() {}
  }
}
</script>

<style lang="scss" scoped>
.navbar {
  height: 50px;
  overflow: hidden;
  position: relative;
  background: #fff;
  box-shadow: 0 1px 4px rgba(0, 21, 41, 0.08);

  .hamburger-container {
    line-height: 46px;
    height: 100%;
    float: left;
    cursor: pointer;
    transition: background 0.3s;
    -webkit-tap-highlight-color: transparent;

    &:hover {
      background: rgba(0, 0, 0, 0.025);
    }
  }

  .breadcrumb-container {
    float: left;
  }

  .errLog-container {
    display: inline-block;
    vertical-align: top;
  }

  .right-menu {
    float: right;
    height: 100%;
    line-height: 50px;

    &:focus {
      outline: none;
    }

    .right-menu-item {
      display: inline-block;
      padding: 0 8px;
      height: 100%;
      font-size: 18px;
      color: #5a5e66;
      vertical-align: text-bottom;

      &.hover-effect {
        cursor: pointer;
        transition: background 0.3s;

        &:hover {
          background: rgba(0, 0, 0, 0.025);
        }
      }
    }

    .avatar-container {
      margin-right: 30px;

      .avatar-wrapper {
        margin-top: 5px;
        position: relative;

        .user-avatar {
          cursor: pointer;
          width: 40px;
          height: 40px;
          border-radius: 10px;
        }

        .el-icon-caret-bottom {
          cursor: pointer;
          position: absolute;
          right: -20px;
          top: 25px;
          font-size: 12px;
        }
      }
    }
  }
}
</style>
