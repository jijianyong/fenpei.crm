<template>
  <div class="crm-main">
    <div class="btn-operation">
      <el-button type="danger" size="mini" @click="batchRemove">
        批量删除
        <i class="el-icon-delete el-icon--right"></i>
      </el-button>
      <el-button type="success" size="small" @click="handleAdd">
        新增规则
        <i class="el-icon-plus el-icon--right"></i>
      </el-button>
      <!-- 编辑弹出框 -->
      <el-dialog title="编辑" :visible.sync="editFormVisible" :close-on-click-modal="false">
        <el-form :model="editForm" label-width="80px" :rules="editFormRules" ref="editForm">
          <el-form-item label="父级">
            <el-select clearable v-model="editForm.pid" placeholder="请选择">
              <el-option
                v-for="item in selectData"
                :key="item.id"
                :label="item.title"
                :value="item.id"
              >
                <span>{{ item.spacer }}{{ item.title }}</span>
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="规则">
            <el-input v-model="editForm.name" autocomplete="off"></el-input>
          </el-form-item>
          <el-form-item label="标题">
            <el-input v-model="editForm.title" autocomplete="off"></el-input>
          </el-form-item>
          <el-form-item label="权重">
            <el-input v-model="editForm.weigh" autocomplete="off" :precision="0"></el-input>
          </el-form-item>
          <el-form-item label="备注">
            <el-input v-model="editForm.remark" autocomplete="off"></el-input>
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click.native="editFormVisible = false">取消</el-button>
          <el-button type="primary" @click.native="editSubmit" :loading="editLoading">提交</el-button>
        </div>
      </el-dialog>
      <!-- 新增弹出框 -->
      <el-dialog title="新增" :visible.sync="addFormVisible">
        <el-form :model="addForm" label-width="80px" :rules="addFormRules" ref="addForm">
          <el-form-item label="父级">
            <el-select v-model="addForm.pid" clearable placeholder="请选择">
              <el-option
                v-for="item in selectData"
                :key="item.id"
                :label="item.title"
                :value="item.id"
              >
                <span>{{ item.spacer }}{{ item.title }}</span>
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="规则">
            <el-input v-model="addForm.name" placeholder="规则" autocomplete="off"></el-input>
          </el-form-item>
          <el-form-item label="标题">
            <el-input v-model="addForm.title" placeholder="标题" autocomplete="off"></el-input>
          </el-form-item>
          <el-form-item label="权重">
            <el-input v-model="addForm.weigh" placeholder="权重" autocomplete="off" :precision="0"></el-input>
          </el-form-item>
          <el-form-item label="备注">
            <el-input v-model="addForm.remark" placeholder="备注" autocomplete="off"></el-input>
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click.native="addFormVisible = false">取消</el-button>
          <el-button type="primary" @click.native="addSubmit" :loading="addLoading">提交</el-button>
        </div>
      </el-dialog>
      <el-divider></el-divider>
    </div>
    <!-- 数据表格 -->
    <template>
      <div class="crm-tada-table">
        <el-table
          v-loading="listLoading"
          ref="multipleTable"
          :height="tableHeight"
          :data="ruleData"
          stripe
          row-key="id"
          default-expand-all
          tooltip-effect="dark"
          :header-cell-style="{background:'#f5f7fa'}"
          style="width: 100%"
          @selection-change="selsChange"
        >
          <el-table-column type="selection" width="55px"></el-table-column>

          <el-table-column prop="title" label="标题" show-overflow-tooltip></el-table-column>
          <el-table-column prop="id" label="ID" show-overflow-tooltip></el-table-column>
          <el-table-column prop="name" label="规则" show-overflow-tooltip></el-table-column>
          <el-table-column prop="weigh" label="权重" show-overflow-tooltip></el-table-column>
          <el-table-column prop="remark" label="备注" show-overflow-tooltip></el-table-column>
          <el-table-column
            prop="operation"
            label="操作"
            width="210px"
            fixed="right"
            show-overflow-tooltip
          >
            <template slot-scope="scope">
              <el-button
                type="primary"
                plain
                size="mini"
                @click="handleEdit(scope.$index, scope.row)"
              >
                编辑
                <i class="el-icon-edit el-icon--right"></i>
              </el-button>
              <el-button
                type="danger"
                plain
                size="mini"
                @click="handleDel(scope.$index, scope.row)"
              >
                删除
                <i class="el-icon-delete el-icon--right"></i>
              </el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </template>
  </div>
</template>

<script>
import * as rule from '@/api/setting/rule'
export default {
  name: 'Rule',
  data() {
    return {
      // 标题高亮
      tabActiveName: 'ruleList',
      // 列表选中列
      sels: [],
      // 编辑设置
      editFormVisible: false, // 编辑界面是否显示
      // 加载
      editLoading: false,
      // 规则
      editFormRules: {
        name: [{ required: true, message: '请输入规则名称', trigger: 'blur' }]
      },

      // 编辑界面数据
      editForm: {
        ismenu: '',
        pid: '',
        name: '',
        title: '',
        weigh: '',
        condition: '',
        remark: '',
        status: ''
      },

      // 新增账户弹出框
      addFormVisible: false,
      addLoading: false,
      // 新增规则
      addFormRules: {
        account_person: [
          { required: true, message: '请输入规则名称', trigger: 'blur' }
        ]
      },
      addForm: {
        pid: '',
        name: '',
        title: '',
        weigh: '',
        condition: '',
        remark: '',
        status: ''
      },
      // 表格数据
      tableHeight: window.innerHeight - 210,

      ruleData: [],
      formLabelWidth: '100px',
      // 加载动画
      // 下拉列表
      selectData: [],
      listLoading: true,
      // 权限节点树
      ruletree: [],
      // 权限节点树默认选中、同时也作用于默认展开
      ruleSelected: [],
      // 权限节点树配置
      defaultRuleTree: {
        children: 'children',
        label: 'label'
      }
    }
  },
  methods: {
    handleClick(tab, event) {
      // console.log(tab, event);
    },
    selsChange: function(sels) {
      this.sels = sels
    },
    // 显示开关
    changeIsmenuSwitch(data) {
      rule.disabledIsmenu({ id: data.id }).then((res) => {
        if (res.code === 0) {
          this.$message({ message: res.msg, type: 'success' })
        }
      })
    },
    changeStatusSwitch(data) {
      rule.disabledIsmenu({ id: data.id }).then((res) => {
        if (res.code === 0) {
          this.$message({ message: res.msg, type: 'success' })
        }
      })
    },
    getSelect() {
      rule.getSelect().then((res) => {
        this.selectData = res.data
      })
    },
    // 获取列表
    getRule() {
      this.listLoading = true
      rule.getRuleList().then((res) => {
        this.ruleData = res.data
        this.listLoading = false
      })
    },
    // 显示新增界面
    handleAdd: function() {
      this.addFormVisible = true
      this.getSelect()
    },

    // 新增
    addSubmit: function() {
      this.$refs.addForm.validate((valid) => {
        if (valid) {
          this.addLoading = true
          const para = Object.assign({}, this.addForm)
          rule.addRule(para).then((res) => {
            this.addLoading = false
            if (res.code === 0) {
              this.$message({
                message: '提交成功',
                type: 'success'
              })
              this.addForm = {
                pid: '',
                name: '',
                title: '',
                weigh: '',
                condition: '',
                remark: '',
                status: ''
              }
              this.addFormVisible = false
              this.getRule()
            }
          })
        }
      })
    },
    // 显示编辑界面
    handleEdit: function(index, row) {
      this.editFormVisible = true
      this.editForm = Object.assign({}, row)
      this.editForm.pid = this.editForm.pid === 0 ? '' : this.editForm.pid
      this.getSelect()
    },
    // 编辑
    editSubmit: function() {
      this.$refs.editForm.validate((valid) => {
        if (valid) {
          this.editLoading = true
          const para = Object.assign({}, this.editForm)
          rule.editRule(para).then((res) => {
            this.editLoading = false
            if (res.code === 0) {
              this.$message({
                message: '提交成功',
                type: 'success'
              })
            }
            this.$refs['editForm'].resetFields()
            this.editFormVisible = false
            this.getRule()
          })
        }
      })
    },
    // 删除
    handleDel: function(index, row) {
      this.$confirm('确认删除该记录吗?', '提示', {
        type: 'warning'
      })
        .then(() => {
          this.listLoading = true
          // NProgress.start();
          const para = { id: row.id }
          rule.removeRule(para).then((res) => {
            this.listLoading = false
            // NProgress.done();
            if (res.code === 0) {
              this.$message({
                message: '删除成功',
                type: 'success'
              })
            }
            this.getRule()
          })
        })
        .catch(() => {})
    },
    // 批量删除
    batchRemove: function() {
      var ids = this.sels.map((item) => item.id).toString()
      if (!ids) {
        this.$message({ message: '请选择要删除的菜单规则', type: 'warning' })
        return
      }
      this.$confirm('确认删除选中记录吗？', '提示', {
        type: 'warning'
      })
        .then(() => {
          this.listLoading = true
          // NProgress.start();
          const para = { id: ids }

          rule.allRemoveRule(para).then((res) => {
            this.listLoading = false
            // NProgress.done();
            if (res.code === 0) {
              this.$message({
                message: '删除成功',
                type: 'success'
              })
            }
            this.getRule()
          })
        })
        .catch(() => {})
    }
  },
  mounted() {
    this.getRule()
  }
}
</script>

<style scoped>
.el-divider--horizontal {
  margin: 10px 0;
}
</style>

