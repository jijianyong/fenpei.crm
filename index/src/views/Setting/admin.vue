<template>
  <div class="app-container">
    <div class="filter-container">
      <el-form :inline="true">
        <el-form-item>
          <el-input v-model="filter.search" placeholder="搜索" size="mini" class="filter-input" />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" size="mini" @click="getList">查询</el-button>
          <el-button type="success" size="mini" @click="handleDialogAdd">
            添加
            <i class="el-icon-plus el-icon--right"></i>
          </el-button>
        </el-form-item>
        <!-- 设置按钮对话框 -->
        <el-form-item style="float:right;">
          <el-popover placement="bottom" width="300" trigger="click">
            <el-col :span="12" v-for="(v, index) in field" :key="index">
              <el-checkbox
                v-model="v.isShow"
                class="filter-item"
                style="margin-left:15px;"
                @change="tableKey=tableKey+Math.random()"
                :label="v.label"
              ></el-checkbox>
            </el-col>
            <el-button type="primary" icon="el-icon-setting" size="mini" slot="reference">设置</el-button>
          </el-popover>
        </el-form-item>
      </el-form>
    </div>
    <!-- 数据表格 -->
    <el-table
      ref="multipleTable"
      :key="tableKey"
      :data="list"
      tooltip-effect="dark"
      stripe
      :header-cell-style="{background:'#f5f7fa'}"
      fit
      border
      v-loading="loading"
      style="width: 100%"
    >
      <el-table-column type="selection" width="55"></el-table-column>
      <template v-for="(v, index) in field">
        <el-table-column
          :key="index"
          v-if="v.isShow"
          :prop="v.prop"
          :label="v.label"
          show-overflow-tooltip
        ></el-table-column>
      </template>
      <!-- 操作列 -->
      <el-table-column prop="operation" label="操作" width="135" show-overflow-tooltip>
        <template slot-scope="scope">
          <el-button type="primary" plain size="mini" @click="handleDialogEdit(scope.row)">
            <i class="el-icon-edit el-icon--right"></i>
          </el-button>
          <el-button type="danger" plain size="mini" @click="delRow(scope.row.id)">
            <i class="el-icon-delete el-icon--right"></i>
          </el-button>
        </template>
      </el-table-column>
    </el-table>
    <!-- 分页 -->
    <div class="data-page">
      <el-pagination
        @size-change="sizeChange"
        @current-change="currentChange"
        :current-page="currentPage"
        background
        :page-sizes="[10, 20, 30, 40, 50, 100 ,200]"
        :page-size="pageSize"
        class="common-page"
        layout="total, sizes, prev, pager, next, jumper"
        :total="dateTotal"
      ></el-pagination>
    </div>
    <!-- 添加-编辑按钮对话框 -->
    <el-dialog
      :title="dialogTitle"
      :visible.sync="dialogBox"
      :append-to-body="true"
      :before-close="dialogClose"
      :close-on-click-modal="false"
    >
      <el-form :model="dialogForm" :rules="formRules" ref="dialogForm">
        <el-form-item label="角色组" prop="gid" :label-width="formLabelWidth">
          <template>
            <el-select multiple v-model="dialogForm.gid" placeholder="请选择">
              <el-option
                v-for="item in permissions"
                :key="item.id"
                :label="item.name"
                :value="item.id">
                <span>{{ item.spacer }}{{ item.name }}</span>
              </el-option>
            </el-select>
          </template>
        </el-form-item>
        <el-form-item label="登录名" prop="name" :label-width="formLabelWidth">
          <el-input v-model="dialogForm.name" placeholder="请输入登录名" clearable autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item label="密码" prop="password" :label-width="formLabelWidth">
          <!-- 放置两个隐藏input解决浏览器自动填充账号密码的问题 -->
          <el-input style="position:fixed;opacity:0;left:0;"></el-input>
          <el-input type="password" style="position:fixed;opacity:0;left:0;"></el-input>
          <el-input
            v-model="dialogForm.password"
            :placeholder="passwordStr"
            clearable
            autocomplete="off"
            show-password
          ></el-input>
        </el-form-item>
        <el-form-item
          v-if="dialogForm.password"
          label="确认密码"
          prop="repassword"
          :label-width="formLabelWidth"
        >
          <el-input type="password" style="position:fixed;opacity:0;left:0;"></el-input>
          <el-input
            v-model="dialogForm.repassword"
            placeholder="请再次输入密码"
            clearable
            autocomplete="off"
            show-password
          ></el-input>
        </el-form-item>
        <el-form-item label="昵称" prop="nickname" :label-width="formLabelWidth">
          <el-input v-model="dialogForm.nickname" placeholder="请输入昵称" clearable autocomplete="off"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogClose">关 闭</el-button>
        <el-button type="primary" @click="dialogSubmit" :loading="dialogLoading">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { getList, addAdmin, editAdmin, delAdmin } from '@/api/setting/admin'
import * as group from '@/api/setting/group'

export default {
  data() {
    const repassword = (rule, value, callback) => {
      if (value !== this.dialogForm.password) {
        callback(new Error('两次输入的密码不一样'))
      } else {
        callback()
      }
    }
    return {
      tableKey: 0,
      filter: {
        search: ''
      },
      list: [],
      field: [
        { prop: 'id', label: 'ID', isShow: false },
        { prop: 'name', label: '管理员', isShow: true },
        { prop: 'nickname', label: '昵称', isShow: true },
        { prop: 'group_name', label: '所属角色组', isShow: true },
        { prop: 'logintime', label: '最后登陆', isShow: true },
        { prop: 'createtime', label: '创建时间', isShow: true }
      ],
      currentPage: 1,
      pageSize: 20,
      dateTotal: 0,
      loading: false,

      dialogBox: false,
      dialogLoading: false,
      passwordStr: '请输入密码',
      formLabelWidth: '100px',
      formRules: {
        name: [{ required: true, message: '请输入登陆名', trigger: 'blur' }],
        password: [{ required: true, message: '请输入密码', trigger: 'blur' }],
        repassword: [
          { required: true, trigger: 'blur', validator: repassword }
        ]
      },
      permissions: [],
      dialogTitle: '',
      dialogForm: {
        gid: '',
        id: '',
        name: '',
        password: '',
        repassword: '',
        nickname: '',
        group_name: ''
      }
    }
  },
  methods: {
    sizeChange(val) {
      this.pageSize = val
      this.getList()
    },
    currentChange(val) {
      this.currentPage = val
      this.getList()
    },
    dialogClose() {
      this.dialogBox = false
      this.dialogTitle = ''
      this.dialogForm = {
        id: '',
        name: '',
        password: '',
        repassword: '',
        nickname: '',
        group_name: ''
      }
    },
    // 打开新增窗口
    handleDialogAdd() {
      this.dialogBox = true
      this.dialogTitle = '添加管理员'
      this.passwordStr = '请输入密码'

      // 添加密码验证权限
      this.formRules.password = [
        { required: true, message: '请输入密码', trigger: 'blur' }
      ]
    },
    // 打开编辑窗口
    handleDialogEdit(data) {
      this.dialogBox = true
      this.dialogTitle = '编辑[' + data.name + ']管理员'
      this.passwordStr += '，留空则不修改密码'

      // 取消密码验证权限
      this.formRules.password = null

      // 数据赋值
      this.dialogForm.id = data.id
      this.dialogForm.gid = data.gid
      this.dialogForm.name = data.name
      this.dialogForm.nickname = data.nickname
      this.dialogForm.group_name = data.group_name
    },
    // 窗口提交
    dialogSubmit() {
      this.$refs.dialogForm.validate((valid) => {
        if (valid) {
          if (this.dialogForm.id) {
            this.dialogLoading = true
            editAdmin(this.dialogForm)
              .then((res) => {
                this.dialogLoading = false
                this.dialogClose()
                this.getList()
                this.$message({ message: res.msg, type: 'success' })
              })
              .catch(() => {
                this.dialogLoading = false
              })
          } else {
            this.dialogLoading = true
            addAdmin(this.dialogForm)
              .then((res) => {
                this.dialogLoading = false
                this.dialogClose()
                this.getList()
                this.$message({ message: res.msg, type: 'success' })
              })
              .catch(() => {
                this.dialogLoading = false
              })
          }
        }
      })
    },
    // 获取管理员下拉列表
    getGroupSelect() {
      group.getSelect().then((res) => {
        this.permissions = res.data
      })
    },
    // 删除
    delRow(id) {
      this.$confirm('确认删除该管理员吗?', '提示', {
        type: 'warning'
      })
        .then(() => {
          delAdmin({ id: id })
            .then((res) => {
              this.getList()
              this.$message({ message: res.msg, type: 'success' })
            })
            .catch(() => {})
        })
        .catch(() => {})
    },
    getList() {
      this.loading = true
      const para = {
        page: this.currentPage,
        size: this.pageSize,
        filter: this.filter
      }
      getList(para)
        .then((res) => {
          this.list = res.data.list
          this.dateTotal = res.data.total
          this.loading = false
        })
        .catch(() => {
          this.loading = false
        })
    }
  },
  created: function() {
    this.getGroupSelect()
  },
  mounted() {
    this.getList()
  }
}
</script>

<style lang="scss" scroed>
.filter-input {
  display: inline-block;
}
</style>
