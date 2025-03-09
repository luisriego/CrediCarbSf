### Use Case 1: Add Item to Cart ✅

**Description:** As a user, I want to add an item to my shopping cart so that I can purchase it later.

**Endpoint:** `POST /api/shopping-cart/items`

**Steps:**
1. User sends a POST request with product ID and quantity.
2. System validates product availability and stock.
3. System adds item to the cart.
4. System returns updated cart information.

**Preconditions:**
- User must be authenticated
- Product must exist and be in stock

**Postconditions:**
- Item is added to the cart
- Cart total is updated

### Use Case 2: Remove Item from Cart ✅

**Description:** As a user, I want to remove an item from my shopping cart.

**Endpoint:** `DELETE /api/shopping-cart/items/{itemId}`

**Steps:**
1. User sends DELETE request with item ID.
2. System removes item from cart.
3. System returns updated cart information.

**Preconditions:**
- User must be authenticated
- Item must exist in cart

**Postconditions:**
- Item is removed from cart
- Cart total is updated

### Use Case 3: Update Item Quantity ✅

**Description:** As a user, I want to update the quantity of an item in my cart.

**Endpoint:** `PUT /api/shopping-cart/items/{itemId}`

**Steps:**
1. User sends PUT request with item ID and new quantity.
2. System validates stock availability.
3. System updates item quantity.
4. System returns updated cart information.

**Preconditions:**
- User must be authenticated
- Item must exist in cart
- New quantity must be available in stock

**Postconditions:**
- Item quantity is updated
- Cart total is updated

### Use Case 4: Clear Shopping Cart ✅

**Description:** As a user, I want to remove all items from my shopping cart.

**Endpoint:** `DELETE /api/shopping-cart`

**Steps:**
1. User sends DELETE request to clear cart.
2. System removes all items from cart.
3. System returns empty cart confirmation.

**Preconditions:**
- User must be authenticated
- Cart must exist

**Postconditions:**
- Cart is empty
- Cart total is zero

### Use Case 5: View Cart Summary ✅

**Description:** As a user, I want to view my current shopping cart contents and total.

**Endpoint:** `GET /api/shopping-cart`

**Steps:**
1. User requests cart information.
2. System retrieves current cart items and calculates total.
3. System returns cart details.

**Preconditions:**
- User must be authenticated

**Postconditions:**
- User receives cart contents and total

### Use Case 6: Proceed to Checkout ✅

**Description:** As a user, I want to proceed to checkout with my current cart items.

**Endpoint:** `POST /api/shopping-cart/checkout`

**Steps:**
1. User initiates checkout process.
2. System validates cart items and stock.
3. System calculates final total with taxes and shipping.
4. System creates order from cart items.
5. System returns checkout information.

**Preconditions:**
- User must be authenticated
- Cart must not be empty
- All items must be in stock

**Postconditions:**
- Order is created
- Cart is cleared
- User is redirected to payment

### Use Case 7: Apply Discount Code

**Description:** As a user, I want to apply a discount code to my cart.

**Endpoint:** `POST /api/shopping-cart/discount`

**Steps:**
1. User submits discount code.
2. System validates discount code.
3. System applies discount to cart total.
4. System returns updated cart information.

**Preconditions:**
- User must be authenticated
- Cart must not be empty
- Discount code must be valid
- Discount code applies to cart items

**Postconditions:**
- Discount is applied to cart
- Cart total is updated

### Summary of Use Cases:

1. **Add Item to Cart**
2. **Remove Item from Cart**
3. **Update Item Quantity**
4. **Clear Shopping Cart**
5. **View Cart Summary**
6. **Proceed to Checkout**
7. **Apply Discount Code**