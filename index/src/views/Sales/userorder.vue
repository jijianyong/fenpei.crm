<template>
  <div class="app-container">
    <div class="filter-container">
      <el-form :inline="true">
        <el-form-item>
          <el-input v-model="filter" placeholder="咨询项目" size="mini" class="filter-input" />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" size="mini" @click="getList">查询</el-button>
        </el-form-item>
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
      <el-table-column prop="operation" label="操作" width="105" show-overflow-tooltip>
        <template slot-scope="scope">
          <el-button type="info" plain size="mini" @click="copyRow(scope.row)">
            <i class="el-icon-document el-icon--right"></i>
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
  </div>
</template>

<script>
import { getUserList as getList, copyOrder } from '@/api/sales/order'
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

      dialogBox: false,
      dialogLoading: false,
      formLabelWidth: '100px',
      dialogTitle: ''
    }
  },
  methods: {
    // 记录选中数据的长度
    handleSelectionChange(val) {
      this.multipleSelection = val.length > 0 ? val : [] // 记录选中的数据
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
      //   console.log(this.name)
      document.body.appendChild(oInput)
      oInput.select()
      document.execCommand('Copy')
      oInput.className = 'oInput'
      oInput.style.display = 'none'
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
        filter: this.filter,
        user: this.name
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
</style>
