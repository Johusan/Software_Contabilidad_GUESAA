import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ProductoController::index
 * @see app/Http/Controllers/ProductoController.php:12
 * @route '/inventario'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/inventario',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ProductoController::index
 * @see app/Http/Controllers/ProductoController.php:12
 * @route '/inventario'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProductoController::index
 * @see app/Http/Controllers/ProductoController.php:12
 * @route '/inventario'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ProductoController::index
 * @see app/Http/Controllers/ProductoController.php:12
 * @route '/inventario'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ProductoController::index
 * @see app/Http/Controllers/ProductoController.php:12
 * @route '/inventario'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ProductoController::index
 * @see app/Http/Controllers/ProductoController.php:12
 * @route '/inventario'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ProductoController::index
 * @see app/Http/Controllers/ProductoController.php:12
 * @route '/inventario'
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
* @see \App\Http\Controllers\ProductoController::storeProducto
 * @see app/Http/Controllers/ProductoController.php:23
 * @route '/inventario/producto'
 */
export const storeProducto = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeProducto.url(options),
    method: 'post',
})

storeProducto.definition = {
    methods: ["post"],
    url: '/inventario/producto',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ProductoController::storeProducto
 * @see app/Http/Controllers/ProductoController.php:23
 * @route '/inventario/producto'
 */
storeProducto.url = (options?: RouteQueryOptions) => {
    return storeProducto.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProductoController::storeProducto
 * @see app/Http/Controllers/ProductoController.php:23
 * @route '/inventario/producto'
 */
storeProducto.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeProducto.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\ProductoController::storeProducto
 * @see app/Http/Controllers/ProductoController.php:23
 * @route '/inventario/producto'
 */
    const storeProductoForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: storeProducto.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ProductoController::storeProducto
 * @see app/Http/Controllers/ProductoController.php:23
 * @route '/inventario/producto'
 */
        storeProductoForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: storeProducto.url(options),
            method: 'post',
        })
    
    storeProducto.form = storeProductoForm
/**
* @see \App\Http\Controllers\ProductoController::updateProducto
 * @see app/Http/Controllers/ProductoController.php:49
 * @route '/inventario/producto/{id}'
 */
export const updateProducto = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateProducto.url(args, options),
    method: 'put',
})

updateProducto.definition = {
    methods: ["put"],
    url: '/inventario/producto/{id}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\ProductoController::updateProducto
 * @see app/Http/Controllers/ProductoController.php:49
 * @route '/inventario/producto/{id}'
 */
updateProducto.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return updateProducto.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProductoController::updateProducto
 * @see app/Http/Controllers/ProductoController.php:49
 * @route '/inventario/producto/{id}'
 */
updateProducto.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateProducto.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\ProductoController::updateProducto
 * @see app/Http/Controllers/ProductoController.php:49
 * @route '/inventario/producto/{id}'
 */
    const updateProductoForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateProducto.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ProductoController::updateProducto
 * @see app/Http/Controllers/ProductoController.php:49
 * @route '/inventario/producto/{id}'
 */
        updateProductoForm.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateProducto.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    updateProducto.form = updateProductoForm
/**
* @see \App\Http\Controllers\ProductoController::toggleProductoEstado
 * @see app/Http/Controllers/ProductoController.php:76
 * @route '/inventario/producto/{id}/toggle'
 */
export const toggleProductoEstado = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggleProductoEstado.url(args, options),
    method: 'post',
})

toggleProductoEstado.definition = {
    methods: ["post"],
    url: '/inventario/producto/{id}/toggle',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ProductoController::toggleProductoEstado
 * @see app/Http/Controllers/ProductoController.php:76
 * @route '/inventario/producto/{id}/toggle'
 */
toggleProductoEstado.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return toggleProductoEstado.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProductoController::toggleProductoEstado
 * @see app/Http/Controllers/ProductoController.php:76
 * @route '/inventario/producto/{id}/toggle'
 */
toggleProductoEstado.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggleProductoEstado.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\ProductoController::toggleProductoEstado
 * @see app/Http/Controllers/ProductoController.php:76
 * @route '/inventario/producto/{id}/toggle'
 */
    const toggleProductoEstadoForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: toggleProductoEstado.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ProductoController::toggleProductoEstado
 * @see app/Http/Controllers/ProductoController.php:76
 * @route '/inventario/producto/{id}/toggle'
 */
        toggleProductoEstadoForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: toggleProductoEstado.url(args, options),
            method: 'post',
        })
    
    toggleProductoEstado.form = toggleProductoEstadoForm
/**
* @see \App\Http\Controllers\ProductoController::deleteProducto
 * @see app/Http/Controllers/ProductoController.php:117
 * @route '/inventario/producto/{id}'
 */
export const deleteProducto = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteProducto.url(args, options),
    method: 'delete',
})

deleteProducto.definition = {
    methods: ["delete"],
    url: '/inventario/producto/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ProductoController::deleteProducto
 * @see app/Http/Controllers/ProductoController.php:117
 * @route '/inventario/producto/{id}'
 */
deleteProducto.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return deleteProducto.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProductoController::deleteProducto
 * @see app/Http/Controllers/ProductoController.php:117
 * @route '/inventario/producto/{id}'
 */
deleteProducto.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteProducto.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\ProductoController::deleteProducto
 * @see app/Http/Controllers/ProductoController.php:117
 * @route '/inventario/producto/{id}'
 */
    const deleteProductoForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: deleteProducto.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ProductoController::deleteProducto
 * @see app/Http/Controllers/ProductoController.php:117
 * @route '/inventario/producto/{id}'
 */
        deleteProductoForm.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: deleteProducto.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    deleteProducto.form = deleteProductoForm
/**
* @see \App\Http\Controllers\ProductoController::storeCategoria
 * @see app/Http/Controllers/ProductoController.php:85
 * @route '/inventario/categoria'
 */
export const storeCategoria = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeCategoria.url(options),
    method: 'post',
})

storeCategoria.definition = {
    methods: ["post"],
    url: '/inventario/categoria',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ProductoController::storeCategoria
 * @see app/Http/Controllers/ProductoController.php:85
 * @route '/inventario/categoria'
 */
storeCategoria.url = (options?: RouteQueryOptions) => {
    return storeCategoria.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProductoController::storeCategoria
 * @see app/Http/Controllers/ProductoController.php:85
 * @route '/inventario/categoria'
 */
storeCategoria.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeCategoria.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\ProductoController::storeCategoria
 * @see app/Http/Controllers/ProductoController.php:85
 * @route '/inventario/categoria'
 */
    const storeCategoriaForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: storeCategoria.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ProductoController::storeCategoria
 * @see app/Http/Controllers/ProductoController.php:85
 * @route '/inventario/categoria'
 */
        storeCategoriaForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: storeCategoria.url(options),
            method: 'post',
        })
    
    storeCategoria.form = storeCategoriaForm
/**
* @see \App\Http\Controllers\ProductoController::updateCategoria
 * @see app/Http/Controllers/ProductoController.php:100
 * @route '/inventario/categoria/{id}'
 */
export const updateCategoria = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateCategoria.url(args, options),
    method: 'put',
})

updateCategoria.definition = {
    methods: ["put"],
    url: '/inventario/categoria/{id}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\ProductoController::updateCategoria
 * @see app/Http/Controllers/ProductoController.php:100
 * @route '/inventario/categoria/{id}'
 */
updateCategoria.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return updateCategoria.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProductoController::updateCategoria
 * @see app/Http/Controllers/ProductoController.php:100
 * @route '/inventario/categoria/{id}'
 */
updateCategoria.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateCategoria.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\ProductoController::updateCategoria
 * @see app/Http/Controllers/ProductoController.php:100
 * @route '/inventario/categoria/{id}'
 */
    const updateCategoriaForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateCategoria.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ProductoController::updateCategoria
 * @see app/Http/Controllers/ProductoController.php:100
 * @route '/inventario/categoria/{id}'
 */
        updateCategoriaForm.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateCategoria.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    updateCategoria.form = updateCategoriaForm
/**
* @see \App\Http\Controllers\ProductoController::deleteCategoria
 * @see app/Http/Controllers/ProductoController.php:132
 * @route '/inventario/categoria/{id}'
 */
export const deleteCategoria = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteCategoria.url(args, options),
    method: 'delete',
})

deleteCategoria.definition = {
    methods: ["delete"],
    url: '/inventario/categoria/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ProductoController::deleteCategoria
 * @see app/Http/Controllers/ProductoController.php:132
 * @route '/inventario/categoria/{id}'
 */
deleteCategoria.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return deleteCategoria.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProductoController::deleteCategoria
 * @see app/Http/Controllers/ProductoController.php:132
 * @route '/inventario/categoria/{id}'
 */
deleteCategoria.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteCategoria.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\ProductoController::deleteCategoria
 * @see app/Http/Controllers/ProductoController.php:132
 * @route '/inventario/categoria/{id}'
 */
    const deleteCategoriaForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: deleteCategoria.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ProductoController::deleteCategoria
 * @see app/Http/Controllers/ProductoController.php:132
 * @route '/inventario/categoria/{id}'
 */
        deleteCategoriaForm.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: deleteCategoria.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    deleteCategoria.form = deleteCategoriaForm
const ProductoController = { index, storeProducto, updateProducto, toggleProductoEstado, deleteProducto, storeCategoria, updateCategoria, deleteCategoria }

export default ProductoController