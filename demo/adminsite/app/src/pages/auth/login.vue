<script setup lang="ts">
import { toTypedSchema } from "@vee-validate/zod"
import { useForm, useField } from "vee-validate"
import { ref } from "vue"
import { z } from "zod"
import { useNotyf } from "/@src/composables/notyf.ts"

type StepId = 'login' | 'forgot-password'
const step = ref<StepId>('login')
const isLoading = ref(false)
const router = useRouter()
const route = useRoute()
const notyf = useNotyf()
const userSession = useUserSession()
const api = createApi()
const redirect = route.query.redirect as string
const zodSchema = z.object({
  email: z
      .string({
        required_error: "Ingrese su usuario",
      })
      .email("Debe ingresar un email válido"),
  password: z
      .string({
        required_error: "Ingrese su password",
      })
      .min(6, "Al menos 6 caracteres"),
})
type FormInput = z.infer<typeof zodSchema>;
const validationSchema = toTypedSchema(zodSchema);
const initialValues = computed<FormInput>(() => ({
  email: "",
  password: "",
}))
const { handleSubmit, errors } = useForm({
  validationSchema,
  initialValues,
})
const { value: email, meta: emailMeta } = useField("email")
const { value: password, meta: passwordMeta } = useField("password")

const handleLogin = handleSubmit(async (values) => {
  if (!isLoading.value) {
    isLoading.value = true;
  }

  await api
      .post("auth/login", values)
      .then(async ({data}) => {
        console.log(data)
        if (data.Success) {
          userSession.setUser(data.user)
          await userSession.setTokenUser(data.token)
          await userSession.setRoleUser(data.role)
          notyf.success({
            message:
                "Bienvenido de nuevo, " +
                userSession.user?.nickname,
            duration: 3000,
            position: {
              x: 'center',
              y: 'center',
            },
            icon: {
              className: 'fas fa-user-check',
              tagName: 'i',
              text: '',
            },
          })

          if (userSession.roleUser === 'administrador') {
            await router.push("/dashboard/site");
          }
          else {
            await router.push("/sidebar/inicio")
          }
        }
        else {
          notyf.error({
            message:data.Mensaje,
            duration: 3000,
            position: {
              x: 'center',
              y: 'center',
            }
          })
        }
      })
      .catch((err) => {
        notyf.error({
          message: err,
          duration: 6000,
        });
      })
      .finally(() => {
        isLoading.value = false;
      })
})

useHead({
  title: 'Iniciar Sesión - turespacio',
})
</script>

<template>
  <div class="modern-login">
    <div class="underlay h-hidden-mobile h-hidden-tablet-p" />

    <div class="columns is-gapless is-vcentered">
      <div class="column is-relative is-8 h-hidden-mobile h-hidden-tablet-p">
        <div class="hero is-fullheight is-image">
          <div class="hero-body">
            <div class="container">
              <div class="columns">
                <div class="column text-center">
                  <img
                      class="light-image-block hero-image-logo"
                      src="/images/logos/logo/tlogocolor.png"
                      alt=""
                  >
                  <img
                      class="dark-image-block hero-image-logo"
                      src="/images/logos/logo/tlogocolorwhite.png"
                      alt=""
                  >
                </div>
              </div>
              <div class="columns">
                <div class="column">
                  <img
                    class="hero-image"
                    src="/images/svg/maleta.svg"
                    alt="logoviaje"
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="column is-4 is-relative">
        <div class="top-tools">
          <RouterLink
            to="/"
            class="top-logo"
          >
            <img class="img is-128x128" src="/favicon.png" alt="logo"></img>
          </RouterLink>

          <VDarkmodeToggle />
        </div>
        <div class="is-form">
          <div class="is-form-inner">
            <div
              class="form-text"
              :class="[step !== 'login' && 'is-hidden']"
            >
              <h2 class="has-text-centered">Iniciar Sesión</h2>
            </div>
            <div
              class="form-text"
              :class="[step === 'login' && 'is-hidden']"
            >
              <h2>Recuperar cuenta</h2>
              <p>Verifica tu identidad.</p>
            </div>
            <form
              method="post"
              novalidate
              :class="[step !== 'login' && 'is-hidden']"
              class="login-wrapper"
              @submit.prevent="handleLogin"
            >
              <VField>
                <VLabel>Email de acceso</VLabel>
                <VControl :is-valid="emailMeta.valid" :has-error="!emailMeta.valid
                                                    " icon="lucide:user">
                  <VInput v-model="email" type="text" placeholder=""
                          autocomplete="" />
                  <span class="help is-danger">{{
                      errors.email
                    }}</span>
                </VControl>
              </VField>
              <VField>
                <VLabel>Contraseña</VLabel>
                <VControl :is-valid="passwordMeta.valid
                                                    " :has-error="!passwordMeta.valid
                                                        " icon="lucide:lock">
                  <VInput v-model="password" type="password" placeholder=""
                          autocomplete="" />
                  <span class="help is-danger">{{
                      errors.password
                    }}</span>
                </VControl>
              </VField>
              <VField>
                <VControl class="is-flex">
                  <a
                    tabindex="0"
                    role="button"
                    @keydown.enter.prevent="step = 'forgot-password'"
                    @click="step = 'forgot-password'"
                  >
                    ¿Problemas para ingresar?
                  </a>
                </VControl>
              </VField>
              <VButton :loading="isLoading" type="submit" color="primary"
                       icon="line-md:account" bold fullwidth raised>
                Inicia Sesión
              </VButton>
            </form>

            <form
              method="post"
              novalidate
              :class="[step !== 'forgot-password' && 'is-hidden']"
              class="login-wrapper"
              @submit.prevent
            >
              <p class="recover-text">
                Ingresa tu email de acceso, para enviar el link de recuperación, y poder ingresar a nuestro portal
              </p>
              <br>
              <VField>
                <VLabel class="auth-label">
                  Email de acceso
                </VLabel>
                <VControl icon="lucide:user">
                  <VInput
                    type="email"
                    autocomplete="current-email"
                  />
                </VControl>
              </VField>
              <div class="button-wrap">
                <VButton
                  color="danger"
                  size="big"
                  lower
                  rounded
                  @click="step = 'login'"
                >
                  Cancelar
                </VButton>
                <VButton
                  color="success"
                  size="big"
                  type="submit"
                  lower
                  rounded
                  solid
                  @click="step = 'login'"
                >
                  Confirmar
                </VButton>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.modern-login {
  position: relative;
  background: var(--white);
  min-height: 100vh;

  .column {
    &.is-relative {
      position: relative;
    }
  }

  .hero {
    &.has-background-image {
      position: relative;

      .hero-overlay {
        position: absolute;
        top: 0;
        inset-inline-start: 0;
        width: 100%;
        height: 100%;
        background: #5d4298 !important;
        opacity: 0.6;
      }
    }
  }

  .underlay {
    display: block;
    position: absolute;
    top: 0;
    inset-inline-start: 0;
    width: 66.6%;
    height: 100%;
    background: #fdfdfd;
    z-index: 0;
  }

  .top-tools {
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 400px;
    margin: 0 auto;
    padding: 0 1.25rem;
    margin-bottom: 5rem;

    .dark-mode {
      transform: scale(0.6);
      z-index: 2;
    }

    .top-logo {
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1;

      img {
        display: block;
        width: 100%;
        max-width: 50px;
        margin: 0 auto;
      }

      .iconify {
        height: 50px;
        width: 50px;
      }
    }
  }

  .is-image {
    position: relative;
    border-inline-end: 1px solid var(--fade-grey);

    .hero-image-logo {
      position: relative;
      z-index: 2;
      display: block;
      margin: -140px auto 0;
      max-width: 20%;
      width: 20%;

    }
    .hero-image {
      position: relative;
      z-index: 2;
      display: block;
      margin: -80px auto 0;
      max-width: 60%;
      width: 60%;
    }
  }

  .is-form {
    position: relative;
    max-width: 400px;
    margin: 0 auto;

    form {
      animation: fadeInLeft 0.5s;
    }

    .form-text {
      padding: 0 20px;
      animation: fadeInLeft 0.5s;

      h2 {
        font-family: var(--font-alt);
        font-weight: 400;
        font-size: 2rem;
        color: var(--primary);
      }

      p {
        color: var(--muted-grey);
        margin-top: 10px;
      }
    }

    .recover-text {
      font-size: 0.9rem;
      color: var(--dark-text);
    }

    .login-wrapper {
      padding: 30px 20px;

      .control {
        position: relative;
        width: 100%;
        margin-top: 16px;



        .error-text {
          color: var(--danger);
          font-size: 0.8rem;
          display: none;
          padding: 2px 6px;
        }

        .auth-label {
          position: absolute;
          top: 6px;
          inset-inline-start: 55px;
          font-size: 0.8rem;
          color: var(--dark-text);
          font-weight: 500;
          z-index: 2;
          transition: all 0.3s; // transition-all test
        }

        .autv-icon,
        :deep(.autv-icon) {
          position: absolute;
          top: 0;
          inset-inline-start: 0;
          height: 60px;
          width: 60px;
          display: flex;
          justify-content: center;
          align-items: center;
          font-size: 24px;
          color: var(--placeholder);
          transition: all 0.3s;
        }

        &.has-validation {
          .validation-icon {
            position: absolute;
            top: 0;
            inset-inline-end: 0;
            height: 60px;
            width: 60px;
            display: none;
            justify-content: center;
            align-items: center;

            .icon-wrapper {
              height: 20px;
              width: 20px;
              display: flex;
              justify-content: center;
              align-items: center;
              border-radius: var(--radius-rounded);

              .iconify {
                height: 10px;
                width: 10px;
                stroke-width: 3px;
                color: var(--white);
              }
            }

            &.is-success {
              .icon-wrapper {
                background: var(--success);
              }
            }

            &.is-error {
              .icon-wrapper {
                background: var(--danger);
              }
            }
          }

          &.has-success {
            .validation-icon {
              &.is-success {
                display: flex;
              }

              &.is-error {
                display: none;
              }
            }
          }

          &.has-error {
            .input {
              border-color: var(--danger);
            }

            .error-text {
              display: block;
            }

            .validation-icon {
              &.is-error {
                display: flex;
              }

              &.is-success {
                display: none;
              }
            }
          }
        }

        &.is-flex {
          display: flex;
          align-items: center;

          a {
            display: block;
            margin-inline-start: auto;
            color: var(--muted-grey);
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.3s;

            &:hover,
            &:focus {
              color: var(--primary);
            }
          }

          .remember-me {
            font-size: 0.9rem;
            color: var(--muted-grey);
            font-weight: 500;
          }
        }
      }

      .button-wrap {
        margin: 40px 0;

        &.has-help {
          display: flex;
          align-items: center;

          > span {
            margin-inline-start: 12px;
            font-family: var(--font);

            a {
              color: var(--primary);
              font-weight: 500;
              padding: 0 2px;
            }
          }
        }

        .button {
          height: 46px;
          width: 140px;
          margin-inline-start: 6px;

          &:first-child {
            &:hover {
              opacity: 0.8;
            }
          }
        }
      }
    }
  }
}

.remember-toggle {
  width: 65px;
  display: block;
  position: relative;
  cursor: pointer;
  font-size: 22px;
  user-select: none;
  transform: scale(0.9);

  input {
    position: absolute;
    opacity: 0;
    cursor: pointer;

    &:checked ~ .toggler {
      border-color: var(--primary);

      .active,
      .inactive {
        transform: translateX(calc(var(--transform-direction) * 100%)) rotate(360deg);
      }

      .active {
        opacity: 1;
      }

      .inactive {
        opacity: 0;
      }
    }
  }

  .toggler {
    position: relative;
    display: block;
    height: 34px;
    width: 61px;
    border: 2px solid var(--placeholder);
    border-radius: 100px;
    transition: all 0.3s; // transition-all test

    .active,
    .inactive {
      position: absolute;
      top: 2px;
      inset-inline-start: 2px;
      height: 26px;
      width: 26px;
      border-radius: var(--radius-rounded);
      background: black;
      display: flex;
      justify-content: center;
      align-items: center;
      transform: translateX(calc(var(--transform-direction) * 0))
        rotate(calc(var(--transform-direction) * 0));
      transition: all 0.3s ease;

      .iconify {
        color: var(--white);
        font-size: 14px;
      }
    }

    .inactive {
      background: var(--placeholder);
      border-color: var(--placeholder);
      opacity: 1;
      z-index: 1;
    }

    .active {
      background: var(--primary);
      border-color: var(--primary);
      opacity: 0;
      z-index: 0;
    }
  }
}

@media only screen and (width <= 767px) {
  .modern-login {
    .top-logo {
      top: 30px;
    }

    .dark-mode {
      top: 36px;
      inset-inline-end: 44px;
    }

    .is-form {
      padding-top: 100px;
    }
  }
}

@media only screen and (width >= 768px) and (width <= 1024px) and (orientation: portrait) {
  .modern-login {
    .top-logo {
      .iconify {
        height: 60px;
        width: 60px;
      }
    }

    .dark-mode {
      top: -58px;
      inset-inline-end: 30%;
    }

    .columns {
      display: flex;
      height: 100vh;
    }
  }
}

/* ==========================================================================
Dark mode
========================================================================== */

.is-dark {
  .modern-login {
    background: var(--dark-sidebar);

    .underlay {
      background: color-mix(in oklab, var(--dark-sidebar), white 10%);
    }

    .is-image {
      border-color: color-mix(in oklab, var(--dark-sidebar), white 10%);
    }

    .is-form {
      .form-text {
        h2 {
          color: var(--primary);
        }
      }

      .login-wrapper {
        .control {
          &.is-flex {
            a:hover {
              color: var(--primary);
            }
          }

          .input {
            background: color-mix(in oklab, var(--dark-sidebar), white 4%);

            &:focus {
              border-color: var(--primary);

              ~ .autv-icon {
                .iconify {
                  color: var(--primary);
                }
              }
            }
          }

          .auth-label {
            color: var(--light-text);
          }
        }

        .button-wrap {
          &.has-help {
            span {
              color: var(--light-text);

              a {
                color: var(--primary);
              }
            }
          }
        }
      }
    }
  }

  .remember-toggle {
    input {
      &:checked + .toggler {
        border-color: var(--primary);

        > span {
          background: var(--primary);
        }
      }
    }

    .toggler {
      border-color: color-mix(in oklab, var(--dark-sidebar), white 12%);

      > span {
        background: color-mix(in oklab, var(--dark-sidebar), white 12%);
      }
    }
  }
}
</style>
