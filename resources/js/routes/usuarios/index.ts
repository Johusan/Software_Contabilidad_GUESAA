import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\UsuarioController::index
 * @see app/Http/Controllers/UsuarioController.php:18
 * @route '/usuarios'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/usuarios',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UsuarioController::index
 * @see app/Http/Controllers/UsuarioController.php:18
 * @route '/usuarios'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UsuarioController::index
 * @see app/Http/Controllers/UsuarioController.php:18
 * @route '/usuarios'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\UsuarioController::index
 * @see app/Http/Controllers/UsuarioController.php:18
 * @route '/usuarios'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\UsuarioController::index
 * @see app/Http/Controllers/UsuarioController.php:18
 * @route '/usuarios'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\UsuarioController::index
 * @see app/Http/Controllers/UsuarioController.php:18
 * @route '/usuarios'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\UsuarioController::index
 * @see app/Http/Controllers/UsuarioController.php:18
 * @route '/usuarios'
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
* @see \App\Http\Controllers\UsuarioController::store
 * @see app/Http/Controllers/UsuarioController.php:36
 * @route '/usuarios'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/usuarios',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UsuarioController::store
 * @see app/Http/Controllers/UsuarioController.php:36
 * @route '/usuarios'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UsuarioController::store
 * @see app/Http/Controllers/UsuarioController.php:36
 * @route '/usuarios'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\UsuarioController::store
 * @see app/Http/Controllers/UsuarioController.php:36
 * @route '/usuarios'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\UsuarioController::store
 * @see app/Http/Controllers/UsuarioController.php:36
 * @route '/usuarios'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\UsuarioController::update
 * @see app/Http/Controllers/UsuarioController.php:65
 * @route '/usuarios/{id}'
 */
export const update = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/usuarios/{id}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\UsuarioController::update
 * @see app/Http/Controllers/UsuarioController.php:65
 * @route '/usuarios/{id}'
 */
update.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    id: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        id: args.id,
                }

    return update.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UsuarioController::update
 * @see app/Http/Controllers/UsuarioController.php:65
 * @route '/usuarios/{id}'
 */
update.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\UsuarioController::update
 * @see app/Http/Controllers/UsuarioController.php:65
 * @route '/usuarios/{id}'
 */
    const updateForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\UsuarioController::update
 * @see app/Http/Controllers/UsuarioController.php:65
 * @route '/usuarios/{id}'
 */
        updateForm.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    update.form = updateForm
/**
* @see \App\Http\Controllers\UsuarioController::toggle
 * @see app/Http/Controllers/UsuarioController.php:105
 * @route '/usuarios/{id}/toggle'
 */
export const toggle = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(args, options),
    method: 'post',
})

toggle.definition = {
    methods: ["post"],
    url: '/usuarios/{id}/toggle',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UsuarioController::toggle
 * @see app/Http/Controllers/UsuarioController.php:105
 * @route '/usuarios/{id}/toggle'
 */
toggle.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    id: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        id: args.id,
                }

    return toggle.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UsuarioController::toggle
 * @see app/Http/Controllers/UsuarioController.php:105
 * @route '/usuarios/{id}/toggle'
 */
toggle.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\UsuarioController::toggle
 * @see app/Http/Controllers/UsuarioController.php:105
 * @route '/usuarios/{id}/toggle'
 */
    const toggleForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: toggle.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\UsuarioController::toggle
 * @see app/Http/Controllers/UsuarioController.php:105
 * @route '/usuarios/{id}/toggle'
 */
        toggleForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: toggle.url(args, options),
            method: 'post',
        })
    
    toggle.form = toggleForm
const usuarios = {
    index: Object.assign(index, index),
store: Object.assign(store, store),
update: Object.assign(update, update),
toggle: Object.assign(toggle, toggle),
}

export default usuarios