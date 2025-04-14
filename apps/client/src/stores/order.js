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
      }
    },
  })