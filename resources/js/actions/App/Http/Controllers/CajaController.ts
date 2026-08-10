import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\CajaController::index
 * @see app/Http/Controllers/CajaController.php:15
 * @route '/caja'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/caja',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CajaController::index
 * @see app/Http/Controllers/CajaController.php:15
 * @route '/caja'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CajaController::index
 * @see app/Http/Controllers/CajaController.php:15
 * @route '/caja'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CajaController::index
 * @see app/Http/Controllers/CajaController.php:15
 * @route '/caja'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\CajaController::index
 * @see app/Http/Controllers/CajaController.php:15
 * @route '/caja'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\CajaController::index
 * @see app/Http/Controllers/CajaController.php:15
 * @route '/caja'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\CajaController::index
 * @see app/Http/Controllers/CajaController.php:15
 * @route '/caja'
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
* @see \App\Http\Controllers\CajaController::abrir
 * @see app/Http/Controllers/CajaController.php:26
 * @route '/caja/abrir'
 */
export const abrir = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: abrir.url(options),
    method: 'post',
})

abrir.definition = {
    methods: ["post"],
    url: '/caja/abrir',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CajaController::abrir
 * @see app/Http/Controllers/CajaController.php:26
 * @route '/caja/abrir'
 */
abrir.url = (options?: RouteQueryOptions) => {
    return abrir.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CajaController::abrir
 * @see app/Http/Controllers/CajaController.php:26
 * @route '/caja/abrir'
 */
abrir.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: abrir.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\CajaController::abrir
 * @see app/Http/Controllers/CajaController.php:26
 * @route '/caja/abrir'
 */
    const abrirForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: abrir.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\CajaController::abrir
 * @see app/Http/Controllers/CajaController.php:26
 * @route '/caja/abrir'
 */
        abrirForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: abrir.url(options),
            method: 'post',
        })
    
    abrir.form = abrirForm
/**
* @see \App\Http\Controllers\CajaController::registrarEgreso
 * @see app/Http/Controllers/CajaController.php:50
 * @route '/caja/egreso'
 */
export const registrarEgreso = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: registrarEgreso.url(options),
    method: 'post',
})

registrarEgreso.definition = {
    methods: ["post"],
    url: '/caja/egreso',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CajaController::registrarEgreso
 * @see app/Http/Controllers/CajaController.php:50
 * @route '/caja/egreso'
 */
registrarEgreso.url = (options?: RouteQueryOptions) => {
    return registrarEgreso.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CajaController::registrarEgreso
 * @see app/Http/Controllers/CajaController.php:50
 * @route '/caja/egreso'
 */
registrarEgreso.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: registrarEgreso.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\CajaController::registrarEgreso
 * @see app/Http/Controllers/CajaController.php:50
 * @route '/caja/egreso'
 */
    const registrarEgresoForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: registrarEgreso.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\CajaController::registrarEgreso
 * @see app/Http/Controllers/CajaController.php:50
 * @route '/caja/egreso'
 */
        registrarEgresoForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: registrarEgreso.url(options),
            method: 'post',
        })
    
    registrarEgreso.form = registrarEgresoForm
/**
* @see \App\Http\Controllers\CajaController::cerrar
 * @see app/Http/Controllers/CajaController.php:108
 * @route '/caja/cerrar'
 */
export const cerrar = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cerrar.url(options),
    method: 'post',
})

cerrar.definition = {
    methods: ["post"],
    url: '/caja/cerrar',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CajaController::cerrar
 * @see app/Http/Controllers/CajaController.php:108
 * @route '/caja/cerrar'
 */
cerrar.url = (options?: RouteQueryOptions) => {
    return cerrar.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CajaController::cerrar
 * @see app/Http/Controllers/CajaController.php:108
 * @route '/caja/cerrar'
 */
cerrar.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cerrar.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\CajaController::cerrar
 * @see app/Http/Controllers/CajaController.php:108
 * @route '/caja/cerrar'
 */
    const cerrarForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: cerrar.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\CajaController::cerrar
 * @see app/Http/Controllers/CajaController.php:108
 * @route '/caja/cerrar'
 */
        cerrarForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: cerrar.url(options),
            method: 'post',
        })
    
    cerrar.form = cerrarForm
const CajaController = { index, abrir, registrarEgreso, cerrar }

export default CajaController