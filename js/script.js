// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
  // Prevent multiple initialization
  if (window.scriptInitialized) return;
  window.scriptInitialized = true;

  // Selecting Elements - with null checks
  const searchForm = document.querySelector('.search-form');
  const shoppingCart = document.querySelector('.shopping-cart');
  const loginForm = document.querySelector('.login-form');
  const navbar = document.querySelector('.navbar');
  const searchBtn = document.querySelector('#search-btn');
  const cartBtn = document.querySelector('#cart-btn');
  const loginBtn = document.querySelector('#login-btn');
  const menuBtn = document.querySelector('#menu-btn');
  const cartItemsContainer = document.querySelector('.shopping-cart .cart-items');
  const cartTotalElement = document.querySelector('.shopping-cart .total');

  // Function to display cart items
  function displayCartItems() {
    if (cartItemsContainer && cartTotalElement) {
        fetch('/includes/get_cart_details.php')
            .then(response => response.json())
            .then(cartData => {
                let cartHTML = '';
                if (cartData && cartData.items && Array.isArray(cartData.items) && cartData.items.length > 0) {
                    cartData.items.forEach(item => {
                        cartHTML += `
                            <div class="cart-item">
                                <img src="${item.image}" alt="${item.name}">
                                <div class="content">
                                    <h3>${item.name}</h3>
                                    <span class="price">$${parseFloat(item.price).toFixed(2)} x ${item.quantity}</span>
                                </div>
                                <i class="fa fa-trash-o remove-from-cart-btn" data-cart-id="${item.id}"></i>
                            </div>
                        `;
                    });
                } else {
                    cartHTML = '<p>Your cart is empty.</p>';
                }
                cartItemsContainer.innerHTML = cartHTML;
                cartTotalElement.textContent = `Total: $${parseFloat(cartData.total_price).toFixed(2)}`;
                attachRemoveFromCartListeners(); // Attach listeners after rendering
            })
            .catch(error => {
                console.error('Error fetching cart details:', error);
                cartItemsContainer.innerHTML = '<p>Error loading cart.</p>';
                cartTotalElement.textContent = 'Total: $0.00';
            });
    }
  }




  // Function to attach event listeners to remove buttons
  document.querySelectorAll('.remove-from-cart-btn').forEach(button => {
    button.addEventListener('click', function () {
        const cartId = this.dataset.cartId;

        fetch('includes/remove_from_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ cart_id: cartId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Item removed from cart successfully!');
                displayCartItems(); // Re-render the cart
                updateCartCount(data.cart_count); // Update header cart count
            } else {
                alert(data.message || 'Error removing item from cart.');
            }
        })
        .catch(error => console.error('Error:', error));
    });
});

  // Only set event handlers if elements exist
  if (searchBtn && searchForm) {
      searchBtn.onclick = () => {
          searchForm.classList.toggle('active');
          if (shoppingCart) shoppingCart.classList.remove('active');
          if (loginForm) loginForm.classList.remove('active');
          if (navbar) navbar.classList.remove('active');
      }
  }

  if (cartBtn && shoppingCart) {
      cartBtn.onclick = () => {
          shoppingCart.classList.toggle('active');
          if (searchForm) searchForm.classList.remove('active');
          if (loginForm) loginForm.classList.remove('active');
          if (navbar) navbar.classList.remove('active');
          displayCartItems(); // Load cart items when cart is opened
      }
  }

  if (loginBtn && loginForm) {
      loginBtn.onclick = () => {
          loginForm.classList.toggle('active');
          if (shoppingCart) shoppingCart.classList.remove('active');
          if (searchForm) searchForm.classList.remove('active');
          if (navbar) navbar.classList.remove('active');
      }
  }

  if (menuBtn && navbar) {
      menuBtn.onclick = () => {
          navbar.classList.toggle('active');
          if (searchForm) searchForm.classList.remove('active');
          if (shoppingCart) shoppingCart.classList.remove('active');
          if (loginForm) loginForm.classList.remove('active');
      }
  }

  // Close elements on scroll
  window.onscroll = () => {
      if (searchForm) searchForm.classList.remove('active');
      if (shoppingCart) shoppingCart.classList.remove('active');
      if (loginForm) loginForm.classList.remove('active');
      if (navbar) navbar.classList.remove('active');
  }

  // Add to Cart
  document.querySelectorAll('.add_to_cart_btn').forEach(button => {
      button.addEventListener('click', function () {
          const productId = this.dataset.productId;
          const quantity = this.dataset.quantity || 1;

          console.log("Adding product ID:", productId); // Debug log

          fetch('includes/add_to_cart.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ product_id: productId, quantity: quantity })
          })
          .then(response => {
              console.log("Raw response:", response);
              if (!response.ok) {
                  return response.text().then(text => {
                      console.error("Server response:", text);
                      throw new Error('Network response was not ok');
                  });
              }
              return response.json();
          })
          .then(data => { // 'data' is now defined within this block
              console.log("Response data:", data);
              if (data.success) {
                  updateCartCount(data.cart_count);
                  alert('Product added to cart!');
                  displayCartItems(); // Update the cart display after adding
              } else {
                  alert(data.message || 'Failed to add to cart.');
                  console.warn("Add to cart failed:", data);
              }
          })
          .catch(error => {
              console.error('Fetch error:', error);
              alert('Failed to add to cart. Check the console for details.');
          });
      });
  });

  // Update Cart
  document.querySelectorAll('.quantity-input').forEach(input => {
      input.addEventListener('change', function () {
          const cartId = this.dataset.cartId;
          const quantity = this.value;

          fetch('includes/update_cart.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ cart_id: cartId, quantity: quantity })
          })
          .then(response => response.json())
          .then(data => {
              if (data.success) {
                  alert('Cart updated successfully!');
                  displayCartItems(); // Update the cart display after updating quantity
                  updateCartTotal(data.cart_total); // Update the total in the cart
              } else {
                  alert(data.message || 'Error updating cart.');
              }
          })
          .catch(error => console.error('Error:', error));
      });
  });

  // Remove from Cart
  document.querySelectorAll('.remove-from-cart-btn').forEach(button => {
      button.addEventListener('click', function () {
          const cartId = this.dataset.cartId;

          fetch('includes/remove_from_cart.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ cart_id: cartId })
          })
          .then(response => response.json())
          .then(data => {
              if (data.success) {
                  alert('Item removed from cart successfully!');
                  displayCartItems(); // Re-render the cart
                  updateCartCount(data.cart_count); // Update header cart count
              } else {
                  alert(data.message || 'Error removing item from cart.');
              }
          })
          .catch(error => console.error('Error:', error));
      });
  });

  // Initial call to display cart items on page load
  displayCartItems();
}); // Closing brace for the DOMContentLoaded event listener

// Update Cart Count - Keep this function outside the DOMContentLoaded event
function updateCartCount(count) {
  const cartCountElement = document.getElementById('cart-count');
  if (cartCountElement) {
      cartCountElement.textContent = count;
  }
}

// This function would typically be defined in the DOMContentLoaded handler,
// but it might be called elsewhere, so define it globally
function updateCartTotal(total) {
  const totalElement = document.querySelector('.shopping-cart .total');
  if (totalElement) {
      totalElement.textContent = `Total: $${parseFloat(total).toFixed(2)}`;
  }
}

// Add this before the DOMContentLoaded event handler ends
function attachRemoveFromCartListeners() {
    document.querySelectorAll('.remove-from-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            const cartId = this.dataset.cartId;
            
            fetch('includes/remove_from_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cart_id: cartId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayCartItems(); // Re-render the cart
                    updateCartCount(data.cart_count); // Update header cart count
                } else {
                    alert(data.message || 'Error removing item from cart.');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
}




// Add this inside your DOMContentLoaded event listener

// Login Tab Switching
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const targetTab = this.dataset.tab;
        
        // Remove active class from all tabs and sections
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.login-section').forEach(s => s.classList.remove('active'));
        
        // Add active class to clicked tab and corresponding section
        this.classList.add('active');
        document.getElementById(targetTab).classList.add('active');
    });
});

// Telegram OTP Login
const sendOtpBtn = document.getElementById('send-otp-btn');
const backToEmailBtn = document.getElementById('back-to-email');
const emailStep = document.getElementById('email-step');
const otpStep = document.getElementById('otp-step');

if (sendOtpBtn) {
    sendOtpBtn.addEventListener('click', function() {
        const email = document.getElementById('telegram-email').value.trim();
        
        if (!email) {
            alert('Please enter your email address');
            return;
        }
        
        // Show loading
        this.value = 'Sending...';
        this.disabled = true;
        
        fetch('includes/send_otp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username: email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Switch to OTP step
                emailStep.style.display = 'none';
                otpStep.style.display = 'block';
                document.getElementById('hidden-email').value = email;
                alert(data.message);
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to send OTP. Please try again.');
        })
        .finally(() => {
            this.value = 'Send OTP';
            this.disabled = false;
        });
    });
}

if (backToEmailBtn) {
    backToEmailBtn.addEventListener('click', function(e) {
        e.preventDefault();
        emailStep.style.display = 'block';
        otpStep.style.display = 'none';
        document.querySelector('input[name="otp"]').value = '';
    });
}