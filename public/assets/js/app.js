import Alpine from 'alpinejs';
import productFilter from "./components/productFilter.js";
import productPage from './components/productPage.js';


window.Alpine = Alpine;

Alpine.data('productFilter', productFilter);
Alpine.data('productPage', productPage);

Alpine.start();