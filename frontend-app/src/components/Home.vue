<template>
  <div class="home">
    <h2>Welcome... {{ username }}</h2>
    <div class="user-info">
      <p>Points: {{ userPoints }}</p>
    </div>
    <hr>
    <div class="product-cover">
      <h3>Available Products</h3>
      <swiper
        :slides-per-view="3"
        space-between="20"
        navigation
        pagination
        loop
        class="product-slider"
      >
        <swiper-slide v-for="product in products" :key="product.id">
          <div class="product" @click="$router.push(`/product/${ product.id }`)">
            <img :src="product.image" alt="Product" class="product-image" />
            <h4>{{ product.name }}</h4>
            <p>{{ product.description }}</p>
            <p>points: {{ product.points }}</p>
            <p>วันหมดอายุ: {{ product.expiry_date }}</p>
            <!-- <button @click="$router.push(`/product/${ product.id }`)">view detail</button> -->
          </div>
        </swiper-slide>
      </swiper>

    </div>
    <BottomNav />
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import BottomNav from './BottomNav.vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import 'swiper/css';

export default {
  components: {
    BottomNav,
    Swiper,
    SwiperSlide,
  },
  setup() {
    const username = ref('')
    const userPoints = ref(0)
    const products = ref([])

    const fetchUserData = async () => {
      const token = localStorage.getItem('token')
      if (token) {
        try {
          const response = await axios.get('http://127.0.0.1:8000/api/user', {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          })
          username.value = response.data.name
          userPoints.value = response.data.points
        } catch (error) {
          console.error('Failed to fetch user data:', error)
        }
      }
    }

    const fetchProducts = async () => {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/products')
        products.value = response.data
      } catch (error) {
        console.error('Failed to fetch products:', error)
      }
    }

    // Fetch data on component mount
    onMounted(() => {
      fetchUserData()
      fetchProducts()
    })

    return { username, userPoints, products }
  },
}
</script>

<style scoped>
/* Global Styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: Arial, sans-serif;
  background-color: #f4f4f9;
  color: #333;
}

.home {
  max-width: 1200px;
  margin: 20px auto;
  padding: 20px;
  background-color: #fff;
}

.product-cover{
  padding-bottom:5%;
}

h2 {
  font-size: 2rem;
  color: #007042;
  margin-bottom: 10px;
  font-weight: bold;
}

h3 {
  font-size: 1.5rem;
  color: #333;
  margin-top: 30px;
}

.user-info {
  margin: 20px 0;
  font-size: 1.1rem;
  font-weight: bold;
}

.user-info p{
  font-weight: bold;
}

.product-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
  margin-top: 20px;
  padding-bottom:4%;
}

.product {
  background-color: #fff;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 20px;
  text-align: center;
  transition: transform 0.3s ease-in-out;
}

.product:hover {
  transform: scale(1.05);
}

.product-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 8px;
}

.product h4 {
  font-size: 1.2rem;
  margin-top: 10px;
  color: #333;
}

.product p {
  font-size: 0.9rem;
  margin-top: 10px;
  color: #777;
}

.product button {
  margin-top: 15px;
  padding: 10px 20px;
  background-color: #007042;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.product button:hover {
  background-color: #45a049;
}

/* Bottom Navigation */
.bottom-navigation {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: #333;
  padding: 10px 0;
  display: flex;
  justify-content: space-around;
  align-items: center;
}

.bottom-navigation a {
  color: white;
  font-size: 1.2rem;
  text-decoration: none;
}

.bottom-navigation a:hover {
  color: #4caf50;
}

/* Responsive Styles */
@media (max-width: 768px) {
  .home {
    padding: 15px;
  }

  .product-list {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 480px) {
  .product-list {
    grid-template-columns: 1fr;
  }
}
</style>
