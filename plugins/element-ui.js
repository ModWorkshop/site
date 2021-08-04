import Vue from 'vue'
import Element, { Button, Checkbox, Col, Collapse, CollapseItem, Dropdown, DropdownItem, DropdownMenu, Form, FormItem, Input, Option, Pagination, Select, TabPane, Tabs, Upload } from 'element-ui'
import locale from 'element-ui/lib/locale/lang/en'

Vue.component(Checkbox.name, Checkbox);
Vue.component(Form.name, Form);
Vue.component(FormItem.name, FormItem);
Vue.component(Input.name, Input);
Vue.component(Collapse.name, Collapse);
Vue.component(CollapseItem.name, CollapseItem);
Vue.component(Button.name, Button);
Vue.component(Pagination.name, Button);
Vue.component(Dropdown.name, Dropdown);
Vue.component(DropdownItem.name, DropdownItem);
Vue.component(DropdownMenu.name, DropdownMenu);
Vue.component(Col.name, Col);
Vue.component(Select.name, Select);
Vue.component(Option.name, Option);
Vue.component(Tabs.name, Tabs);
Vue.component(TabPane.name, TabPane);
Vue.component(Upload.name, Upload);
//Vue.use(Element, { locale })