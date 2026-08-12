export default (mainImage) => ({
    activeImage: mainImage,
    quantity: 1,

    increaseQuantity() {
        this.quantity++;
    },

    decreaseQuantity() {
        if (this.quantity > 1) {
            this.quantity--;
        }
    },

    selectImage(image) {
        this.activeImage = image;
    }
});