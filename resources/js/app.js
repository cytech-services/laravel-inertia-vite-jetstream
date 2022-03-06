import './bootstrap.js'
import 'vue-toastification/dist/index.css'
import '../css/app.css'

import { plugin as FormkitPlugin, defaultConfig } from '@formkit/vue'
import Toast, { POSITION } from 'vue-toastification'
import { createApp, h } from 'vue'

import { InertiaProgress } from '@inertiajs/progress'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import { useToast } from 'vue-toastification'

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel'
const toast = useToast()

let asyncViews = () => {
    return import.meta.glob('./Pages/**/*.vue')
}

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: async (name) => {
        if (import.meta.env.DEV) {
            return (await import(`./Pages/${name}.vue`)).default
        } else {
            let pages = asyncViews()
            const importPage = pages[`./Pages/${name}.vue`]
            return importPage().then((module) => module.default)
        }
    },
    setup({ el, app, props, plugin }) {
        const myApp = createApp({ render: () => h(app, props) })
            .use(Toast, {
                // Setting the global default position
                position: POSITION.TOP_CENTER
            })
            .use(plugin)
            .use(FormkitPlugin, defaultConfig({
                config: {
                    rootClasses(sectionKey, node) {
                        const type = node.props.type

                        const classConfig = {
                            outer: 'mb-5',
                            legend: 'block mb-1 font-bold',
                            label() {
                                if (type === 'text' || type === 'email' || type === 'password') { return 'block text-sm font-medium text-gray-700' }
                                if (type === 'radio') { return 'text-sm text-gray-600 mt-0.5' }
                            },
                            options() {
                                if (type === 'radio') { return 'flex flex-col flex-grow mt-2' }
                            },
                            input() {
                                if (type === 'text' || type === 'email' || type === 'password') { return 'mt-1 mb-2 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md' }
                                if (type === 'radio') { return 'mr-2' }
                                if (type === 'submit') { return 'inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500' }
                            },
                            wrapper() {
                                if (type === 'radio') { return 'flex flex-row flex-grow' }
                            },
                            messages: 'text-red-500',
                            help: 'text-xs text-gray-500'
                        }

                        // helper function to created class object from strings
                        function createClassObject(classesArray) {
                            const classList = {}
                            if (typeof classesArray !== 'string') return classList
                            classesArray.split(' ').forEach(className => {
                                classList[className] = true
                            })
                            return classList
                        }

                        const target = classConfig[sectionKey]
                        // return a class objects from strings and return them for each
                        // section key defined by inputs in FormKit
                        if (typeof target === 'string') {
                            return createClassObject(target)
                        } else if (typeof target === 'function') {
                            return createClassObject(target())
                        }

                        return {} // if no matches return an empty object
                    }
                }
            }))
            .mixin({ methods: { route } })

        // config global property after createApp and before mount
        myApp.config.globalProperties.$route = route
        myApp.config.globalProperties.$toast = toast

        myApp.mount(el)
        return myApp
    },
})

InertiaProgress.init({ color: '#4B5563' })
