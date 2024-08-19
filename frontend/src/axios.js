import axios from 'axios';
const axiosIns = axios.create({
// You can add your headers here
// ================================
  baseURL:import.meta.env.VUE_APP_API_URL,
  headers: {
    "Content-Type" : "application/json",
    "Accept" : "application/json",
    "Access-Control-Allow-Origin" : "*",
    "Authorization":`Bearer ` +localStorage.getItem('authToken'),

  }
})

export default axiosIns
