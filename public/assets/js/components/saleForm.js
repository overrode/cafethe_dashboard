export default () => ({
    items: [
        {
            productId: '',
            quantity: 1,
            unitPrice: 0,
            vatRate: 0,
        }
    ],

    addItem() {
        this.items.push({
            productId: '',
            quantity: 1,
            unitPrice: 0,
            vatRate: 0,
        });
    },

    removeItem(index) {
        if (this.items.length > 1) {
            this.items.splice(index, 1);
        }
    },

    updateProduct(index, option) {
        this.items[index].unitPrice = Number(option.dataset.price || 0);
        this.items[index].vatRate = Number(option.dataset.vat || 0);
    },

    get totalHt() {
        return this.items.reduce((total, item) => {
            return total + (item.unitPrice * Number(item.quantity || 0));
        }, 0);
    },

    get totalVat() {
        return this.items.reduce((total, item) => {
            const ht = item.unitPrice * Number(item.quantity || 0);
            return total + (ht * item.vatRate / 100);
        }, 0);
    },

    get totalTtc() {
        return this.totalHt + this.totalVat;
    },
});