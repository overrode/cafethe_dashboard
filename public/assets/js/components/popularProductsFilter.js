export default () => ({
    activeCategory: 'all',
    changing: false,

    filter(category) {
        if (this.activeCategory === category) {
            return;
        }

        this.changing = true;

        setTimeout(() => {
            this.activeCategory = category;

            setTimeout(() => {
                this.changing = false;
            }, 50);
        }, 200);
    },

    isActive(category) {
        return this.activeCategory === category;
    },

    isVisible(category) {
        return this.activeCategory === 'all'
            || this.activeCategory === category;
    }
});