<template>
  <div class="app-container">
    <div class="filter-container">
      <el-form :inline="true">
        <el-form-item>
          <el-input
            v-model="filter"
            placeholder="咨询项目"
            size="mini"
            class="filter-input"
          />
        </el-form-item>
        <el-form-item>
          <el-button
type="primary"
size="mini"
@click="getList"
            >查询</el-button
          >
          <el-button
            type="danger"
            :disabled="multipleSelection.length == 0"
            size="mini"
            @click="delMultiple"
          >
            批量删除
            <i class="el-icon-delete el-icon--right"></i>
          </el-button>
          <el-button type="success" size="mini" @click="handleDialogAdd">
            添加
            <i class="el-icon-plus el-icon--right"></i>
          </el-button>
          <el-button type="warning" size="mini" @click="exportTemplate()">
            下载导入模板
            <i class="el-icon-folder-opened el-icon--right"></i>
          </el-button>
          <el-button type="primary" size="mini" @click="handleUpload()">
            上传
            <i class="el-icon-upload el-icon--right"></i>
          </el-button>
          <!-- 文件上传编辑框 -->
          <el-dialog
            title="上传文件"
            :visible.sync="upload"
            :append-to-body="true"
          >
            <el-upload
              class="upload-demo"
              v-model="upload"
              ref="upload"
              drag
              multiple
              accept=".xls, .xlsx, .csv"
              action="http://interface.tianquji.com/sales/order/imports"
              :headers="{ token }"
              :on-success="handle_success"
              :show-file-list="true"
              :before-upload="beforeUpload"
              :auto-upload="false"
            >
              <i class="el-icon-upload"></i>
              <div class="el-upload__text">{{ filename }}</div>
              <div class="el-upload__tip" slot="tip">
                只能上传excel文件，且不超过1.5mb
              </div>
            </el-upload>
            <div slot="footer" class="dialog-footer">
              <el-button @click="submitCancel()">取 消</el-button>
              <el-button
                type="primary"
                @click="submitUpload()"
                :loading="importLoading"
                >确 定</el-button
              >
            </div>
          </el-dialog>
        </el-form-item>
        <el-form-item style="float: right">
          <el-popover placement="bottom" width="300" trigger="click">
            <el-col :span="12" v-for="(v, index) in field" :key="index">
              <el-checkbox
                v-model="v.isShow"
                class="filter-item"
                style="margin-left: 15px"
                @change="tableKey = tableKey + Math.random()"
                :label="v.label"
              ></el-checkbox>
            </el-col>
            <el-button
              type="primary"
              icon="el-icon-setting"
              size="mini"
              slot="reference"
              >设置</el-button
            >
          </el-popover>
        </el-form-item>
      </el-form>
    </div>
    <!-- 数据表格 -->
    <el-table
      ref="multipleTable"
      :key="tableKey"
      :data="list"
      stripe
      tooltip-effect="dark"
      :header-cell-style="{ background: '#f5f7fa' }"
      fit
      border
      v-loading="loading"
      @selection-change="handleSelectionChange"
      style="width: 100%"
    >
      <el-table-column type="selection" width="55"></el-table-column>
      <template v-for="(v, index) in field">
        <el-table-column
          :key="index"
          v-if="v.isShow"
          :prop="v.prop"
          :label="v.label"
          :sortable="v.sortable"
          show-overflow-tooltip
        ></el-table-column>
      </template>
      <el-table-column
        prop="remark"
        label="分配对象"
        filter-placement="bottom-end"
      >
        <template slot-scope="scope">
          <el-tag type="success" v-if="scope.row.name">
            {{ scope.row.name }}
          </el-tag>
          <el-tag type="danger" v-else>
            暂无分配
          </el-tag>
        </template>
      </el-table-column>
      <!-- 操作列 -->
      <el-table-column
        prop="operation"
        label="操作"
        width="225"
        show-overflow-tooltip
      >
        <template slot-scope="scope">
          <el-button
            type="primary"
            plain
            size="mini"
            @click="handleDialogEdit(scope.row)"
          >
            <i class="el-icon-edit el-icon--right"></i>
          </el-button>
          <el-button
            type="danger"
            plain
            size="mini"
            @click="delRow(scope.row.id)"
          >
            <i class="el-icon-delete el-icon--right"></i>
          </el-button>
          <el-button type="info" plain size="mini" @click="copyRow(scope.row)">
            <i class="el-icon-document el-icon--right"></i>
          </el-button>
        </template>
      </el-table-column>
    </el-table>
    <!-- 翻页 -->
    <div class="data-page">
      <el-pagination
        @size-change="sizeChange"
        @current-change="currentChange"
        :current-page="currentPage"
        background
        :page-sizes="[10, 20, 30, 40, 50, 100, 200]"
        :page-size="pageSize"
        class="common-page"
        layout="total, sizes, prev, pager, next, jumper"
        :total="dateTotal"
      ></el-pagination>
    </div>
    <el-dialog
      :title="dialogTitle"
      :visible.sync="dialogBox"
      :append-to-body="true"
      :before-close="dialogClose"
      :close-on-click-modal="false"
    >
      <el-form :model="dialogForm" :rules="formRules" ref="dialogForm">
        <el-form-item
          label="资源编号"
          prop="express_no"
          :label-width="formLabelWidth"
        >
          <el-input
            v-model="dialogForm.express_no"
            :disabled="true"
            placeholder="请输入资源编号"
            clearable
            autocomplete="off"
          ></el-input>
        </el-form-item>
        <el-form-item
          label="客户姓名"
          prop="express_name"
          :label-width="formLabelWidth"
        >
          <el-input
            v-model="dialogForm.express_name"
            placeholder="请输入客户姓名"
            clearable
            autocomplete="off"
          ></el-input>
        </el-form-item>
        <el-form-item
          label="联系方式"
          prop="express_moblie"
          :label-width="formLabelWidth"
        >
          <el-input
            placeholder="请输入联系方式"
            v-model="dialogForm.express_moblie"
            class="input-with-select"
            oninput="value=value.replace(/[^\d]/g,'')"
            clearable
            autocomplete="off"
            @change="changeCode"
          >
            <el-select
              v-model="dialogForm.prefix_moblie"
              slot="prepend"
              placeholder="请选择前缀"
            >
              <el-option label="T" value="T"></el-option>
              <el-option label="W" value="W"></el-option>
              <el-option label="Q" value="Q"></el-option>
            </el-select>
          </el-input>
        </el-form-item>
        <el-form-item
          label="地址"
          prop="express_address"
          :label-width="formLabelWidth"
        >
          <el-input
            v-model="dialogForm.express_address"
            placeholder="请输入地址"
            clearable
            autocomplete="off"
          ></el-input>
        </el-form-item>
        <el-form-item
          label="来源"
          prop="express_source"
          :label-width="formLabelWidth"
        >
          <el-input
            v-model="dialogForm.express_source"
            placeholder="请输入来源"
            clearable
            autocomplete="off"
          ></el-input>
        </el-form-item>
        <el-form-item
          label="咨询项目"
          prop="express_advisory"
          :label-width="formLabelWidth"
        >
          <el-input
            v-model="dialogForm.express_advisory"
            placeholder="请输入咨询项目"
            clearable
            autocomplete="off"
          ></el-input>
        </el-form-item>
        <el-form-item
          label="咨询链接"
          prop="express_adviseryurl"
          :label-width="formLabelWidth"
        >
          <el-input
            v-model="dialogForm.express_adviseryurl"
            placeholder="请输入咨询链接"
            clearable
            autocomplete="off"
          ></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogClose">关 闭</el-button>
        <el-button
type="primary"
@click="dialogSubmit"
:loading="dialogLoading"
          >确 定</el-button
        >
      </div>
    </el-dialog>
  </div>
</template>
<script>
import {
  getList,
  addOrder,
  editOrder,
  delOrder,
  copyOrder
} from '@/api/sales/order'
import { mapGetters } from 'vuex'

export default {
  computed: {
    ...mapGetters(['name', 'token'])
  },
  data() {
    return {
      tableKey: 0,
      filter: '',
      multipleSelection: [],
      list: [],
      field: [
        { prop: 'id', label: 'ID', isShow: false },
        { prop: 'express_no', label: '资源编号', isShow: true },
        { prop: 'express_name', label: '客户姓名', isShow: true },
        { prop: 'express_moblie', label: '联系方式', isShow: true },
        { prop: 'express_address', label: '地址', isShow: true },
        { prop: 'express_source', label: '来源', isShow: true },
        {
          prop: 'express_advisory',
          label: '咨询项目',
          isShow: true,
          sortable: true
        },
        { prop: 'express_adviseryurl', label: '咨询链接', isShow: true },
        { prop: 'updatetime', label: '修改时间', isShow: false },
        { prop: 'createtime', label: '创建时间', isShow: false },
        { prop: 'copy_num', label: '复制次数', isShow: true }
      ],

      currentPage: 1,
      pageSize: 20,
      dateTotal: 0,
      loading: false,

      // 导入
      importFile: '',
      filename: '请将文件拖到此处，或点击上传',
      importLoading: false,
      upload: false,
      dialogBox: false,
      dialogLoading: false,

      formLabelWidth: '100px',
      dialogTitle: '',
      formRules: {
        express_no: [
          { required: false, message: '请输入资源编号', trigger: 'blur' }
        ],
        express_name: [
          { required: true, message: '请输入客户姓名', trigger: 'blur' }
        ],
        profix_moblie: [
          { required: true, message: '请输入前缀/联系方式', trigger: 'blur' }
        ],
        express_moblie: [
          { required: true, message: '请输入前缀/联系方式', trigger: 'blur' }
        ],
        express_address: [
          { required: true, message: '请输入地址', trigger: 'blur' }
        ],
        express_source: [
          { required: true, message: '请输入来源', trigger: 'blur' }
        ],
        express_advisory: [
          { required: true, message: '请输入咨询项目', trigger: 'blur' }
        ],
        express_adviseryurl: [
          { required: true, message: '请输入咨询链接', trigger: 'blur' }
        ]
      },
      dialogForm: {
        id: '',
        username: '',
        express_no: '',
        express_name: '',
        express_moblie: '',
        prefix_moblie: '',
        express_address: '',
        express_source: '',
        express_advisory: '',
        express_adviseryurl: ''
      }
    }
  },

  methods: {
    // 校验电话号码
    changeCode() {
      if (this.dialogForm.prefix_moblie === 'T') {
        if (this.dialogForm.express_moblie.length !== 11) {
          alert('联系方式必须输入为11位数')
          return false
        }
      }
      return true
    },

    // 下载导入模板
    formatJson(filterVal, jsonData) {
      return jsonData.map((v) => filterVal.map((j) => v[j]))
    },
    exportTemplate() {
      require.ensure([], () => {
        const { export_json_to_excel } = require('@/vendor/Export2Excel')
        const tHeader = [
          '客户姓名',
          '联系方式',
          '地址',
          '来源',
          '咨询项目',
          '咨询链接'
        ]
        const filterVal = [
          'express_name',
          'express_moblie',
          'express_address',
          'express_source',
          'express_advisory',
          'express_adviseryurl'
        ]
        const list = []
        const data = this.formatJson(filterVal, list)
        export_json_to_excel(tHeader, data, '导入资源模板')
      })
    },

    // 导入按钮
    handleUpload() {
      this.importLoading = false
      this.upload = true
    },

    // 上传文件之前的钩子，参数为上传的文件，若返回false或者返回Promise且被reject，则停止上传
    beforeUpload(file) {
      const size = file.size / 1024 / 1024
      if (size > 1.5) {
        this.$message.warning('文件大小不得超过1.5M')
      }
    },

    // 提交文件上传
    submitUpload: function() {
      this.importLoading = true
      const fileFormData = new FormData()
      fileFormData.append('file', this.importFile)
      // 调用此方法才能把数据传送到后台
      this.$refs.upload.submit()
      console.log(this.importFile)
    },

    // 上传成功后的钩子
    handle_success(res) {
      this.importLoading = false
      if (res.code === 0) {
        this.$refs.upload.clearFiles()
        this.importFile = ''
        this.filename = '请将文件拖到此处，或点击上传'
        this.upload = false
        this.$message({ message: res.msg, type: 'success' })
        this.getList()
      }
    },

    // 取消文件上传
    submitCancel: function() {
      this.upload = false
      this.filename = '请将文件拖到此处，或点击上传'
      this.$refs.upload.clearFiles()
    },

    // 记录选中数据的长度
    handleSelectionChange(val) {
      this.multipleSelection = val.length > 0 ? val : [] // 记录选中的数据
    },

    // 筛选是否已分配对象
    // filterTag(value, row) {
    //   if (value) {
    //     return row.remark != null
    //   } else {
    //     return row.remark === ''
    //   }
    // },

    // 批量删除
    delMultiple() {
      this.$confirm('确认批量删除资源吗?', '提示', {
        type: 'warning'
      })
        .then(() => {
          // ids
          const ids = this.multipleSelection.map((item) => item.id)
          delOrder({ orderid: ids })
            .then((res) => {
              this.getList()
              this.$message({ message: res.msg, type: 'success' })
            })
            .catch(() => {})
        })
        .catch(() => {})
    },

    // 删除
    delRow(ids) {
      this.$confirm('确认删除该资源吗?', '提示', {
        type: 'warning'
      })
        .then(() => {
          delOrder({ orderid: ids })
            .then((res) => {
              this.getList()
              this.$message({ message: res.msg, type: 'success' })
            })
            .catch(() => {})
        })
        .catch(() => {})
    },

    // 复制内容
    copyRow(row) {
      const para = {
        ids: row.id
      }
      copyOrder(para)
        .then((res) => {
          this.$message({
            showClose: true,
            message: '复制成功！',
            type: 'success'
          })
        })
        .catch(() => {})
      var oInput = document.createElement('textarea')
      oInput.value = [
        '编号：' + row.express_no,
        '\n' + '姓名：' + row.express_name,
        '\n' + '电话：' + row.express_moblie,
        '\n' + '地区：' + row.express_address,
        '\n' + '来源：' + row.express_source,
        '\n' + '咨询项目：' + row.express_advisory,
        '\n' + '咨询页面：' + row.express_adviseryurl
      ]
      console.log(oInput.value.replace('/\n/g', ''))
      document.body.appendChild(oInput) // 将临时组件加入到页面最底下
      oInput.select()
      document.execCommand('Copy')
      oInput.className = 'oInput'
      oInput.style.display = 'none'
      document.body.removeChild(oInput) // 删除临时的textarea
    },

    // 打开新增窗口
    handleDialogAdd() {
      this.dialogBox = true
      this.dialogTitle = '添加资源'
    },

    // 打开编辑窗口
    handleDialogEdit(data) {
      this.dialogBox = true
      this.dialogTitle = '编辑资源'
      this.dialogForm.id = data.id
      this.dialogForm.express_no = data.express_no
      this.dialogForm.express_name = data.express_name
      this.dialogForm.express_moblie = data.express_moblie
      this.dialogForm.prefix_moblie = data.express_moblie.substr(0, 1)
      this.dialogForm.express_address = data.express_address
      this.dialogForm.express_source = data.express_source
      this.dialogForm.express_advisory = data.express_advisory
      this.dialogForm.express_adviseryurl = data.express_adviseryurl
    },

    // 窗口提交

    dialogSubmit() {
      if (this.changeCode()) {
        this.$refs.dialogForm.validate((valid) => {
          if (valid) {
            if (this.dialogForm.id) {
              this.dialogLoading = true
              this.dialogForm.username = this.name
              editOrder(this.dialogForm)
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
              this.dialogForm.username = this.name
              addOrder(this.dialogForm)
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
      }
    },

    dialogClose() {
      this.dialogBox = false
      this.dialogTitle = ''
      this.dialogForm = {
        id: '',
        username: '',
        express_no: '',
        express_name: '',
        express_moblie: '',
        express_address: '',
        express_source: '',
        express_advisory: '',
        express_adviseryurl: ''
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

    getList() {
      this.loading = true
      const para = {
        page: this.currentPage,
        size: this.pageSize,
        remark: this.pageSize,
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

  mounted() {
    this.getList()
  }
}
</script>

<style lang="scss" scroed>
.filter-input {
  display: inline-block;
}
.el-dialog {
  position: relative;
  margin: 0 auto 50px;
  background: #fff;
  border-radius: 2px;
  -webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
  /* width: 50%; */
  width: 400px;
}
</style>

<style>
.el-select .el-input {
  width: 130px;
}
.el-upload-dragger .el-icon-upload {
  font-size: 67px;
  color: #1890ff;
  margin: 40px 0 16px;
  line-height: 50px;
}
.input-with-select .el-input-group__prepend {
  background-color: #fff;
}
.el-dialog__footer {
  padding: 20px;
  padding-top: 10px;
  text-align: center;
  box-sizing: border-box;
}
</style>
