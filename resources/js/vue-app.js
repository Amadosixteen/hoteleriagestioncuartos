import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import App from './views/App.vue';

export default (el) => {
    const app = createApp(App);
    const pinia = createPinia();

    app.use(pinia);
    app.use(router);

    app.mount(el);
};
