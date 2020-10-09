<template>
  <div class="crm-main">
    <template>
      <el-tabs v-model="tabActiveName" @tab-click="handleClick">
      </el-tabs>
    </template>
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
          <el-form :model="editForm" label-width="110px" :rules="formMenuRule" ref="editForm">
              <el-form-item label="关联权限规则" prop="rule_id">
                <el-select v-model="editForm.rule_id" clearable placeholder="请选择,没有选择，则默认通过">
                  <el-option
                          v-for="item in selectRule"
                          :key="item.id"
                          :label="item.title"
                          :value="item.id">
                      <span>{{ item.spacer }}{{ item.title }}</span>
                  </el-option>
                </el-select>
              </el-form-item>
              <el-form-item label="父级" prop="pid">
                  <el-select v-model="editForm.pid" clearable placeholder="请选择">
                      <el-option
                              v-for="item in selectData"
                              :key="item.id"
                              :label="item.path"
                              :value="item.id">
                          <span>ID:{{ item.id }}  {{ item.name }}  {{ item.path }}</span>
                      </el-option>
                  </el-select>
              </el-form-item>
              <el-form-item label="菜单名称" prop="name">
                  <el-input v-model="editForm.name" placeholder="菜单名称" autocomplete="off"></el-input>
              </el-form-item>
              <el-form-item label="菜单路径" prop="path">
                  <el-input v-model="editForm.path" placeholder="菜单路径" autocomplete="off"></el-input>
              </el-form-item>
              <el-form-item label="图标" prop="icon">
                  <el-input v-model="editForm.icon" placeholder="图标" autocomplete="off"></el-input>
              </el-form-item>
              <!-- <el-form-item label="Vue对象" prop="component">
                  <el-input v-model="editForm.component" placeholder="关联Vue对象,一级菜单请填Home" autocomplete="off"></el-input>
              </el-form-item> -->

              <el-form-item label="是否隐藏" prop="hidden">
                  <el-radio v-model="editForm.hidden" :label="0">显示</el-radio>
                  <el-radio v-model="editForm.hidden" :label="1">隐藏</el-radio>
              </el-form-item>

              <el-form-item label="顶级菜单" prop="leaf">
                  <el-radio v-model="editForm.leaf" :label="0">否</el-radio>
                  <el-radio v-model="editForm.leaf" :label="1">是</el-radio>
              </el-form-item>

              <el-form-item label="权重" prop="weigh">
                  <el-input v-model="editForm.weigh" placeholder="权重" autocomplete="off" :precision="0"></el-input>
              </el-form-item>
            </el-form>
          <div slot="footer" class="dialog-footer">
            <el-button @click.native="editFormVisible = false">取消</el-button>
            <el-button type="primary" @click.native="editSubmit" :loading="editLoading">提交</el-button>
          </div>
        </el-dialog>
      <!-- 新增弹出框 -->
      <el-dialog title="新增" :visible.sync="addFormVisible">
        <el-form :model="addForm" label-width="110px" :rules="formMenuRule" ref="addForm">
          <el-form-item label="关联权限规则" prop="rule_id">
            <el-select v-model="addForm.rule_id" clearable placeholder="请选择,没有选择，则默认通过">
              <el-option
                v-for="item in selectRule"
                :key="item.id"
                :label="item.title"
                :value="item.id"
              >
                <span>{{ item.spacer }}{{ item.title }}</span>
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="父级" prop="pid">
            <el-select v-model="addForm.pid" clearable placeholder="请选择">
              <el-option
                v-for="item in selectData"
                :key="item.id"
                :label="item.path"
                :value="item.id"
              >
                <span>ID:{{ item.id }} {{ item.name }} {{ item.path }}</span>
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="菜单名称" prop="name">
            <el-input v-model="addForm.name" placeholder="菜单名称" autocomplete="off"></el-input>
          </el-form-item>
          <el-form-item label="菜单路径" prop="path">
            <el-input v-model="addForm.path" placeholder="菜单路径" autocomplete="off"></el-input>
          </el-form-item>
          <el-form-item label="图标" prop="icon">
            <el-input v-model="addForm.icon" placeholder="图标" autocomplete="off"></el-input>
          </el-form-item>
          <el-form-item label="是否隐藏" prop="hidden">
            <el-radio v-model="addForm.hidden" :label="0">显示</el-radio>
            <el-radio v-model="addForm.hidden" :label="1">隐藏</el-radio>
          </el-form-item>
          <el-form-item label="顶级菜单" prop="leaf">
            <el-radio v-model="addForm.leaf" :label="0">否</el-radio>
            <el-radio v-model="addForm.leaf" :label="1">是</el-radio>
          </el-form-item>
          <el-form-item label="权重" prop="weigh">
            <el-input v-model="addForm.weigh" placeholder="权重" autocomplete="off" :precision="0"></el-input>
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
          <el-table-column type="selection" width="55"></el-table-column>
          <el-table-column prop="id" label="ID" show-overflow-tooltip></el-table-column>
          <el-table-column prop="name" label="名称" show-overflow-tooltip></el-table-column>
          <el-table-column prop="path" label="路径" show-overflow-tooltip></el-table-column>
          <el-table-column prop="icon" label="图标" show-overflow-tooltip></el-table-column>
          <el-table-column prop="weigh" label="权重" show-overflow-tooltip></el-table-column>
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
import * as menu from '@/api/setting/menu'
import { getSelect as getSelectRule } from '@/api/setting/rule'
export default {
  name: 'Menu',
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
        rule_id: '',
        pid: '',
        name: '',
        path: '',
        icon: '',
        component: '',
        hidden: '',
        leaf: '',
        weigh: ''
      },
      // 新增账户弹出框
      addFormVisible: false,
      addLoading: false,
      // 新增规则
      formMenuRule: {},
      addForm: {
        rule_id: '',
        pid: '',
        name: '',
        path: '',
        icon: '',
        component: '',
        hidden: '',
        leaf: '',
        weigh: ''
      },
      // 表格数据
      tableHeight: window.innerHeight - 210,
      ruleData: [],
      formLabelWidth: '100px',
      // 加载动画
      // 下拉列表
      selectData: [],
      selectRule: [],
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
      console.log(tab, event)
    },
    selsChange: function(sels) {
      this.sels = sels
    },
    // 显示开关
    changeIsmenuSwitch(data) {
      // menu.disabledIsmenu({id:data.id}).then( res => {
      //     if( res.code === 0 ){
      //         this.$message({message: res.msg,type: 'success'});
      //     }
      // });
    },
    changeStatusSwitch(data) {
      // menu.disabledIsmenu({id:data.id}).then( res => {
      //     if( res.code === 0 ){
      //         this.$message({message: res.msg,type: 'success'});
      //     }
      // });
    },
    getSelect() {
      menu.getSelect().then((res) => {
        this.selectData = res.data
      })
    },
    getSelectRule() {
      getSelectRule().then((res) => {
        this.selectRule = res.data
      })
    },
    // 获取列表
    getMenu() {
      this.listLoading = true
      menu.getMenuList().then((res) => {
        this.ruleData = res.data
        this.listLoading = false
      })
    },
    // 显示新增界面
    handleAdd: function() {
      this.addFormVisible = true
      this.getSelect()
      this.getSelectRule()
    },
    // 新增
    addSubmit: function() {
      this.$refs.addForm.validate((valid) => {
        if (valid) {
          this.addLoading = true
          const para = Object.assign({}, this.addForm)
          menu.addMenu(para).then((res) => {
            // 关闭新增表单
            this.addLoading = false
            // 如果返回code等于0，则新增成功
            if (res.code === 0) {
              this.clearCache()
              this.$message({ message: '提交成功', type: 'success' })
              this.$refs['addForm'].resetFields()
              this.addFormVisible = false
              // 最后再刷新一下页面，获取最新的列表数据
              this.getMenu()
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
      this.getSelectRule()
    },
    // 编辑
    editSubmit: function() {
      this.$refs.editForm.validate((valid) => {
        if (valid) {
          this.editLoading = true
          const para = Object.assign({}, this.editForm)
          menu.editMenu(para).then((res) => {
            this.editLoading = false
            if (res.code === 0) {
              this.clearCache()
              this.$message({ message: '提交成功', type: 'success' })
              this.$refs['editForm'].resetFields()
              this.editFormVisible = false
              this.getMenu()
            }
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
          const para = { id: row.id }
          menu.removeMenu(para).then((res) => {
            this.listLoading = false
            if (res.code === 0) {
              this.clearCache()
              this.$message({
                message: '删除成功',
                type: 'success'
              })
            }
            this.getMenu()
          })
        })
        .catch(() => {})
    },
    // 批量删除
    batchRemove: function() {
      // 将id转换为字符串
      const ids = this.sels.map((item) => item.id).toString()
      if (!ids) {
        this.$message({ message: '请选择要删除的菜单规则', type: 'warning' })
        return
      }
      this.$confirm('确认删除选中的记录吗？', '提示', {
        type: 'warning'
      })
        .then(() => {
          this.listLoading = true
          const para = { id: ids }

          menu.removeMenu(para).then((res) => {
            this.listLoading = false
            if (res.code === 0) {
              this.clearCache()
              this.$message({
                message: '删除成功',
                type: 'success'
              })
            }
            this.getMenu()
          })
        })
        .catch(() => {})
    },
    // 清除菜单的缓存字段，在编辑行为的时候调用
    clearCache() {
      sessionStorage.setItem('ismenu', '')
    }
  },
  mounted() {
    this.getMenu()
  }
}
</script>

<style scoped>
.el-divider--horizontal {
  margin: 10px 0;
}
</style>
