import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import _ from 'lodash'
import axios from 'axios'

// window._ = require('lodash')
window._ = _

// window.axios = require('axios')
window.axios = axios

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

window.Pusher = Pusher

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    wsHost: window.location.hostname,
    wsPort: 6001,
    wssPort: 6001,
    forceTLS: false,
    disableStats: true,
})
