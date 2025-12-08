<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Catálogo Gamer</title>

  <style>
    :root {
      --bg: #0f1724;
      --card: #101a2d;
      --accent: #6ee7b7;
      --neon1: #6d5cff;
      --neon2: #00eaff;
      --muted: #9aa4b2;
      --glass: rgba(255, 255, 255, 0.03);
    }

    body {
      margin: 0;
      font-family: Inter, Arial;
      background: linear-gradient(180deg, #071122, #0f1724);
      color: #e6eef6;
      padding: 24px;
      display: flex;
      gap: 20px;
    }

    /* CONTENEDOR */
    .container {
      max-width: 1100px;
      width: 70%;
    }

    /* GRID */
    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 20px;
    }

    /* CARD */
    .card {
      background: var(--card);
      padding: 16px;
      border-radius: 14px;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.3);
      transition: 0.2s ease;
      border: 1px solid rgba(255, 255, 255, 0.04);
    }

    .card:hover {
      transform: translateY(-4px);
      box-shadow: 0 0 14px rgba(109, 92, 255, 0.45);
    }

    .thumb {
      height: 300px;
      background: #061226;
      border-radius: 10px;
      overflow: hidden;
      margin-bottom: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    /* BOTONES GLOBALES */
    button {
      width: 100%;
      margin-top: 12px;
      padding: 10px;
      border: none;
      border-radius: 10px;
      background: var(--neon1);
      color: white;
      font-weight: 700;
      cursor: pointer;
      transition: 0.2s;
      box-shadow: 0 0 10px var(--neon1);
    }

    button:hover {
      background: #5a4aff;
      box-shadow: 0 0 14px var(--neon1);
    }

    /* BOTÓN DE USUARIO */
    .user-btn {
      position: fixed;
      left: 20px;
      top: 20px;
      width: 48px;
      height: 48px;
      border-radius: 50%;
      background: var(--neon1);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      cursor: pointer;
      z-index: 2000;
      box-shadow: 0 0 10px var(--neon1);
      font-size: 1rem;
    }

    .user-menu {
      position: fixed;
      left: 20px;
      top: 78px;
      background: #0b1220;
      border: 1px solid var(--neon2);
      border-radius: 10px;
      padding: 10px;
      display: none;
      z-index: 2001;
      width: 140px;
      box-shadow: 0 0 12px rgba(0, 234, 255, 0.25);
    }

    .user-menu div {
      padding: 10px;
      cursor: pointer;
      border-radius: 6px;
    }

    .user-menu div:hover {
      background: rgba(255, 255, 255, 0.06);
    }

    /* CARRITO */
    #cartToggle {
      position: fixed;
      right: 20px;
      top: 20px;
      background: var(--neon1);
      border: none;
      padding: 10px;
      border-radius: 50%;
      cursor: pointer;
      font-size: 1.4rem;
      width: 50px;
      height: 50px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 0 10px var(--neon1);
      z-index: 999;
    }

    #cartMenu {
      position: fixed;
      right: -330px;
      top: 0;
      width: 300px;
      height: 100%;
      background: #0b1220;
      box-shadow: -2px 0 10px rgba(0, 0, 0, 0.5);
      transition: 0.3s ease;
      padding: 20px;
      z-index: 1000;
      display: flex;
      flex-direction: column;
    }

    #cartMenu.active {
      right: 0;
    }

    #cartItems {
      margin-top: 15px;
      overflow-y: auto;
      max-height: 55%;
      padding-right: 10px;
    }

    #cartItems li {
      margin-bottom: 12px;
      padding-bottom: 6px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .empty {
      margin-top: 40px;
      text-align: center;
      font-size: 1.1rem;
      color: var(--muted);
    }
  </style>
</head>

<body>
  <!-- USUARIO -->
  <div id="userBtn" class="user-btn"></div>
  <div id="userMenu" class="user-menu">
    <div id="logoutOption">Cerrar sesión</div>
  </div>

  <!-- CATÁLOGO -->
  <div class="container">
    <div id="catalogo"></div>
  </div>

  <!-- CARRITO -->
  <button id="cartToggle">🛒</button>

  <div id="cartMenu">
    <h2>Carrito</h2>
    <ul id="cartItems"></ul>

    <!-- TOTAL -->
    <div id="cartTotal" style="margin-top:15px;font-size:1.1rem;font-weight:700;">
      Total: $0.00
    </div>

    <!-- FINALIZAR COMPRA -->
    <button id="finalizarCompra" style="margin-top:15px;background:var(--neon2);box-shadow:0 0 10px var(--neon2);">
      Finalizar compra
    </button>
  </div>
  <script>
    const API = "../php/obtener_videojuegos.php";

    /* ===================== CARGAR JUEGOS ===================== */
    async function loadGames() {
      try {
        const res = await fetch(API);
        const json = await res.json();
        const juegos = json.data;

        if (!juegos || juegos.length === 0) {
          document.getElementById("catalogo").innerHTML =
            `<div class="empty">No se encontraron juegos.</div>`;
          return;
        }

        const html = `
        <div class="grid">
          ${juegos.map(g => `
            <div class="card">
              <div class="thumb">
                <img src="../Cliente/images/${g.id_videojuego}.jpg"
                     onerror="this.src='../images/default.jpg';"
                     alt="${g.titulo}">
              </div>
              <h3>${g.titulo}</h3>
              <p style="font-size:0.9rem;color:var(--muted)">
                ${g.descripcion || ""}
              </p>
              <div style="margin-top:10px;font-weight:700">
                $${Number(g.precio).toFixed(2)}
              </div>
              <button onclick="addToCart(${g.id_videojuego}, '${g.titulo}', ${g.precio})">
                Añadir al carrito
              </button>
            </div>
          `).join("")}
        </div>
      `;

        document.getElementById("catalogo").innerHTML = html;

      } catch (e) {
        console.error(e);
        document.getElementById("catalogo").innerHTML =
          `<div class="empty">Error al cargar juegos.</div>`;
      }
    }
    loadGames();


    /* ===================== CARRITO ===================== */
    const cartMenu = document.getElementById("cartMenu");
    const cartItems = document.getElementById("cartItems");

    let cart = [];

    const cartTotalBox = document.createElement("div");
    cartTotalBox.style.marginTop = "15px";
    cartTotalBox.style.fontSize = "1.1rem";
    cartTotalBox.style.fontWeight = "700";
    cartTotalBox.innerText = "Total: $0.00";

    cartMenu.appendChild(cartTotalBox);

    const finalizarBtn = document.createElement("button");
    finalizarBtn.id = "finalizarCompra";
    finalizarBtn.innerText = "Finalizar compra";
    finalizarBtn.style.marginTop = "15px";
    finalizarBtn.style.background = "var(--neon2)";
    finalizarBtn.style.boxShadow = "0 0 10px var(--neon2)";
    cartMenu.appendChild(finalizarBtn);

    document.getElementById("cartToggle").onclick = () => {
      cartMenu.classList.toggle("active");
    };

    function actualizarTotal() {
      const total = cart.reduce((sum, x) => sum + x.precio, 0);
      cartTotalBox.innerText = `Total: $${total.toFixed(2)}`;
    }

    async function addToCart(id, titulo, precio) {
      cart.push({ id, titulo, precio });

      const li = document.createElement("li");
      li.textContent = `${titulo} - $${precio.toFixed(2)}`;
      cartItems.appendChild(li);

      actualizarTotal();
    }

    /* ===================== FINALIZAR COMPRA ===================== */
    finalizarBtn.onclick = async () => {
      if (cart.length === 0) {
        alert("Tu carrito está vacío.");
        return;
      }

      let errores = 0;

      for (let juego of cart) {
        const formData = new FormData();
        formData.append("total", juego.precio);
        formData.append("estado", "pendiente");
        formData.append("id_videojuego", juego.id);

        try {
          const res = await fetch("../php/insertar_pedido.php", {
            method: "POST",
            body: formData
          });

          const text = await res.text();
          if (!text.includes("ok")) errores++;

        } catch (e) {
          errores++;
        }
      }

      if (errores === 0) {
        alert("Pedido realizado (un registro por juego).");
        cart = [];
        cartItems.innerHTML = "";
        actualizarTotal();
      } else {
        alert("Hubo algunos errores al registrar los pedidos.");
      }
    };


    /* ===================== LOGIN / USUARIO ===================== */
    const cliente = localStorage.getItem("cliente");
    if (!cliente) window.location.href = "../index.html";

    function getInitials(name) {
      return name.split(" ").map(x => x[0].toUpperCase()).join("").slice(0, 2);
    }

    const userBtn = document.getElementById("userBtn");
    const userMenu = document.getElementById("userMenu");

    userBtn.textContent = getInitials(cliente);
    userBtn.onclick = () =>
      userMenu.style.display = userMenu.style.display === "block" ? "none" : "block";

    document.getElementById("logoutOption").onclick = () => {
      localStorage.removeItem("cliente");
      window.location.href = "../index.html";
    };

    document.body.onclick = e => {
      if (!userBtn.contains(e.target) && !userMenu.contains(e.target))
        userMenu.style.display = "none";
    };
  </script>

</body>

</html>