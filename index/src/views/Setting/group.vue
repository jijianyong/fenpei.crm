<template>
  <div class="crm-main">
    <div class="table-form" style="margin: 10px 10px -10px;">
      <el-form :inline="true" :model="formInline" class="demo-form-inline">
        <el-form-item>
          <el-button type="primary" size="small" @click="addBox()">添加权限组</el-button>
          <!-- 添加弹出框 -->
          <el-dialog title="新增角色组" :visible.sync="permissionsAdd" :append-to-body="true">
            <el-form
              :model="permissionsAttribute"
              :rules="permissionsRules"
              ref="permissionsAttribute"
              label-width="100px"
            >
              <el-form-item label="父级角色组">
                <el-select
                  v-model="permissionsAttribute.pid"
                  placeholder="请选择"
                  @change="addSelectChange"
                >
                  <el-option
                    v-for="item in selectData"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  >
                    <span>{{ item.spacer }}{{ item.name }}</span>
                  </el-option>
                </el-select>
              </el-form-item>
              <el-form-item label="角色组名称">
                <el-input v-model="permissionsAttribute.name" autocomplete="off"></el-input>
              </el-form-item>
              <el-form-item label="设置权限">
                <el-tree
                  ref="addtree"
                  :data="ruletree"
                  show-checkbox
                  node-key="id"
                  :default-expanded-keys="ruleSelected"
                  :default-checked-keys="ruleSelected"
                  :props="defaultRuleTree"
                ></el-tree>
              </el-form-item>

            </el-form>
            <div slot="footer" class="dialog-footer">
              <el-button @click="permissionsAdd = false">取 消</el-button>
              <el-button type="primary" @click="addSubmit()">确 定</el-button>
            </div>
          </el-dialog>
        </el-form-item>
      </el-form>
    </div>
    <el-divider></el-divider>
    <!-- 数据表格 -->
    <template>
      <div class="crm-tada-table">
        <el-table
          ref="multipleTable"
          :data="permissionsData"
          :max-height="tableHeight"
          stripe
          tooltip-effect="dark"
          :header-cell-style="{background:'#f5f7fa'}"
          style="width: 100%"
          row-key="id"
          v-loading="listLoading"
          default-expand-all
          @selection-change="handleSelectionChange"
        >
          <el-table-column type="selection" width="55"></el-table-column>
          <el-table-column prop="name" label="权限组" show-overflow-tooltip>
            <template slot-scope="scope">{{ scope.row.name }}</template>
          </el-table-column>
          <el-table-column prop="id" label="ID" show-overflow-tooltip></el-table-column>
          <el-table-column prop="pid" label="父级" show-overflow-tooltip></el-table-column>
          <el-table-column prop="operation" label="操作" show-overflow-tooltip>
            <template slot-scope="scope">
              <el-button type="primary" plain size="mini" @click="editBox(scope.row)">
                编辑
                <i class="el-icon-edit el-icon--right"></i>
              </el-button>
              <el-button type="danger" plain size="mini" @click="delGroup(scope.row)">
                删除
                <i class="el-icon-delete el-icon--right"></i>
              </el-button>
            </template>
            <!-- 编辑弹出框 -->
            <el-dialog
              title="编辑角色组"
              :visible.sync="roleEdit"
              :before-close="editBoxClose"
              :append-to-body="true"
              :close-on-click-modal="false"
            >
              <el-form
                :model="roleAttribute"
                :rules="permissionsRules"
                ref="roleAttribute"
                label-width="100px"
              >
                <el-form-item label="父级角色组" prop="pid">
                  <el-select
                    v-model="roleAttribute.pid"
                    placeholder="请选择"
                    @change="editSelectChange"
                  >
                    <el-option
                      v-for="item in selectData"
                      :key="item.id"
                      :label="item.name"
                      :value="item.id"
                    >
                      <span>{{ item.spacer }}{{ item.name }}</span>
                    </el-option>
                  </el-select>
                </el-form-item>
                <el-form-item label="角色组名称" prop="name">
                  <el-input v-model="roleAttribute.name" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item label="设置权限">
                  <el-tree
                    ref="edittree"
                    :data="ruletree"
                    show-checkbox
                    node-key="id"
                    :default-expanded-keys="ruleSelected"
                    :default-checked-keys="ruleSelected"
                    :props="defaultRuleTree"
                  ></el-tree>
                </el-form-item>

              </el-form>
              <div slot="footer" class="dialog-footer">
                <el-button @click="editBoxClose">取 消</el-button>
                <el-button type="primary" @click="editSubmit()">确 定</el-button>
              </div>
            </el-dialog>
          </el-table-column>
        </el-table>
      </div>
    </template>
  </div>
</template>

<script>
import * as api from '@/api/setting/group'

export default {
  name: 'Corp',
  data() {
    return {
      tableHeight: window.innerHeight - 245,

      tabActiveName: 'Permissionset',
      formInline: {
        company: '',
        project: '',
        platform: '',
        mes_project: ''
      },
      permissionsAdd: false,
      roleEdit: false,
      permissionsRules: {
        pid: [{ required: true, message: '请选择父级角色组', trigger: 'blur' }],
        name: [{ required: true, message: '请输入角色组名', trigger: 'blur' }]
      },
      // 新增角色组数据
      permissionsAttribute: {
        pid: 1,
        name: ''
      },
      // 编辑角色组数据
      roleAttribute: {
        pid: 1,
        name: ''
      },
      roleItem: [],
      // 角色组列表
      permissionsData: [],
      // 下拉列表
      selectData: [],
      //
      grouppid: [],
      // 权限节点树
      ruletree: [],
      // 权限节点树默认选中、同时也作用于默认展开
      ruleSelected: [],
      // 权限节点树配置
      defaultRuleTree: {
        children: 'children',
        label: 'label'
      },
      // 加载动画
      listLoading: true

    }
  },
  methods: {
    handleClick(tab, event) {
      console.log(tab, event)
    },
    handleSelectionChange(val) {
      this.multipleSelection = val
    },
    handleSizeChange(val) {
      console.log(`每页 ${val} 条`)
    },
    handleCurrentChange(val) {
      console.log(`当前页: ${val}`)
    },
    delGroup(data) {
      this.$confirm('确认删除该角色组吗?', '提示', {
        type: 'warning'
      })
        .then(() => {
          // 开启加载动画
          this.listLoading = true
          api.delGroup({ id: data.id }).then((res) => {
            if (res.code === 0) {
              this.$message({ message: res.msg, type: 'success' })
              this.getGroup()
            }
            this.listLoading = false
          })
        })
        .catch(() => {})
    },
    // 编辑弹框
    editBox(data) {
      this.roleEdit = true
      this.roleAttribute = Object.assign({}, data)
      this.getSelect()
      this.getRule(this.roleAttribute.pid, this.roleAttribute.id)

      setTimeout(() => {
        // this.$refs.editFieldData.setCheckedKeys(Object.values(data.field))
      }, 0)
      // this.fieldKeyTree = Object.values(data.field)
    },
    // 编辑框取消的时候，重置树形选中
    editBoxClose() {
      // this.$refs.editFieldData.setCheckedKeys([])
      this.roleEdit = false
    },
    // 新增窗口 下拉框改变
    editSelectChange(val) {
      this.ruletree = []
      this.permissionsAttribute.pid = val
      this.getRule(val, this.roleAttribute.id)
    },
    // 编辑提交
    editSubmit() {
      let rule = [] // 得到所有选中和半选中的权限节点
      rule = this.$refs.edittree
        .getCheckedKeys()
        .concat(this.$refs.edittree.getHalfCheckedKeys())

      var param = {
        id: this.roleAttribute.id,
        pid: this.roleAttribute.pid,
        name: this.roleAttribute.name,
        rule: rule
      }

      this.$refs.roleAttribute.validate((valid) => {
        if (valid) {
          api.editGroup(param).then((res) => {
            if (res.code === 0) {
              this.$message({ message: res.msg, type: 'success' })
              this.getGroup()
              this.$refs['roleAttribute'].resetFields() // 清空编辑表单
              this.roleEdit = false
            }
          })
        }
      })
    },
    // 新增提交
    addSubmit() {
      let rule = [] // 得到所有选中和半选中的权限节点
      rule = this.$refs.addtree
        .getCheckedKeys()
        .concat(this.$refs.addtree.getHalfCheckedKeys())

      var param = {
        pid: this.permissionsAttribute.pid,
        name: this.permissionsAttribute.name,
        rule: rule
      }

      this.$refs.permissionsAttribute.validate((valid) => {
        if (valid) {
          api.addGroup(param).then((res) => {
            if (res.code === 0) {
              this.$message({ message: res.msg, type: 'success' })
              this.getGroup()
              this.$refs['permissionsAttribute'].resetFields() // 清空新增表单
              this.permissionsAdd = false
              // this.$refs.addFieldData.setCheckedKeys([])
            }
          })
        }
      })
    },
    // 新增弹框
    addBox() {
      this.permissionsAdd = true
      this.getSelect()
      this.getRule(this.permissionsAttribute.pid, 0)
    },
    // 新增窗口 下拉框改变
    addSelectChange(val) {
      this.ruletree = []
      this.permissionsAttribute.pid = val
      this.getRule(val, 0)
    },
    // 角色组列表
    getGroup() {
      // 开启加载动画
      this.listLoading = true

      api.getGroup().then((res) => {
        this.permissionsData = res.data
        this.listLoading = false
      })
    },
    getSelect() {
      api.getSelect().then((res) => {
        this.selectData = res.data
      })
    },
    // 开启添加权限组按钮传递的参数
    getRule(pid, id) {
      this.ruleloading = true
      this.ruletree = []
      var param = {
        pid: pid,
        id: id
      }
      api.getRule(param).then((res) => {
        this.ruleloading = false

        if (res.code === 0) {
          this.ruletree = res.data.rule
          this.ruleSelected = res.data.selected
          var bIds = this.ruletree.map((v) => v.id)
          this.ruletree.forEach((c) => {
            this.formatRuleTree(c, null, bIds)
          })
        }
      })
    },
    // 格式化初始选定列表
    formatRuleTree(node, pid, brotherIds) {
      if (brotherIds.some((v) => this.ruleSelected.some((s) => s === v))) {
        // 子级有勾选的 去掉父id

        this.ruleSelected.remove(String(pid))
      }
      if (typeof node.children !== 'undefined') {
        node.children.forEach((e) => {
          var bIds = node.children.map((v) => v.id)
          this.formatRuleTree(e, node.id, bIds)
        })
      }
    }
  },
  created: function() {
    this.getGroup()
  }
}

</script>

<style scoped>
</style>

