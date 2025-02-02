<template>
    <div class="profile">
        <h2>User Profile</h2>
        <div class="profile-info">
            <p><strong>Username:</strong> {{ user?.username }}</p>
            <p><strong>name:</strong> {{ user?.name }}</p>
            <p><strong>Points:</strong> {{ user?.points }}</p>
        </div>
    
    </div>
    <BottomNav />
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import BottomNav from './BottomNav.vue'

export default {
    name: 'Profile',
    components: {
        BottomNav,
    },
    setup() {
        const user = ref(null)

        const fetchUserProfile = async () => {
            const token = localStorage.getItem('token')
            if (token) {
                try {
                    const response = await axios.get('http://127.0.0.1:8000/api/user', {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    })
                    user.value = response.data
                } catch (error) {
                    console.error('Failed to fetch user profile:', error)
                }
            }
        }


        onMounted(() => {
            fetchUserProfile()
        })

        return { user }
    },
}
</script>

<style scoped>

strong{
    font-weight: bold;
}

.profile {
    padding: 20px;
    max-width: 800px;
    margin: 0 auto;

}

.profile h2 {
    font-size: 2rem;
    color: #007042;
    margin-bottom: 20px;
    text-align: center;
    font-weight: bold;
}

.profile-info p {
    font-size: 1.2rem;
    color: #555;
    margin-bottom: 15px;
}

</style>