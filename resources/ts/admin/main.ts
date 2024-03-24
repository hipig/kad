import {createApp} from 'vue'
import ArcoVue from '@arco-design/web-vue';
import ArcoVueIcon from '@arco-design/web-vue/es/icon';
import router from './router';
import store from './store';
import App from './App.vue';
import '@admin/api/interceptor';
import ListData from "@admin/components/common/list-data/Index.vue";
import Breadcrumb from "@admin/components/common/breadcrumb/Index.vue";
import Panel from "@admin/components/common/panel/Index.vue";

import '@arco-design/web-vue/dist/arco.css';
import 'virtual:uno.css';
import '@unocss/reset/tailwind.css';

const app = createApp(App);

app.use(ArcoVue);
app.use(ArcoVueIcon);

app.use(router);
app.use(store);

app.component('ListData', ListData);
app.component('Breadcrumb', Breadcrumb);
app.component('Panel', Panel);

app.mount('#app');
