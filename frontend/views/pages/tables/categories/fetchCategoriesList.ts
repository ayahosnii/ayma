import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { BASE_URL } from '@/config/apiConfig';
import { checkAuthToken } from '@/config/checkAuthToken';


export function fetchCategoriesList() {
  const categories = ref([]);
  const currentPage = ref(1);
  const totalPages = ref(1);
  const loading = ref(false);

  const fetchCategories = async (page = 1) => {
    try {
      loading.value = true;

      const token = checkAuthToken(); // Get the authentication token
      if (!token) {
        return;
      }

      const response = await axios.get(`${BASE_URL}/categories?page=${page}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      categories.value = response.data.data;
      totalPages.value = response.data.last_page;
      currentPage.value = response.data.current_page;
    } catch (error) {
      console.error('Failed to fetch categories:', error);
    } finally {
      loading.value = false;
    }
  };

  const onPageChange = (page) => {
    currentPage.value = page;
  };

  onMounted(() => fetchCategories(currentPage.value));
  watch(currentPage, (newPage) => fetchCategories(newPage));

  return {
    categories,
    currentPage,
    totalPages,
    loading,
    onPageChange,
  };
}
