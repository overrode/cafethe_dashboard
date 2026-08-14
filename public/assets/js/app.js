import Alpine from 'alpinejs';
import productFilter from "./components/productFilter.js";
import productPage from './components/productPage.js';
import saleForm from './components/saleForm.js';


window.Alpine = Alpine;

Alpine.data('productFilter', productFilter);
Alpine.data('productPage', productPage);
Alpine.data('saleForm', saleForm);

Alpine.start();