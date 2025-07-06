import TomSelect from './TomSelect'

document.addEventListener('alpine:init', () => {
    Alpine.data('tomSelect', TomSelect)
})