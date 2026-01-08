import './bootstrap';

// Vue.js initialization (only if #app exists)
const appElement = document.getElementById('app');
if (appElement) {
    import('./vue-app.js').then(module => {
        module.default(appElement);
    });
}
