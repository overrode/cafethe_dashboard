export default () => ({
    items: [],

    productModalOpen: false,

    selectedProductId: '',
    selectedQuantity: 1,

    openProductModal() {
        this.selectedProductId = '';
        this.selectedQuantity = 1;
        this.productModalOpen = true;
    },

    closeProductModal() {
        this.productModalOpen = false;
    },

    addProduct(option) {
        if (!this.selectedProductId || !option) {
            return;
        }

        const productId = String(this.selectedProductId);
        const quantity = Number(this.selectedQuantity);

        if (quantity <= 0) {
            return;
        }

        const existingItem = this.items.find(
            item => item.productId === productId
        );

        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            this.items.push({
                productId: productId,
                name: option.dataset.name,
                quantity: quantity,
                unitPrice: Number(option.dataset.price || 0),
                vatRate: Number(option.dataset.vat || 0),
            });
        }

        this.closeProductModal();
    },

    removeItem(index) {
        this.items.splice(index, 1);
    },

    itemTotalHt(item) {
        return item.unitPrice * Number(item.quantity || 0);
    },

    itemTotalVat(item) {
        return this.itemTotalHt(item) * item.vatRate / 100;
    },

    itemTotalTtc(item) {
        return this.itemTotalHt(item) + this.itemTotalVat(item);
    },

    get totalHt() {
        return this.items.reduce(
            (total, item) => total + this.itemTotalHt(item),
            0
        );
    },

    get totalVat() {
        return this.items.reduce(
            (total, item) => total + this.itemTotalVat(item),
            0
        );
    },

    get totalTtc() {
        return this.totalHt + this.totalVat;
    },

    clientModalOpen: false,

    selectedClientId: '',
    selectedClientName: 'Client non renseigné',

    openClientModal() {
        this.clientModalOpen = true;
    },

    closeClientModal() {
        this.clientModalOpen = false;
    },

    selectClient(option) {
        if (!option || !option.value) {
            this.selectedClientId = '';
            this.selectedClientName = 'Client non renseigné';
        } else {
            this.selectedClientId = option.value;
            this.selectedClientName = option.dataset.name;
        }

        this.closeClientModal();
    },

    newClientModalOpen: false,
    clientSaving: false,
    clientError: '',
    newClient: {
        name: '',
        email: '',
        phone: '',
        address: '',
    },

    openNewClientModal() {
    this.newClient = {
        name: '',
        email: '',
        phone: '',
        address: '',
    };

    this.clientError = '';
    this.newClientModalOpen = true;
    },

    closeNewClientModal() {
        this.newClientModalOpen = false;
        this.clientError = '';
    },

    async createClient() {
        if (!this.newClient.name.trim()) {
            this.clientError = 'Le nom du client est obligatoire.';
            return;
        }

        this.clientSaving = true;
        this.clientError = '';

        const formData = new FormData();

        formData.append('name', this.newClient.name);
        formData.append('email', this.newClient.email);
        formData.append('phone', this.newClient.phone);
        formData.append('address', this.newClient.address);

        try {
            const response = await fetch(
                '/public/index.php?route=/clients/store-from-sale',
                {
                    method: 'POST',
                    body: formData,
                }
            );

            const data = await response.json();

            if (!response.ok || !data.success) {
                this.clientError = data.message ?? 'Erreur lors de la création du client.';
                return;
            }

            this.selectedClientId = String(data.client.id);
            this.selectedClientName = data.client.name;

            this.closeNewClientModal();
            this.closeClientModal();

        } catch (error) {
            this.clientError = 'Une erreur est survenue.';
        } finally {
            this.clientSaving = false;
        }
    },
});