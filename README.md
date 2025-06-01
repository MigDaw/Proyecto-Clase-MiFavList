# <img src="frontend/src/assets/Logo 1.png" alt="mFL Logo" width="30"/>  MiFavList - Plataforma Social de Contenido

## 📝 Descripción
mFL es una plataforma social moderna desarrollada con Vue.js y Symfony, que permite a los usuarios compartir y gestionar contenido, interactuar con otros usuarios y mantener perfiles personalizados. La aplicación ofrece una experiencia fluida y responsive, con características como gestión de perfiles, sistema de comentarios, y gestión de contenido multimedia.

## 🚀 Características Principales
- Autenticación y gestión de usuarios
- Perfiles personalizables
- Sistema de publicación de contenido
- Comentarios y interacciones
- Sistema de privacidad configurable
- Interfaz responsive y moderna
- Sistema de contacto integrado

## 🛠️ Tecnologías Utilizadas

### Frontend
- Vue 3 (Composition API)
- TypeScript
- Vue Router para navegación
- Vue Toastification
- Axios para peticiones HTTP
- CSS3 (Flexbox/Grid)

### Backend
- Symfony 6
- PHP 8.2
- MongoDB
- Doctrine ODM
- Symfony Mailer
- Symfony Security

### Infraestructura
- Docker
- Docker Compose
- Nginx
- MongoDB Atlas

## 📋 Requisitos Previos
- Docker y Docker Compose
- Node.js (v16 o superior)
- PHP 8.2
- Composer
- MongoDB

## 🔧 Instalación

1. Clonar el repositorio:
```bash
git clone https://github.com/tu-usuario/mFL.git
cd mFL
```

2. Configurar el frontend:
```bash
cd frontend
npm install
```

3. Configurar el backend:
```bash
cd backend
composer install
```

4. Configurar variables de entorno:
- Copiar `.env.example` a `.env` en el directorio backend
- Ajustar las variables según tu entorno

5. Iniciar los contenedores Docker:
```bash
docker-compose up -d
```

6. Iniciar el frontend en modo desarrollo:
```bash
cd frontend
npm run dev
```

## 🚀 Uso
La aplicación estará disponible en:
- Frontend: http://localhost:3000
- Backend API: http://localhost:8080

## 📁 Estructura del Proyecto

### Frontend
```
frontend/
├── src/
│   ├── components/    # Componentes Vue reutilizables
│   ├── views/        # Vistas principales
│   ├── router/       # Configuración de rutas
│   ├── stores/       # Estado global
│   └── assets/       # Recursos estáticos
```

### Backend
```
backend/
├── src/
│   ├── Controller/   # Controladores
│   ├── Document/     # Modelos de datos
│   └── Service/      # Servicios
├── config/           # Configuración
└── public/          # Punto de entrada
```

## 🔒 Endpoints API

### Autenticación
- `POST /api/auth/register` - Registro
- `POST /api/auth/login` - Login
- `POST /api/auth/logout` - Logout

### Usuarios
- `GET /api/users` - Listar usuarios
- `GET /api/users/{id}` - Obtener usuario
- `PUT /api/users/{id}` - Actualizar usuario

### Contenido
- `GET /api/content` - Listar contenido
- `POST /api/content` - Crear contenido
- `GET /api/content/{id}` - Obtener contenido
- `PUT /api/content/{id}` - Actualizar contenido
- `DELETE /api/content/{id}` - Eliminar contenido

## 👥 Contribución
Las contribuciones son bienvenidas. Por favor, sigue estos pasos:

1. Fork el proyecto
2. Crea una rama para tu característica (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request
