import Alpine from 'alpinejs';
import popularProductsFilter from './components/popularProductsFilter.js';

window.Alpine = Alpine;

Alpine.data('popularProductsFilter', popularProductsFilter);

Alpine.start();