    <?php
    session_start();

    // Validar sesión: si no hay usuario logueado, redirigir al login
    if (!isset($_SESSION['usuario'])) {
        header('Location:index.html');
        exit;
    }


    ?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Shooping iscjlchavezG - Tienda Online</title>
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>

    <body>
        <!-- Header -->
        <header class="header">
            <div class="logo">
                <i class="fas fa-shopping-cart"></i>
                <span>Shoping IscjlchavezG</span>
            </div>
            <div class="cart-icon" id="cartIcon">
                <span>Bienvenido: <?php echo $_SESSION['usuario']; ?></span>
                <i class="fas fa-shopping-bag"></i>&nbsp;&nbsp;
                <span class="cart-count" id="cartCount">0</span>
            </div>
        </header>

        <!-- Main Content -->
        <div class="container">
            <!-- Products Section -->
            <div class="products-section" id="productsSection">
                <!-- Products will be loaded here by JavaScript -->
            </div>

            <!-- Cart Sidebar -->
            <div class="cart-sidebar">
                <h2 class="cart-title">
                    <i class="fas fa-shopping-cart"></i>
                    Carrito de Compras
                </h2>
                <div class="cart-items" id="cartItems">
                    <div class="empty-cart" id="emptyCart">Tu carrito está vacío</div>
                </div>
                <div class="cart-summary">
                    <div class="summary-row">
                        <span class="subtotal">Subtotal:</span>
                        <span id="subtotal">$0</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span id="total">$0</span>
                    </div>
                    <button class="checkout-btn" id="checkoutBtn">Proceder al Pago</button>
                </div>
            </div>
        </div>

        <!-- Ticket Modal -->
        <div id="ticketModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeTicket()">&times;</span>
                <div id="ticketContent"></div>
            </div>
        </div>

        <script>
            let cart = [];

            // Función para formatear precios
            function formatPrice(price) {
                return new Intl.NumberFormat('es-ES', {
                    style: 'currency',
                    currency: 'MNX'
                }).format(price);
            }

            // Cargar productos desde la API
            async function loadProducts() {
                try {
                    const response = await fetch('api/productos.php');
                    const products = await response.json();

                    const productsSection = document.getElementById('productsSection');
                    productsSection.innerHTML = '';

                    products.forEach(product => {
                        const productCard = document.createElement('div');
                        productCard.className = 'product-card';
                        productCard.innerHTML = `
                            <img src="${product.imagen}" alt="${product.nombre}" class="product-image" onerror="this.src='https://placehold.co/300x200/e64c2e/white?text=Producto'">
                            <div class="product-info">
                                <h3 class="product-title">${product.nombre}</h3>
                                <p class="product-description">${product.descripcion}</p>
                                <div class="product-price">${formatPrice(parseFloat(product.precio))}</div>
                                <div class="quantity-control">
                                    <button class="quantity-btn minus" data-id="${product.id}">-</button>
                                    <input type="number" class="quantity-input" value="1" min="1" data-id="${product.id}">
                                    <button class="quantity-btn plus" data-id="${product.id}">+</button>
                                </div>
                                <button class="add-to-cart" data-id="${product.id}" data-price="${product.precio}" data-name="${product.nombre}" data-image="${product.imagen}">
                                    <i class="fas fa-plus"></i> Agregar al Carrito
                                </button>
                            </div>
                        `;
                        productsSection.appendChild(productCard);
                    });

                    // Añadir event listeners
                    document.querySelectorAll('.quantity-btn').forEach(btn => {
                        btn.addEventListener('click', handleQuantityChange);
                    });

                    document.querySelectorAll('.add-to-cart').forEach(btn => {
                        btn.addEventListener('click', addToCart);
                    });

                } catch (error) {
                    console.error('Error al cargar productos:', error);
                    document.getElementById('productsSection').innerHTML = '<p>Error al cargar productos</p>';
                }
            }

            // Manejar cambios en la cantidad
            function handleQuantityChange(e) {
                const productId = parseInt(e.target.dataset.id);
                const input = document.querySelector(`.quantity-input[data-id="${productId}"]`);
                let currentValue = parseInt(input.value);

                if (e.target.classList.contains('plus')) {
                    input.value = currentValue + 1;
                } else if (e.target.classList.contains('minus') && currentValue > 1) {
                    input.value = currentValue - 1;
                }
            }

            // Agregar producto al carrito
            function addToCart(e) {
                const productId = parseInt(e.target.dataset.id);
                const productName = e.target.dataset.name;
                const productPrice = parseFloat(e.target.dataset.price);
                const productImage = e.target.dataset.image;
                const quantityInput = document.querySelector(`.quantity-input[data-id="${productId}"]`);
                const quantity = parseInt(quantityInput.value);

                const existingItem = cart.find(item => item.id === productId);
                if (existingItem) {
                    existingItem.quantity += quantity;
                } else {
                    cart.push({
                        id: productId,
                        name: productName,
                        price: productPrice,
                        quantity: quantity,
                        image: productImage
                    });
                }

                updateCart();
                quantityInput.value = 1;
            }

            // Actualizar el carrito
            function updateCart() {
                const cartItems = document.getElementById('cartItems');
                const emptyCart = document.getElementById('emptyCart');
                const cartCount = document.getElementById('cartCount');
                const subtotalElement = document.getElementById('subtotal');
                const totalElement = document.getElementById('total');

                let subtotal = 0;
                cart.forEach(item => {
                    subtotal += item.price * item.quantity;
                });

                const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
                cartCount.textContent = totalItems;

                if (cart.length === 0) {
                    cartItems.innerHTML = '<div class="empty-cart">Tu carrito está vacío</div>';
                } else {
                    cartItems.innerHTML = '';
                    cart.forEach(item => {
                        const cartItem = document.createElement('div');
                        cartItem.className = 'cart-item';
                        cartItem.innerHTML = `
                            <img src="${item.image}" alt="${item.name}" class="cart-item-image" onerror="this.src='https://placehold.co/60x60/e64c2e/white?text=P'">
                            <div class="cart-item-info">
                                <div class="cart-item-title">${item.name}</div>
                                <div class="cart-item-price">${formatPrice(item.price)}</div>
                                <div class="cart-item-quantity">
                                    <button class="cart-quantity-btn minus" data-id="${item.id}">-</button>
                                    <input type="number" class="cart-quantity-input" value="${item.quantity}" min="1" data-id="${item.id}">
                                    <button class="cart-quantity-btn plus" data-id="${item.id}">+</button>
                                </div>
                                <button class="remove-item" data-id="${item.id}">Eliminar</button>
                            </div>
                        `;
                        cartItems.appendChild(cartItem);
                    });

                    // Añadir event listeners al carrito
                    document.querySelectorAll('.cart-quantity-btn').forEach(btn => {
                        btn.addEventListener('click', handleCartQuantityChange);
                    });

                    document.querySelectorAll('.cart-quantity-input').forEach(input => {
                        input.addEventListener('change', handleCartQuantityInput);
                    });

                    document.querySelectorAll('.remove-item').forEach(btn => {
                        btn.addEventListener('click', removeFromCart);
                    });
                }

                subtotalElement.textContent = formatPrice(subtotal);
                totalElement.textContent = formatPrice(subtotal);
            }

            // Manejar cambios en la cantidad del carrito
            function handleCartQuantityChange(e) {
                const productId = parseInt(e.target.dataset.id);
                const item = cart.find(item => item.id === productId);
                if (!item) return;

                if (e.target.classList.contains('plus')) {
                    item.quantity++;
                } else if (e.target.classList.contains('minus') && item.quantity > 1) {
                    item.quantity--;
                } else if (e.target.classList.contains('minus') && item.quantity === 1) {
                    cart = cart.filter(item => item.id !== productId);
                }

                updateCart();
            }

            // Manejar cambios en el input de cantidad del carrito
            function handleCartQuantityInput(e) {
                const productId = parseInt(e.target.dataset.id);
                const item = cart.find(item => item.id === productId);
                if (!item) return;

                const newQuantity = parseInt(e.target.value);
                if (newQuantity >= 1) {
                    item.quantity = newQuantity;
                } else {
                    e.target.value = 1;
                }

                updateCart();
            }

            // Eliminar item del carrito
            function removeFromCart(e) {
                const productId = parseInt(e.target.dataset.id);
                cart = cart.filter(item => item.id !== productId);
                updateCart();
            }

            // Procesar pago
            async function processPayment() {
                if (cart.length === 0) {
                    alert('Tu carrito está vacío');
                    return;
                }

                const pedidoData = {
                    items: cart.map(item => ({
                        id: item.id,
                        nombre: item.name,
                        precio: item.price,
                        cantidad: item.quantity
                    }))
                };

                try {
                    const response = await fetch('api/procesar_pedido.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(pedidoData)
                    });

                    const result = await response.json();

                    if (result.success) {
                        showTicket(result.ticket);
                        cart = [];
                        updateCart();
                    } else {
                        throw new Error(result.error || 'Error desconocido');
                    }
                } catch (error) {
                    console.error('Error al procesar el pago:', error);
                    alert('Error al procesar el pago: ' + error.message);
                }
            }

            // Mostrar ticket
            function showTicket(ticket) {
                const ticketContent = document.getElementById('ticketContent');
                let ticketHTML = `
                    <div class="ticket-header">
                        <h2>TICKET DE COMPRA</h2>
                        <p>Número de Pedido: #${ticket.numero_pedido}</p>
                        <p>Fecha: ${ticket.fecha}</p>
                    </div>
                    <div class="ticket-items">
                `;

                ticket.items.forEach(item => {
                    const subtotal = item.precio * item.cantidad;
                    ticketHTML += `
                        <div class="ticket-item">
                            <div>
                                <strong>${item.nombre}</strong><br>
                                ${item.cantidad} x ${formatPrice(item.precio)}
                            </div>
                            <div>${formatPrice(subtotal)}</div>
                        </div>
                    `;
                });

                ticketHTML += `
                    </div>
                    <div class="ticket-item" style="border-top: 2px solid var(--ml-orange); margin-top: 1rem; padding-top: 1rem;">
                        <strong>TOTAL:</strong>
                        <strong>${formatPrice(ticket.total)}</strong>
                    </div>
                    <div style="text-align: center; margin-top: 1.5rem;">
                        <p>¡Gracias por tu compra!</p>
                        <p>Tu pedido será procesado en breve.</p>
                    </div>
                `;

                ticketContent.innerHTML = ticketHTML;
                document.getElementById('ticketModal').style.display = 'block';
            }

            function closeTicket() {
                document.getElementById('ticketModal').style.display = 'none';
            }

            // Inicializar la página
            document.addEventListener('DOMContentLoaded', () => {
                loadProducts();
                document.getElementById('checkoutBtn').addEventListener('click', processPayment);
            });

            // Cerrar modal al hacer clic fuera
            window.onclick = function(event) {
                const modal = document.getElementById('ticketModal');
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            }
        </script>
    </body>

    </html>