import { defineStore } from 'pinia'

export const useOrderStore = defineStore('order', {
    state: () => ({
      order: {
        "comment": "",
        "user-id": "",
        "status": "",
        "products": [],
      },
    }),
    actions: {
      getOrder() {
        return this.order;
      },
      addOrder(order) {
        // console.log(order);
        this.order.id = order.order_id;
        this.order.comment = order.comment;
        this.order['user-id'] = order['user-id'];
        this.order.status = order.status;
        this.order.products = order.products.map(item => ({
          "product-id": item['product_id'],
          "quantity": item.quantity          ,
        }));
        // this.order = order;
      },
      addOrderItem(productID, quantity) {
        const existingProduct = this.order.products.find(
          item => item['product-id'] === productID
        );
        
        if (existingProduct) {
          existingProduct.quantity += quantity;
        } else {
          this.order.products.push({
            "product-id": productID,
            "quantity": quantity,
          });
        }
      },
      increaseQuantity(productId) {
        const product = this.order.products.find(item => item['product-id'] === productId);
        if (product) {
          product.quantity++;
        }
      },
      decreaseQuantity(productId) {
        const product = this.order.products.find(item => item['product-id'] === productId);
        if (product && product.quantity > 1) {
          product.quantity--;
        }
        if (product && product.quantity === 0) {
          this.removeOrderItem(productId);
        }
      },
      deleteOrderProducts() {
        this.order.products = [];
      },
      removeOrderItem(productId) {
        this.order.products = this.order.products.filter(item => item['product-id'] !== productId);
      },
    },
})