<template>
  <div class="profile-comments">
    <div class="comments-header">
      <h3>Comentarios</h3>
      <button
        v-if="!isOwnProfile && profileUser?.chatPublic"
        @click="showCommentModal = true"
        class="btn add-comment-btn"
      >
        Añadir comentario
      </button>
    </div>
    <div v-if="loadingComments" class="spinner"></div>
    <div v-else>
      <div v-if="comments.length === 0" class="no-comments">
        No hay comentarios todavía.
      </div>
      <div v-for="comment in orderedComments" :key="comment.id" class="comment">
        <div class="comment-header">
          <img
            :src="
              comment.author.profilePic
                ? `http://localhost:8080${comment.author.profilePic}`
                : imagenGenerica
            "
            class="comment-avatar"
            alt="avatar"
          />
          <span class="comment-username">{{ comment.author.username }}</span>
          <span class="comment-date">{{
            formatDate(comment.commentDate)
          }}</span>
          <button
            v-if="canDeleteComment(comment)"
            @click="openDeleteModal(comment.id)"
            class="btn-delete"
            title="Borrar comentario"
          >
            <img :src="iconoDelete" alt="Icono de borrar" class="icon-delete" />
          </button>
        </div>
        <div class="comment-message">{{ comment.message }}</div>
      </div>
    </div>
    <!-- MODAL PARA AÑADIR COMENTARIO -->
    <div v-if="showCommentModal" class="modal-overlay">
      <div class="modal-content">
        <p>Nuevo comentario</p>
        <textarea
          v-model="newComment"
          :disabled="postingComment"
          placeholder="Escribe un comentario..."
          @keyup.enter.exact.prevent="postComment"
          maxlength="300"
          rows="4"
          class="comment-textarea"
        ></textarea>
        <div class="char-counter">{{ newComment.length }}/300 caracteres</div>
        <div class="modal-actions">
          <button
            @click="postComment"
            :disabled="
              postingComment || !newComment.trim() || newComment.length > 300
            "
          >
            Enviar
          </button>
          <button @click="closeCommentModal" :disabled="postingComment">
            Cancelar
          </button>
        </div>
        <div v-if="postingComment" class="spinner-container">
          <span class="spinner"></span>
        </div>
      </div>
    </div>
    <!-- MODAL PARA CONFIRMAR BORRADO -->
    <div v-if="showDeleteModal" class="modal-overlay">
      <div class="modal-content">
        <p>¿Seguro que quieres borrar este comentario?</p>
        <div class="modal-actions">
          <button @click="confirmDeleteComment" :disabled="loadingDelete">
            Confirmar
          </button>
          <button @click="showDeleteModal = false" :disabled="loadingDelete">
            Cancelar
          </button>
        </div>
        <div v-if="loadingDelete" class="spinner-container">
          <span class="spinner"></span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, computed } from "vue";
import imagenGenerica from "../assets/Imagen-generica.png";
import api from "../axios";
import { useToast } from "vue-toastification";
import iconoDelete from "../assets/icono-delete.svg";

const props = defineProps<{
  profileUser: any;
  isOwnProfile: boolean;
  userStore: any;
}>();

const toast = useToast();

const loadingComments = ref(false);
const postingComment = ref(false);
const showCommentModal = ref(false);
const newComment = ref("");
const comments = ref<any[]>([]);
const showDeleteModal = ref(false);
const commentToDelete = ref<any>(null);
const loadingDelete = ref(false);

const fetchComments = async () => {
  if (!props.profileUser || !props.profileUser.id) {
    comments.value = [];
    return;
  }
  loadingComments.value = true;
  try {
    const res = await api.get(`/api/profiles/${props.profileUser.id}/comments`);
    comments.value = res.data;
  } catch {
    comments.value = [];
  } finally {
    loadingComments.value = false;
  }
};

const postComment = async () => {
  if (!newComment.value.trim()) return;
  postingComment.value = true;
  try {
    await api.post(`/api/profiles/${props.profileUser.id}/comments`, {
      message: newComment.value.trim(),
    });
    newComment.value = "";
    await fetchComments();
    toast.success("Comentario publicado");
    showCommentModal.value = false;
  } catch (err: any) {
    toast.error(err.response?.data?.error || "No se pudo enviar el comentario");
  } finally {
    postingComment.value = false;
  }
};

const closeCommentModal = () => {
  showCommentModal.value = false;
  newComment.value = "";
};

const openDeleteModal = (commentId: string) => {
  commentToDelete.value = commentId;
  showDeleteModal.value = true;
};

const confirmDeleteComment = async () => {
  if (!commentToDelete.value) return;
  loadingDelete.value = true;
  try {
    await api.delete(`/api/comments/${commentToDelete.value}`);
    await fetchComments();
    toast.success("Comentario borrado");
  } catch (err: any) {
    toast.error(err.response?.data?.error || "No se pudo borrar el comentario");
  }
  loadingDelete.value = false;
  showDeleteModal.value = false;
  commentToDelete.value = null;
};

const canDeleteComment = (comment: any) => {
  if (props.isOwnProfile) return true;
  return comment.author.id === props.userStore?.id;
};

const formatDate = (dateStr: string) => {
  const date = new Date(dateStr);
  return date.toLocaleString();
};

const orderedComments = computed(() =>
  [...comments.value].sort(
    (a, b) =>
      new Date(b.commentDate).getTime() - new Date(a.commentDate).getTime()
  )
);

watch(() => props.profileUser, fetchComments, { immediate: true });
onMounted(fetchComments);
</script>

<style scoped>
.profile-comments {
  margin-top: 0;
  max-width: 420px;
  width: 100%;
  background: #565256;
  border-radius: 16px;
  padding: 32px 24px;
  box-shadow: 10px 10px 24px 0px rgba(0, 0, 0, 1);
  -webkit-box-shadow: 10px 10px 24px 0px rgba(0, 0, 0, 1);
  -moz-box-shadow: 10px 10px 24px 0px rgba(0, 0, 0, 1);
  min-width: 320px;
  max-height: 505px;
  overflow-y: auto;
}

.comment {
  background: #393a3b;
  border-radius: 8px;
  margin-bottom: 16px;
  padding: 12px 16px;
  min-height: 60px;
}

.comment-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 4px;
}

.comment-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
}

.comment-username {
  font-weight: bold;
}

.comment-date {
  font-size: 0.85em;
  color: #bbb;
  margin-left: auto;
}

.btn {
  margin: 0 10px;
  padding: 8px 20px;
  border-radius: 4px;
  border: none;
  cursor: pointer;
  font-family: Lexend;
  font-size: 1rem;
  background-color: rgb(54, 65, 60);
  color: white;
}

.btn:hover {
  background-color: #292727;
}

.btn-delete {
  background: none;
  border: none;
  color: #e44;
  font-size: 1.2em;
  cursor: pointer;
  margin-left: 10px;
}

.comment-message {
  margin-left: 42px;
  word-break: break-word;
}

.add-comment-btn {
  margin: 0;
  box-shadow: 10px 10px 24px 0px rgba(0,0,0,1);
-webkit-box-shadow: 10px 10px 24px 0px rgba(0,0,0,1);
-moz-box-shadow: 10px 10px 24px 0px rgba(0,0,0,1);
}

.comments-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 18px;
}

.no-comments {
  color: #ccc;
  text-align: center;
  margin: 20px 0;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  backdrop-filter: blur(4px) brightness(0.8);
}

.modal-content {
  background: #222;
  padding: 2rem 2.5rem;
  border-radius: 10px;
  box-shadow: 0 4px 32px rgba(0, 0, 0, 0.25);
  text-align: center;
  min-width: 300px;
}

.modal-actions {
  margin-top: 1.5rem;
}

.modal-actions button {
  margin: 0 10px;
  padding: 8px 20px;
  border-radius: 4px;
  border: none;
  cursor: pointer;
  font-family: Lexend;
  font-size: 1rem;
  background-color: rgb(97, 111, 104);
  color: white;
}

.modal-actions button:hover {
  background-color: rgb(67, 78, 73);
}

.modal-actions button[disabled] {
  margin-top: 10px;
  background: #888 !important;
  color: #ccc !important;
  cursor: not-allowed !important;
  opacity: 0.7;
}

.icon-delete {
  width: 24px;
  cursor: pointer;
  transition: transform 0.2s;
}

.icon-delete:hover {
  transform: scale(1.15);
}

.comment-textarea {
  width: 100%;
  padding: 10px;
  border-radius: 4px;
  border: 1px solid #444;
  background: #333;
  color: #fff;
  font-family: Lexend;
  font-size: 1rem;
  resize: none;
}

.char-counter {
  color: #ebebeb;
  font-size: 0.85em;
  margin-top: 8px;
  text-align: right;
}

.spinner-container {
  margin-top: 18px;
  display: flex;
  justify-content: center;
}
</style>
