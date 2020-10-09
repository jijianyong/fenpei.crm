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
      :header-cell-style="{background:'#f5f7fa'}"
      fit
      border
      v-loading="loading"
      style="width: 100%"
      :row-class-name="tableRowClassName"
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
      <el-table-column key="11" prop="status" label="分配状态" align="center" show-overflow-tooltip>
        <template slot-scope="scope">
          <el-switch
            v-model="scope.row.status"
            class="scope-demo"
            active-color="#00A854"
            active-text="开启"
            :active-value="0"
            align="left"
            inactive-color="#F04134"
            inactive-text="关闭"
            :inactive-value="1"
            @change="changeSwitch(scope.row)"
          />
        </template>
      </el-table-column>
      <!-- 操作列 -->
      <el-table-column prop="operation" label="操作" width="185px" show-overflow-tooltip>
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
      <!-- 添加-编辑表单内容 -->
      <el-form v-model="dialogForm" ref="dialogForm" label-width="120px">
        <el-form-item label="选择规则用户">
          <el-select clearable v-model="dialogForm.uid" placeholder="请选择">
            <el-option
              v-for="item in permissions"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            >
              <span>{{ item.name }}</span>
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="选择分配时间">
          <el-time-select
            placeholder="分配开始时间"
            v-model="dialogForm.start_time"
            :picker-options="{
      start: '00:00',
      step: '00:30',
      end: '24:00'
    }"
          ></el-time-select>-
          <el-time-select
            placeholder="分配结束时间"
            v-model="dialogForm.end_time"
            :picker-options="{
      start: '00:00',
      step: '00:30',
      end: '24:00',
      minTime: dialogForm.start_time
    }"
          ></el-time-select>
        </el-form-item>
        <el-form-item label="分配地域">
          <el-input v-model="dialogForm.express_address"></el-input>
        </el-form-item>
        <el-form-item label="分配来源">
          <el-input v-model="dialogForm.express_source"></el-input>
        </el-form-item>
        <el-form-item label="分配项目">
          <el-input v-model="dialogForm.express_advisory"></el-input>
        </el-form-item>
        <el-form-item label="分配资源数量">
          <el-input-number v-model="dialogForm.nums" :min="1"></el-input-number>
        </el-form-item>
        <el-form-item label="分配状态">
          <el-switch
            v-model="dialogForm.status"
            active-color="#13ce66"
            inactive-color="#ff4949"
            :active-value=0
            :inactive-value=1
            :active-text="dialogForm.status < 1 ? '开启分配' : '关闭分配'"
          ></el-switch>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogBox = false">取 消</el-button>
        <el-button type="primary" @click="addSubmit()" :loading="dialogLoading">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import {
  getList,
  addDistribution,
  editDistribution,
  delDistribution
} from '@/api/setting/distribution'
import * as admin from '@/api/setting/admin'

export default {
  data() {
    return {
      tableKey: 0,
      filter: {
        search: ''
      },
      list: [],
      field: [
        { prop: 'id', label: 'ID', isShow: false },
        { prop: 'uid', label: '用户id', isShow: false },
        { prop: 'name', label: '分配用户', isShow: true },
        { prop: 'start_time', label: '分配开始时间', isShow: true },
        { prop: 'end_time', label: '分配结束时间', isShow: true },
        { prop: 'express_address', label: '分配地域', isShow: true },
        { prop: 'express_source', label: '分配来源', isShow: true },
        { prop: 'express_advisory', label: '分配项目', isShow: true },
        { prop: 'nums', label: '分配资源数量', isShow: true },
        { prop: 'remark', label: '今日已分配数量', isShow: true }
      ],
      currentPage: 1,
      pageSize: 20,
      dateTotal: 0,
      loading: false,
      ifAddBox: false, // 是否开启了新增窗口

      dialogBox: false,
      dialogLoading: false,
      formLabelWidth: '100px',
      permissions: [],
      dialogTitle: '',
      dialogForm: {
        id: '',
        uid: '',
        username: '',
        start_time: '',
        end_time: '',
        express_address: '',
        express_source: '',
        express_advisory: '',
        nums: 1,
        status: 0
      }
    }
  },
  methods: {
    tableRowClassName({ row, rowIndex }) {
      if (row.status === 1) {
        return 'warning-row'
      } else {
        return 'success-row'
      }
    },
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
        uid: '',
        username: '',
        start_time: '',
        end_time: '',
        express_address: '',
        express_source: '',
        express_advisory: '',
        nums: 1,
        status: 0
      }
    },
    // 打开新增窗口
    handleDialogAdd() {
      this.dialogClose()
      this.dialogBox = true
      this.dialogTitle = '添加资源分配规则'
      this.ifAddBox = true
    },
    // 打开编辑窗口
    handleDialogEdit(data) {
      this.dialogBox = true
      this.dialogTitle = '编辑资源分配规则'
      this.ifAddBox = false

      // 数据赋值
      this.dialogForm.id = data.id
      this.dialogForm.uid = data.uid
      this.dialogForm.username = data.username
      this.dialogForm.start_time = data.start_time
      this.dialogForm.end_time = data.end_time
      this.dialogForm.express_address = data.express_address
      this.dialogForm.express_source = data.express_source
      this.dialogForm.express_advisory = data.express_advisory
      this.dialogForm.nums = data.nums
      this.dialogForm.status = data.status
    },
    // 窗口提交
    addSubmit() {
      if (this.ifAddBox) {
        if (!this.dialogForm.id) {
          this.dialogLoading = true
          addDistribution(this.dialogForm).then((res) => {
            this.dialogLoading = false
            this.ifAddBox = false
            this.dialogClose()
            this.getList()
            this.$message({
              message: res.msg,
              type: 'success'
            })
          })
        }
      } else {
        this.dialogLoading = true
        editDistribution(this.dialogForm).then((res) => {
          this.dialogLoading = false
          this.dialogClose()
          this.getList()
          this.$message({ message: res.msg, type: 'success' })
        })
      }
    },

    // 分配状态更新
    changeSwitch(data) {
      editDistribution(data).then((res) => {
        this.getList()
        this.$message({ message: res.msg, type: 'success' })
      })
    },

    // 获取用户下拉列表
    getAdminSelect() {
      admin.select().then((res) => {
        this.permissions = res.data
      })
    },
    // 删除
    delRow(id) {
      this.$confirm('确认删除该管理员吗?', '提示', {
        type: 'warning'
      })
        .then(() => {
          delDistribution({ id: id })
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
    this.getAdminSelect()
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
.el-date-editor.el-input,
.el-date-editor.el-input__inner {
  width: 150px;
}
.el-table .warning-row {
  background: #fef0f0;
}

.el-table .success-row {
  background: #ffffff;
}

.scope-demo .el-switch__label {
  position: absolute;
  display: none;
  color: #fff;
}

.scope-demo .el-switch__label--right {
  z-index: 1;
  right: -3px;
}

.scope-demo .el-switch__label--left {
  z-index: 1;
  left: 19px;
}

.scope-demo .el-switch__label.is-active {
  display: block;
}

.scope-demo.el-switch .el-switch__core,
.el-switch .el-switch__label {
  width: 50px !important;
}
</style>
