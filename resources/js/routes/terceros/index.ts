import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\TerceroController::index
 * @see app/Http/Controllers/TerceroController.php:12
 * @route '/terceros'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/terceros',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TerceroController::index
 * @see app/Http/Controllers/TerceroController.php:12
 * @route '/terceros'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TerceroController::index
 * @see app/Http/Controllers/TerceroController.php:12
 * @route '/terceros'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\TerceroController::index
 * @see app/Http/Controllers/TerceroController.php:12
 * @route '/terceros'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\TerceroController::index
 * @see app/Http/Controllers/TerceroController.php:12
 * @route '/terceros'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\TerceroController::index
 * @see app/Http/Controllers/TerceroController.php:12
 * @route '/terceros'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\TerceroController::index
 * @see app/Http/Controllers/TerceroController.php:12
 * @route '/terceros'
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
const terceros = {
    index: Object.assign(index, index),
}

export default terceros