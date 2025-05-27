<template>
  <div class="contacto-container">
    <h1>Contacto</h1>
    <form class="form-container" @submit.prevent="enviarCorreo">
      <label for="nombre">Nombre:</label>
      <input
        v-model="nombre"
        type="text"
        id="nombre"
        required
        placeholder="Tu nombre"
      />

      <label for="email">Correo electrónico:</label>
      <input
        v-model="email"
        type="email"
        id="email"
        required
        placeholder="Tu correo"
      />

      <label for="asunto">Asunto:</label>
      <input
        v-model="asunto"
        type="text"
        id="asunto"
        required
        placeholder="Asunto"
      />

      <label for="mensaje">Mensaje:</label>
      <textarea
        v-model="mensaje"
        id="mensaje"
        required
        placeholder="Escribe tu mensaje"
        maxlength="2000"
      ></textarea>
      <small class="contador">{{ mensaje.length }}/2000</small>

      <div class="modal-actions">
        <button type="submit" :disabled="loading">Enviar</button>
      </div>
      <div v-if="loading" class="spinner-container">
        <span class="spinner"></span>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import api from "../axios";
import { useToast } from "vue-toastification";
import "../assets/estilos/spinner.css";

const toast = useToast();

const nombre = ref("");
const email = ref("");
const asunto = ref("");
const mensaje = ref("");
const loading = ref(false);

function validarEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

const enviarCorreo = async () => {
  if (!email.value || !mensaje.value) {
    toast.error("El correo y el mensaje son obligatorios.");
    return;
  }
  if (!validarEmail(email.value)) {
    toast.info("Por favor, introduce un correo válido.", { timeout: 4000 });
    return;
  }
  loading.value = true;
  try {
    await api.post("/api/contact", {
      nombre: nombre.value,
      email: email.value,
      asunto: asunto.value,
      mensaje: mensaje.value,
    });
    toast.success("¡Correo enviado correctamente!");
    nombre.value = "";
    email.value = "";
    asunto.value = "";
    mensaje.value = "";
  } catch (err) {
    toast.error("Error al enviar el correo.");
  }
  loading.value = false;
};
</script>

<style scoped>
.contacto-container {
  width: 100%;
  max-width: 600px;
  margin: 20px auto;
  padding: 2rem;
  background: #565256;
  border-radius: 8px;
  border: solid 2px #ccc;
  box-shadow: 10px 10px 24px 0px rgba(0, 0, 0, 1);
  -webkit-box-shadow: 10px 10px 24px 0px rgba(0, 0, 0, 1);
  -moz-box-shadow: 10px 10px 24px 0px rgba(0, 0, 0, 1);
  box-sizing: border-box;
}

.form-container {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-container label {
  font-weight: 600;
  margin-bottom: 4px;
  color: white;
}

.form-container input[type="text"],
.form-container input[type="email"],
.form-container textarea {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
  background: #f9f9f9;
  transition: border 0.2s;
  font-family: Lexend;
  width: 100%;
  box-sizing: border-box;
}

.form-container input[type="text"]:focus,
.form-container input[type="email"]:focus,
.form-container textarea:focus {
  border-color: #0078d4;
  outline: none;
}

.form-container textarea {
  min-height: 100px;
  resize: vertical;
}

.contador {
  color: #aaa;
  font-size: 0.9em;
  align-self: flex-end;
  margin-bottom: 8px;
}

.form-container button[type="submit"] {
  padding: 10px;
  background-color: #3b3b3b;
  color: #fff;
  border: none;
  border-radius: 4px;
  font-weight: bold;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s;
}

.form-container button[type="submit"]:hover {
  background-color: #5c5c5c;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.spinner-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
}

.form-container button[disabled] {
  background: #888 !important;
  color: #ccc !important;
  cursor: not-allowed !important;
  opacity: 0.7;
}

@media (max-width: 700px) {
  .contacto-container {
    padding: 1rem;
    max-width: 95vw;
  }
}

@media (max-width: 500px) {
  .contacto-container {
    padding: 0.5rem;
    border-radius: 0;
    box-shadow: none;
    border: none;
  }
  .form-container {
    gap: 10px;
  }
  .form-container button[type="submit"] {
    width: 100%;
  }
}
</style>
