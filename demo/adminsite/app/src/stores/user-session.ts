import { acceptHMRUpdate, defineStore } from 'pinia'
import { computed, ref } from 'vue'

export type UserData = Record<string, any>

export const useUserSession = defineStore('userSession', () => {
  const user = useStorage('user', {})
  const token = useUserToken()
  const roleUser = useStorage('roleUser', '')
  const isLoggedIn = useStorage('isLoggedIn', false)

  function setUser(newUser: {}) {
    user.value = newUser
  }

  async function logoutUser() {
    const token = useUserToken()
    token.value = undefined
    user.value = undefined
    roleUser.value = ''
    isLoggedIn.value = false
  }

  async function setTokenUser(newToken: string) {
    token.value = newToken
    isLoggedIn.value = true
  }
  async function setRoleUser(newRole: string) {
    roleUser.value = newRole
  }

  return {
    user,
    isLoggedIn,
    roleUser,
    token,
    logoutUser,
    setUser,
    setTokenUser,
    setRoleUser
  } as const
})

/**
 * Pinia supports Hot Module replacement so you can edit your stores and
 * interact with them directly in your app without reloading the page.
 *
 * @see https://pinia.esm.dev/cookbook/hot-module-replacement.html
 * @see https://vitejs.dev/guide/api-hmr.html
 */
if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useUserSession, import.meta.hot))
}
