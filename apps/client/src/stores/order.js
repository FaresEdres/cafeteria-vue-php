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
      getOrder(){
           return this.order;
        },
      addOrder(order) {
        this.order = order;
      },
      addOrderItem(productID, quantity) {
        this.order.products.push({
          "product-id": productID,
          "quantity": quantity,
        });
      },
      increaseQuantity(productId){
        const product = this.order.products.find(item => item['product-id'] === productId);
        if (product) {
          product.quantity++;
        }
      },
      decreaseQuantity(productId){
        const product = this.order.products.find(item => item['product-id'] === productId);
        if (product && product.quantity > 1) {
          product.quantity--;
        }
        if (product.quantity ==0){
          this.removeOrderItem(productId);
        }
      },

    },
  })