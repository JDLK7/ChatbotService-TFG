
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
import { TinkerComponent } from 'botman-tinker';
import * as VueGoogleMaps from 'vue2-google-maps';

window.Vue = require('vue');

Vue.use(Element);

/**
 * Componentes importados
 */
Vue.component('botman-tinker', TinkerComponent);

/**
 * Vistas importadas
 */
Vue.component('import-view', require('./views/Import.vue'));
Vue.component('dashboard', require('./views/Dashboard.vue'));

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));

Vue.use(VueGoogleMaps, {
    load: {
        key: 'AIzaSyDK87O4M7ib6w9cJfuCzwV5wvAglmDtDN8',
        libraries: 'places', // This is required if you use the Autocomplete plugin
        // OR: libraries: 'places,drawing'
        // OR: libraries: 'places,drawing,visualization'
        // (as you require)
    },
});

const app = new Vue({
    el: '#app',
    computed: {
        csrf() {
            return document.getElementsByName('csrf-token')[0].getAttribute('content');
        }
    },
});
