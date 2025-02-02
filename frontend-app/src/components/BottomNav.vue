<template>
  <div class="bottom-nav">
    <button @click="$router.push('/home')">HOME</button>
    <button @click="$router.push('/productlist')">REWARD</button>
    <button @click="$router.push('/profile')">PROFILE</button>
  </div>
</template>

<script>
export default {
  name: 'BottomNav',
}
</script>

<style scoped>
/* Bottom Navigation Bar */
.bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: #007042;
  display: flex;
  justify-content: space-around;
  align-items: center;
  padding: 10px 0;
  box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.2);
  z-index: 1000;
}

.bottom-nav button {
  background-color: #fff;
  color: #333;
  border: none;
  font-size: 1rem;
  font-weight: bold;
  width: 30%;
  height: 3rem;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.3s ease;
}

.bottom-nav button:hover {
  background-color: black;
  transform: scale(1.1);
}

.bottom-nav button:focus {
  outline: none;
}

/* Responsive Design */
@media (max-width: 768px) {
  .bottom-nav {
    padding: 10px;
  }

  .bottom-nav button {
    font-size: 0.9rem;
  }
}
</style>
