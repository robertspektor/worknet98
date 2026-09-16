import { formatEuro } from '../core/money.js';
import { escapeHtml } from '../ui/dom.js';

export class MockPaymentGateway {
  #dialog;

  constructor(dialog) {
    this.#dialog = dialog;
  }

  async checkout(product) {
    const price = formatEuro(product.priceCents);
    const confirmed = await this.#dialog.show({
      title: 'CorpPay Secure Checkout',
      icon: 'floppy',
      body: `
        <p class="checkout-product">${escapeHtml(product.name)}</p>
        <p class="checkout-price">${price}</p>
        <p class="fineprint">Prototype mode: no real money is charged.</p>`,
      buttons: [
        { label: `Pay ${price}`, value: true, primary: true },
        { label: 'Cancel', value: false },
      ],
    });

    return Boolean(confirmed);
  }
}
