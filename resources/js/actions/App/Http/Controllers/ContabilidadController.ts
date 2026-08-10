import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ContabilidadController::index
 * @see app/Http/Controllers/ContabilidadController.php:15
 * @route '/contabilidad'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/contabilidad',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ContabilidadController::index
 * @see app/Http/Controllers/ContabilidadController.php:15
 * @route '/contabilidad'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContabilidadController::index
 * @see app/Http/Controllers/ContabilidadController.php:15
 * @route '/contabilidad'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ContabilidadController::index
 * @see app/Http/Controllers/ContabilidadController.php:15
 * @route '/contabilidad'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ContabilidadController::index
 * @see app/Http/Controllers/ContabilidadController.php:15
 * @route '/contabilidad'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ContabilidadController::index
 * @see app/Http/Controllers/ContabilidadController.php:15
 * @route '/contabilidad'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ContabilidadController::index
 * @see app/Http/Controllers/ContabilidadController.php:15
 * @route '/contabilidad'
 */
        indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
/**
* @see \App\Http\Controllers\ContabilidadController::storeAsientoManual
 * @see app/Http/Controllers/ContabilidadController.php:120
 * @route '/contabilidad/asientos'
 */
export const storeAsientoManual = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeAsientoManual.url(options),
    method: 'post',
})

storeAsientoManual.definition = {
    methods: ["post"],
    url: '/contabilidad/asientos',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ContabilidadController::storeAsientoManual
 * @see app/Http/Controllers/ContabilidadController.php:120
 * @route '/contabilidad/asientos'
 */
storeAsientoManual.url = (options?: RouteQueryOptions) => {
    return storeAsientoManual.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContabilidadController::storeAsientoManual
 * @see app/Http/Controllers/ContabilidadController.php:120
 * @route '/contabilidad/asientos'
 */
storeAsientoManual.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeAsientoManual.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\ContabilidadController::storeAsientoManual
 * @see app/Http/Controllers/ContabilidadController.php:120
 * @route '/contabilidad/asientos'
 */
    const storeAsientoManualForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: storeAsientoManual.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ContabilidadController::storeAsientoManual
 * @see app/Http/Controllers/ContabilidadController.php:120
 * @route '/contabilidad/asientos'
 */
        storeAsientoManualForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: storeAsientoManual.url(options),
            method: 'post',
        })
    
    storeAsientoManual.form = storeAsientoManualForm
const ContabilidadController = { index, storeAsientoManual }

export default ContabilidadController