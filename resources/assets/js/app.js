
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Librerías importadas
 */
import Element from 'element-ui';

window.Vue = require('vue');

Vue.use(Element)

/**
 * Vistas importadas
 */
Vue.component('import-view', require('./views/Import.vue'));

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));

const app = new Vue({
    el: '#app',
    computed: {
        csrf() {
            return document.getElementsByName('csrf-token')[0].getAttribute('content');
        }
    },
});
