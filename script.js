document.addEventListener('DOMContentLoaded', () => {
    const cart = [];
    const cartItemsContainer = document.getElementById('cart-items');
    const totalPriceContainer = document.getElementById('total-price');
    const addToCartButtons = document.querySelectorAll('.add-to-cart');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', () => {
            const product = button.parentElement;
            const productName = product.querySelector('p').innerText;
            const productPrice = parseFloat(product.querySelector('.price').innerText.replace('$', ''));
            addToCart(productName, productPrice);
        });
    });

    function addToCart(name, price) {
        const cartItem = cart.find(item => item.name === name);
        if (cartItem) {
            cartItem.quantity++;
        } else {
            cart.push({ name, price, quantity: 1 });
        }
        renderCart();
    }

    function renderCart() {
        cartItemsContainer.innerHTML = '';
        let totalPrice = 0;

        cart.forEach(item => {
            const cartItemElement = document.createElement('div');
            cartItemElement.innerText = `${item.name} - $${item.price.toFixed(2)} x ${item.quantity}`;
            cartItemsContainer.appendChild(cartItemElement);
            totalPrice += item.price * item.quantity;
        });

        totalPriceContainer.innerText = `Total: $${totalPrice.toFixed(2)}`;
    }

    document.getElementById('contact-form').addEventListener('submit', function(event) {
        event.preventDefault();
        alert('Form submitted!');
        this.reset();
    });

    document.getElementById('checkout').addEventListener('click', () => {
        if (cart.length === 0) {
            alert('Your cart is empty!');
        } else {
            alert('Checkout successful!');
            cart.length = 0;
            renderCart();
        }
    });
});
