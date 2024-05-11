import Alpine from 'alpinejs'
import mask from '@alpinejs/mask'
import intersect from '@alpinejs/intersect'
import persist from '@alpinejs/persist'
import sort from '@alpinejs/sort'
import collapse from '@alpinejs/collapse'

Alpine.plugin(collapse)
Alpine.plugin(sort)
Alpine.plugin(persist)
Alpine.plugin(mask)
Alpine.plugin(intersect)

window.Alpine = Alpine

Alpine.start()
