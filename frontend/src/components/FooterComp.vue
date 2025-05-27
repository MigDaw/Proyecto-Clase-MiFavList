<template>
  <footer>
    <div class="footer-content">
      <div class="footer-col">
        <h4>Redes sociales</h4>
        <div class="footer-socials">
          <a href="/contacto" title="Correo">
            <img :src="logoGmail" alt="Icono gmail" />
          </a>
          <a href="https://github.com/MigDaw" target="_blank" title="GitHub">
            <img :src="logoGithub" alt="Logo de GitHub" />
          </a>
          <a href="https://instagram.com/" target="_blank" title="Instagram">
            <img :src="logoInstagram" alt="Logo de Instagram" />
          </a>
          <a href="https://x.com/?lang=es" target="_blank" title="X">
            <img :src="logoX" alt="Logo de X" />
          </a>
        </div>
      </div>
      <div class="footer-col">
        <h4>Preguntas frecuentes</h4>
        <ul>
          <li><a href="#">¿Cómo funciona?</a></li>
          <li><a href="#">¿Quién puede registrarse?</a></li>
          <li><a href="#">¿Cómo contacto con soporte?</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>¿Dónde estamos?</h4>
        <ul>
          <li>
            <a href="https://maps.google.com/?q=Puerta+del+Sol,+Madrid" target="_blank">
              Ver en Google Maps
            </a>
          </li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Legal</h4>
        <ul>
          <li><a href="#">Política de privacidad</a></li>
          <li><a href="#">Aviso legal</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      Miguel Rico Pérez &copy; {{ new Date().getFullYear() }} - Todos los derechos reservados
    </div>
  </footer>
</template>

<script setup>
import logoGithub from "../assets/logo-github.svg";
import logoGmail from "../assets/logo-gmail.svg";
import logoInstagram from "../assets/logo-instagram.svg";
import logoX from "../assets/logo-x.svg";
</script>

<style scoped>
footer {
  background-color: rgb(61, 58, 58);
  color: #fff;
  width: 100%;
  padding: 18px 10px 0 10px;
}

.footer-content {
  display: flex;
  justify-content: center;
  gap: 40px;
  flex-wrap: wrap;
  padding-bottom: 10px;
}

.footer-col {
  display: flex;
  flex-direction: column;
  align-items: center;
  min-width: 180px;
  margin-bottom: 10px;
  text-align: center;
}

.footer-col h4 {
  margin-bottom: 10px;
  font-size: 1.2rem;
  color: #ffffff;
  margin: 20px;
}

.footer-col ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-col ul li {
  margin-bottom: 6px;
}

.footer-col ul li a {
  color: #fff;
  text-decoration: underline;
  font-size: 0.98rem;
  transition: color 0.2s;
}

.footer-col ul li a:hover {
  color: #ffb347;
}

.footer-socials {
  display: flex;
  gap: 14px;
  justify-content: center;
  align-items: center;
  margin-top: 4px;
}

.footer-socials img {
  width: 28px;
  height: 28px;
  filter: brightness(0) invert(1);
  transition: transform 0.2s;
}

.footer-socials img:hover {
  transform: scale(1.15);
}

.footer-bottom {
  text-align: center;
  font-size: 0.95rem;
  margin-top: 10px;
  padding-bottom: 8px;
  color: #ffffff;
  border-top: 1px solid #444;
}

/* Responsive */
@media (max-width: 900px) {
  .footer-content {
    gap: 20px;
  }
  .footer-col {
    min-width: 140px;
  }
}

@media (max-width: 600px) {
  .footer-content {
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding-bottom: 0;
  }
  .footer-col {
    min-width: unset;
    width: 100%;
    align-items: center;
  }
  .footer-socials img {
    width: 22px;
    height: 22px;
  }
}
</style>
