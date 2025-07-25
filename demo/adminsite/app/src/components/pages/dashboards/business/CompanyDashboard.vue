<script setup lang="ts">
import { useSharingChart } from '/@src/data/dashboards/company/sharingChart'
import { useUsersBarChart } from '/@src/data/dashboards/company/usersBarChart'
import { useUsersChart } from '/@src/data/dashboards/company/usersChart'
import { popovers } from '/@src/data/users/userPopovers'
import { usePersonalScoreGauge } from '/@src/data/widgets/charts/personalScoreGauge'
import type { DatePickerInstance } from '@vuepic/vue-datepicker'

const api = createApi()
const token = useUserToken()
const Horario = ref([new Date(), new Date()])
const { personalScoreGaugeOptions, onPersonalScoreGaugeReady } = usePersonalScoreGauge()
const { barData, barData2, usersBarOptions } = useUsersBarChart()
const { sharingOptions } = useSharingChart()
const { usersOptions } = useUsersChart()
const datepicker = ref<DatePickerInstance>(null)
const date = ref(new Date());
const graficaPie = ref(null);
const datosUsuarios = ref({
  nuevos: 0,
  activos: 0
})
const postTurespacioData = ref([])
const postTurespacio = ref({
  total: 0,
  publicados: 0,
  pendientes: 0,
  programados: 0,
})
const datosGenerales = ref({
  vistasPagina: 0,
  scroll: 0,
  primeraVisita: 0,
  usuariosEnganchados: 0,
  sesionesIniciadas: 0,
})



const handleDate = async (dates: Date[]) => {
  if (dates && dates.length) {
    Horario.value = dates.map(date => {
      // Formatear las fechas a 'dd-MM-yyyy'
      const day = String(date.getDate()).padStart(2, '0')
      const month = String(date.getMonth() + 1).padStart(2, '0')
      const year = date.getFullYear()
      return `${year}-${month}-${day}`
    })
    await getVistasPantallas()
    await getNewUsers()
    await getDataGeneral()
    graficaPie.value.updateChart()
    await getPosts()
  } else {
    Horario.value = []
  }

}
const getSessiones = async() => {
  try {
    // Enviar solicitud POST al backend
    const response = await api.post('/analytics/test', {},{
      headers: {
        Authorization: `Bearer ${token.value}` // Asumiendo que el token está en localStorage
      }
    })
    //console.log(response)
  }
  catch (error) {
    // Manejar errores si la solicitud falla
    console.error(error);
  }
}
const getVistasPantallas = async() => {
  try {
    // Enviar solicitud POST al backend
    const response = await api.post('/analytics/pantallas', {
      inicio: Horario.value[0],
      fin: Horario.value[1],
    },{
      headers: {
        Authorization: `Bearer ${token.value}` // Asumiendo que el token está en localStorage
      }
    })
    //console.log(response)
  }
  catch (error) {
    // Manejar errores si la solicitud falla
    console.error(error);
  }
}
const getNewUsers = async() => {
  try {
    // Enviar solicitud POST al backend
    const response = await api.post('/analytics/usuariosNuevos', {
      inicio: Horario.value[0],
      fin: Horario.value[1],
    },{
      headers: {
        Authorization: `Bearer ${token.value}` // Asumiendo que el token está en localStorage
      }
    })
    //console.log(response)
    datosUsuarios.value.activos = response.data.activeUsers.reduce((acumulador, numero) => acumulador + numero, 0)
    datosUsuarios.value.nuevos = response.data.newUsers.reduce((acumulador, numero) => acumulador + numero, 0)
  }
  catch (error) {
    // Manejar errores si la solicitud falla
    console.error(error);
  }
}
const getPosts = async() => {
  try {
    // Enviar solicitud POST al backend
    const response = await api.post('/analytics/postWordPress', {
      inicio: Horario.value[0],
      fin: Horario.value[1],
    },{
      headers: {
        Authorization: `Bearer ${token.value}` // Asumiendo que el token está en localStorage
      }
    })
    postTurespacio.value.total = response.data.total
    postTurespacio.value.publicados = response.data.publicados
    postTurespacio.value.pendientes = response.data.pendientes
    postTurespacio.value.programados = response.data.programados
  }
  catch (error) {
    // Manejar errores si la solicitud falla
    console.error(error);
  }
}
const getPostsData = async() => {
  try {
    // Enviar solicitud POST al backend
    const response = await api.post('/analytics/postWordPressData', {},{
      headers: {
        Authorization: `Bearer ${token.value}` // Asumiendo que el token está en localStorage
      }
    })
    postTurespacioData.value = response.data.posts

  }
  catch (error) {
    // Manejar errores si la solicitud falla
    console.error(error);
  }
}
const getDataGeneral = async() => {
  try {
    // Enviar solicitud POST al backend
    const response = await api.post('/analytics/dataGeneral', {
      inicio: Horario.value[0],
      fin: Horario.value[1],
    },{
      headers: {
        Authorization: `Bearer ${token.value}` // Asumiendo que el token está en localStorage
      }
    })
    //console.log(response)
    datosGenerales.value.vistasPagina = response.data.eventos.find(evento => evento.nombre === "page_view")?.conteo || 0
    datosGenerales.value.scroll = response.data.eventos.find(evento => evento.nombre === "scroll")?.conteo || 0
    datosGenerales.value.primeraVisita = response.data.eventos.find(evento => evento.nombre === "first_visit")?.conteo || 0
    datosGenerales.value.usuariosEnganchados = response.data.eventos.find(evento => evento.nombre === "user_engagement")?.conteo || 0
    datosGenerales.value.sesionesIniciadas = response.data.eventos.find(evento => evento.nombre === "session_start")?.conteo || 0
  }
  catch (error) {
    // Manejar errores si la solicitud falla
    console.error(error);
  }
}
onMounted(async () => {
  setTimeout(() => {
    usersBarOptions.series = [
      {
        name: 'Orders',
        data: barData.value,
      },
    ]
  }, 1000)
  setTimeout(() => {
    usersBarOptions.series = [
      ...usersBarOptions.series,
      {
        name: 'Abandonned',
        data: barData2.value,
      },
    ]
  }, 2500)
  // Obtener fecha actual
  const today = new Date()

  // Calcular el lunes de esta semana
  const dayOfWeek = today.getDay() // Día de la semana (0 - Domingo, 1 - Lunes, ..., 6 - Sábado)
  const mondayOffset = dayOfWeek === 0 ? -6 : (1 - dayOfWeek) // Si hoy es domingo, retrocede 6 días; si no, ajusta al lunes
  const monday = new Date(today)
  monday.setDate(today.getDate() + mondayOffset)

  // Calcular el domingo de esta semana
  const sundayOffset = dayOfWeek === 0 ? 0 : (7 - dayOfWeek)
  const sunday = new Date(today)
  sunday.setDate(today.getDate() + sundayOffset)

  // Asignar las fechas formateadas al array Horario
  Horario.value = [
    monday.toISOString().split('T')[0], // Lunes
    sunday.toISOString().split('T')[0] // Domingo
  ]
  await getVistasPantallas()
  await getNewUsers()
  await getDataGeneral()
  await getPosts()
  graficaPie.value.updateChart()
  await getPostsData()
})
</script>

<template>
  <div class="business-dashboard company-dashboard">
    <div class="columns">
      <div class="column is-4"></div>
      <div class="column is-4  has-text-centered">
        <VField label="Seleccione un rango de fechas">
          <VControl :is-valid="Horario.length > 1" :has-error="Horario.length < 1">
            <!-- <VInput class="is-uppercase" v-model="Horario" type="text" placeholder="Ej: 09:00 a 10:00 hrs" /> -->
            <Datepicker
                ref="datepicker"
                v-model="Horario"
                cancel-text="Cancelar"
                select-text="Seleccione"
                :enable-time-picker="false"
                :disable-time-range-validation="true"
                range
                :week-numbers="true"
                placeholder="Seleccione el rango de fechas para generar el reporte"
                close-on-select
                @update:model-value="handleDate"
                :format="'yyyy-MM-dd'"
            />
          </VControl>
          <span v-if="Horario.length < 1" class="help is-danger">Seleccione el rango de fechas</span>
          <br>
          <span class="is-size-4 title">Datos obtenidos de google Analytics</span>
        </VField>
      </div>
      <div class="column is-4"></div>
    </div>
    <div class="company-header is-dark-card-bordered is-dark-bg-6">
      <div class="header-item is-dark-bordered-12">
        <div class="item-inner">
          <i
            aria-hidden="true"
            class="lnil lnil-users-alt is-dark-primary"
          />
          <span class="dark-inverted">{{ datosUsuarios.nuevos }}</span>
          <p>Usuarios Nuevos</p>
        </div>
      </div>
      <div class="header-item is-dark-bordered-12">
        <div class="item-inner">
          <i
            aria-hidden="true"
            class="lnil lnil-user-alt-1 is-dark-primary"
          />
          <span class="dark-inverted">{{ datosUsuarios.activos }}</span>
          <p>Usuarios Activos</p>
        </div>
      </div>
      <div class="header-item is-dark-bordered-12">
        <div class="item-inner">
          <i
            aria-hidden="true"
            class="lnil lnil-page is-dark-primary"
          />
          <span class="dark-inverted">{{ datosGenerales.vistasPagina }}</span>
          <p>Paginas Vistas</p>
        </div>
      </div>
      <div class="header-item is-dark-bordered-12">
        <div class="item-inner">
          <i
            aria-hidden="true"
            class="lnil lnil-mouse is-dark-primary"
          />
          <span class="dark-inverted">{{ datosGenerales.scroll }}</span>
          <p>Scroll pagína</p>
        </div>
      </div>
    </div>
    <div class="company-header is-dark-card-bordered is-dark-bg-6">
      <div class="header-item is-dark-bordered-12">
        <div class="item-inner">
          <i
            aria-hidden="true"
            class="lnil lnil-home is-dark-primary"
          />
          <span class="dark-inverted">{{ datosGenerales.primeraVisita }}</span>
          <p>Primera Visita</p>
        </div>
      </div>
      <div class="header-item is-dark-bordered-12">
        <div class="item-inner">
          <i
            aria-hidden="true"
            class="lnil lnil-star-empty is-dark-primary"
          />
          <span class="dark-inverted">{{ datosGenerales.sesionesIniciadas }}</span>
          <p>Sesiones</p>
        </div>
      </div>
      <div class="header-item is-dark-bordered-12">
        <div class="item-inner">
          <i
            aria-hidden="true"
            class="lnil lnil-block-user is-dark-primary"
          />
          <span class="dark-inverted">{{ datosGenerales.usuariosEnganchados }}</span>
          <p>Usuarios Enganchados</p>
        </div>
      </div>
      <div class="header-item is-dark-bordered-12">
      </div>
    </div>

    <div class="columns is-multiline">
      <div class="column is-4">
        <div class="dashboard-card is-company">
          <VAvatar
            size="big"
            picture="/images/svg/logoturespaci.svg"
            picture-dark="/images/svg/logoturespaci.svg"
          >
          </VAvatar>

          <h3 class="dark-inverted">
            Turespacio
          </h3>
          <p>Datos de posts</p>
          <div class="description">
            <p>
              Los datos recolectados, son obtenidos de la base de datos, asi como las fechas seleccionadas al inicio de la pagina.
            </p>
          </div>
          <div class="company-stats is-dark-bordered-12">
            <div class="company-stat">
              <div>
                <span>Posts</span>
                <span class="dark-inverted">{{ postTurespacio.total }}</span>
              </div>
            </div>
            <div class="company-stat">
              <div>
                <span>Publicados</span>
                <span class="dark-inverted">{{ postTurespacio.publicados }}</span>
              </div>
            </div>
            <div class="company-stat">
              <div>
                <span>Pendientes</span>
                <span class="dark-inverted">{{ postTurespacio.pendientes }}</span>
              </div>
            </div>
            <div class="company-stat">
              <div>
                <span>Programados</span>
                <span class="dark-inverted">{{ postTurespacio.programados }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="column is-4">
        <div class="dashboard-card is-base-chart">
          <div class="content-box">
            <div class="revenue-stats is-dark-bordered-12">
              <div class="revenue-stat">
                <span>New</span>
                <span class="current">153</span>
              </div>
              <div class="revenue-stat">
                <span>Renewals</span>
                <span class="dark-inverted">641</span>
              </div>
              <div class="revenue-stat">
                <span>Resigns</span>
                <span class="dark-inverted">64</span>
              </div>
            </div>
          </div>
          <div class="chart-container">
            <ApexChart
              id="users-chart"
              :height="usersOptions.chart.height"
              :type="usersOptions.chart.type"
              :series="usersOptions.series"
              :options="usersOptions"
            />
          </div>
        </div>
      </div>
      <div class="column is-4">
        <div class="dashboard-card is-base-chart">
          <div class="content-box">
            <div class="revenue-stats is-dark-bordered-12">
              <div class="revenue-stat">
                <span>Facebook</span>
                <span class="current">64K</span>
              </div>
              <div class="revenue-stat">
                <span>Instagram</span>
                <span class="dark-inverted">78K</span>
              </div>
              <div class="revenue-stat">
                <span>Twitter</span>
                <span class="dark-inverted">25K</span>
              </div>
            </div>
          </div>
          <div class="chart-container">
            <ApexChart
              id="shares-chart"
              :height="sharingOptions.chart.height"
              :type="sharingOptions.chart.type"
              :series="sharingOptions.series"
              :options="sharingOptions"
            />
          </div>
        </div>
      </div>

      <div class="column is-12">
        <!-- Datatable -->
        <!-- @todo: table data -->
      </div>

      <div class="column is-3">
        <!-- Widget -->
        <UIWidget
          class="gauge-widget"
          straight
        >
          <template #body>
            <div class="gauge-wrap">
              <pie-chart
                  ref="graficaPie"
                  :datos="[datosGenerales]"
              ></pie-chart>
            </div>
            <div class="widget-content">

            </div>
          </template>
        </UIWidget>
      </div>
      <div class="column is-6">
        <div class="dashboard-card">
          <div class="card-head">
            <h3 class="dark-inverted">
              Subscriptions
            </h3>
          </div>
          <ApexChart
            id="bar-chart"
            :height="usersBarOptions.chart.height"
            :type="usersBarOptions.chart.type"
            :series="usersBarOptions.series"
            :options="usersBarOptions"
          />
        </div>
      </div>
      <div class="column is-3">
        <!-- Widget -->
        <UIWidget
          class="picker-widget"
          straight
        >
          <template #header>
            <div class="widget-toolbar">
              <div class="left">
                <a class="action-icon">
                  <VIcon
                    class="ltr-hidden"
                    icon="lucide:chevron-right"
                  />
                  <VIcon
                    class="rtl-hidden"
                    icon="lucide:chevron-left"
                  />
                </a>
              </div>
              <div class="center">
                <h3>October 2020</h3>
              </div>
              <div class="right">
                <a class="action-icon">
                  <VIcon
                    class="rtl-hidden"
                    icon="lucide:chevron-right"
                  />
                  <VIcon
                    class="ltr-hidden"
                    icon="lucide:chevron-left"
                  />
                </a>
              </div>
            </div>
          </template>
          <template #body>
            <table class="calendar">
              <thead>
                <tr>
                  <th scope="col">
                    Mon
                  </th>
                  <th scope="col">
                    Tue
                  </th>
                  <th scope="col">
                    Wed
                  </th>
                  <th scope="col">
                    Thu
                  </th>
                  <th scope="col">
                    Fri
                  </th>
                  <th scope="col">
                    Sat
                  </th>
                  <th scope="col">
                    Sun
                  </th>
                </tr>
              </thead>

              <tbody>
                <tr>
                  <td class="prev-month">
                    29
                  </td>
                  <td class="prev-month">
                    30
                  </td>
                  <td class="prev-month">
                    31
                  </td>
                  <td>1</td>
                  <td>2</td>
                  <td>3</td>
                  <td>4</td>
                </tr>

                <tr>
                  <td>5</td>
                  <td>6</td>
                  <td>7</td>
                  <td>8</td>
                  <td>9</td>
                  <td>10</td>
                  <td>11</td>
                </tr>

                <tr>
                  <td>12</td>
                  <td>13</td>
                  <td>14</td>
                  <td>15</td>
                  <td>16</td>
                  <td>17</td>
                  <td class="current-day">
                    18
                  </td>
                </tr>

                <tr>
                  <td>19</td>
                  <td>20</td>
                  <td>21</td>
                  <td>22</td>
                  <td>23</td>
                  <td>24</td>
                  <td>25</td>
                </tr>

                <tr>
                  <td>26</td>
                  <td>27</td>
                  <td>28</td>
                  <td>29</td>
                  <td>30</td>
                  <td>31</td>
                  <td class="next-month">
                    1
                  </td>
                </tr>
              </tbody>
            </table>
          </template>
        </UIWidget>
      </div>

      <div class="column is-12">
        <div class="dashboard-card is-tickets">
          <div class="card-head">
            <h3 class="dark-inverted">
              Últimos Post
            </h3>
            <a
              class="action-link"
              tabindex="0"
            >Ver Todos</a>
          </div>

          <div class="ticket-list">
            <!-- Ticket -->
            <template v-for="(items, index) in postTurespacioData">
              <VBlock
              :title="items.titulo + '  Autor: ' +  items.autor "
              :subtitle="items.contenido"
              :infratitle="items.fecha_hora"
              m-responsive
              class="is-dark-bordered-12"
            >
              <template #icon>
                <Tippy
                  class="has-help-cursor"
                  interactive
                  :offset="[0, 10]"
                  placement="top-start"
                >
                  <VAvatar
                    size="medium"
                    picture="https://media.cssninja.io/content/avatars/31.jpg"
                  />
                  <template #content>
                    <UserPopoverContent :user="popovers.user31" />
                  </template>
                </Tippy>
              </template>
              <template #action>
                <VButton
                  color="primary"
                  raised
                >
                  Manage
                </VButton>
              </template>
            </VBlock>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss">
@import '/@src/scss/abstracts/all';

.company-dashboard {
  .company-header {
    display: flex;
    padding: 20px;
    background: var(--white);
    border: 1px solid color-mix(in oklab, var(--fade-grey), black 3%);
    border-radius: var(--radius-large);
    margin-bottom: 1.5rem;

    .header-item {
      width: 25%;
      border-inline-end: 1px solid color-mix(in oklab, var(--fade-grey), black 3%);

      &:last-child {
        border-inline-end: none;
      }

      .item-inner {
        text-align: center;

        .lnil,
        .lnir {
          font-size: 1.8rem;
          margin-bottom: 6px;
          color: var(--primary);
        }

        span {
          display: block;
          font-family: var(--font);
          font-weight: 600;
          font-size: 1.6rem;
          color: var(--dark-text);
        }

        p {
          font-family: var(--font-alt);
          font-size: 0.95rem;
        }
      }
    }
  }

  .widget {
    height: 100%;
  }

  .dashboard-card {
    @include vuero-s-card;

    height: 100%;

    &.is-company {
      text-align: center;
      padding: 30px;

      .v-avatar {
        display: block;
        margin: 0 auto 10px;

        .button {
          position: absolute;
          bottom: 0;
          inset-inline-end: 0;
          max-width: 35px;
        }
      }

      > h3 {
        color: var(--dark-text);
        font-family: var(--font-alt);
        font-size: 1.2rem;
        font-weight: 600;
      }

      > p {
        font-size: 0.9rem;
      }

      .description {
        padding: 10px 0 0;
      }

      .company-stats {
        display: flex;
        padding-top: 20px;
        margin-top: 20px;
        border-top: 1px solid color-mix(in oklab, var(--fade-grey), black 3%);

        .company-stat {
          width: 33.3%;
          display: flex;
          align-items: center;
          justify-content: center;
          text-align: center;

          span {
            display: block;
            font-family: var(--font);

            &:first-child {
              text-transform: uppercase;
              font-family: var(--font-alt);
              font-size: 0.75rem;
              color: var(--light-text);
            }

            &:nth-child(2) {
              color: var(--dark-text);
              font-size: 1.4rem;
              font-weight: 600;
            }
          }
        }
      }
    }

    &.is-base-chart {
      padding: 0;
      display: flex;
      flex-direction: column;

      .content-box {
        padding: 30px;

        .revenue-stats {
          display: flex;
          padding-bottom: 20px;
          border-bottom: 1px solid color-mix(in oklab, var(--fade-grey), black 3%);

          .revenue-stat {
            margin-inline-end: 30px;
            font-family: var(--font);

            span {
              display: block;

              &:first-child {
                text-transform: uppercase;
                font-family: var(--font-alt);
                font-size: 0.75rem;
                color: var(--light-text);
              }

              &:nth-child(2) {
                color: var(--dark-text);
                font-size: 1.6rem;
                font-weight: 600;
              }

              &.current {
                color: var(--primary);
              }
            }
          }
        }
      }

      .chart-container {
        margin-top: auto;
      }
    }

    &.is-tickets {
      padding: 30px;

      .ticket-list {
        padding: 10px 0;

        .media-flex {
          + .media-flex {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid color-mix(in oklab, var(--fade-grey), black 3%);
          }

          .flex-meta {
            span {
              &:nth-child(2) {
                font-size: 1rem;
                margin: 4px 0;
                color: color-mix(in oklab, var(--light-text), black 20%);
                max-width: 430px;
              }

              &:nth-child(3) {
                font-size: 0.9rem;
                color: var(--light-text);
              }
            }
          }
        }
      }
    }

    .card-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;

      h3 {
        font-family: var(--font-alt);
        font-size: 1rem;
        font-weight: 600;
        color: var(--dark-text);
      }
    }
  }

  .table-wrapper {
    min-height: 0;
  }

  .dataTable-wrapper {
    .dataTable-top {
      padding: 0 !important;
      margin: 0 !important;
    }
  }
}

.is-dark {
  .company-dashboard {
    .dashboard-card {
      @include vuero-card--dark;
    }
  }
}

@media only screen and (width <= 767px) {
  .company-dashboard {
    .company-header {
      flex-wrap: wrap;

      .header-item {
        width: 50%;
        border-inline-end: none;
        border: none;
        padding: 16px 0;
      }
    }

    .dashboard-card {
      &.is-tickets {
        padding: 30px;

        .ticket-list {
          .media-flex {
            .flex-meta {
              margin-bottom: 1rem;
            }
          }
        }
      }
    }
  }
}
</style>
