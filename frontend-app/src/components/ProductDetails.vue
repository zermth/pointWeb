<template>
  <button @click="goBack" class="back-button">Back</button>
  <div class="product-details">
    <div class="product-header">
      <h2>{{ product?.name }}</h2>
      <p class="product-description">{{ product?.description }}</p>
    </div>

    <div class="product-image">
      <img :src="product?.image" alt="Product Image" class="product-img" />
    </div>

    <div class="product-info">
      <p><strong>Points required:</strong> {{ product?.points }}</p>
      <p><strong>Expiry Date:</strong> {{ product?.expiry_date }}</p>
      <p><strong>Condition:</strong> {{ product?.condition }}</p>
      <p v-if="product?.redeemed" class="redeemed">Already Redeemed</p>
    </div>

    <!-- Disable button if product is redeemed or user points are insufficient -->
    <button @click="redeemProduct" :disabled="product?.redeemed || userPoints < product?.points" class="redeem-button">
      Redeem
    </button>

    <div v-if="product?.redeemed" class="redeemed-message">
      <p>You have already redeemed this product!</p>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRoute } from 'vue-router'
import Swal from 'sweetalert2' // Import SweetAlert2

export default {
  setup() {
    const product = ref(null)
    const userPoints = ref(0) // Add user points as a ref
    const route = useRoute()

    // Fetch product details by product ID from route
    const fetchProductDetails = async () => {
      const productId = route.params.id
      try {
        const response = await axios.get(`http://127.0.0.1:8000/api/product/${productId}`)
        product.value = response.data
      } catch (error) {
        console.error('Failed to fetch product details:', error)
      }
    }

    // Fetch user data (points) from the API
    const fetchUserData = async () => {
      const token = localStorage.getItem('token')
      if (token) {
        try {
          const response = await axios.get('http://127.0.0.1:8000/api/user', {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          })
          userPoints.value = response.data.points // Store user points
        } catch (error) {
          console.error('Failed to fetch user data:', error)
        }
      }
    }

    // Handle product redemption with SweetAlert2 confirmation
    const redeemProduct = async () => {
      const token = localStorage.getItem('token')
      if (token && product.value && !product.value.redeemed) {
        // Show SweetAlert2 confirmation dialog
        const result = await Swal.fire({
          title: 'Are you sure?',
          text: `Do you want to redeem the product ${product.value.name}?`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#007042',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, Redeem!',
        })

        if (result.isConfirmed) {
          try {
            const response = await axios.post(
              'http://127.0.0.1:8000/api/redeem',
              { product_id: product.value.id },
              {
                headers: {
                  Authorization: `Bearer ${token}`,
                },
              }
            )
            Swal.fire(
              'Redeemed!',
              'You have successfully redeemed the product.',
              'success'
            )
            product.value.redeemed = true
          } catch (error) {
            console.error('Redeem failed:', error)
            Swal.fire(
              'Failed!',
              'There was an error redeeming the product.',
              'error'
            )
          }
        }
      }
    }

    const goBack = () => {
      window.history.back()
    }

    // Fetch product details and user data when the component is mounted
    onMounted(() => {
      fetchProductDetails()
      fetchUserData() // Fetch user data to get points
    })

    return { product, userPoints, redeemProduct, goBack }
  },
}
</script>

<style scoped>
/* Back Button Styling */
.back-button {
  margin-top: 20px;
  padding: 10px 20px;
  border: 1px solid #007042;
  background: transparent;
  color: #007042;
  border-radius: 4px;
  margin-left:5%;
  cursor: pointer;
  font-size: 1rem;
  transition: background-color 0.3s ease;
}

.back-button:hover {
  background-color: #007042;
  color: white;
}

.product-details {
  text-align: center;
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.product-header {
  text-align: center;
  margin-bottom: 20px;
}

.product-header h2 {
  font-size: 2rem;
  color: #333;
}

.product-description {
  font-size: 1rem;
  color: #666;
  line-height: 1.5;
}

.product-image {
  text-align: center;
  margin: 20px 0;
}

.product-img {
  max-width: 50%;
  /* Makes the image smaller */
  height: auto;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.product-info {
  margin-bottom: 20px;
  text-align: center;
}

.product-info p {
  font-size: 1.2rem;
  color: #333;
  margin-bottom: 10px;
}

.redeemed {
  color: #e74c3c;
  font-weight: bold;
}

.redeem-button {
  background-color: #007042;
  color: white;
  padding: 12px 24px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1.1rem;
  transition: background-color 0.3s ease;
}

.redeem-button:hover {
  background-color: #45a049;
}

.redeem-button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.redeemed-message {
  margin-top: 20px;
  padding: 15px;
  background-color: #f8d7da;
  color: #721c24;
  border-radius: 5px;
  text-align: center;
}

@media (max-width: 768px) {
  .product-details {
    padding: 15px;
  }

  .product-header h2 {
    font-size: 1.5rem;
  }

  .product-description {
    font-size: 0.9rem;
  }

  .product-info p {
    font-size: 1rem;
  }

  .redeem-button {
    font-size: 1rem;
  }

  .product-img {
    max-width: 80%;
    /* Adjust image size on smaller screens */
  }
}
</style>
