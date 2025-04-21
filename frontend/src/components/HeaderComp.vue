<template>
    <header>
        <img src="../assets/Logo 1.png" alt="">
        <a href="#">Iniciar Sesión/Registrarse</a>
    </header>
</template>

<style scoped>
    header{
        background-color: rgb(61, 58, 58);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px
    }

    img{
        width: 150px;
    }

    a {
        text-decoration: none;
        color: white;
        border: solid 1px white;
        padding: 10px;
        border-radius: 1rem;
        font-weight: bold;
    }

    a:hover {
        background: white;
        color: rgb(61, 58, 58);
    }
</style>