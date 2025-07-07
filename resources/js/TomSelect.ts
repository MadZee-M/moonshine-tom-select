import TomSelect from 'tom-select'
import type { RecursivePartial, TomSettings } from 'tom-select/src/types'
import { TPluginHash } from 'tom-select/src/contrib/microplugin'
import { TomOption } from 'tom-select/src/types/core'

import { crudFormQuery, getQueryString, prepareFormExtraData } from '@moonshine/Support/Forms.js'
import { dispatchEvents as de } from '@moonshine/Support/DispatchEvents.js'
import { formToJSON } from 'axios'

let pluginInitialize = false

type UserSettings = RecursivePartial<TomSettings> & Record<string, any>

export default (asyncUrl = '', settings: UserSettings = {}, plugins: TPluginHash = {}) => ({
    selectInstance: null,
    isMultiple: false,
    placeholder: null,
    searchEnabled: null,
    removeItemButton: null,
    associatedWith: null,

    isLoadedOptions: false,

    init() {
        if (! pluginInitialize) {
            document.dispatchEvent(new CustomEvent('moonshine:select.add_plugin', {
                detail: TomSelect.define
            }))
            pluginInitialize = true
        }

        const _this = this
        const commonPlugins: Record<string, Record<string, any>> = {}

        this.isMultiple = this.$el.hasAttribute('multiple')
        this.placeholder = this.$el.getAttribute('placeholder')
        this.searchEnabled = !! this.$el.dataset.searchEnabled
        this.removeItemButton = !! this.$el.dataset.removeItemButton
        this.associatedWith = this.$el.dataset.associatedWith

        if (this.associatedWith) {
            this.$el.removeAttribute('data-associated-with')
        }

        if (this.isMultiple) {
            commonPlugins.remove_button = {
                title: ''
            }
        }

        if (this.removeItemButton) {
            commonPlugins.clear_button = {
                title: ''
            }
        } else {
            commonPlugins.no_backspace_delete = {}
        }

        const commonSettings: UserSettings = {
            plugins: {
                ...commonPlugins,
                ...plugins
            },
            allowEmptyOption: ! asyncUrl && ! this.isMultiple,
            // hidePlaceholder: true,
            placeholder: this.placeholder,
            loadThrottle: 300,

            optgroupValueField: 'value',
            optgroupLabelField: 'label',
            optgroupField: 'optgroup',

            valueField: 'value',
            labelField: 'label',
            descriptionField: 'description',
            // imageField: 'image',
            childrenField: 'values',

            disabledField: 'disabled',
            searchField: ['label'],

            ...settings,

            load: asyncUrl
                ? query => this.asyncSearch(query)
                : null,

            render: {
                item(data, escape) {
                    const label = escape(data[this.settings.labelField])
                    const { image } = (data.customProperties || {})

                    return `<div class="flex gap-x-2 items-center">
                                ${_this.imageRender(image)}
                                <div>${label}</div>
                            </div>`
                },
                option(data, escape) {
                    const label = escape(data[this.settings.labelField])
                    const description = data[this.settings.descriptionField]
                        ? escape(data[this.settings.descriptionField])
                        : null
                    const { image } = (data.customProperties || {})

                    return `<div class="flex gap-x-2 items-center">
                                ${_this.imageRender(image)}
                                <div class="flex flex-col gap-2">
                                    <div>${label}</div>
                                    ${description ? `<div class="text-gray-400">${description}</div>` : ''}
                                </div>
                            </div>`
                }
            },
        }

        if (! this.searchEnabled) {
            commonSettings.controlInput = null
        }

        this.selectInstance = new TomSelect(this.$el, commonSettings)

        this.$nextTick(() => {
            this.setClassSelectedEmptyValue.call(this.selectInstance)
            this.selectInstance.on('change', function (value) {
                _this.setClassSelectedEmptyValue.call(this, value)
            })

            this.asyncOnInit()

            if (this.associatedWith && asyncUrl) {
                document.querySelector(`[name="${this.associatedWith}"]`).addEventListener(
                    'change',
                    e => {
                        this.selectInstance.clear()
                        this.selectInstance.trigger('change')
                        this.isLoadedOptions = false
                    },
                    false,
                )
            }
        })
    },

    asyncOnInit() {
        // Загрузка сразу после "Инициализации"
        if (asyncUrl && this.$el.dataset.asyncOnInit) {
            if (this.$el.dataset.asyncOnInitDropdown) {
                const asyncOnInitFocusCb = () => {
                    if (! this.isLoadedOptions) {
                        this.$el.dataset.asyncOnInitDropdownWithLoading
                            ? this.selectInstance.load('')
                            : this.asyncSearch()
                    }

                    this.selectInstance.off('focus', asyncOnInitFocusCb)
                }
                this.selectInstance.on('focus', asyncOnInitFocusCb)
            } else {
                this.selectInstance.preload()
            }
        }
    },

    async asyncSearch(query: string | null = null) {
        let canRequest = this.$el.dataset.asyncOnInit || (query !== null && query.length > 0)
        let options = []
        let optgroups = []

        if (canRequest) {
            const url = new URL(
                asyncUrl,
                asyncUrl.startsWith('/')
                    ? window.location.origin
                    : undefined
            )

            url.searchParams.append(this.$el.dataset.asyncQueryKey || 'query', query || '')

            const form = this.$el.form
            const inputs = form ? form.querySelectorAll('[name]') : []
            const value = this.selectInstance.getValue()

            let formQuery = this.$el.dataset.asyncSelectedValuesKey
                ? getQueryString({ [this.$el.dataset.asyncSelectedValuesKey]: value || '' })
                : ''

            if (this.$el.dataset.asyncWithAllFields && inputs.length) {
                formQuery += (formQuery ? '&' : '') + crudFormQuery(inputs)
            }

            options = await this.fetchOptions(url.toString() + '&' + formQuery)
            const normalizeOptions = this.normalizeOptions(options)

            optgroups = normalizeOptions.groups
            options = normalizeOptions.options
        }

        this.selectInstance.loadCallback(options, optgroups)

        this.isLoadedOptions = true
    },
    async fetchOptions(url: string) {
        let options = []
        try {
            const response = await fetch(url)
            options = await response.json()
            if (this.$el.dataset.asyncResultKey) {
                options = options[this.$el.dataset.asyncResultKey]
            }
        } catch (e) {}

        return options
    },
    normalizeOptions(items): { options: Record<string, any>, groups: Record<string, any> } {
        const {
            optgroupValueField, optgroupLabelField,
            childrenField, disabledField
        } = this.selectInstance.settings

        const options = []
        const groups = []

        for (const key in items) {
            let item = items[key]

            // Если передаются группы в виде
            // { "groupName": [...], "groupsName2": [...], .. }
            if (! /^\d+$/g.test(key) && typeof item !== 'string') {
                item = {
                    [optgroupLabelField]: key,
                    [childrenField]: item
                }
            }

            // Если есть дочерние элементы, то форматируем как Группы.
            if (item.hasOwnProperty(childrenField)) {
                let groupOptions = item[childrenField]
                delete item[childrenField]

                let groupData = item

                const group = {
                    [optgroupValueField]: JSON.stringify(groupData[optgroupLabelField]),
                    [disabledField]: !! groupData[disabledField],
                    ...groupData
                }
                groups.push(group)

                for (let key in groupOptions) {
                    options.push(this.normalizeOption(groupOptions[key], key, group[optgroupValueField]))
                }

                continue
            }

            options.push(this.normalizeOption(item, key))
        }

        return { options, groups }
    },
    normalizeOption(option, key, group): Record<string, any> {
        // Если передается в виде объекта, то нормализуем
        // { "value": "Label", "value_2": "Label 2", ... }
        if (typeof option === 'string') {
            option = {
                [this.selectInstance.settings.valueField]: key,
                [this.selectInstance.settings.labelField]: option,
            }
        }

        if (group) {
            option[this.selectInstance.settings.optgroupField] = group
        }

        const { properties, ...rest } = option

        return {
            ...rest,
            [this.selectInstance.settings.disabledField]: !! rest[this.selectInstance.settings.disabledField],
            customProperties: Array.isArray(properties) ? {} : properties || {}
        }
    },

    imageRender(image): string {
        let result = ''
        if (image) {
            const imageData = this.normalizeImageData(image)
            result = `<div class="zoom-in overflow-hidden h-${imageData.height} w-${imageData.width}">
                            <img src="${imageData.src}" class="h-full w-full object-${imageData.objectFit}" alt="" />
                        </div>`
        }

        return result
    },

    normalizeImageData(image: string|Record<string, any>): Record<string, any> {
        if (typeof image === 'string') {
            image = { src: image }
        }

        return {
            width: 10,
            height: 10,
            objectFit: 'cover',
            ...image
        }
    },

    setClassSelectedEmptyValue(value) {
        if (this.settings.mode === 'single' && this.settings.allowEmptyOption) {
            if (! arguments.length) {
                value = this.getValue()
            }

            this.wrapper.classList.toggle('selected-empty-value', value === '')
        }
    },


    dispatchEvents(componentEvent, exclude = null, extra = {}) {
        const form = this.$el.closest('form')

        if (exclude !== '*') {
            extra['_data'] = form
                ? formToJSON(prepareFormExtraData(new FormData(form), exclude))
                : { value: this.selectInstance.getValue() }
        }

        de(componentEvent, '', this, extra)
    },
})