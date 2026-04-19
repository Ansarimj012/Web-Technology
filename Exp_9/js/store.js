// ===================== STORE STATE =====================
const Store = {
  products: [
    { id: 1, name: "Laptop", price: 70000, oldPrice: 84999, img: "Images/Laptop.avif", rating: 4.2, reviews: 321, category: "electronics" },
    { id: 2, name: "Moto G96 5G (Pantone Blue, 256GB)", price: 18999, oldPrice: 22999, img: "Images/motorola.jpg", rating: 4.1, reviews: 7695, category: "electronics" },
    { id: 3, name: "Protonic Headphones", price: 1768, oldPrice: 2999, img: "Images/headphone.jpg", rating: 4.3, reviews: 1689, category: "electronics" },
    { id: 4, name: "iPhone 13", price: 67359, oldPrice: 82499, img: "Images/phone.jpg", rating: 4.4, reviews: 119, category: "electronics" },
    { id: 5, name: "Tibra Attire Men Kurta", price: 373, oldPrice: 1499, img: "Images/kurta.jpg", rating: 3.1, reviews: 10321, category: "clothes" },
    { id: 6, name: "Kriska Women Viscose Rayon Kurta", price: 559, oldPrice: 2499, img: "Images/Kurti.avif", rating: 3.9, reviews: 155, category: "clothes" },
    { id: 7, name: "Ladies Ethnic Wear", price: 559, oldPrice: 2499, img: "Images/Ladies.avif", rating: 4.0, reviews: 519, category: "clothes" },
    { id: 8, name: "Denim Jeans", price: 1559, oldPrice: 2499, img: "Images/Jeans.webp", rating: 4.2, reviews: 119, category: "clothes" },
    { id: 9, name: "RED TAPE Men Shoes", price: 1568, oldPrice: 2999, img: "Images/Redtapshoes.jpg", rating: 4.5, reviews: 1689, category: "accessories" },
    { id: 10, name: "Smart Watch", price: 1268, oldPrice: 2999, img: "Images/watch.jpg", rating: 4.1, reviews: 2689, category: "accessories" },
    { id: 11, name: "Gold Locket", price: 1159, oldPrice: 2499, img: "Images/locket.jpg", rating: 4.0, reviews: 190, category: "accessories" },
    { id: 12, name: "Women Fashion Top", price: 799, oldPrice: 1999, img: "Images/femaleclothes.jpg", rating: 4.1, reviews: 432, category: "clothes" },
  ],

  getCart() {
    try { return JSON.parse(localStorage.getItem("cart")) || []; }
    catch { return []; }
  },

  saveCart(cart) {
    localStorage.setItem("cart", JSON.stringify(cart));
    this.updateCartBadge();
  },

  addToCart(productId) {
    const product = this.products.find(p => p.id === productId);
    if (!product) return;
    const cart = this.getCart();
    const existing = cart.find(i => i.id === productId);
    if (existing) {
      existing.qty += 1;
    } else {
      cart.push({ ...product, qty: 1 });
    }
    this.saveCart(cart);
    showToast(`"${product.name}" added to cart! 🛒`);
  },

  removeFromCart(productId) {
    const cart = this.getCart().filter(i => i.id !== productId);
    this.saveCart(cart);
  },

  updateQty(productId, qty) {
    const cart = this.getCart();
    const item = cart.find(i => i.id === productId);
    if (item) {
      item.qty = Math.max(1, parseInt(qty) || 1);
      this.saveCart(cart);
    }
  },

  getCartTotal() {
    return this.getCart().reduce((sum, i) => sum + i.price * i.qty, 0);
  },

  getCartCount() {
    return this.getCart().reduce((sum, i) => sum + i.qty, 0);
  },

  updateCartBadge() {
    const badge = document.getElementById("cart-badge");
    if (badge) {
      const count = this.getCartCount();
      badge.textContent = count;
      badge.style.display = count > 0 ? "flex" : "none";
    }
  },

  // Auth helpers
  getUser() {
    try { return JSON.parse(localStorage.getItem("user")) || null; }
    catch { return null; }
  },
  saveUser(user) { localStorage.setItem("user", JSON.stringify(user)); },
  logout() {
    localStorage.removeItem("user");
    showToast("Logged out successfully.");
    setTimeout(() => window.location.href = "index.php", 1000);
  },
  isLoggedIn() { return !!this.getUser(); }
};

// ===================== TOAST =====================
function showToast(message, type = "success") {
  let container = document.getElementById("toast-container");
  if (!container) {
    container = document.createElement("div");
    container.id = "toast-container";
    container.style.cssText = `position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px;`;
    document.body.appendChild(container);
  }
  const toast = document.createElement("div");
  toast.style.cssText = `
    background: ${type === "error" ? "#e53e3e" : "#00adb5"};
    color: white; padding: 12px 20px; border-radius: 8px;
    font-family: 'Poppins', sans-serif; font-size: 14px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.18);
    animation: slideIn 0.3s ease; max-width: 320px;
    display: flex; align-items: center; gap: 10px;
  `;
  toast.innerHTML = `<span style="font-size:18px">${type === "error" ? "⚠️" : "✅"}</span> ${message}`;
  container.appendChild(toast);
  setTimeout(() => { toast.style.opacity = "0"; toast.style.transition = "opacity 0.4s"; setTimeout(() => toast.remove(), 400); }, 3000);
}

// ===================== SEARCH =====================
function initSearch() {
  const input = document.getElementById("search-input");
  const dropdown = document.getElementById("search-dropdown");
  if (!input || !dropdown) return;

  input.addEventListener("input", () => {
    const q = input.value.trim().toLowerCase();
    dropdown.innerHTML = "";
    if (!q) { dropdown.style.display = "none"; return; }
    const results = Store.products.filter(p => p.name.toLowerCase().includes(q)).slice(0, 6);
    if (!results.length) { dropdown.style.display = "none"; return; }
    results.forEach(p => {
      const item = document.createElement("div");
      item.className = "search-item";
      item.innerHTML = `<img src="${p.img}" style="width:40px;height:40px;object-fit:cover;border-radius:6px;"><div><div style="font-weight:600;font-size:14px;">${p.name}</div><div style="color:#00adb5;font-size:13px;">₹${p.price.toLocaleString()}</div></div>`;
      item.style.cssText = "display:flex;align-items:center;gap:12px;padding:10px 14px;cursor:pointer;border-bottom:1px solid #f0f0f0;";
      item.addEventListener("mouseenter", () => item.style.background = "#f7fffe");
      item.addEventListener("mouseleave", () => item.style.background = "white");
      item.addEventListener("click", () => {
        Store.addToCart(p.id);
        input.value = "";
        dropdown.style.display = "none";
      });
      dropdown.appendChild(item);
    });
    dropdown.style.display = "block";
  });

  document.addEventListener("click", (e) => {
    if (!input.contains(e.target) && !dropdown.contains(e.target)) dropdown.style.display = "none";
  });

  input.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      const q = input.value.trim().toLowerCase();
      if (q) {
        window.location.href = `products.php?search=${encodeURIComponent(q)}`;
      }
    }
  });
}

// ===================== NAV AUTH UPDATE =====================
function updateNavAuth() {
  const user = Store.getUser();
  const loginLink = document.getElementById("nav-login-link");
  const userMenu = document.getElementById("nav-user-menu");
  const userNameSpan = document.getElementById("nav-user-name");
  if (user && loginLink && userMenu) {
    loginLink.style.display = "none";
    userMenu.style.display = "flex";
    if (userNameSpan) userNameSpan.textContent = user.name.split(" ")[0];
  }
}

// Init on every page
document.addEventListener("DOMContentLoaded", () => {
  Store.updateCartBadge();
  initSearch();
  updateNavAuth();
});
