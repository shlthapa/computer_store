document.addEventListener('DOMContentLoaded', function() {
    // --- Registration Form Validation ---
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password.length < 6) {
                e.preventDefault();
                alert("Password must be at least 6 characters long.");
                return false;
            }
            if (password !== confirmPassword) {
                e.preventDefault();
                alert("Passwords do not match.");
                return false;
            }
            return true;
        });
    }

    // --- Add to Cart Quantity Check ---
    const addToCartForm = document.getElementById('add-to-cart-form');
    if (addToCartForm) {
        addToCartForm.addEventListener('submit', function(e) {
            const quantityInput = document.getElementById('quantity');
            const quantity = parseInt(quantityInput.value);
            const max = parseInt(quantityInput.getAttribute('max'));
            
            if (quantity <= 0 || quantity > max) {
                e.preventDefault();
                alert(`Please enter a quantity between 1 and ${max} (Available Stock).`);
                return false;
            }
            return true;
        });
    }
    
    // --- Cart Quantity Update Check ---
    const cartUpdateForms = document.querySelectorAll('.update-cart-form');
    cartUpdateForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const qtyInput = form.querySelector('.cart-qty-input');
            const quantity = parseInt(qtyInput.value);
            
            if (quantity < 1) {
                e.preventDefault();
                alert("Quantity must be at least 1.");
                return false;
            }
            // Note: Server-side (PHP) handles the stock limit check
            return true;
        });
    });
});