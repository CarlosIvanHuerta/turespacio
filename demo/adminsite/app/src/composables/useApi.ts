import axios, { type AxiosInstance } from 'axios'

let api: AxiosInstance

export function createApi() {
  api = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL,
    withCredentials: true,
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    },
  })

  api.interceptors.response.use(
    (response: any) => {
      return response
    },
    (err: any) => {
      return Promise.reject(err)
    })

  return api
}

export function useApi() {
  if (!api) {
    createApi()
  }
  return api
}
