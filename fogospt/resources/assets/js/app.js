import './bootstrap';
import { createApp } from 'vue';
import ExampleComponent from './components/ExampleComponent.vue';

/**
 * Create Vue 3 application and mount it to the page.
 */

const app = createApp({});

app.component('example-component', ExampleComponent);

app.mount('#app');
