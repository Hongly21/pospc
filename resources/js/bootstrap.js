import axios from 'axios';
import jQuery from 'jquery';
import * as bootstrap from 'bootstrap';

window.axios = axios;
window.$ = window.jQuery = jQuery;
window.bootstrap = bootstrap;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
